<div style="display: inline-block;margin-bottom: -31px;">
    <div class="title">
        <div style="float:left;">
            <?php echo Tools::maxLength($this->concert['concert_libel'], 90); ?>
        </div>
    </div>
    <div style="float:left;">
        <a href="<?php echo $this->concert['fb_event']; ?>" target="_blank"><img style="max-width:720px;max-height:349px;" src="<?php echo $this->concert['flyer_url']; ?>"/></a>
    </div>
    <div class="shadow"></div>
    <div style="float:left;margin-left:19px;margin-top: -16px;">
        <table class="tableProfil">
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
            <tr>
                <th style="color:black;">Prix : </th>
                <td>
                    <?php echo !empty($this->concert['price']) ? $this->concert['price'] . ' euros' : 'non précisé '; ?>
                </td>
            </tr>
        </table>
        <h2 class="profileInfo" style="color:black;text-align: left;">Artistes</h2>
        <table>
            <?php foreach ($this->concert['bands'] as $band) : ?>
                <tr>
                    <td>
                        <a class="popup greyLink" href="band/<?php echo $band['band_id']; ?>">
                        - <?php echo strtoupper($band['band_libel']); ?>
                        </a>
                    </td>
                    <td style="padding-left: 10px;">
                        <?php if (!empty($band['band_style'])) : ?>
                            <?php echo '(' . Tools::getCleanBandStyle($band['band_style']) . ')'; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
