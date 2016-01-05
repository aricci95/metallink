<?php $this->render('message/wNew'); ?>
<?php if(!empty($this->parentMessages)) : ?>
    <?php $this->_helper->blackBoxOpen(); ?>
        <table class="results">
            <?php $this->render('message/wItems'); ?>
            <tr>
                <td>
                    <img class="loading" src="MLink/appli/inc/ajax/loading.gif" data-end="false" style="display:none;" data-show="false" data-offset="0" data-href="message" data-option="<?php echo $this->destinataire['user_id']; ?>" />
                </td>
            </tr>
        </table>
    <?php $this->_helper->blackBoxClose(); ?>
<?php endif;?>
