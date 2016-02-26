<form id="search_form" action="#" method="post">
    <div id="search_form_table" align="center" style="width:760px;border:1px grey dotted;font-size:12px;padding:5px;">
        Type :
        <select id="search_type" name="search_type">
            <option value="1" <?php if ($this->criterias['search_type'] == 1) echo 'selected="selected" '; ?>>Membre</option>
            <option value="2" <?php if ($this->criterias['search_type'] == 2) echo 'selected="selected" '; ?>>Concert</option>
            <option value="3" <?php if ($this->criterias['search_type'] == 3) echo 'selected="selected" '; ?>>Vente</option>
            <option value="4" <?php if ($this->criterias['search_type'] == 3) echo 'selected="selected" '; ?>>Covoiturage</option>
        </select>
        <span id="search_criterias">
            <?php $this->render('search/wUser'); ?>
        </span>
        <input id="submit_button" type="submit" src="MLink/images/boutons/bnt_search.png" ALT="Rechercher" value="Chercher" />
    </div>
</form>
<?php $this->_helper->blackBoxOpen(); ?>
    <div align="center" class="results maxWidth">
        <?php if(empty($this->elements)) : ?>
            Aucun résultat pour les critères choisis.
        <?php else : ?>
            <?php $this->render('user/wItems'); ?>
        <?php endif; ?>
    </div>
    <img class="loading" src="MLink/appli/inc/ajax/loading.gif" style="display:none;" data-show="false" data-end="false" data-offset="0" data-href="search" />
<?php $this->_helper->blackBoxClose(); ?>
