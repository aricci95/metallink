<?php foreach($this->userMessages as $key => $message) : ?>
    <tr <?php if ($message['state_id'] == MESSAGE_STATUS_SENT && $message['expediteur_id'] != $this->context->get('user_id')) : ?> class="trNewMessage" <?php else : ?> class="trReadMessage" <?php endif; ?>>
        <td align="center" width="100">
            <?php $this->render('user/wThumb', array('user' => $message)); ?>
        </td>
        <td>
            <a href="<?php echo 'message/'.$message['user_id']; ?>">
                <img src="MLink/images/icone/<?php echo ($message['state_id'] == MESSAGE_STATUS_SENT) ? 'message.png' : 'messagelu.png'; ?>" />
            </a>
        </td>
        <td align="left" style="overflow:hidden;">
            <div style="width:450px;">
                <a class="blackLink" href="<?php echo 'message/'.$message['user_id']; ?>" >
                    <?php echo nl2br(Tools::toSmiles($message['content'])); ?>
                </a>
            </div>
        </td>
        <td>
            <a href="<?php echo 'message/'.$message['user_id']; ?>" >
                <?php echo Tools::timeConvert($message['delais']); ?>
            </a>
        </td>
    </tr>
<?php endforeach; ?>
