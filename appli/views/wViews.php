<div class="main minHeight" style="margin-top: -31px;">
    <div class="title form">VOS VISITEURS</div>
    <div class="shadow"></div>
    <div align="center" class="results">
    	<?php if(empty($this->elements)) : ?>Aucune visite pour le moment.
    	<?php else : ?>
    		<?php $this->render('user/wItems', array('elements' => $this->elements)); ?>
    		<img class="loading" src="MLink/appli/js/loading.gif" style="display:none;" data-show="false" data-end="false" data-offset="0" data-href="views" />
    	<?php endif; ?>
    </div>
</div>