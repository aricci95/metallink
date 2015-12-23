<?php if(!empty($this->elements)) : ?>
    <table width="100%" style="font-size:16px;font-weight:bold;">
        <?php foreach($this->elements as $key => $covoiturage) : ?>
            <tr class="covoiturage">
                <td>
                    <table>
                        <tr>
                            <td><?php $this->_helper->printUserSmall($covoiturage); ?></td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table width="100%" style="font-size:16px;font-weight:bold;text-align:center;">
                        <tr style="color:white;">
                            <td width="20%"><?php echo (!empty($covoiturage['nom'])) ? $covoiturage['nom'] : ''; ?></td>
                            <td><img src="MLink/images/icone/target.png" /></td>
                            <td><?php echo (!empty($covoiturage['concert_libel'])) ? nl2br($covoiturage['concert_libel']) : ''; ?></td>
                            <td><?php echo (empty($covoiturage['price'])) ? '<i>à définir</i>' : $covoiturage['price'].' €'; ?></td>
                        </tr>
                        <tr>
                            <td><i><?php echo (!empty($covoiturage['date_depart'])) ? $covoiturage['date_depart'] : ''; ?></i></td>
                            <td></td>
                            <td><i><?php echo (!empty($covoiturage['date_retour'])) ? $covoiturage['date_retour'] : ''; ?></i></td>
                            <td>
                                <?php if(User::getContextUser('id') == $covoiturage['user_id']) : ?>
                                    <a class="delete" data-id="<?php echo $covoiturage['covoit_id']; ?>" href="#" style="font-size:12px;font-weight:normal;">Supprimer <img data-id="<?php echo $covoiturage['covoit_id']; ?>" src="MLink/images/icone/delete.png" /></a>
                                <?php else : ?>
                                    <div class="divLink"><?php $this->render('link/wItem', array('user' => $covoiturage)); ?></div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>

                        </tr>
                    </table>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
