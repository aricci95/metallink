Mot clé (Groupe, Ville) :
<input id="search_keyword" name="search_keyword" size="10" value="<?php if(isset($this->criterias['search_keyword'])) echo $this->criterias['search_keyword']; ?>" />
Distance :
<select id="search_distance" name="search_distance">
    <option value=""></option>
    <option value="5" <?php if($this->criterias['search_distance'] == 5) echo 'selected="selected" '; ?>>20 km</option>
    <option value="10" <?php if($this->criterias['search_distance'] == 10) echo 'selected="selected" '; ?>>50 km</option>
    <option value="15" <?php if($this->criterias['search_distance'] == 15) echo 'selected="selected" '; ?>>100 km</option>
    <option value="20" <?php if($this->criterias['search_distance'] == 20) echo 'selected="selected" '; ?>>200 km</option>
    <option value="40" <?php if($this->criterias['search_distance'] == 40) echo 'selected="selected" '; ?>>300 km</option>
</select>