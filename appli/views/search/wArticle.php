<input class="create_article" type="button" value="Vendre un produit" />
<?php /*
Libellé :
<input name="search_libel" size="5" value="<?php if(isset($this->criterias['search_libel'])) echo $this->criterias['search_libel']; ?>" />
Catégorie :
<select name="search_categorie">
    <option value=""></option>
    <?php foreach($this->categories as $key => $value) : ?>
        <option value="<?php echo $value['id']; ?>" <?php if($value['id'] == $this->criterias['search_categorie']) : ?> selected="selected" <?php endif; ?> >
        <?php echo $value['libel']; ?></option>
    <?php endforeach; ?>
</select>
*/?>
