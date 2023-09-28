<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php if (isset($title)) echo $title  . ' - ' ; ?>Kyselo resizer</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.min.css">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
</head>
<body>
<section class="hero is-link">
    <div class="hero-body">
        <div class="container">
            <p class="title">
                Kyselo resizer <i class="fa fa-camera-retro" aria-hidden="true"></i>
            </p>
            <p class="subtitle">
                makes your multimedia smaller.
            </p>
        </div>
    </div>
</section>
<section class="section" id="app">
    <div class="container">
        <?php if (isset($_SESSION['flashes'])) {
            foreach ($_SESSION['flashes'] as $flashtype=>$messages) {
                foreach ($messages as $message) {
                    echo '<div class="message is-'.$flashtype.'"><div class="message-body">'.$message.'</div></div>';
                }
                unset($_SESSION['flashes'][$flashtype]);
            }
        }
        ?>
        <main>
