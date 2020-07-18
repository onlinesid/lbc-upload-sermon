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
                    <input type="hidden" name="upload_id" value="<?=date('Ymdhis').rand(100, 999)?>" />
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

// these fields can't be empty
foreach (['title', 'date', 'upload_id', 'submit'] as $f) {
    if (empty($_POST[$f])) {
        echo "<a href=''>Ooops! something is wrong, try again!</a>";
        exit();
    }
}

// these file(s) must be uploaded
if (!isset($_FILES['audio'])) {
    echo "<a href=''>Ooops! something is wrong, try again!</a>";
    exit();
}

// type must be audio
if (substr($_FILES['audio']['type'], 0, 6) !== 'audio/') {
    echo "<a href=''>Ooops! something is wrong, try again!</a>";
    exit();
}

$upload_dir = __DIR__."/../wp-content/uploads/";

$base_file_name = preg_replace("/[^A-Za-z0-9\\-\\.]/", "_", trim($_FILES['audio']['name']));
$path_info = pathinfo($file_name);
$file_name = $path_info['filename'].'.'.$path_info['extension'];

$i = 1;
while (file_exists($upload_dir.'/'.$file_name)) {
    $i++;
    $file_name = $path_info['filename'].'-'.$i.'.'.$path_info['extension'];
}

echo "<pre>";
echo "$file_name\n";
// print_r($_POST);
// print_r($_FILES);
echo "</pre>";



echo "Successful!";