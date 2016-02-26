<?php if(empty($this->elements)) : ?>
    Aucun résultat pour les critères choisis.
<?php else : ?>
    <?php $this->render('user/wItems'); ?>
<?php endif; ?>
