<?php if(empty($this->elements)) : ?>
    <div style="text-align: center;">Aucun résultat pour les critères choisis.</div>
<?php else : ?>
    <?php $this->render($this->type . '/wItems'); ?>
<?php endif; ?>
