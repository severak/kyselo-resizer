<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php if (isset($title)) echo $title  . ' - ' ; ?>Stela</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.min.css">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    <script src="/static/vue.min.js"></script>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
</head>
<body>
<nav class="navbar has-background-grey-lighter" role="navigation" aria-label="main navigation">
    <div class="container" style="max-width: 800px">
    <div class="navbar-brand">
        <a class="navbar-item" href="/">
            Ｓｔｅｌａ&nbsp;<img src="/favicon-32x32.png">
        </a>

        <a role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample" id="navbar-burger">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </a>
    </div>

    <div id="navbar-menu" class="navbar-menu">
        <div class="navbar-start">
            <?php if (user()) { ?>
            <a class="navbar-item" href="/bar/">
                bar
            </a>

            <a class="navbar-item" href="/pokladna/">
                pokladna
            </a>

            <a class="navbar-item" href="/sklad/">
                sklad
            </a>

            <a class="navbar-item" href="/clenove/">
                členové
            </a>

            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link">
                    ostatní
                </a>

                <div class="navbar-dropdown">
                    <?php if ((user())['is_superuser']) { ?>
                    <a class="navbar-item" href="/obsluha/">
                        obsluha
                    </a>
                    <hr class="navbar-divider">
                    <?php } ?>
                    <a class="navbar-item" href="/zmena-hesla/">
                        změnit heslo
                    </a>
                </div>
            </div>
            <?php } ?>
        </div>

        <div class="navbar-end">
            <?php if (user()) { ?>
            <div class="navbar-item">
                <div class="buttons">
                    <a class="button is-light" href="/logout/">
                        odhlásit
                    </a>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    </div>
</nav>
<section class="section" id="app">
    <div class="container" style="max-width: 800px">
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