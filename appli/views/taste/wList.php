<div style="text-align:center;padding-bottom:10px;font-size:14px;">
    <i>30 caractères maxi par champ</i>
</div>
<form action="taste" method="post" enctype="multipart/form-data">
    <?php foreach($this->tasteTypes as $typeId => $typeLibel) : ?>
        <div style=" margin-left:10px;margin-bottom:10px;">
            <?php $title = ($typeLibel == 'livres') ? 'Films & Livres' : $typeLibel; ?>
            <h2><?php echo ucfirst($title); ?></h2>
            <ul class="tasteDatas" data-taste-type="<?php echo $typeLibel; ?>">
                <?php if(!empty($this->tastes['data'][$typeLibel])) : ?>
                    <?php foreach($this->tastes['data'][$typeLibel] as $info) : ?>
                        <?php $this->render('taste/wItem', array('type' => $typeLibel, 'libel' => $info)); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                <li>
                    <input class="addTaste taste" name="<?php echo $typeLibel ?>[]" maxlength="30" />
                </li>
            </ul>
        </div>
    <?php endforeach; ?>
    <?php $this->_helper->formFooter('profile/'.$this->context->get('user_id')); ?>
</form>
