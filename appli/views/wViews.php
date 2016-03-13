<h2>Vos visites</h2>
<?php $this->_helper->blackBoxOpen(); ?>
	<div align="center" class="maxWidth results">
		<?php if(empty($this->elements)) : ?>Aucune visite pour le moment.
		<?php else : ?>
			<?php $this->render('user/wItems', array('elements' => $this->elements)); ?>
			<img class="loading" src="MLink/appli/js/loading.gif" style="display:none;" data-show="false" data-end="false" data-offset="0" data-href="views" />
		<?php endif; ?>
	</div>
<?php $this->_helper->blackBoxClose(); ?>
