<?php foreach($this->parentMails as $mail) : ?>
	<tr>
		<td style="border-top:1px grey dotted;overflow:hidden;">
			<div class="maxWidth">
				<?php $this->_helper->printUserSmall($mail);?>
				<?php Tools::timeConvert($mail['mail_delais']); ?>
				<span style="float:right;"><?php echo $mail['mail_state_libel']; ?></span>
				<hr>
				<?php echo nl2br(Tools::toSmiles($mail['mail_content'])); ?>
			</div>
		</td>
	</tr>
<?php endforeach; ?>
