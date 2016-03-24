<?php foreach($this->parentMessages as $message) : ?>
	<tr <?php echo ($message['expediteur_id'] == $this->context->get('user_id')) ? '' : 'class="grey"'; ?>>
		<td>
			<table>
				<tr>
					<td style="vertical-align: top;">
						<a href="profile/<?php echo $message['expediteur_id']; ?>">
							<?php $photo = empty($message['user_photo_url']) ? 'unknowUser.jpg' : $message['user_photo_url']; ?>
							<div class="smallPortrait" style="height: 120px;float:left;background-image:url(MLink/photos/small/<?php echo $photo; ?>);"></div>
						</a>
					</td>
					<td style="width: 672px;vertical-align: top;">
						<?php Tools::timeConvert($message['delais']); ?>
						<span style="float:right;"><?php echo $message['state_libel']; ?></span>
						<hr>
						<?php echo Tools::toSmiles($message['content']); ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
<?php endforeach; ?>
