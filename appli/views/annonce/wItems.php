<script>
  $(function() {
    $( "#accordion" ).accordion();
  });
</script>
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
    <div id="form" style="display: none;height:258px;" class="annonce">
        <form action="annonce/save" method="post" enctype="multipart/form-data">
            <table style="width:691px;padding:10px;">
                <tr>
                    <td>Titre * :</td>
                    <td>
                        <input name="annonce_title" size="70" />
                        <span style="float:right;">
                            <b><a class="blackLink" href="#" id="close">X</a></b>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>Photo :</td>
                    <td><input name="photo" type="file" /></td>
                </tr>
                <tr>
                    <td>Description :</td>
                    <td><textarea name="annonce_content" cols="72" rows="10"></textarea></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td style="text-align: center;">
                        <input type="submit" style="width:230px;" />
                    </td>
                </tr>
            </table>
        </form>
    </div>
<?php endif; ?>

<div id="accordion">
<?php
    $tmp = true;

    foreach($this->elements as $annonce) :
        echo $tmp ? '<div class="annonce grey">' : '<div class="annonce">';
            $this->render('annonce/wItem', array('annonce' => $annonce));
        echo '</div>';

        $tmp = ($tmp) ? false : true;
    endforeach;
    ?>
</div>
