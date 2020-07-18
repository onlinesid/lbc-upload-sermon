<?php 

require_once 'config.php';

$user = $_SERVER['PHP_AUTH_USER'];
$pass = $_SERVER['PHP_AUTH_PW'];

$validated = $user === AUTH_LOGIN && $pass === AUTH_PASS;

if (!$validated) {
  header('WWW-Authenticate: Basic realm="Please login first"');
  header('HTTP/1.0 401 Unauthorized');
  die ("Not authorized");
}

// display the upload form
if (!isset($_POST['upload'])) { ?>

    <html>
        <head>
            <title>Upload sermon</title>
        </head>
        <body>
            <form action="" method="post">
                <p>
                    <label>Label:</label>
                    <input type="text" name="label" value="Matthews 5 (Dave) <?=date('d-M')?>" />
                </p>
            </form>
        </body>
    </html>

    <?php exit();
}

// require wp-load.php to use built-in WordPress functions
require_once("../wp-load.php");

