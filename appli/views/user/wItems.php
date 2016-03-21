<?php
foreach($this->elements as $user) {
	$this->render('user/wItem', array('user' => $user));
}
?>
