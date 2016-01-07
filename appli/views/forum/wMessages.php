<?php foreach($this->messages as $message) : ?>
    <li data-id="<?php echo $message['id']; ?>" style="margin-top:4px;margin-bottom:4px;"><?php echo '<span style="font-weight:bold;">'.$message['user_login'].' :</span> '.Tools::toSmiles($message['content']); ?><span style="float:right;color:#BDBDBD;"><?php echo $message['date']; ?></span></li>
<?php endforeach; ?>
