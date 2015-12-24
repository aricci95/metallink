<?php

class Util extends AppModel
{

    // Liste une table
    function getItemsFromTable($table, $order = false)
    {

        $type = str_replace("ref_", "", $table);
        if ($order == false) {
            $libel = $type.'_libel';
        } else {
            $libel = $order;
        }
        $sql = "SELECT * FROM $table ORDER BY $libel";
        return $this->fetch($sql);
    }

    // Affiche la mini liste pour l'édition du profil
    function printList($list, $libel)
    {
        echo '<div class="tableExtBorder"><ul">';
        foreach ($list as $key => $value) {
            echo '<li>'.$value[$libel.'_libel'].' <img src="../images/corbeille.png"/></li>';
        }
        echo '</ul>';
        echo '<input name["'.$libel.'"_libel] /> <a href="">ajouter</a>';
        echo '</div>';
    }

    // Récupère une liste
    function getList($libel)
    {
        $sql = 'SELECT
					'.$libel.'_libel,
					ref_'.$libel.'.'.$libel.'_id as '.$libel.'_id
				FROM ref_'.$libel.',
					list_'.$libel.',
					user
				WHERE user.list_id = list_'.$libel.'.list_id
				AND ref_'.$libel.'.'.$libel.'_id = list_'.$libel.'.'.$libel.'_id';

        return $this->fetch($sql);
    }

    // Affiche la liste de goûts
    function tastePannel($userId, $type, $datas, $libel)
    {
        ?>
		<form action="edittastes.php?action=add" method="post">
			<input type="hidden" name="type" value="<?php echo $type; ?>" />
			<table class="tableExtBorder">
				<th colspan="2"><?php echo $libel; ?></th>
				<?php
                if (count($datas) > 0) {
                    foreach ($datas as $key => $value) {
                        echo '<tr><td>';
                        if ($type == 'band') {
                            echo '<a target="_blank" href="http://www.spirit-of-metal.com/groupe-groupe-'.str_replace(' ', '_', stripslashes($value[$type.'_libel'])).'-l-fr.html">'.stripslashes($value[$type.'_libel']).'</a>';
                        } else {
                            echo stripslashes($value[$type.'_libel']);
                        }
                        echo '</td>';
                        echo '<td><a href="edittastes.php?action=delete&type='.$type.'&id='.$value[$type.'_id'].'" ><img src="../images/corbeille.png" /></a></td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="2">Aucun</td></tr>';
                }
                ?>
				<tr>
					<td><input name="newEntry" /></td>
					<td><input type="submit" value="ajouter" /></td>
				</tr>
			</table>
			</form>
			<?php
    }

    // Affiche login, photo et état
    function printUser($user, $controlPanel = false, $links = null, $requests = null)
    {
        echo '<div class="divElement">';
    //	if($controlPanel) {
            echo '<table>';
            echo '<tr>';
            echo '<td colspan="2" align="center">';
    //	}
        echo '<a href="profile.php?id='.$user['user_id'].'" >';
        echo '<img class="smallPortait" src="photos/small/';
        if (!empty($user['user_photo_url']) && file_exists("photos/small/".$user['user_photo_url'])) {
            echo $user['user_photo_url'];
        } else {
            echo 'unknowUser.jpg';
        }
        echo '" />';
        echo '</a>';
        //if($controlPanel) {
            // Initialisation des variables
            $linked = false;
            $request = false;
            $sended = false;
            echo '</td>';
            echo '</tr>';
            echo '<tr>';

            // On parcours tous les linkRequests de l'utilisateur pour changer l'affichage
        if ($requests != null) {
            foreach ($requests as $key => $value) {
                // SI demande Link
                if ($request == false) {
                    if ($value['mail_expediteur'] == $user['user_id']) {
                        $request = $value['mail_id'];
                    } elseif ($value['mail_destinataire'] == $user['user_id']) $sended = $value['mail_id'];
                }
            }
        }
            // On parcours tous les link de l'utilisateur pour changer l'affichage
        if ($links != null) {
            foreach ($links as $key => $value) {
                // SI déjà Linked
                if ($linked == false) {
                    if ($value['mail_destinataire'] == $user['user_id']
                    || $value['mail_expediteur'] == $user['user_id'] ) {
                        $linked = $value['mail_id'];
                    }
                }
            }
        }

            // Affichage des infos
            echo '<tr>';
            echo '<td align="center"><font class="userFont" color="';
        if ($user['user_gender'] == 1) {
            echo '#3333CC';
        } elseif ($user['user_gender'] == 2) echo '#CC0000';
            echo '">';
            // Etat de l'utilisateur
            echo '<img style="position:relative;top:5px;" src="../images/';
            echo (array_key_exists($userId, $this->userStatuses)) ? 'online.gif' : 'offline.png';
            echo '" />&nbsp;';
            echo $user['user_login'];
            //	'('.$user['age']. ' ans)';
            // Si l'âge est défini
        if (isset($user['age']) && $user['age'] < 2000) {
            echo ' ('.$user['age'].' ans)';
        }
            echo '</font></td>';
            echo '</tr>';
            echo '<tr>';
            // Choix du pannel
        if ($controlPanel) {
            // SI différent de lui même
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $user['user_id']) {
                // SI Linked
                if ($linked != false) {
                    echo '<td title="Blacklist" class="userFont" align="center"><a href="blacklist.php?action=add&id='.$user['user_id'].'" ><img src="../images/disc_close.png" /></a>';
                    echo '<a title="Envoyer un message" href="mail.php?user_id='.$user['user_id'].'" ><img src="../images/mail.png" /></a>';
                    if (array_key_exists($userId, $this->userStatuses)) {
                        echo '<a title="Chatter" href="javascript:void(0)" onclick="javascript:chatWith(\''.$user['user_login'].'\')"><img src="../images/chat.png" /></a>';
                    }
                    echo '</td>';
                } // SI demande Link
                elseif ($sended != false) {
                    echo '<td align="center" class="userFont"><img src="../images/disc_sans.png" title="Demande en attente" />Demande envoyée</td>';
                } // SI demande Link à valider
                elseif ($request != false) {
                    echo '<td class="userFont" align="center"><a href="link.php?action=validate&value=7&id='.$request.'" ><img src="../images/disc_resolue.png" /> oui</a> ';
                    echo '<a href="link.php?action=validate&value=8&id='.$request.'" ><img src="../images/disc_close.png" /> non</a></td>';
                } // SI pas linked, ni En attente, on propose le link
                else {
                    echo '<td class="userFont" align="center">';
                    echo '<a id="lienlink'.$user['user_id'].'" href="javascript:void(0)"
						onClick="$.x_links_link(\''.$user['user_id'].'\');changeToWait(link'.$user['user_id'].' , lienlink'.$user['user_id'].', newimg'.$user['user_id'].');" >';
                    echo '<img id="link'.$user['user_id'].'" src="../images/disc.png" title="Linker cette personne" />Link</a>';
                    echo '<img id="newimg'.$user['user_id'].'" src="" style="display:none;"/>';
                    echo '</td>';
                }
            }
        }
            echo '</tr>';
            echo '</table>';
        //}
        echo '</div>';
    }

