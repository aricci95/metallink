<form action="article/save" method="post" enctype="multipart/form-data">
	<?php if(!empty($this->article['art_id'])) : ?>
		<input type="hidden" name="art_id" value="<?php echo $this->article['art_id']; ?>"/>
		<input type="hidden" name="id" value="<?php echo $this->article['art_id']; ?>"/>
	<?php endif; ?>
	<table class="tableWhiteBox">
		<tr>
			<th>Libellé :</th>
			<td><input name="art_libel" value="<?php echo $this->article['art_libel']; ?>" />
	         <span style="margin-left:40%;"><img src="MLink/images/icone/delete.png"/> <a href="article/<?php if(!empty($this->article['art_id'])) echo $this->article['art_id']; ?>/delete">Supprimer cet article</a></span>
	         </td>
		</tr>
		<tr>
			<th>Prix :</th>
			<td>
				<input name="art_price" value="<?php echo $this->article['art_price']; ?>" />
	        </td>
		</tr>
		<tr>
			<th>Catégorie :</th>
			<td>
				<select name="categorie_id">
					<?php foreach($this->categories as $key => $value) : ?>
						<option value="<?php echo $value['id']; ?>" <?php if($value['id'] == $this->article['categorie_id']) : ?> selected="selected" <?php endif; ?> >
						<?php echo $value['libel']; ?></option> 
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<?php if(!isset($this->article['art_id'])) : ?>
			<tr>
				<th>Ajouter une photo :</th>
				<td>
					<input type="file" name="new_photo"/>
				</td>
			</tr>
		<?php endif; ?>
		<tr>
			<th>Description :</th>
			<td><textarea name="art_description" cols="105" rows="10"><?php echo stripslashes($this->article['art_description']); ?></textarea></td>
		</tr>
		<tr>
			<th>Livré par la poste :</th>
			<td>
				<select name="livre_poste">
					<option value="0" <?php if($this->article['livre_poste'] == 0) echo 'selected="selected" '; ?>>non</option>
					<option value="1" <?php if($this->article['livre_poste'] == 1) echo 'selected="selected" '; ?>>oui</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>Récupérable sur place :</th>
			<td>
				<select name="livre_surplace">
					<option value="0" <?php if($this->article['livre_surplace'] == 0) echo 'selected="selected" '; ?>>non</option>
					<option value="1" <?php if($this->article['livre_surplace'] == 1) echo 'selected="selected" '; ?>>oui</option>
				</select>
			</td>
		</tr>
	</table>
	<br/>
	<?php if(!empty($this->article['art_id'])) : ?>
		<?php $this->_helper->formFooter('article/'.$this->article['art_id']); ?>
	<?php else : ?>
		<?php $this->_helper->formFooter('sales'); ?>
	<?php endif; ?>
</form>
