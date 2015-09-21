<?php

// my_snippets.php is private.

require_once('config.php');
require_once('class.mysqli.php');
require_once('class.util.php');
require_once('class.customs.php');

// HNAuthLib
if (!defined('HNAUTH_DIR')) define('HNAUTH_DIR', './HNAuthLib/');
require_once HNAUTH_DIR . 'HNAuth.php';
$errorMsg = '';

if ( !is_user_logged_in() ) {
  header('Location: login.php');
} else {
  $session = new Session();
  $loggedInUser = $session->getItem('username');
  $loggedInLevel = $session->getItem('userlevel');
  $user_loggedin_id = get_userid($loggedInUser);
}

$db = DB::getInstance();
$query = "SELECT * FROM ".DBTABLE_SNIPPETS." WHERE user_id='".$user_loggedin_id."' AND visibility='public'";
$total_found = $db->num_rows($query);
if ($total_found > 0) {
  $results = $db->get_results($query);
  $html_content = '<div class="list-group snippet-list">';
  foreach ($results as $row) {
    $html_content .= '<a href="view_snippet.php?id='.$row['id'].'" class="list-group-item"><span class="snippet-title">'.$row['name'].'</span> <span class="label '.get_category_color($row['category_tag']).'">'.get_category_title($row['category_tag']).'</span> &middot; <span class="created-date">updated '.get_timeago($row['updated_time']).'</span></a>';
  }
  $html_content .= '</div>';
} else {
  // empty cat
  $html_content = 'Oops.. you don\'t have any snippet saved here yet. Why not <a href="add_snippet.php">submit one</a>?';
}

$query2 = "SELECT * FROM ".DBTABLE_SNIPPETS." WHERE user_id='".$user_loggedin_id."' AND visibility='private'";
$total_found2 = $db->num_rows($query2);
if ($total_found2 > 0) {
  $results2 = $db->get_results($query2);
  $html_content2 = '<div class="list-group snippet-list">';
  foreach ($results2 as $row2) {
    $html_content2 .= '<a href="view_snippet.php?id='.$row2['id'].'" class="list-group-item"><span class="snippet-title">'.$row2['name'].'</span> <span class="label '.get_category_color($row2['category_tag']).'">'.get_category_title($row2['category_tag']).'</span> &middot; <span class="created-date">updated '.get_timeago($row2['updated_time']).'</span></a>';
  }
  $html_content2 .= '</div>';
} else {
  // empty cat
  $html_content2 = 'Oops.. you don\'t have any snippet saved here yet. Why not <a href="add_snippet.php">submit one</a>?';
}

$query3 = "SELECT * FROM ".DBTABLE_SNIPPETS." WHERE user_id='".$user_loggedin_id."'";
$total_found3 = $db->num_rows($query3);
$html_content3 = '<div class="alert alert-info" role="alert"><span class="label label-info">INFO</span> You have <strong>'.$total_found3.'</strong> snippets in total; <em>'.$total_found.' public snippets</em> and <em>'.$total_found2.' private snippets</em>.</div>';

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
    <title><?php echo APP_NAME; ?></title>
    <meta name="description" content="<?php echo APP_DESC; ?>">
    <meta name="author" content="<?php echo APP_AUTHOR; ?>">
    <meta name="keyword" content="<?php echo APP_KEYWORD; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/bootstrap-select.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/sweet-alert.css">
    <link rel="stylesheet" href="js/highlight.styles/monokai.css">
    <link rel="stylesheet" href="css/main.css">

    <script src="js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
</head>

<body>
    <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

    <nav class="navbar navbar-default" role="navigation">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php"><i class="fa fa-code"></i> Code Snippets Saver</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse navbar-ex1-collapse">
            <ul class="nav navbar-nav">
                <?php html_main_nav(); ?>
            </ul>
            <form class="navbar-form navbar-left" role="search" action="search.php" method="get">
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search for snippets.." id="inputSearch" spellcheck="off" autocomplete="off" name="q">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit" id="btnSearch"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </div>
            </form>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> Signed in as <strong><?php echo $loggedInUser; ?></strong> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <?php html_user_nav(); ?>
                    </ul>
                </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </nav>

    <div class="container" role="main">

        <div class="row">

            <div class="col-md-3" id="leftSide">
                <div class="panel panel-default box">
                    <div class="panel-heading">
                        <i class="fa fa-list"></i> Snippet Categories
                    </div>
                    <div class="panel-body">
                        <div class="list-group catlist" id="catList">
                            <a href="list.php?category=plain-text" class="list-group-item palette-silver-light text-dark" data-category="plain-text"><span class="badge stotal palette-silver-dark"><?php echo $plain_text; ?></span>Plain Text</a>
                            <a href="list.php?category=html" class="list-group-item palette-red-light" data-category="html"><span class="badge stotal palette-red-dark"><?php echo $html; ?></span>HTML</a>
                            <a href="list.php?category=css" class="list-group-item palette-orange-light" data-category="css"><span class="badge stotal palette-orange-dark"><?php echo $css; ?></span>CSS</a>
                            <a href="list.php?category=javascript" class="list-group-item palette-yellow-light" data-category="javascript"><span class="badge stotal palette-yellow-dark"><?php echo $javascript; ?></span>JavaScript</a>
                            <a href="list.php?category=php" class="list-group-item palette-blue-light" data-category="php"><span class="badge stotal palette-blue-dark"><?php echo $php; ?></span>PHP</a>
                            <a href="list.php?category=mysql" class="list-group-item palette-green-light" data-category="mysql"><span class="badge stotal palette-green-dark"><?php echo $mysql; ?></span>MySQL</a>
                            <a href="list.php?category=dos" class="list-group-item palette-black-light" data-category="dos"><span class="badge stotal palette-black-dark"><?php echo $dos; ?></span>DOS</a>
                            <a href="list.php?category=csharp" class="list-group-item palette-purple-light" data-category="csharp"><span class="badge stotal palette-purple-dark"><?php echo $csharp; ?></span>C#</a>
                        </div>
                    </div>
                </div>
                
                <?php html_footer(); ?>
            </div>

            <div class="col-md-9" id="rightSide">
                <div class="panel panel-default box">
                    <div class="panel-heading" id="contentHeading">
                        <i class="fa fa-list-alt"></i> Your Snippets
                    </div>
                    <div class="panel-body" id="content">

                        <?php echo $html_content3; ?>

                        <ul class="nav nav-tabs" id="myTab">
                            <li class="active"><a href="#publicSnippets">Public Snippets</a></li>
                            <li><a href="#privateSnippets">Private Snippets</a></li>
                        </ul>
                        <div class="tab-content" style="margin-top:10px;">
                            <div class="tab-pane active" id="publicSnippets"><?php echo $html_content; ?></div>
                            <div class="tab-pane" id="privateSnippets"><?php echo $html_content2; ?></div>
                        </div>

                    </div>
                    <div class="panel-footer">
                        <small><strong>Today date:</strong> <span id="todayDate"></span> &middot; <strong>Current time:</strong> <span id="currentTime"></span>  &middot; <strong>Total snippets in database:</strong> <span id="totalSnippets"></span></small>
                    </div>
                </div>
            </div>

        </div>

    </div>
    <!-- /container -->

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
    <script>
      todayDate('todayDate');
      currentTime('currentTime');
      totalSnippets('totalSnippets');
      $('#myTab a').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
      });
    </script>

</body>

</html>