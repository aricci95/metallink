<?php $this->_helper->blackBoxOpen('maxWidth'); ?>
    <form action="lostpwd/submit" method="post">
        <div style="margin-left:182px;">
            <table>
                <tr>
                    <td>Login : </td>
                    <td><input name="user_login" /></td>
                </tr>
                <tr>
                    <td>OU</td>
                </tr>
                <tr>
                    <td>Adresse e-mail : </td>
                    <td><input name="user_mail" /></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;padding:10px;">
                        <input size="20" type="submit" value="Valider" />
                    </td>
                </tr>
            </table>
        </div>
    </form>
<?php $this->_helper->blackBoxClose(); ?>
