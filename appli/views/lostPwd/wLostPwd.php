<?php $this->_helper->blackBoxOpen('maxWidth'); ?>
    <form action="lostpwd/submit" method="post">
        <div style="margin-left:140px;">
            <table>
                <tr>
                    <td>Login : </td>
                    <td><input name="user_login" /></td>
                </tr>
                <tr>
                    <td>OU</td>
                </tr>
                <tr>
                    <td>Adresse message : </td>
                    <td><input name="user_mail" /></td>
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
