<form action="covoit" method="post">
   <table id="covoit_search" class="tableWhiteBox" align="center" style="width:760px;border:1px grey dotted;font-size:12px;padding-bottom:10px;padding-top:5px;">
        <tr style="text-align:center;">
            <th></th>
            <th>Concert / Festival :</th>
            <th>Ville départ : </th>
            <th></th>
        </tr>
        <tr style="text-align:center;">
            <td><input class="create" type="button" value="Proposer un trajet" /></td>
            <!-- Autocomplete des concerts -->
            <td>
                <span class="autocomplete" data-type="concert">
                    <input class="autocomplete" type="text" show-value="0" add-value="1" autocomplete="off" />
                    <input type="hidden" name="search_concert" />
                    <div class="autocomplete" style="margin-left:9px;">
                        <ul class="autocomplete"></ul>
                    </div>
                </span>
            </td>
            <!-- Autocomplete des villes -->
            <td>
                <span class="autocomplete" data-type="ville">
                    <input class="autocomplete" type="text" show-value="1" autocomplete="off"/>
                    <input type="hidden" name="search_ville" />
                    <div class="autocomplete" style="margin-left:9px;">
                        <ul class="autocomplete"></ul>
                    </div>
                </span>
            </td>
            <td><input type="image" src="MLink/images/boutons/bnt_search.png" ALT="Rechercher" /></td>
        </tr>
    </table>
</form>
<?php $this->_helper->blackBoxOpen(); ?>
    <div align="center" class="results maxWidth">
        <?php if(empty($this->elements)) : ?>
            Aucun résultat pour les critères choisis.
            <table id="covoitCreate" width="100%" style="display:none;font-size:16px;font-weight:bold;">
                <?php $this->render('covoit/wEdit'); ?>
            </table>
        <?php else : ?>
            <table id="covoitCreate" width="100%" style="display:none;font-size:16px;font-weight:bold;">
                <?php $this->render('covoit/wEdit'); ?>
            </table>
            <?php $this->render('covoit/wItems', array('elements' => $this->elements)); ?>
        <?php endif; ?>
    </div>
    <img class="loading" src="MLink/appli/js/loading.gif" style="display:none;" data-show="false" data-end="false" data-offset="0" data-href="covoit" />
<?php $this->_helper->blackBoxClose(); ?>
