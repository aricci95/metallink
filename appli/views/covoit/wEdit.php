<tr>
    <td>
        <table>
            <tr>
                <td><?php $this->_helper->printUserSmall($this->user); ?></td>
            </tr>
        </table>
    </td>
    <td>
        <form action="covoit/save" method="post">
            <table width="100%" style="font-size:16px;font-weight:bold;text-align:center;">
                <tr style="color:white;">
                    <!-- Autocomplete des villes -->
                    <td>
                        <span class="autocomplete" data-type="ville">
                            <input class="autocomplete" size="15" type="text" show-value="1" autocomplete="off"/>
                            <input type="hidden" name="ville_id" />
                            <div class="autocomplete" style="margin-left:9px;">
                                <ul class="autocomplete"></ul>
                            </div>
                        </span>
                    </td>
                    <td><img src="MLink/images/icone/target.png" /></td>
                    <!-- Autocomplete des concerts -->
                    <td>
                        <span class="autocomplete" data-type="concert">
                            <input class="autocomplete" size="15" type="text" show-value="0" add-value="1" autocomplete="off" />
                            <input type="hidden" name="concert_id" />
                            <div class="autocomplete" style="margin-left:9px;">
                                <ul class="autocomplete"></ul>
                            </div>
                        </span>
                    </td>
                    <td><input type="text" size="5" name="price" value="0"/> €</td>
                </tr>
                <tr>
                    <td><input id="datetimepicker" class="datetimepicker" size="15" name="date_depart" type="text" timepicker="true" format="d/m/Y H:i"></td>
                    <td></td>
                    <td><input class="datetimepicker" size="15" name="date_retour" type="text" timepicker="true"></td>
                    <td><input class="participate" type="submit" value="Valider" style="width:80px;"/></td>
                </tr>
                <tr>
                    
                </tr>
            </table>
        </form>
    </td>
</tr>