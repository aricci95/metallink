<?php

class ViewHelper {

    public $now = 0;
    public $context;
    private $_helper;

    public function __construct()
    {
        $this->context = Context::getInstance();
        $this->now = time();
        $this->_helper = $this;
    }

    public function render($view)
    {
        $view = trim($view);
        $view = str_replace("../","protect", $view);
        $view = str_replace(";","protect", $view);
        $view = str_replace("%","protect", $view);

        $viewPath = ROOT_DIR . '/appli/views/' . $view . '.php';

        if (file_exists($viewPath)) {
            include $viewPath;
        }
    }

    public function status($timestamp)
    {
        $delay = $this->now - $timestamp;
        return ($delay < ONLINE_TIME_LIMIT) ? 'online.gif' : 'offline.png';
    }

    public function getLinkStatus($userId)
    {
        $links = $this->context->get('links');

        return !empty($links[$userId]) ? $links[$userId] : LINK_STATUS_NONE;
    }

    // Affiche une div Blanche cool
    public function whiteBoxMainOpen()
    {
        echo '<table class="whiteBox" width="800">';
            echo '<tr>';
                echo '<td class="whiteBoxleftUpCorner"></td>';
                echo '<td class="whiteBoxup"></td>';
                echo '<td class="whiteBoxrightUpCorner"></td>';
            echo '</tr>';
            echo '<tr>';
                echo '<td class="whiteBoxleft"></td>';
                echo '<td>';
                echo '<div class="MainContent"style="width:772px;height:100%;">';
    }

    public function whiteBoxMainClose()
    {
                echo '</div>';
                echo '</td>';
                echo '<td class="whiteBoxright"></td>';
            echo '</tr>';
            echo '<tr>';
                echo '<td class="whiteBoxleftDownCorner"></td>';
                echo '<td class="whiteBoxdown"></td>';
                echo '<td class="whiteBoxrightDownCorner"></td>';
            echo '</tr>';
        echo '</table>';
    }

    // Affiche une div Blanche cool
    public function whiteBox($width = null, $height = '100%', $margin = null)
    {
        if($width == null) {
            $width = 740;
        }
        echo '<table class="whiteBox"';
        if($margin != null) {
            echo ' style="margin-top:'.$margin.'">';
        }
        echo '<tr><td class="whiteBoxleftUpCorner"></td><td class="whiteBoxup" style=""></td><td class="whiteBoxrightUpCorner"></td></tr>';
        echo '<tr><td class="whiteBoxleft"></td><td class="whiteBoxmiddle" style="width:'.$width.'px;height:'.$height.'px;"></td><td class="whiteBoxright"></td></tr>';
        echo '<tr><td class="whiteBoxleftDownCorner"></td><td class="whiteBoxdown"></td><td class="whiteBoxrightDownCorner"></td></tr>';
        echo '</table>';
    }

    public function formFooter($previousUrl, $submit = true)
    {
        echo '<div align="center" style="clear:both;">';
        if($submit) {
            echo '<input type="image" src="MLink/images/boutons/valider.png" value="Valider" style="border:0px;" border="0" /><br/>';
        }
        echo '<a href="'.$previousUrl.'" /><img src="MLink/images/boutons/retour.png" /></a>';
        echo '</div>';
    }

    public function printArticle($article)
    {
        if(!empty($article['art_id'])) {
            $imageUrl = ((!empty($article['art_photo_url']) && file_exists($_SERVER["DOCUMENT_ROOT"]."/MLink/photos/small/".$article['art_photo_url']))) ? $article['art_photo_url'] : 'unknowUser.jpg';

            echo '<div class="divElement">';
            echo '<a href="article/'.$article['art_id'].'" >';
                // Partie PHOTO
                echo '<div class="divPhoto" style="background:url(\'';
                echo '/MLink/photos/small/'.$imageUrl.'\');background-position: top center;">';

                echo '</div>';
                // Partie INFO
                echo '<div class="divInfo">';
                    echo '<div style="float:left;font-size:13px;color:black;">';
                        echo $this->_maxLength($article['art_libel'], 50);
                    echo '</div>';
                    echo '<br/>';
                    if(!empty($article['ville_nom_reel'])) echo '<br/>'.$article['ville_nom_reel'];
                    if(!empty($article['art_price'])) echo '<br/>'.$article['art_price'].' €';
                    else echo '<br/>prix à négocier';
                echo '</div>';
                echo '</a>';
            echo '</div>';
        }

    }

