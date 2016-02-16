<form id="formPhoto" action="photo/<?php echo $this->typeId;?><?php if($this->typeId == PHOTO_TYPE_ARTICLE) echo '/'.$this->keyId;?>" method="post" enctype="multipart/form-data">
	<?php
	$errorMessage = (count($this->photos) >= 5) ? '' : 'style="display:none"';
	$newPhoto     = (count($this->photos) >= 5) ? 'style="display:none"' : '';
	?>
	<div class="errorMessage" <?php echo $errorMessage; ?> >Vous ne pouvez télécharger que 5 photos maximum.</div>
	<div style="margin-left:31%;">
		<input type="file" name="new_photo" style="display:none;" />
		<a href="">
			<img class="newPhoto" src="MLink/images/div/newphoto.png" <?php echo $newPhoto; ?> />
		</a>
		<input type="hidden" name="key_id" value="<?php echo $this->keyId; ?>" />
		<input type="hidden" name="type_id" value="<?php echo $this->typeId; ?>" />
		<div class="photoCollection">
			<?php $this->render('photo/wItems'); ?>
		</div>
	</div>
	<?php $this->_helper->formFooter('profile/'.$this->context->get('user_id'), false); ?>
</form>
