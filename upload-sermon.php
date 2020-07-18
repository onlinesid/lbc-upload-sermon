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

$content_dir = "/wp-content/uploads/";
$upload_dir = __DIR__."/..".$content_dir;

$file_name = preg_replace("/[^A-Za-z0-9\\-\\.]/", "_", trim($_FILES['audio']['name']));
$path_info = pathinfo($upload_dir.$file_name);
$file_name = $path_info['filename'].'.'.$path_info['extension'];

$i = 1;
while (file_exists($upload_dir.'/'.$file_name)) {
    $i++;
    $file_name = $path_info['filename'].'-'.$i.'.'.$path_info['extension'];
}

if (!move_uploaded_file($_FILES['audio']['tmp_name'], $upload_dir.$file_name)) {
    echo "<a href=''>Ooops! something is wrong with the upload, try again!</a>";
    exit();
}

//echo "<pre>";
//print_r($path_info);echo "\n";
// echo "$file_name\n";
// print_r($_POST);
// print_r($_FILES);
//echo "</pre>";

$postType = 'post'; // set to post or page
$userID = WP_USER_ID; // set to user id
$categoryID = WP_CATEGORY_ID; // set a single category id or a chain of integer ids separated by commas (e.g. ‘2,3,4,5’)
$postStatus = 'publish';  // set to future, draft, or publish

$leadTitle = $_POST['title'];

$audio_link = $content_dir.$file_name;
$leadContent = "
<!-- wp:audio -->
<figure class=\"wp-block-audio\"><audio controls src=\"$audio_link\"></audio></figure>
<!-- /wp:audio -->
";

/*******************************************************
** TIME VARIABLES / CALCULATIONS
*******************************************************/
// VARIABLES
$timeStamp = $minuteCounter = 0;  // set all timers to 0;
$iCounter = 1; // number use to multiply by minute increment;
$minuteIncrement = 1; // increment which to increase each post time for future schedule
$adjustClockMinutes = 0; // add 1 hour or 60 minutes - daylight savings

// CALCULATIONS
$minuteCounter = $iCounter * $minuteIncrement; // setting how far out in time to post if future.
$minuteCounter = $minuteCounter + $adjustClockMinutes; // adjusting for server timezone

$timeStamp = date('Y-m-d H:i:s', strtotime("+$minuteCounter min")); // format needed for WordPress
 
/*******************************************************
** WordPress Array and Variables for posting
*******************************************************/

$new_post = array(
    'post_title' => $leadTitle,
    'post_content' => $leadContent,
    'post_status' => $postStatus,
    'post_date' => $timeStamp,
    'post_author' => $userID,
    'post_type' => $postType,
    'post_category' => array($categoryID)
);

/*******************************************************
** WordPress Post Function
*******************************************************/

$post_id = wp_insert_post($new_post);

/*******************************************************
** SIMPLE ERROR CHECKING
*******************************************************/

$finaltext = '';

if($post_id){

    $finaltext .= 'Successful!!!<br>';

} else{

    $finaltext .= "Something went wrong and I didn\'t insert a new post. <a href=''>try again?</a>";

}

echo $finaltext;
