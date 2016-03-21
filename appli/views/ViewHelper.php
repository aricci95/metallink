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
        echo $this->maxLength($user['user_login'], 13);
        echo '</a></div>';
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
            echo $this->maxLength($user['user_login'], 14);
            echo '</span>';

        echo '</div>';
        echo '</a>';
    }

    public function maxLength($string, $length)
    {
        if(strlen($string) > $length) {
            return substr($string, 0, $length).'...';
        } else {
            return $string;
        }
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
            echo  '<img src="MLink/images/icone/online.gif" title="online" />';
        } else {
            echo '<img src="MLink/images/icone/offline.png" title="offline" />';
        }

        if($full) echo '</span>';
    }
}


