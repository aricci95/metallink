<?php foreach($this->elements as $concert) :
    $this->render('concert/wItem', array('concert' => $concert));
endforeach; ?>