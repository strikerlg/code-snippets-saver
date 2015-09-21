<?php

// login.php is public to everyone.

require_once('config.php');
require_once('class.mysqli.php');
require_once('class.util.php');
require_once('class.customs.php');

// HNAuthLib
if (!defined('HNAUTH_DIR')) define('HNAUTH_DIR', './HNAuthLib/');
require_once HNAUTH_DIR . 'HNAuth.php';
$errorMsg = '';

if (isset($_POST['submit'])) {
  if (login($_POST['username'], $_POST['password'])) {
    // login is success
    header('Location: index.php'); // go to main page
  } else {
    $errorMsg = '<div class="alert alert-danger alert-dismissable">';
    $errorMsg .= '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
    $errorMsg .= $responseMsg['hnauth'];
    $errorMsg .= '</div>';
  }
}

?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js">
<!--<![endif]-->

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/bootstrap-select.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/sweet-alert.css">
    <link rel="stylesheet" href="js/highlight.styles/monokai.css">
    <link rel="stylesheet" href="css/main.css">
    <style type="text/css">
    html,
    body {
      height: 100%;
      margin: 0;
      padding: 0;
      background-position: center top;
    }
    .container-fluid {
      height: 100%;
      display: table;
      width: 100%;
      padding: 0;
    }
    .row-fluid {
      height: 100%;
      display: table-cell;
      vertical-align: middle;
    }
    .centering {
      float: none;
      margin: 0 auto;
      max-width: 300px;
    }
    .center {
      text-align: center;
    }
    </style>

    <script src="js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
</head>

<body>
    <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

    <div class="container-fluid">

    <div class="row-fluid">
      <div class="col-md-4 col-md-offset-4">
      <p style="max-width: 300px;margin: 0 auto 20px auto;"><a href="index.php">&larr; Back to <?php echo APP_NAME; ?></a></p>
        <div class="panel panel-default centering box">
          <div class="panel-heading">
            <h3 class="panel-title center">Login</h3>
          </div>
          <div class="panel-body">
            <?php echo $errorMsg; ?>
            <form accept-charset="UTF-8" role="form" enctype="application/x-www-form-urlencoded" method="post" action="login.php">
              <fieldset>
                <div class="form-group gray-bg">
                  <input class="form-control" placeholder="Enter username.." name="username" type="text">
                </div>
                <div class="form-group">
                  <input class="form-control gray-bg" placeholder="Enter password.." name="password" type="password">
                </div>
                <div class="form-group">
                  <a href="forgot_password.php">Forgot password? Click here!</a>
                </div>
                <button class="btn btn-lg btn-success btn-block" type="submit" name="submit">Sign In</button>
              </fieldset>
            </form>
            <hr>
            <a href="register.php" class="btn btn-danger btn-block">Register</a>
          </div>
        </div>
      </div>

    </div>
    <!-- ./container-fluid -->

    <script src="js/vendor/jquery-1.11.0.min.js"></script>
    <script src="js/vendor/jquery-ui-1.10.3.min.js"></script>
    <script src="js/vendor/bootstrap.min.js"></script>
    <script src="js/vendor/bootstrap-select.min.js"></script>
    <script src="js/vendor/holder.js"></script>
    <script src="js/highlight.pack.js"></script>
    <script src="js/autogrow.min.js"></script>
    <script src="js/textarealinecount.min.js"></script>
    <script src="js/placeholder.min.js"></script>
    <script src="js/sweet-alert.min.js"></script>
    <script src="js/main.js"></script>

</body>

</html>