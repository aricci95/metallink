<?php if(empty($this->elements)) : ?>
    <div style="text-align: center;margin-top:50px;">Aucun résultat pour les critères choisis.</div>
<?php else : ?>
    <?php $this->render($this->type . '/wItems'); ?>
<?php endif; ?>
