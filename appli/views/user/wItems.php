<?php foreach($this->elements as $element) :
	$this->render('user/wItem', array('user' => $element));
endforeach; ?>