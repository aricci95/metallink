<?php $this->render('message/wNew'); ?>
<?php if(!empty($this->parentMessages)) : ?>
    <table class="results" style="padding-right:10px;">
        <?php $this->render('message/wItems'); ?>
        <tr>
            <td>
                <img class="loading" src="MLink/appli/js/loading.gif" data-end="false" style="display:none;" data-show="false" data-offset="0" data-href="message" data-option="<?php echo $this->destinataire['user_id']; ?>" />
            </td>
        </tr>
    </table>
<?php endif;?>
