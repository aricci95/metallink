<?php

class CronController extends AppController
{

    protected $_authLevel = AUTH_LEVEL_NONE;

    public function renderGetConcerts()
    {
        include_once(ROOT_DIR . '/appli/inc/simplehtmldom/simple_html_dom.php');

        $regions = array(
            'alsace',
            'aquitaine',
            'auvergne',
            'basse-normandie',
            'bourgogne',
            'bretagne',
            'centre',
            'champagne-ardennes',
            'franche-comte',
            'haute-normandie',
            'ile-france',
            'languedoc-roussillon',
            'limousin',
            'lorraine',
            'midi-pyrenees',
            'nord-calais',
            'paca',
            'pays-loire',
            'picardie',
            'poitou-charente',
            'rhone-alpes',
        );

        $concerts = array();
        $limit    = 40;
        $counter  = 0;
        $done     = 0;

        $villes_list  = $this->model->find('ville', array('ville_id', 'nom'));
        $bands_list   = $this->model->find('ref_band', array('band_id', 'band_libel'));
        $concert_list = $this->model->find('concert', array('external_id'));

        foreach ($villes_list as $ville) {
            $cleanName = Tools::getCleanName($ville['nom']);
            $villes[$cleanName] = $ville['ville_id'];
        }

        foreach ($bands_list as $bandName) {
            $cleanName              = Tools::getCleanName($bandName['band_libel']);
            $bandsNames[$cleanName] = $bandName['band_id'];
        }

        foreach ($concert_list as $gig) {
            $gigList[$gig['external_id']] = $gig['external_id'];
        }

        foreach ($regions as $region) {
            if ($counter >= $limit) {
                break;
            }

            $html = curlCall('http://sueurdemetal.com/agenda-regions/concerts-metal-' . $region . '.htm');

            foreach($html->find('table.texte[width=750]') as $article) {
                $tmp = array();

                if ($counter >= $limit) {
                    break;
                }

                $detail_url = $article->find('div a', 0)->href;

                $explode_detail_url = explode('?c=', $detail_url);
                $tmp['concert_id'] = $explode_detail_url[1];
                $tmp['detail_url'] = 'http://sueurdemetal.com' . str_replace('..', '', $detail_url);

                if (!empty($gigList[$tmp['concert_id']])) {
                    continue;
                }

                if (!empty($tmp['detail_url']) && !isset($concerts[$tmp['concert_id']])) {
                    $detail_html = curlCall($tmp['detail_url']);

                    $container = $detail_html->find('#contenupage table', 0);

                    $tmp['date_full'] = trim(preg_replace('/\s+/', ' ', $container->find('h3', 0)->plaintext));

                    $organization_array = explode(' ', trim(str_replace(' :', '', $container->find('p', 0)->plaintext)));
                    unset($organization_array[count($organization_array) - 1]);

                    $tmp['organization'] = str_replace('?', '', implode(' ',  $organization_array));

                    $data_tables = $container->find('table');

                    $bands = array();
                    $band_rows = $data_tables[0]->find('tr');

                    foreach ($band_rows as $row) {
                        $bandCell = $row->find('td');

                        $name_raw = explode('[' , strtolower(trim(preg_replace('/\s+/', ' ', $bandCell[0]->plaintext))));

                        $website = !empty($bandCell[2]) ? $bandCell[2]->find('a', 0) : null;

                        $cleanName = Tools::getCleanName($name_raw[0]);

                        if (!isset($bandsNames[$cleanName]) && !strpos('guest', $cleanName)) {
                            $band_data = array(
                                'name' => $name_raw[0],
                                'website' => !empty($website) ? $website->href : null,
                            );

                            $band_id = $this->model->band->add($band_data);
                        }

                        $tmp['bands'][] = array(
                            'band_id' => isset($bandsNames[$cleanName]) ? $bandsNames[$cleanName] : $band_id,
                            'name'    => ucfirst($name_raw[0]),
                            'website' => !empty($website) ? $website->href : null,
                        );
                    }

                    $flyer = $article->find('img.avec_contour', -1);

                    $tmp['flyer'] = !empty($flyer) ? 'http://sueurdemetal.com' . str_replace('..', '', $flyer->src) :  null;

                    foreach ($data_tables[1]->find('tr') as $row) {
                        $cells = $row->find('td');

                        $tmpKey = explode(' ', strtolower(trim(str_replace(' :', '', $cells[0]->plaintext))));

                        $key = $tmpKey[0];

                        $tmp[$key] = isset($cells[1]) ? strtolower(trim($cells[1]->plaintext)) : strtolower(trim($cells[0]));
                    }

                    unset($tmp['infos']);

                    $tmp['adresse'] = strtolower(preg_replace('/\s+/', ' ', str_replace('-', '', $tmp['adresse'])));

                    $ville_array  = explode('(', $tmp['ville']);
                    $tmp['ville'] = strtolower($ville_array[0]);
                    $tmp['departement'] = !empty($ville_array[1]) ? str_replace(')', '', $ville_array[1]) : '';

                    $tmp['heure'] = strtolower(str_replace('h', ':', $tmp['heure']));
                    $tmp['date']  = str_replace(' ', '', $tmp['date']) . ' ' . $tmp['heure'];

                    $dtime = DateTime::createFromFormat("d/m/Y G:i", $tmp['date']);

                    $tmp['date_timestamp'] = ($dtime) ? $dtime->getTimestamp() : null;

                    $prix_array = explode(' ', $tmp['prix']);
                    $tmp['prix'] = ($prix_array[0] == 0) ? null : $prix_array[0];

                    $cleanName = Tools::getCleanName($tmp['ville']);

                    $tmp['ville_id'] = isset($villes[$cleanName]) ? $villes[$cleanName] : null;

                    if (!empty($tmp)) {
                        try {
                            $this->model->concert->add($tmp);
                            $done++;
                        } catch (Exception $e) {
                            Log::err($e->getMessage());
                            $controller->get('Mailer')->sendError($e);
                        }
                    }

                    $counter++;
                }
            }
        }

        Log::info('[CRON::getConcerts] : ' . $done . ' concerts created.');

        $this->get('mailer')->send('aricci95@gmail.com', 'CRON getConcerts OK', 'CRON getConcerts via sueurdemetal ok, ' . $done . ' concerts importés.');
    }
}
