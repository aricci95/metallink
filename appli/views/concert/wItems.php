<div style="margin-top:-47px">
	<?php foreach($this->elements as $concert) :
	    $this->render('concert/wItem', array('concert' => $concert));
	endforeach; ?>
</div>