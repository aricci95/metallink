<table style="width:100%;border-collapse: collapse;" class="results maxWidth">
    <?php if (empty($this->userMessages)) : ?>
        <tr>
            <td style='text-align:center;padding-top:20px;' colspan='4'>Aucun message.</td>
        </tr>
    <?php else : ?>
        <?php $this->render('mailbox/wItems'); ?>
    <?php endif; ?>
    <tr>
        <td>
            <img class="loading" src="MLink/appli/js/loading.gif" style="display:none;" data-show="false" data-offset="0" data-href="mailbox" data-end="false" />
        </td>
    </tr>
</table>
