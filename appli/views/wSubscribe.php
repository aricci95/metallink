<?php $this->_helper->blackBoxOpen('maxWidth'); ?>
<?php print_r($this->context->params, true); ?>
<form action="subscribe/save" method="post">
    <div style="margin-left:140px;">
        <table>
            <tr>
                <td>Pseudo :</td>
                <td><input name="user_login" value="<?php echo $this->context->getParam('user_login'); ?>" /></td>
            </tr>
            <tr>
                <td>Sexe :</td>
                <td><select name="user_gender">
                    <option value="1" <?php if($this->context->getParam('user_gender') == "1") : ?> selected="selected" <?php endif; ?>>Homme</option>
                    <option value="2" <?php if($this->context->getParam('user_gender') == "2") : ?> selected="selected" <?php endif; ?>>Femme</option>
                </select></td>
            </tr>
            <tr>
                <td>Mot de passe :</td>
                <td><input name="user_pwd" type="password" <?php if(!empty($this->context->params['user_pwd'])) : ?> value="<?php echo $this->context->params['user_pwd']; ?>"<?php endif; ?> /></td>
            </tr>
            <tr>
                <td>Répéter mot de passe :</td>
                <td><input name="verif_pwd" type="password" <?php if(!empty($this->context->params['verif_pwd'])) : ?> value="<?php echo $this->context->params['verif_pwd']; ?>"<?php endif; ?> /></td>
            </tr>
            <tr>
                <td>Adresse email :</td>
                <td><input name="user_mail" type="text" <?php if(!empty($this->context->params['user_mail'])) : ?> value="<?php echo $this->context->params['user_mail']; ?>"<?php endif; ?> /></td>
            </tr>
        </table>
        <table>
            <tr>
                <td>
                    <label><input type="checkbox" name="agreements" />&nbsp;J'ai lu et j'accepte les mentions légales du site MetalLinK.fr <a href="subscribe/terms" target="_blank"><u>disponibles ICI</u></a></label>
                </td>
            </tr>
            <tr>
                <td style="color:red;">
                    <br/>
                    <i>Pensez à vérifier vos spams si vous ne recevez aucune confirmation</i>
                    <br/><br/>
                </td>
            </tr>
            <tr>
                <td>
                    <input style="margin-left:170px;" size="20" type="submit" value="S'inscrire !" />
                </td>
            </tr>
        </table>
    </div>
</form>
<?php $this->_helper->blackBoxClose(); ?>
