<form action="profile/save" method="post" enctype="multipart/form-data">
<h2>General</h2>
<table class="tableWhiteBox">
    <tr>
        <th>Pseudo :</th>
        <td><input name="user_login" value="<?php echo $this->user['user_login']; ?>" />
         <span style="margin-left:40%;"><img src="MLink/images/icone/delete.png"/> <a href="profile/delete">Supprimer mon compte</a></span>
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
        <th>Message :</th>
        <td><input name="user_mail" value="<?php echo $this->user['user_mail']; ?>" /></td>
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
            <input type="text" class="autocomplete" data-type="city" />
            <input type="hidden" name="ville_id" class="autocompleteValue" />
        </td>
    </tr>
    <tr>
        <th>Profession :</th>
            <td><input name="user_profession" size="40" value="<?php $this->user['user_profession']; ?>" /></td>
        <tr>
    </tr>
    <tr>
        <th>Description courte :</th>
        <td><input name="user_light_description" size="68" value="<?php echo $this->user['user_light_description']; ?>" /></td>
    </tr>
    <tr>
        <th>Description longue :</th>
        <td><textarea name="user_description" cols="105" rows="10"><?php echo $this->user['user_description']; ?></textarea></td>
    </tr>
    </table>

    <h2>Physique</h2>
    <table class="tableWhiteBox">
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
    <h2>Addictions</h2>
    <table class="tableWhiteBox">
    <tr>
        <th>Tabagisme :</th>
        <td style="text-align:left;width:578px;">
            <select name="user_smoke">
                <option value="">selectionnez</option>
                <?php foreach($this->quantities as $key => $value)
                {
                     echo '<option value="'.$value['quantity_id'].'"';
                    if($value['quantity_id'] == $this->user['user_smoke']) echo ' selected="selected" ';
                    echo '>'.$value['quantity_libel'].'</option>';
                }
                ?>
            </select>
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
        <th>Drogue :</th>
        <th>
            <select name="user_drugs">
                <option value="">selectionnez</option>
                <?php foreach($this->quantities as $key => $value)
                {
                     echo '<option value="'.$value['quantity_id'].'"';
                    if($value['quantity_id'] == $this->user['user_drugs']) echo ' selected="selected" ';
                    echo '>'.$value['quantity_libel'].'</option>';
                }
                ?>
            </select>
        </td>
    </tr>
    </table>
    <br/>
    <?php $this->_helper->formFooter('profile/'.$this->context->get('user_id')); ?>
</form>
