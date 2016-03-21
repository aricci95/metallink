Mot clé (Groupe, Ville) :
<input id="search_keyword" name="search_keyword" size="10" value="<?php if(isset($this->criterias['search_keyword'])) echo $this->criterias['search_keyword']; ?>" style="margin:10px;" />
Style :
<select id="search_style" name="search_style" style="margin:10px;">
    <option value="0"></option>
    <?php foreach ($this->styles as $style) :?>
    	<option value="<?php echo $style['style_id']; ?>" <?php if($this->criterias['search_style'] == $style['style_id']) echo 'selected="selected" '; ?>>
    		<?php echo ucfirst($style['style_libel']); ?>
    	</option>
    <?php endforeach; ?>
</select>
Distance :
<select id="search_distance" name="search_distance" style="margin:10px;">
    <option value="0"></option>
    <option value="20" <?php if($this->criterias['search_distance'] == 20) echo 'selected="selected" '; ?>>20 km</option>
    <option value="50" <?php if($this->criterias['search_distance'] == 50) echo 'selected="selected" '; ?>>50 km</option>
    <option value="100" <?php if($this->criterias['search_distance'] == 100) echo 'selected="selected" '; ?>>100 km</option>
    <option value="200" <?php if($this->criterias['search_distance'] == 200) echo 'selected="selected" '; ?>>200 km</option>
    <option value="300" <?php if($this->criterias['search_distance'] == 300) echo 'selected="selected" '; ?>>300 km</option>
</select>