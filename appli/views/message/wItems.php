<?php foreach($this->parentMessages as $message) : ?>
	<tr>
		<td style="overflow:hidden;">
			<div>
				<?php $this->_helper->printUserSmall($message);?>
				<?php Tools::timeConvert($message['delais']); ?>
				<span style="float:right;"><?php echo $message['state_libel']; ?></span>
				<hr>
				<?php echo Tools::toSmiles($message['content']); ?>
			</div>
		</td>
	</tr>
<?php endforeach; ?>
