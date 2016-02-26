<?php foreach($this->elements as $key => $concert) : ?>
    <br/>
    <div class="divElement" style="padding:10px;background-image: url('/MLink/images/structure/middle.jpg');min-height: 400px; width: 97%">
        <h2 style="color:black;margin:auto;width:550px;margin-bottom:10px;" align="center"><?php echo $concert['concert_libel']; ?></h2>
        <div style="float:left;">
            <div>
                <a href="<?php echo $concert['fb_event']; ?>" target="_blank"><img style="max-width:720px;max-height:500px;" src="<?php echo $concert['flyer_url']; ?>"/></a>
            </div>
        </div>
        <div style="float:left;margin:10px;">
            <h2 class="profileInfo" style="color:black;text-align: left;">Informations</h2>
            <table width="100%" class="tableProfil">
                <tr>
                    <th style="color:black;">Orga : </th>
                    <td><?php echo $concert['organization']; ?></td>
                </tr>
                <tr>
                    <th style="color:black;">Ville : </th>
                    <td><?php echo $concert['nom']. ' (' . $concert['departement'] . ')'; ?></td>
                </tr>
                <tr>
                    <th style="color:black;">Prix : </th>
                    <td><?php echo $concert['price'] . ' euros'; ?></td>
                </tr>
            </table>
            <h2 class="profileInfo" style="color:black;text-align: left;">Artistes</h2>
            <table width="100%" class="tableProfil" style="text-align: left;">
                <ul>
                <?php foreach ($concert['bands'] as $band) : ?>
                    <li style="color:black;"><?php echo '- <a href="' . $band['band_website'] . '" >' . strtoupper($band['band_libel']) . '</a>'; ?></li>
                <? endforeach; ?>
                </ul>
            </table>
        </div>
    </div>
<?php endforeach; ?>
