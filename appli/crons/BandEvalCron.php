<?php
class BandEvalCron extends Cron
{

	public function execute()
    {
        $limit    = 10;
        $done     = 0;

        include_once(ROOT_DIR . '/libraries/simplehtmldom/simple_html_dom.php');

        $som_url = 'http://www.spirit-of-metal.com';

        $bands = $this->model->band->findBandsToUpdate($limit);

        foreach ($bands as $key => $bandRow) {
            if ($done >= $limit) {
                break;
            }

            $bandName = strtoupper(trim($bandRow['band_libel']));

            $score  = 0;
            $scores = array();

            $band = array(
                'band_libel' => ucfirst(strtolower($bandName)),
                'band_country' => null,
                'band_logo_url' => null,
                'band_lineup_photo' => null,
                'band_style' => null,
                'band_score' => null,
                'band_sample_video_url' => null,
            );

            $html = file_get_html($som_url . '/liste_groupe.php?recherche_groupe=' . str_replace(' ', '_', $bandName));

            $link = $html->find('.StandardWideCadre a.smallTahoma', -1);

            if (!empty($link->href)) {
                $bandHtml = file_get_html($som_url . $link->href);

                $band['band_country'] = trim($bandHtml->find('ul.mediumTahoma a', -1)->plaintext);

                $styleHtml = $bandHtml->find('a[itemprop="description"]', 0);

                $band['band_style'] = trim(strtolower($styleHtml->plaintext));

                foreach($bandHtml->find('.MainTable img') as $img) {
                    if (!empty($band['band_logo_url']) && !empty($band['band_lineup_photo'])) {
                        break;
                    }

                    $imgUrl = $img->src;

                    if (strpos($imgUrl, 'logo')) {
                        $band['band_logo_url'] = $som_url . $imgUrl;
                    } else if(strpos($imgUrl, '_min.')) {
                        $band['band_lineup_photo'] = $som_url . $imgUrl;
                    }
                }

                $sampleVideoHtml = $bandHtml->find('a');

                foreach ($sampleVideoHtml as $html_a) {
                    if (strpos($html_a->href, 'video_read')) {
                        $videoUrl = explode("'", str_replace(array("javascript:popup('", "');"), array(), $html_a->href));

                        $videoHtml = file_get_html($som_url . $videoUrl[0]);

                        $band['band_sample_video_url'] = $videoHtml->find('iframe', 0)->src;
                    }
                }

                foreach($bandHtml->find('tr.ligne_disco') as $tr) {
                    $a_album = $tr->find('a[itemprop="name"]', 0);
                    $explodedString = explode("'", $a_album->onmouseover);
                    $albumId = $explodedString[1];

                    $albumHtml = file_get_html($som_url . '/ajax/getInfoAlbum.php?id_album=' . $albumId);

                    $note = str_replace('/20', '', $albumHtml->find('NOTE', 0)->plaintext);

                    if ($note > 0) {
                        $scores[] = $note;
                    }
                }

                $scoreSum = 0;
                foreach ($scores as $value) {
                    $scoreSum += $value;
                }

                $band['band_score'] = round($scoreSum / count($scores));
            }

            if ($this->model->band->update($band)) {
                echo $band['band_libel'] . ' updated<br>';
                $done++;
            }
        }

        Log::info('[CRON::BandEval] : ' . $done . ' band updated.');

        $this->get('mailer')->send(ADMIN_MAIL, 'CRON band Eval OK', 'CRON band eval via spirit-of-metal ok, ' . $done . ' groupes mis à jour.', false);

        echo $done;
    }
}