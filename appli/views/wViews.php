<div class="heading topShadow">VOS VISITEURS</div>
<?php if(empty($this->elements)) : ?>
    <div align="center" class="noresults">
        Aucune visite pour le moment.
    </div>
<?php else : ?>
    <div align="center" class="results">
        <?php $this->render('user/wItems', array('elements' => $this->elements)); ?>
        <img class="loading" src="MLink/appli/js/loading.gif" style="display:none;" data-show="false" data-end="false" data-offset="0" data-href="views" />
    </div>
<?php endif; ?>