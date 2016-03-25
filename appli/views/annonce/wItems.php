<?php if (!empty($this->new)) : ?>
    <div>
        <a href="profile/<?php echo $this->context->get('user_id'); ?>">
            <?php $photo = empty($this->context->get('user_photo_url')) ? 'unknowUser.jpg' : $this->context->get('user_photo_url'); ?>
            <div class="smallPortrait" style="float:left;background-image:url(MLink/photos/small/<?php echo $photo; ?>);"></div>
        </a>
        <a id="new" href="#">
            <img src="MLink/images/div/newannonce.png" />
        </a>
    </div>
    <div id="form" style="display: none;" class="annonce">
        <form action="annonce/save">
            <table>
                <tr>
                    <td>Titre :</td>
                    <td><input name="annonce_title" /></td>
                </tr>
                <tr>
                    <td>Description :</td>
                    <td><input name="annonce_content" /></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" />
                    </td>
                </tr>
            </table>
        </form>
    </div>
<?php endif;

$tmp = true;

foreach($this->elements as $annonce) :
    echo $tmp ? '<div class="annonce grey">' : '<div class="annonce">';
        $this->render('annonce/wItem', array('annonce' => $annonce));
    echo '</div>';

    $tmp = ($tmp) ? false : true;
endforeach;
?>