    public function printUserLogin($user)
    {
        echo '<div class="userFont" style="font-size:12px;float:left;margin-right:100px;">';
        echo '<a target="_blank" style="color:';
        if($user['user_gender'] == 1) echo '#3333CC';
        elseif($user['user_gender'] == 2) echo '#CC0000';
        echo '" href="profile/'.$user['user_id'].'">';
        echo $this->_maxLength($user['user_login'], 13);
        echo '</a></div>';
    }

    public function printConcert($concert)
    {
        ?>
        <div class="divElement" style="padding:10px;background-image: url('/MLink/images/structure/middle.jpg');min-height: 412px; width: 97%">
            <h2 style="color:black;margin:auto;width:550px;margin-bottom:10px;" align="center"><?php echo $concert['concert_libel']; ?></h2>
            <div style="float:left;">
                <div>
                    <a href="<?php echo $concert['fb_event']; ?>" target="_blank"><img style="max-width:720px;max-height:500px;" src="<?php echo $concert['flyer_url']; ?>"/></a>
                </div>
            </div>
            <div style="float:left;margin:10px;">
                <h2 class="profileInfo" style="color:black;text-align: left;">Informations</h2>
                <table width="100%" class="tableProfil">
                    <tr>
                        <th style="color:black;">Adresse : </th>
                        <td><?php echo $concert['location']; ?></td>
                    </tr>
                    <tr>
                        <th style="color:black;">Ville : </th>
                        <td><?php echo $concert['ville_nom_reel']. ' (' . $concert['departement'] . ')'; ?></td>
                    </tr>
                    <tr>
                        <th style="color:black;">Orga : </th>
                        <td><?php echo $concert['organization']; ?></td>
                    </tr>
                    <?php if (!empty($concert['price'])) : ?>
                        <tr>
                            <th style="color:black;">Prix : </th>
                            <td><?php echo $concert['price'] . ' euros'; ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
                <h2 class="profileInfo" style="color:black;text-align: left;">Artistes</h2>
                <table width="100%" class="tableProfil" style="text-align: left;">
                    <ul>
                    <?php foreach ($concert['bands'] as $band) : ?>
                        <li style="color:black;"><?php echo '- <a href="' . $band['band_website'] . '" >' . strtoupper($band['band_libel']) . '</a><span style="margin-left:10px;float:right;">' . Tools::getCleanBandStyle($band['band_style']); ?></span></li>
                    <?php endforeach; ?>
                    </ul>
                </table>
            </div>
        </div>
        <?php
    }

    public function printUser($user, $links = array())
    {
        if(!empty($user['user_id'])) {
            $imageUrl = ((!empty($user['user_photo_url']) && file_exists($_SERVER["DOCUMENT_ROOT"]."/MLink/photos/small/".$user['user_photo_url']))) ? $user['user_photo_url'] : 'unknowUser.jpg';
            ?>
            <div class="divElement">
                <a href="profile/<?php echo $user['user_id']; ?>" >
                <div class="divPhoto" style="background:url('/MLink/photos/small/<?php echo $imageUrl; ?>');background-position: top center;">
                    <img class="pictoStatus" src="MLink/images/icone/<?php echo $this->status($user['user_last_connexion']); ?>" />
                </div>
                <div class="divInfo">
                    <div class="userFont" style="float:left;margin-right:100px;color:<?php echo ($user['user_gender'] == 1) ? '#3333CC' : '#CC0000'; ?>" >
                        <?php echo $this->_maxLength($user['user_login'], 13); ?>
                    </div>
                    <?php
                        echo (isset($user['age']) && $user['age'] < 2000) ? '<br/>' . $user['age'].' ans' : '';
                        echo !empty($user['ville_nom_reel']) ? '<br/>' . $user['ville_nom_reel'] : '';
                        echo !empty($user['look_libel']) ? '<br/>' . $user['look_libel'] : '';
                    ?>
                    <div class="divLink" style="position:absolute;bottom:1;left:3;">
                    <?php
                        $this->user = $user;
                        $this->link = $this->_searchLink($links, $user['user_id']);
                        $this->render('link/wItem');
                    ?>
                    </div>
                </div>
                </a>
            </div>
            <?php
        }
    }

