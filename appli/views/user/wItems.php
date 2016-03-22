<div style="overflow: auto;">
	<?php foreach($this->elements as $user) :
		$this->render('user/wItem', array('user' => $user));
	endforeach; ?>
</div>