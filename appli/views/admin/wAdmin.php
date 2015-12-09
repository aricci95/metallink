<?php $this->_helper->blackBoxOpen(); ?>
    <?php if($_SESSION['role_id'] == AUTH_LEVEL_SUPERADMIN) : ?>
        <ul align="center">
            <li><a href="admin/switch">Prendre la place d'un utilisateur</a></li>
            <li><a href="admin/deleteUser">Supprimer un utilisateur</a></li>
            <li><a href="admin/mail">Mail à tous les utilisateurs</a></li>
         </ul>
    <?php endif; ?>
<?php $this->_helper->blackBoxClose(); ?>