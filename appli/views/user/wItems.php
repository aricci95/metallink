<?php if (!empty($this->elements)) : ?>
    <div style="overflow: auto;">
        <?php foreach($this->elements as $element) :
            $this->render('user/wItem', array('user' => $element));
        endforeach; ?>
    </div>
<?php endif; ?>