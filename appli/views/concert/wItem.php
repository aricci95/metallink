<div class="divElement" style="padding:10px;background-image: url('/MLink/images/structure/middle.jpg');min-height: 412px; width: 97%">
    <h2 style="color:black;margin:auto;width:550px;margin-bottom:10px;" align="center"><?php echo $this->concert['concert_libel']; ?></h2>
    <div style="float:left;">
        <div>
            <a href="<?php echo $this->concert['fb_event']; ?>" target="_blank"><img style="max-width:720px;max-height:500px;" src="<?php echo $this->concert['flyer_url']; ?>"/></a>
        </div>
    </div>
    <div style="float:left;margin:10px;">
        <h2 class="profileInfo" style="color:black;text-align: left;">Informations</h2>
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
        <table width="100%" class="tableProfil" style="text-align: left;">
            <ul>
            <?php foreach ($this->concert['bands'] as $band) : ?>
                <li style="color:black;"><?php echo '- <a class="popup" href="band/' . $band['band_id'] . '" >' . strtoupper($band['band_libel']) . '</a><span style="margin-left:10px;float:right;">(' . Tools::getCleanBandStyle($band['band_style']); ?>)</span></li>
            <?php endforeach; ?>
            </ul>
        </table>
    </div>
</div>