<?php

class CronController extends AppController
{

    protected $_authLevel = AUTH_LEVEL_NONE;

    public function renderGetConcerts()
    {
        $concerts = array();
        $limit    = 10;
        $counter  = 0;

        include_once(ROOT_DIR . '/appli/inc/simplehtmldom/simple_html_dom.php');

        $html = file_get_html('http://sueurdemetal.com/agenda-regions/concerts-metal-ile-france.htm');

        foreach($html->find('table.texte[width=750]') as $article) {
            $tmp = array();

            if ($counter <= $limit) {
                $detail_url = $article->find('div a', 0)->href;

                $explode_detail_url = explode('?c=', $detail_url);
                $tmp['concert_id'] = $explode_detail_url[1];
                $tmp['detail_url'] = 'http://sueurdemetal.com' . str_replace('..', '', $detail_url);

                if (isset($concerts[$tmp['concert_id']])) {
                    echo 'IGNORE  ' .$tmp['concert_id'] ."\n";
                    die;
                }

                if (!empty($tmp['detail_url']) && !isset($concerts[$tmp['concert_id']])) {
                    $detail_html = file_get_html($tmp['detail_url']);

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

                        $tmp['bands'][] = array(
                            'name'    => ucfirst($name_raw[0]),
                            'website' => !empty($website) ? $website->href : '',
                        );
                    }

                    $flyer = $article->find('img.avec_contour', -1);

                    $tmp['flyer'] = !empty($flyer) ? 'http://sueurdemetal.com' . str_replace('..', '', $flyer->src) :  '';

                    foreach ($data_tables[1]->find('tr') as $row) {
                        $cells = $row->find('td');

                        $tmpKey = explode(' ', strtolower(trim(str_replace(' :', '', $cells[0]->plaintext))));

                        $key = $tmpKey[0];

                        $tmp[$key] = isset($cells[1]) ? trim($cells[1]->plaintext) : trim($cells[0]);
                    }

                    unset($tmp['infos']);

                    $tmp['adresse'] = strtolower(preg_replace('/\s+/', ' ', str_replace('-', '', $tmp['adresse'])));

                    $ville_array  = explode('(', $tmp['ville']);
                    $tmp['ville'] = strtolower($ville_array[0]);
                    $tmp['departement'] = str_replace(')', '', $ville_array[1]);

                    $tmp['heure'] = str_replace('H', ':', $tmp['heure']);
                    $tmp['date']  = str_replace(' ', '', $tmp['date']) . ' ' . $tmp['heure'];

                    $dtime = DateTime::createFromFormat("d/m/Y G:i", $tmp['date']);
                    $tmp['date_timestamp'] = $dtime->getTimestamp();

                    $prix_array = explode(' ', $tmp['prix']);
                    $tmp['prix'] = $prix_array[0];

                    $counter++;
                }
            }

            if (!empty($tmp)) {
                $concerts[$tmp['concert_id']] = $tmp;
            }
        }

        $done = 0;
        foreach ($concerts as $key => $concert) {
            try {
                $this->model->concert->add($concert);
                $done++;
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        echo $done;
    }
}
