<?php if($this->status == LINK_STATUS_ACCEPTED) : ?>
	<h2>Demandes acceptées</h2>
	<?php $this->_helper->blackBoxOpen(); ?>
		<div align="center" class="maxWidth results">
			<?php if(empty($this->users)) : ?>Aucune demande acceptée.
			<?php else : ?>
				<?php $this->render('user/wItems', array('elements' => $this->users)); ?>
				<img class="loading" src="MLink/appli/js/loading.gif" style="display:none;" data-show="false" data-end="false" data-offset="0" data-href="link" data-option="<?php echo LINK_STATUS_ACCEPTED; ?>" />
			<?php endif; ?>
		</div>
	<?php $this->_helper->blackBoxClose(); ?>
<?php elseif($this->status == LINK_STATUS_BLACKLIST) : ?>
	<h2>Utilisateurs ignorés</h2>
	<?php $this->_helper->blackBoxOpen(); ?>
		<div align="center" class="maxWidth results blacklist">
			<?php if(empty($this->users)) : ?>Aucun utilisateur ignoré.
			<?php else : ?>
				<?php $this->render('user/wItems', array('elements' => $this->users)); ?>
				<img class="loading" src="MLink/appli/js/loading.gif" style="display:none;" data-show="false" data-end="false" data-offset="0" data-href="link" data-option="<?php echo LINK_STATUS_BLACKLIST; ?>" />
			<?php endif; ?>
		</div>
	<?php $this->_helper->blackBoxClose(); ?>
<?php else : ?>
	<h2>Demandes reçues</h2>
	<?php $this->_helper->blackBoxOpen(); ?>
		<div align="center" class="maxWidth received">
			<?php if(empty($this->users['received'])) : ?>Aucune demande reçue.
			<?php else : ?>
				<?php	$this->render('user/wItems', array('elements' => $this->users['received'])); ?>
			<?php endif; ?>
		</div>
	<?php $this->_helper->blackBoxClose(); ?>
	<h2>Demandes envoyées</h2>
	<?php $this->_helper->blackBoxOpen(); ?>
		<div align="center" class="maxWidth results">
			<?php if(empty($this->users['sent'])) : ?>Aucune demande envoyée.
			<?php else : ?>
				<?php $this->render('user/wItems', array('elements' => $this->users['sent'])); ?>
				<img class="loading" src="MLink/appli/js/loading.gif" style="display:none;" data-show="false" data-end="false" data-offset="0" data-href="link" data-option="<?php echo LINK_STATUS_SENT; ?>" />
			<?php endif; ?>
		</div>
	<?php $this->_helper->blackBoxClose(); ?>
<?php endif; ?>
