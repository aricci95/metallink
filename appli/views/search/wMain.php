<form id="search_form" action="#" method="post">
    <div id="search_form_table" align="center" style="width:760px;border:1px grey dotted;font-size:12px;padding:5px;">
        Type :
        <select id="search_type" name="search_type">
            <option value="<?php echo SEARCH_TYPE_USER; ?>" <?php if ($this->type == SEARCH_TYPE_USER) echo 'selected="selected" '; ?>>Metalheads</option>
            <option value="<?php echo SEARCH_TYPE_CONCERT; ?>" <?php if ($this->type == SEARCH_TYPE_CONCERT) echo 'selected="selected" '; ?>>Concerts</option>
            <option value="<?php echo SEARCH_TYPE_ARTICLE; ?>" <?php if ($this->type == SEARCH_TYPE_ARTICLE) echo 'selected="selected" '; ?>>Ventes</option>
           <?php /* <option value="<?php echo SEARCH_TYPE_COVOIT; ?>" <?php if ($this->type == SEARCH_TYPE_COVOIT) echo 'selected="selected" '; ?>>Covoiturage</option> */ ?>
        </select>
        <span id="search_criterias">
            <?php $this->render('search/w' . ucfirst($this->type)); ?>
        </span>
        <input id="submit_button" type="submit" src="MLink/images/boutons/bnt_search.png" ALT="Rechercher" value="Chercher" />
    </div>
</form>
<?php $this->_helper->blackBoxOpen(); ?>
    <div class="results maxWidth">
        <?php if(empty($this->elements)) : ?>
            Aucun résultat pour les critères choisis.
        <?php else : ?>
            <?php $this->render($this->type . '/wItems'); ?>
        <?php endif; ?>
    </div>
    <img class="loading" src="MLink/appli/inc/ajax/loading.gif" style="display:none;" data-show="false" data-end="false" data-offset="0" data-href="search" />
<?php $this->_helper->blackBoxClose(); ?>
