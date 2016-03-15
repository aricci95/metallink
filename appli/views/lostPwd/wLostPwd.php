<?php $this->_helper->blackBoxOpen('maxWidth'); ?>
    <form action="lostpwd/submit" method="post">
        <div align="center">
            <p>
                Login :<br/>
                <input name="user_login" style="margin:5px;" />
            </p>
            <p>
                OU
            </p>
            <p>
                Adresse e-mail :<br/>
                <input name="user_mail" style="margin:5px;" />
            </p>
            <p>
                <input size="20" type="submit" value="Valider" style="margin:5px;" />
            </p>
        </div>
    </form>
<?php $this->_helper->blackBoxClose(); ?>
