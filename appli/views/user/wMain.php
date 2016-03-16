<script>
$(function(){
    $('.test-popup-link').magnificPopup({
      items: [
      <?php foreach($this->photos as $photo) : ?>
          <?php echo " {
            src: 'MLink/photos/profile/".$photo['photo_url']."'
          }, ";
          ?>
      <?php endforeach; ?>
    ],
    gallery: {
      enabled: true
    },
    type: 'image'
    });
});
</script>
<?php $this->_helper->blackBoxOpen(); ?>
<div class="divModuleProfil">
    <a class="test-popup-link" href="MLink/photos/profile/<?php echo $this->user['user_photo_url']; ?>"><div class="profilePortrait" style="float:left;background-image:url(MLink/photos/profile/<?php echo $this->user['user_photo_url']; ?>);"></div></a>
    <div class="profileInfo" style="float:left;border:1px #D8D8D8 dotted;width:390px;height:272px;padding:10px;">
    <?php if ($this->context->get('user_id') == $this->user['user_id']) : ?>
        <div style="text-align:right;">
            <a href="profile/edit">Editer le profil</a></br>
            <a href="photo/<?php echo PHOTO_TYPE_USER; ?>">Editer les photos</a></br>
            <a href="taste">Editer les goûts</a>
        </div>
    <?php else : ?>
        <div style="text-align:right;">
            <?php if($this->_helper->getLinkStatus($this->user['user_id']) == LINK_STATUS_BLACKLIST) : ?>
                <a href="profile/unblock/<?php echo $this->user['user_id']; ?>">Débloquer cette personne</a>
            <?php else : ?>
                <a href="profile/block/<?php echo $this->user['user_id']; ?>">Bloquer cette personne</a>
            <?php endif; ?>
            </br>
        </div>
    <?php endif; ?>
     <h1>
        <?php echo $this->user['user_login']; ?> <?php $this->_helper->showStatut($this->user['user_last_connexion'], true); ?>
        <div class="divLink" style="float:right;"><?php $this->render('link/wItem'); ?></div>
    </h1>

    <?php if(!empty($this->user['user_light_description'])) : ?><h2><i><?php echo stripslashes($this->user['user_light_description']); ?></i></h2><?php endif; ?>
    <?php if (isset($this->user['age']) && $this->user['age'] < 2000) : ?>
        <?php echo $this->user['age'] . ' ans'; ?>
        <br/>
        <?php echo $this->user['ville_nom_reel'] . ' (' . $this->user['ville_code_postal'] . ')'; ?>
        <br/>
    <?php endif; ?>
    Dernière connexion : <?php Tools::timeConvert($this->user['user_last_connexion']); ?>
    </div>
</div>
<?php if(!empty($this->user['user_description'])) : ?>
    <div class="divModuleProfil">
        <?php echo nl2br(stripcslashes($this->user['user_description'])); ?>
    </div>
<?php endif; ?>
<!-- INFORMATIONS -->
<div class="divModuleProfil">
    <div style="float:left;background-image:url('MLink/images/260882b-emp.jpg');width:350px;height:350px">
        <div class="vesteAPatchs">
            <?php for ($i=0; $i < 15; $i++) : ?>
                <?php if (!empty($this->tastes['data']['groupes'][$i])) : ?>
                    <span class="bandPatch"><?php echo $this->tastes['data']['groupes'][$i]; ?></span>
            <?php else :
                    break;
                endif;
            endfor; ?>
        </div>
    </div>
    <div style="float:left;">
        <h2 class="profileInfo" style="text-align:center;">Informations</h2>
        <table width="100%" class="tableProfil">
            <?php if (!empty($this->user['style_libel'])) : ?>
                <tr>
                    <th>Style favori : </th>
                    <td><?php echo $this->user['style_libel']; ?></td>
                </tr>
            <?php endif; ?>
            <tr>
                <th>Taille / Poids : </th>
                <td><?php if ($this->user['user_taille'] > 0)
                echo $this->user['user_taille'] . ' cm';
            if ($this->user['user_poids'] > 0)
                echo ' / ' . $this->user['user_poids'] . ' kg';
            ?>
                </td>
            </tr>
            <?php if (!empty($this->user['look_libel'])) : ?>
                <tr>
                    <th>Look : </th>
                    <td><?php echo $this->user['look_libel']; ?></td>
                </tr>
            <?php endif; ?>
            <?php if (!empty($this->addictions['smoke'])) : ?>
                <tr>
                    <th>Tabagisme : </th>
                    <td><?php echo $this->addictions['smoke']; ?></td>
                </tr>
            <?php endif; ?>
            <?php if (!empty($this->addictions['alcohol'])) : ?>
                <tr>
                    <th>Alcool : </th>
                    <td><?php echo $this->addictions['alcohol']; ?></td>
                </tr>
            <?php endif; ?>
            <?php if (!empty($this->addictions['drugs'])) : ?>
                <tr>
                    <th>Drogue : </th>
                    <td><?php echo $this->addictions['drugs']; ?></td>
                </tr>
            <?php endif; ?>
            <?php if (!empty($this->details['tattoo'])) : ?>
                <tr>
                    <th>Tatouages : </th>
                    <td><?php echo $this->details['tattoo']; ?></td>
                </tr>
            <?php endif; ?>
            <?php if (!empty($this->details['piercing'])) : ?>
                <tr>
                    <th>Piercings : </th>
                    <td><?php echo $this->details['piercing']; ?></td>
                </tr>
            <?php endif; ?>
            <?php if (!empty($this->user['user_profession'])) : ?>
                <tr>
                    <th>Profession : </th>
                    <td><?php echo $this->user['user_profession']; ?></td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</div>
    <!-- Gouts -->
    <?php if(!empty($this->tastes)) : ?>
    <div class="divModuleProfil">
    <?php foreach($this->tasteTypes as $typeId => $typeLibel) : ?>
        <div style="float:left;width:175px;">
            <?php $title = ($typeLibel == 'livres') ? 'Films & Livres' : $typeLibel; ?>
            <h2><?php echo ucfirst($title); ?></h2>
            <ul class="tasteDatas" data-taste-type="<?php echo $typeLibel; ?>">
                <?php if(is_array($this->tastes['data'][$typeLibel])) : ?>
                    <?php foreach($this->tastes['data'][$typeLibel] as $info) : ?>
                        <li><?php echo nl2br(stripcslashes($info)); ?></li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php $this->_helper->blackBoxClose(); ?>
