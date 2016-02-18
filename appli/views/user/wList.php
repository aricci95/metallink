<form action="user" method="post">
    <table align="center" style="width:760px;border:1px grey dotted;font-size:12px;">
        <tr>
            <th>Login :</th>
            <td><input name="search_login" size="10" value="<?php if(isset($this->criterias['search_login'])) echo $this->criterias['search_login']; ?>" /></td>
            <th>Sexe : </th>
            <td>
                <select name="search_gender">
                    <option value=""></option>
                    <option value="2" <?php if($this->criterias['search_gender'] == 2) echo 'selected="selected" '; ?>>Femme</option>
                    <option value="1" <?php if($this->criterias['search_gender'] == 1) echo 'selected="selected" '; ?>>Homme</option>
                </select>
            </td>
            <th>Distance : </th>
            <td>
                <select name="search_distance">
                    <option value=""></option>
                    <option value="5" <?php if($this->criterias['search_distance'] == 5) echo 'selected="selected" '; ?>>20 km</option>
                    <option value="10" <?php if($this->criterias['search_distance'] == 10) echo 'selected="selected" '; ?>>50 km</option>
                    <option value="15" <?php if($this->criterias['search_distance'] == 15) echo 'selected="selected" '; ?>>100 km</option>
                    <option value="20" <?php if($this->criterias['search_distance'] == 20) echo 'selected="selected" '; ?>>200 km</option>
                    <option value="40" <?php if($this->criterias['search_distance'] == 40) echo 'selected="selected" '; ?>>300 km</option>
                </select>
            </td>
            <th>Age : </th>
            <td>
                <select name="search_age">
                    <option value=""></option>
                    <?php for($age = 18; $age <= 48; $age+=4) : ?>
                        <option value="<?php echo $age; ?>"<?php if($this->criterias['search_age'] == $age) echo ' selected="selected" ';?>>plus de <?php echo $age; ?> ans</option>
                    <?php endfor; ?>
                </select>
            </td>
            <td><input type="image" src="MLink/images/boutons/bnt_search.png" ALT="Rechercher" /></td>
        </tr>
    </table>
</form>
<?php $this->_helper->blackBoxOpen(); ?>
    <div align="center" class="results maxWidth">
        <?php if(empty($this->elements)) : ?>
            Aucun résultat pour les critères choisis.
        <?php else : ?>
            <?php $this->render('user/wItems'); ?>
        <?php endif; ?>
    </div>
    <img class="loading" src="MLink/appli/inc/ajax/loading.gif" style="display:none;" data-show="false" data-end="false" data-offset="0" data-href="<?php echo $this->type; ?>" />
<?php $this->_helper->blackBoxClose(); ?>
