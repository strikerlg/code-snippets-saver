<?php

// logout.php is only accessible for logged in users.

require_once('config.php');
require_once('class.mysqli.php');
require_once('class.util.php');
require_once('class.customs.php');

// HNAuthLib
if (!defined('HNAUTH_DIR')) define('HNAUTH_DIR', './HNAuthLib/');
require_once HNAUTH_DIR . 'HNAuth.php';

$session = new Session();
if ($session->getItem('username') !== '' && $session->getItem('userlevel') !== '') {
  $session->destroy();
  header('Location: index.php');
} else {
  header('Location: index.php');
}

?>