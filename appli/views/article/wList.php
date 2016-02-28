<form action="sales" method="post">
    <table align="center" style="width:760px;border:1px grey dotted;font-size:12px;">
        <tr>
            <td><input class="create_article" type="button" value="Vendre un produit" /></td>
            <th>Libellé :</th>
            <td><input name="search_libel" size="5" value="<?php if(isset($this->criterias['search_libel'])) echo $this->criterias['search_libel']; ?>" /></td>
            <th>Catégorie : </th>
            <td>
                <select name="search_categorie">
                    <option value=""></option>
                    <?php foreach($this->categories as $key => $value) : ?>
                        <option value="<?php echo $value['id']; ?>" <?php if($value['id'] == $this->criterias['search_categorie']) : ?> selected="selected" <?php endif; ?> >
                        <?php echo $value['libel']; ?></option> 
                    <?php endforeach; ?>
                </select>
            </td>
            <td><input type="image" src="MLink/images/boutons/bnt_search.png" ALT="Rechercher" /></td>
        </tr>
    </table>
    
</form>
<?php $this->_helper->blackBoxOpen(); ?>
    <div align="center" class="results maxWidth">
        <?php if(empty($this->elements)) : ?>
            Aucun résultat pour les critères choisis.
        <?php else : ?>
            <?php $this->render('article/wItems'); ?>
        <?php endif; ?>
    </div>
    <img class="loading" src="MLink/appli/inc/ajax/loading.gif" style="display:none;" data-show="false" data-end="false" data-offset="0" data-href="sales" />
<?php $this->_helper->blackBoxClose(); ?>
