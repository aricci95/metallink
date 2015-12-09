<?php $this->_helper->blackBoxOpen(); ?>
    <ul align="center">
        <?php foreach($this->scripts as $script) : ?>
            <li>
                <a href="script/<?php echo $script; ?>"><?php echo str_replace('Script','', $script); ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php $this->_helper->blackBoxClose(); ?>