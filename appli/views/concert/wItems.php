<?php foreach($this->elements as $concert) :
    echo '<br/>';
    $this->render('concert/wItem', array('concert' => $concert));
endforeach; ?>