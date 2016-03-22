<div style="margin:0 auto;border:1px grey solid;padding:10px;background-image: url('/MLink/images/structure/middle.jpg');height:100%;max-width: 578px;">
    <div class="divModule">
        <p style="text-align:center;">
            <img src="<?php echo $this->band['band_logo_url']; ?>" />
            <h2><?php echo ucfirst($this->band['band_libel']); ?><?php if (!empty($this->band['band_country'])) : ?> (<?php echo ucfirst($this->band['band_country']); ?>)<?php endif; ?></h2>
        </p>
        <p>
            Style : <b><?php echo Tools::getCleanBandStyle($this->band['band_style']); ?></b>
        </p>
        <?php if (!empty($this->band['band_score'])) : ?>
        <p>
            Score : <b><?php echo $this->band['band_score']; ?> / 20</b>
        </p>
        <?php endif; ?>
        <?php if (!empty($this->band['band_website'])) : ?>
        <p>
            Site officiel : <a href="<?php echo $this->band['band_website']; ?>" target="_blank"><?php echo $this->band['band_website']; ?></a>
        </p>
        <?php endif; ?>
        <?php if (!empty($this->band['band_sample_video_url'])) : ?>
        <p>
            <iframe width="560" height="315" src="<?php echo $this->band['band_sample_video_url']; ?>" frameborder="0" allowfullscreen></iframe>
        </p>
        <?php endif; ?>
        <?php if (!empty($this->band['band_lineup_photo'])) : ?>
        <div style="text-align: center;">
            <h2>Line up</h2>
            <img src="<?php echo $this->band['band_lineup_photo']; ?>" />
        </p>
        <?php endif; ?>
    </div>
</div>