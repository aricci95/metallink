<?php foreach($this->parentMessages as $message) : ?>
	<tr <?php echo ($message['expediteur_id'] == $this->context->get('user_id')) ? '' : 'class="grey"'; ?>>
		<td style="overflow:hidden;">
			<div>
				<a href="profile/<?php echo $message['expediteur_id']; ?>">
					<?php $photo = empty($message['user_photo_url']) ? 'unknowUser.jpg' : $message['user_photo_url']; ?>
					<div class="smallPortrait" style="float:left;background-image:url(MLink/photos/small/<?php echo $photo; ?>);"></div>
				</a>
				<?php Tools::timeConvert($message['delais']); ?>
				<span style="float:right;"><?php echo $message['state_libel']; ?></span>
				<hr>
				<?php echo Tools::toSmiles($message['content']); ?>
			</div>
		</td>
	</tr>
<?php endforeach; ?>
