<a style="font-size:18px;" href="adminNews/edit">Ajouter nouveau</a>
<ul>
    <?php foreach ($this->news as $value) : ?>
        <li>
            <a href="adminNews/edit/<?php echo $value['news_id']; ?>" ><b><u><?php echo Tools::toFrenchDate($value['news_date'], false); ?></u></b> :
            <?php echo $value['news_titre']; ?></a>
        </li>
    <?php endforeach; ?>
</ul>
<a href="admin">RETOUR</a>
