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
if (!isset($_POST['submit'])) { ?>

    <html>
        <head>
            <title>Upload sermon</title>
        </head>
        <body>
            <form action="" method="post" enctype='multipart/form-data'>
                <p>
                    <label>Title:</label>
                    <input type="text" name="title" value="Matthews 5 (Dave) <?=date('d-M')?>" />
                </p>
                <p>
                    <label>Date (ex.: <?=date('d-M-Y')?>):</label>
                    <input type="text" name="date" value="<?=date('d-M-Y')?>" />
                </p>
                <p>
                    <label>MP3 file:</label>
                    <input type="file" name="audio" />
                </p>
                <p>
                    <input type="hidden" name="uploadid" value="<?=date('Ymdhis').rand(100, 999)?>" />
                    <input type="submit" name="submit" value="Upload" />
                </p>
            </form>
        </body>
    </html>

    <?php exit();
}

// process the upload

// require wp-load.php to use built-in WordPress functions
require_once("../wp-load.php");

// validation


$upload_dir = __DIR__."/../wp-content/uploads/";


echo "<a href=''>Upload another one</a>";