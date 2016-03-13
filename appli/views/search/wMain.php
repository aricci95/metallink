<form id="search_form" action="#" method="post">
    <div id="search_form_table" align="center" style="width:760px;border:1px grey dotted;font-size:12px;padding:5px;">
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
    <img class="loading" src="MLink/appli/js/loading.gif" style="display:none;" data-show="false" data-end="false" data-offset="0" data-href="<?php echo $this->type; ?>" />
<?php $this->_helper->blackBoxClose(); ?>