    // Affiche login, photo et état
    public function printUserSmall($user)
    {
        $imageUrl = ((!empty($user['user_photo_url']) && file_exists($_SERVER["DOCUMENT_ROOT"] . "/MLink/photos/small/" . $user['user_photo_url']))) ? $user['user_photo_url'] : 'unknowUser.jpg';
        echo '<a href="profile/'.$user['user_id'].'" >';
        echo '<div class="divElementSmall">';

            // Partie PHOTO
            echo '<div class="divPhoto" style="background:url(\'';
            echo '/MLink/photos/small/'.$imageUrl.'\');background-position: top center;">';
            echo '<img class="pictoStatus" src="MLink/images/icone/';
            echo $this->status($user['user_last_connexion']);
            echo '" />&nbsp;';
            echo '</div>';
            // Partie INFO
            echo '<span class="userFont" style="color:';
            if($user['user_gender'] == 1) echo '#3333CC';
            elseif($user['user_gender'] == 2) echo '#CC0000';
            echo '">';
            echo $this->_maxLength($user['user_login'], 14);
            echo '</span>';

        echo '</div>';
        echo '</a>';
    }

    private function _searchLink($links, $userId)
    {
        foreach($links as $key => $link) {
            if($userId == $link['destinataire_id'] || $userId == $link['expediteur_id']) {
                return $link;
                break;
            }
        }
    }

    private function _maxLength($string, $length)
    {
        if(strlen($string) > $length) {
            return substr($string, 0, $length).'...';
        } else {
            return $string;
        }
    }

    // Affiche une div Noire cool
    public function blackBoxOpen($cssParams = 'maxWidth')
    {

        echo '<table class="blackBox">';
        echo '<tr><td class="blackBoxleftUpCorner"></td><td class="blackBoxup"></td><td class="blackBoxrightUpCorner"></td></tr>';
        echo '<tr><td class="blackBoxleft"></td><td class="blackBoxmiddle ';
        if($cssParams == 'maxWidth') echo 'maxWidth';
        echo '" ';
        if($cssParams != 'maxWidth' && count($cssParams) > 0) {
            $this->_addStyle($cssParams);
        }
        echo '>';
    }

    public function blackBoxClose()
    {
        echo '</td><td class="blackBoxright"></td></tr>';
        echo '<tr><td class="blackBoxleftDownCorner"></td><td class="blackBoxdown"></td><td class="blackBoxrightDownCorner"></td></tr>';
        echo '</table>';
    }

    private function _addStyle($cssParams = array())
    {
        echo 'style="';
            foreach($cssParams as $key => $value) {
                echo $key.':'.$value.';';
            }
        echo '"';
    }


    // Affiche le gif Offline ou Online
    public function showStatut($userLastConnexion, $full = false)
    {
        if($this->status($userLastConnexion) == 'online.gif') {
            if($full) echo '<span style="color:green;font-size:12px;">online ';
            echo  '<img src="MLink/images/icone/online.gif" />';
        } else {
            if($full) echo '<span style="color:#B40404;font-size:12px;">offline ';
            echo '<img src="MLink/images/icone/offline.png" />';
        }

        if($full) echo '</span>';
    }
}


