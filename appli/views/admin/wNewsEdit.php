<?php $this->_helper->blackBoxOpen(); ?>
    <form action="adminNews/save" method="post">
        <input type="hidden" name="news_id" value="<?php if(!empty($this->currentNews['news_id'])) echo $this->currentNews['news_id']; ?>" />
        <table>
            <?php if(!empty($this->currentNews['news_id'])) : ?>
                <tr>
                    <td colspan="2" align="right"><img src="MLink/images/icone/delete.png"/> <a href="adminNews/delete/<?php echo $this->currentNews['news_id']; ?>">Supprimer la news</a></td>
                </tr>
            <?php endif; ?>
            <tr>
                <th>Titre :</th>
                <td><input name="news_titre" value="<?php if(!empty($this->currentNews['news_titre'])) echo $this->currentNews['news_titre']; ?>" size="80" /></td>
            </tr>
            <tr>
                <th>Contenu :</th>
                <td><textarea name="news_contenu" cols="95" rows="15"><?php if(!empty($this->currentNews['news_contenu'])) echo $this->currentNews['news_contenu']; ?></textarea></td>
            </tr>
            <tr>
                <th>Photo URL :</th>
                <td><input name="news_photo_url" size="80" value="<?php if(!empty($this->currentNews['news_photo_url'])) echo $this->currentNews['news_photo_url']; ?>" /></td>
            </tr>
            <tr>
                <th>YouTube URL :</th>
                <td><input name="news_media_url"  size="80" value="<?php if(!empty($this->currentNews['news_media_url'])) echo $this->currentNews['news_media_url']; ?>" /></td>
            </tr>
            <tr>
                <td colspan="2" align="center"><input type="submit" value="Valider" /></td>
            </tr>
            <tr>
                <td colspan="2" align="center"><a href="adminNews/news">Retour</a></td>
            </tr>
        </table>
    </form>
<?php $this->_helper->blackBoxClose(); ?>