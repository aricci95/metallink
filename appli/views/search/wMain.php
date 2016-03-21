<div class="main">
    <form id="search_form" action="#" method="post">
        <div class="heading form" id="search_form_table" align="center">
            <span id="search_criterias">
                <?php $this->render('search/w' . ucfirst($this->type)); ?>
            </span>
            <input id="submit_button" type="submit" src="MLink/images/boutons/bnt_search.png" ALT="Rechercher" value="Chercher" />
        </div>
    </form>
    <div class="results" style="margin-top:-47px;">
        <?php if(empty($this->elements)) : ?>
            Aucun résultat pour les critères choisis.
        <?php else : ?>
            <?php $this->render($this->type . '/wItems'); ?>
        <?php endif; ?>
    </div>
    <img class="loading" src="MLink/appli/js/loading.gif" style="display:none;" data-show="false" data-end="false" data-offset="0" data-href="<?php echo $this->type; ?>" />
</div>