    // Rajoute une entrée dans la liste
    function addToList($type, $listId, $libel)
    {
        // On vérifie l'existence du libellé
        $checkSql = "SELECT * FROM ref_".$type." WHERE ".$type."_libel = '".$libel."';";
        $check = $this->fetchOnly($checkSql);
        // SI il existe
        if ($check != null) {
            // On rajoute dans table de liste
            $sqlList = "INSERT INTO list_".$type." VALUES ('".$listId."', '".$check[$type.'_id']."')";
            if ($this->execute($sqlList)) {
                return true;
            } else {
                return false;
            }
        } // SI il existe pas
        else {
            // On cherche le dernier id
            $sqlMax = $this->fetchOnly("SELECT (max(".$type."_id)+1) as max FROM ref_".$type.";");
            // On rajoute dans table de reference
            $sqlRef = "INSERT INTO ref_".$type." VALUES ('".$sqlMax['max']."', '".$libel."')";
            if ($this->execute($sqlRef)) {
                // On rajoute dans table de liste
                $sqlList = "INSERT INTO list_".$type." VALUES ('".$listId."', '".$sqlMax['max']."')";
                if ($this->execute($sqlList)) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    function getListIdByUser($userId)
    {
        $sql = "SELECT list_id FROM user WHERE user_id = '".$this->securize($userId)."';";
        return $this->fetchOnly($sql);
    }

    function deleteTaste($listId, $type, $itemId)
    {
        $sql = "DELETE FROM list_".$type."
				WHERE list_id = '".$this->securize($listId)."'
				AND ".$type."_id = '".$this->securize($itemId)."';";
        $this->execute($sql);
    }

    public function deleteTempDatas()
    {
       // Chat
        $this->execute("DELETE FROM chat WHERE sent < NOW( ) - INTERVAL 2 DAY ;");
       // Vues
        $this->execute("DELETE FROM userviews WHERE view_date < NOW( ) - INTERVAL 2 DAY ;");
       // Mails
        $mailSQL = "DELETE FROM mail
                       WHERE mail_state_id != 7
                       AND mail_date < NOW( ) - INTERVAL 6 MONTH ;";
        $this->execute($mailSQL);
        echo 'nettoyage effectué';
        echo "<meta http-equiv='REFRESH' content='3;URL=admin.php'>";
    }
}
?>
