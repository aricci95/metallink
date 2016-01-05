<?php foreach($this->userMessages as $key => $message) : ?>
    <tr <?php if ($message['state_id'] == STATUS_SENT && $message['expediteur'] != User::getContextUser('id')) : ?> class="trNewMessage" <?php else : ?> class="trReadMessage" <?php endif; ?>>
        <td align="center" width="100">
            <?php echo $this->_helper->printUserSmall($message); ?>
        </td>
        <td>
            <a href="<?php echo 'message/'.$message['user_id']; ?>">
                <img src="MLink/images/icone/<?php echo ($message['state_id'] == STATUS_SENT) ? 'message.png' : 'messagelu.png'; ?>" />
            </a>
        </td>
        <td align="left" style="overflow:hidden;">
            <div style="width:450px;">
                <a href="<?php echo 'message/'.$message['user_id']; ?>" >
                    <?php echo nl2br(Tools::toSmiles($message['content'])); ?>
                </a>
            </div>
        </td>
        <td>
            <a href="<?php echo 'message/'.$message['user_id']; ?>" >
                <?php Tools::timeConvert($message['delais']); ?>
            </a>
        </td>
    </tr>
<?php endforeach; ?>
