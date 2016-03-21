<div style="margin:5% auto;border:1px grey solid;padding:10px;background-image: url('/MLink/images/structure/middle.jpg');min-height: 300px; max-width: 500px">
<h1><?php echo ucfirst($this->band['band_libel']); ?></h1>
<h2>(<?php echo ucfirst($this->band['band_country']); ?>)</h2>
<div class="divModule">
    <p style="text-align:center;">
        <img src="<?php echo $this->band['band_logo_url']; ?>" />
    </p>
    <p>
        Style : <b><?php echo Tools::getCleanBandStyle($this->band['band_style']); ?></b>
    </p>
    <p>
        Score : <b><?php echo $this->band['band_score']; ?></b>
    </p>
    <p>
        Site officiel : <a href="<?php echo $this->band['band_website']; ?>" target="_blank"><?php echo $this->band['band_website']; ?></a>
    </p>
    <p>
        <?php echo $this->band['band_bio']; ?>
    </p>
</div>