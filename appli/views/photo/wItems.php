<?php foreach($this->photos as $photo) : ?>
	<a href="">
		<div class="editPhoto profilePortrait" style="background-image:url(MLink/photos/profile/<?php echo $photo['photo_url']; ?>);" data-photo-url="<?php echo $photo['photo_url']; ?>" data-photo-id="<?php echo $photo['photo_id']; ?>">
			<?php if($photo['photo_url'] == $this->mainPhotoUrl) : ?>
				<div class="profilePhoto"></div>
			<?php else : ?>
				<div class="definePhoto">
					<div class="definePhoto removePhoto" style="margin-left:250px;"></div>
				</div>
			<?php endif; ?>
		</div>
	</a>
<?php endforeach; ?>