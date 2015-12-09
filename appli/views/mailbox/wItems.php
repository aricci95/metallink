<?php foreach($this->userMails as $key => $mail) : ?>
    <tr <?php if ($mail['mail_state_id'] == MAIL_STATUS_SENT && $mail['mail_expediteur'] != $this->getContextUser('id')) : ?> class="trNewMail" <?php else : ?> class="trReadMail" <?php endif; ?>>
        <td align="center" width="100">
            <?php echo $this->_helper->printUserSmall($mail); ?>
        </td>
        <td>
            <a href="<?php echo 'mail/'.$mail['user_id']; ?>">
                <img src="MLink/images/icone/<?php echo ($mail['mail_state_id'] == MAIL_STATUS_SENT) ? 'mail.png' : 'maillu.png'; ?>" />
            </a>
        </td>
        <td align="left" style="overflow:hidden;">
            <div style="width:450px;">
                <a href="<?php echo 'mail/'.$mail['user_id']; ?>" >
                    <?php echo nl2br(Tools::toSmiles($mail['mail_content'])); ?>
                </a>
            </div>
        </td>
        <td>
            <a href="<?php echo 'mail/'.$mail['user_id']; ?>" >
                <?php Tools::timeConvert($mail['mail_delais']); ?>
            </a>
        </td>
    </tr>
<?php endforeach; ?>
