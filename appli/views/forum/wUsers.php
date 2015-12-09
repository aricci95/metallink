<?php foreach($this->users as $user) : ?>
    <li><?php $this->_helper->printUserLogin($user); ?></li>
<?php endforeach; ?>
