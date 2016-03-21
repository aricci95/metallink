<form action="admin/<?php echo $this->action; ?>" method="post">
    Utilisateur :
    <select name="user_id" align="center">
        <option value="">selectionner</option>
        <?php
            foreach($this->users as $key => $value) echo '<option value="'.$value["user_id"].'">'.$value["user_login"].' ('.$value['user_id'].')</option>';
        ?>
    </select>
<?php $this->_helper->formFooter('admin'); ?>
</form>