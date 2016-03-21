<?php if($this->context->get('role_id') == AUTH_LEVEL_SUPERADMIN) : ?>
    <ul align="center">
        <li><a href="admin/switch">Prendre la place d'un utilisateur</a></li>
        <li><a href="admin/deleteUser">Supprimer un utilisateur</a></li>
        <li><a href="admin/message">Message à tous les utilisateurs</a></li>
     </ul>
<?php endif; ?>
