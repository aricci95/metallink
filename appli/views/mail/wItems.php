<?php foreach($this->parentMessages as $message) : ?>
	<tr>
		<td style="border-top:1px grey dotted;overflow:hidden;">
			<div class="maxWidth">
				<?php $this->_helper->printUserSmall($message);?>
				<?php Tools::timeConvert($message['delais']); ?>
				<span style="float:right;"><?php echo $message['state_libel']; ?></span>
				<hr>
				<?php echo nl2br(Tools::toSmiles($message['content'])); ?>
			</div>
		</td>
	</tr>
<?php endforeach; ?>
