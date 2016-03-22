<?php

class BandService extends Service
{
    public function fetch($band)
    {
        include_once(ROOT_DIR . '/libraries/simplehtmldom/simple_html_dom.php');

        $som_url = 'http://www.spirit-of-metal.com';

        $score  = 0;
        $scores = array();

        $band['band_libel'] = trim($band['band_libel']);

        $bandSample = array(
            'band_libel' => null,
            'band_country' => null,
            'band_logo_url' => null,
            'band_lineup_photo' => null,
            'band_style' => null,
            'band_score' => null,
            'band_sample_video_url' => null,
            'band_website' => null,
        );

        $band = array_merge($bandSample, $band);

        $html = file_get_html($som_url . '/liste_groupe.php?recherche_groupe=' . str_replace(' ', '_', $band['band_libel']));

        $link = $html->find('.StandardWideCadre a.smallTahoma', 0);

        if (!empty($link->href)) {
            $bandHtml = file_get_html($som_url . str_replace($som_url, '', $link->href));

            $websiteHtml = $bandHtml->find('div.OfficialLink a', 0);
            $band['band_website'] = !empty($websiteHtml) ? $bandHtml->find('div.OfficialLink a', 0)->href : null;
            $band['band_country'] = $bandHtml->find('ul.mediumTahoma a', -1)->plaintext;
            $band['band_style'] = trim(strtolower($bandHtml->find('a[itemprop="description"]', 0)->plaintext));

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

            if (count($scores) > 0) {
                $band['band_score'] = round($scoreSum / count($scores));
            }
        }

        if ($this->model->band->update($band)) {
            return $band;
        } else {
            return null;
        }
    }
}