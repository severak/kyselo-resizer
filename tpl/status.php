<?= render('_header'); ?>

<?php if (!isset($file)) { ?>
    <article class="message is-danger">
        <div class="message-body">
            File not found... It was deleted probably...
        </div>
    </article>
<?php } ?>

<?php if ($file['status']=='error') { ?>
    <article class="message is-danger">
        <div class="message-body">
            File conversion went wrong...
        </div>
    </article>
<?php } ?>

<?php if ($file['status']=='uploaded') { ?>
    <p class="is-size-3"><i class="fa fa-spinner fa-pulse fa-fw"></i> file uploaded, now it's resizing...</p>
    <script>
        setTimeout(function (){
            window.location.reload();
        }, 10 * 1000);
    </script>
<?php } ?>

<?php if ($file['status']=='resized') {
    $config = di('config');
    $filename = $file['hash'].'.'.$file['ext'];
    $absoluteUrl = $config['self_url'] . '/files/' . $filename;
    $kyseloShareUrl = $config['kyselo_url'] . '/act/post?url=' . urlencode($absoluteUrl);
    $fsize = human_readable_bytes(filesize(di('webroot')  . '/files/' . $filename));

    // TODO - support videos and sounds
    ?>
    <?php if ($file['ext']=='mp4') { ?>
        <p><video src="<?=$absoluteUrl; ?>" controls muted autoplay playsinline loop class="kyselo-image"></video></p>
    <?php } elseif ($file['ext']=='mp3') { ?>
        <p><audio src="<?=$absoluteUrl; ?>" controls></p>
    <?php } else { ?>
        <p><img src="<?=$absoluteUrl; ?>" class="image kyselo-image"></p>
    <?php } ?>
    <p><br></p>
    <p><?= $fsize; ?></p>
    <p><br></p>
    <p><a href="<?=$kyseloShareUrl;?>" class="button is-primary is-large">Share on Kyselo!</a> </p>
    <p><br></p>
    <p><input value="<?=$absoluteUrl; ?>" class="input"></p>
    <p><br></p>
    <p>This is only temporary file which will disappear at <?=date_create('@'.$file['timestamp'])->modify('+20 minutes')->format('H:i'); ?>.</p>


    <style>
        .kyselo-image {
            max-height: 60vh;
        }
    </style>
<?php } ?>

<?= render('_footer'); ?>
