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

echo "Hello World";