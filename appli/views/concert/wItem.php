<div style="height:367px;">
    <div class="title">
        <div style="float:left;">
            <?php echo $this->concert['concert_libel']; ?>
        </div>
    </div>
    <div style="float:left;">
        <a href="<?php echo $this->concert['fb_event']; ?>" target="_blank"><img style="max-width:720px;max-height:349px;" src="<?php echo $this->concert['flyer_url']; ?>"/></a>
    </div>
    <div class="shadow"></div>
    <div style="float:left;margin-left:19px;margin-top: -16px;">
        <table width="100%" class="tableProfil">
            <tr>
                <th style="color:black;">Adresse : </th>
                <td><?php echo $this->concert['location']; ?></td>
            </tr>
            <tr>
                <th style="color:black;">Ville : </th>
                <td><?php echo $this->concert['ville_nom_reel']. ' (' . $this->concert['departement'] . ')'; ?></td>
            </tr>
            <tr>
                <th style="color:black;">Orga : </th>
                <td><?php echo $this->concert['organization']; ?></td>
            </tr>
            <?php if (!empty($this->concert['price'])) : ?>
                <tr>
                    <th style="color:black;">Prix : </th>
                    <td><?php echo $this->concert['price'] . ' euros'; ?></td>
                </tr>
            <?php endif; ?>
        </table>
        <h2 class="profileInfo" style="color:black;text-align: left;">Artistes</h2>
        <script>
            $('.popup').magnificPopup({
                type: 'ajax',
                alignTop: true,
                overflowY: 'scroll'
            });
        </script>
        <table class="tableProfil">
            <?php foreach ($this->concert['bands'] as $band) : ?>
                <tr>
                    <td>
                        <?php echo '<a class="popup" href="band/' . $band['band_id'] . '" >- ' . strtoupper($band['band_libel']) . '</a>'; ?>
                    </td>
                    <td style="padding-left: 10px;">
                        <?php 
                            if (!empty($band['band_style'])) :
                                echo '(' . Tools::getCleanBandStyle($band['band_style']) . ')';
                            endif;
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </ul>
        </table>
    </div>
</div>
