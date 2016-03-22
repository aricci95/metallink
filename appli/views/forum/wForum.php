<div id="forum" style="text-align:center;margin:10px;height: 750px;">
    <div id="feed" style="float:left;overflow-y:scroll;width:620px;">
        <?php $this->render('forum/wFeed'); ?>
    </div>
    <div id="connectedUsers" style="float:right;overflow-y:scroll;overflow-x:hidden;width:130px;">
        <?php $this->render('forum/wConnectedUsers'); ?>
    </div>
    <table style="float:left;">
        <tr>
            <td>
                <input type="checkbox" id="autoScroll" style="margin-left:10px;" checked><label for="autoScroll">scroll automatique</label>
                <input type="checkbox" id="autoEnter" style="margin-left:10px;" checked><label for="autoEnter">valider avec entrée</label>
                <input type="checkbox" id="forum_notification" style="margin-left:10px;" <?php if ($this->notification) : ?>checked<?php endif; ?>><label for="notification">notifications</label>
            </td>
        </tr>
        <tr>
            <td>
                <textarea class="message" style="width:79%;float:left;margin:10px;" cols="100" rows="2"></textarea>
                <input class="send" type="image" src="MLink/images/boutons/valider.png" value="Valider" style="border:0px;float:left;margin-top:12px;" border="0" />
                <input type="hidden" class="tmp"  data-id="<?php echo $this->lastId; ?>" data-login="<?php echo $this->context->get('user_login'); ?>" />
            </td>
        </tr>
    </table>
</div>
