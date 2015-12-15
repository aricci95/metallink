<?php $this->_helper->blackBoxOpen('maxWidth'); ?>
    <form action="lostpwd/submitNew" method="post">
        <input type="hidden" name="pwd_valid" value="<?php echo $this->pwd_valid; ?>" />
        <div style="margin-left:140px;">
            <table>
                <tr>
                    <td>Nouveau mot de passe : </td>
                    <td><input type="password" name="user_pwd" /></td>
                </tr>
                <tr>
                    <td>Confirmez le nouveau mot de passe : </td>
                    <td><input type="password" name="pwd_confirm" /></td>
                </tr>
                <tr>
                    <td>
                        <input style="margin-left:170px;" size="20" type="submit" value="Valider" />
                    </td>
                </tr>
            </table>
        </div>
    </form>
<?php $this->_helper->blackBoxClose(); ?>
