<?php $this->_helper->blackBoxOpen(); ?>
	<table style="border-collapse: collapse;" class="results maxWidth">
		<tr style="font-size:15px;text-align:center;">
			<th>Utilisateur</th>
			<th width="50">Etat</th>
			<th>Objet</th>
			<th width="100">Date</th>
		</tr>
		<?php if (empty($this->userMails)) : ?>
			<tr>
			 	<td style='text-align:center;padding-top:20px;' colspan='4'>Aucun message.</td>
			</tr>
		<?php else : ?>
			<?php $this->render('mailbox/wItems'); ?>
		<?php endif; ?>
		<tr>
			<td>
				<img class="loading" src="MLink/appli/inc/ajax/loading.gif" style="display:none;" data-show="false" data-offset="0" data-href="mailbox" data-end="false" />
			</td>
		</tr>
	</table>
<?php $this->_helper->blackBoxClose(); ?>
