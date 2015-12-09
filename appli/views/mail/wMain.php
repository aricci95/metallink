<?php $this->render('mail/wNew'); ?>
<?php if(!empty($this->parentMails)) : ?>
    <?php $this->_helper->blackBoxOpen(); ?>
        <table class="results">
            <?php $this->render('mail/wItems'); ?>
            <tr>
                <td>
                    <img class="loading" src="MLink/appli/inc/ajax/loading.gif" data-end="false" style="display:none;" data-show="false" data-offset="0" data-href="mail" data-option="<?php echo $this->destinataire['user_id']; ?>" />
                </td>
            </tr>
        </table>
    <?php $this->_helper->blackBoxClose(); ?>
<?php endif;?>
