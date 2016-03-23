<form action="profile/save" method="post" enctype="multipart/form-data">
    <div class="title topShadow">GENERAL</div>
    <div class="shadow"></div>
    <table style="text-align: left;margin:10px;width:800px;">
        <tr>
            <th>Email :</th>
            <td><?php echo $this->user['user_mail']; ?>
                <span style="float:right;margin-right:20px;">
                    <img src="MLink/images/icone/delete.png"/>
                    <a href="profile/delete">Supprimer mon compte</a>
                </span>
            </td>
        </tr>
        <tr>
            <th>Pseudo :</th>
            <td>
                <input name="user_login" value="<?php echo $this->user['user_login']; ?>" />
            </td>
        </tr>
        <tr>
            <th>Mot de passe :</th>
            <td><input name="user_pwd" type="password" value="" /></td>
        </tr>
        <tr>
            <th>Vérification mot de passe :</th>
            <td><input name="verif_pwd" type="password" value="" /></td>
        </tr>
        <tr>
            <th>Sexe :</th>
            <td>
                <select name="user_gender">
                    <option value="">selectionnez</option>
                    <option value="1" <?php if($this->user['user_gender'] == 1) echo 'selected="selected" '; ?>>Homme</option>
                    <option value="2" <?php if($this->user['user_gender'] == 2) echo 'selected="selected" '; ?>>Femme</option>
                </select>
            </td>
        </tr>
        <tr>
            <th>Date de naissance :</th>
            <td>
                <input name="user_birth" class="datetimepicker" id="datetimepicker" type="text" format="d/m/Y" value="<?php if(!empty($this->user['user_birth'])) echo date("d/m/Y", strtotime($this->user['user_birth'])); ?>">
            </td>
        </tr>
        <tr>
            <th>Style de métal favori :</th>
            <td>
                <select name="style_id">
                    <option value="">selectionnez</option>
                    <?php foreach($this->styles as $key => $value)
                    {
                         echo '<option value="'.$value['style_id'].'"';
                        if($value['style_id'] == $this->user['style_id']) echo ' selected="selected" ';
                        echo '>'.$value['style_libel'].'</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th>Ville :</th>
            <td>
                <input type="text" class="autocomplete" data-type="city" value="<?php echo $this->user['ville_nom_reel'] . ' ('. $this->user['ville_code_postal'] . ')'; ?>" />
                <input type="hidden" name="ville_id" class="autocompleteValue" />
            </td>
        </tr>
        <tr>
            <th>Profession :</th>
            <td><input name="user_profession" size="40" value="<?php $this->user['user_profession']; ?>" /></td>
        </tr>
        <tr>
            <th>Description courte :</th>
            <td><input name="user_light_description" size="75" value="<?php echo $this->user['user_light_description']; ?>" /></td>
        </tr>
        <tr>
            <th>Description longue :</th>
            <td><textarea name="user_description" cols="70" rows="10"><?php echo $this->user['user_description']; ?></textarea></td>
        </tr>
    </table>

    <div class="title topShadow">PHYSIQUE</div>
    <div class="shadow"></div>
    <table style="text-align: left;margin:10px;">
        <tr>
            <th>Poids :</th>
            <td>
                <select name="user_poids">
                    <?php for($i=8; $i<=20;$i++) : ?>
                        <option <?php if((integer) $this->user['user_poids'] == ($i*5)) : ?>selected="selected"<?php endif; ?> value="<?php echo ($i*5); ?>"><?php echo ($i*5); ?></option>
                    <?php endfor; ?>
                </select> kg
            </td>
        </tr>
        <tr>
            <th>Taille :</th>
            <td>
                <select name="user_taille">
                    <?php for($i=25; $i<=42;$i++) : ?>
                        <option <?php if((integer) $this->user['user_taille'] == ($i*5)) : ?>selected="selected"<?php endif; ?> value="<?php echo ($i*5); ?>"><?php echo ($i*5); ?></option>
                    <?php endfor; ?>
                </select> cm
            </td>
        </tr>
        <tr>
            <th>Alcool :</th>
            <td>
                <select name="user_alcohol">
                    <option value="">selectionnez</option>
                    <?php foreach($this->quantities as $key => $value)
                    {
                         echo '<option value="'.$value['quantity_id'].'"';
                        if($value['quantity_id'] == $this->user['user_alcohol']) echo ' selected="selected" ';
                        echo '>'.$value['quantity_libel'].'</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th>Tatouages :</th>
            <td>
                <select name="user_tattoo">
                    <option value="">séléctionnez</option>
                    <option value="2" <?php if($this->user['user_tattoo'] == 2) echo 'selected="selected" '; ?>>non</option>
                    <option value="1" <?php if($this->user['user_tattoo'] == 1) echo 'selected="selected" '; ?>>oui</option>
                </select>
            </td>
        </tr>
        <tr>
            <th>Piercings :</th>
            <td>
                <select name="user_piercing">
                    <option value="">séléctionnez</option>
                    <option value="2" <?php if($this->user['user_piercing'] == 2) echo 'selected="selected" '; ?>>non</option>
                    <option value="1" <?php if($this->user['user_piercing'] == 1) echo 'selected="selected" '; ?>>oui</option>
                </select>
            </td>
        </tr>
        <tr>
            <th>Look :</th>
            <td>
                <select name="look_id">
                    <option value="">selectionnez</option>
                    <?php foreach($this->looks as $key => $value)
                    {
                         echo '<option value="'.$value['look_id'].'"';
                        if($value['look_id'] == $this->user['look_id']) echo ' selected="selected" ';
                        echo '>'.$value['look_libel'].'</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
    </table>

    <div class="title">PASSIONS</div>
    <div class="shadow"></div>
    <div style="text-align:center;padding-bottom:10px;font-size:14px;">
        <i>30 caractères maxi par champ</i>
    </div>
        <?php foreach($this->tasteTypes as $typeId => $typeLibel) : ?>
            <div style=" margin-left:10px;margin-bottom:10px;">
                <h2><?php echo ucfirst($typeLibel); ?></h2>
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

