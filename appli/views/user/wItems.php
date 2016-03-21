<div style="overflow: auto;margin-left: 54px;">
	<?php foreach($this->elements as $user) :
		$this->render('user/wItem', array('user' => $user));
	endforeach; ?>
</div>