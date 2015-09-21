<?php

// list.php is public.

require_once('config.php');
require_once('class.mysqli.php');
require_once('class.util.php');
require_once('class.customs.php');

// HNAuthLib
if (!defined('HNAUTH_DIR')) define('HNAUTH_DIR', './HNAuthLib/');
require_once HNAUTH_DIR . 'HNAuth.php';
$errorMsg = '';

if ( !is_user_logged_in() ) {
  $authenticated = false;
} else {
  $session = new Session();
  $loggedInUser = $session->getItem('username');
  $loggedInLevel = $session->getItem('userlevel');
  $user_loggedin_id = get_userid($loggedInUser);
  $authenticated = true;
}

if (isset($_GET['category']) && !empty($_GET['category']) && in_array($_GET['category'], $snippet_category)) {
  $db = DB::getInstance();
  $snippet_cat = $db->filter($_GET['category']);
  if ($authenticated == false) {
    $query = "SELECT * FROM ".DBTABLE_SNIPPETS." WHERE category_tag='".$snippet_cat."' AND visibility='public'";
    $total_found = $db->num_rows($query);
    if ($total_found > 0) {
        $results = $db->get_results($query);
        $html_content = '<div class="alert alert-info" role="alert"><span class="label label-info">INFO</span> This category contains <strong>'.$total_found.'</strong> public snippets.</div>';
        $html_content .= '<div class="list-group snippet-list">';
        foreach ($results as $row) {
            $html_content .= '<a href="view_snippet.php?id='.$row['id'].'" class="list-group-item"><span class="snippet-title">'.$row['name'].'</span><span class="by-creator"> by <strong>'.get_username($row['user_id']).'</strong></span> &middot; <span class="created-date">'.get_timeago($row['updated_time']).'</span></a>';
        }
        $html_content .= '</div>';
    } else {
        $html_content = '<div class="alert alert-warning" role="alert"><span class="label label-warning">OOPS..</span> No available snippet found under this category.</div>';
    }
  } else {
    // authenticated == true
    $query = "SELECT * FROM ".DBTABLE_SNIPPETS." WHERE category_tag='".$snippet_cat."'";
    $total_found = $db->num_rows($query);
    if ($total_found > 0) {
        $results = $db->get_results($query);
        $html_content = '<div class="alert alert-info" role="alert"><span class="label label-info">INFO</span> This category contains <strong>'.$total_found.'</strong> snippets, but some snippets will not be listed here due to private visibility.</div>';
        $html_content .= '<div class="list-group snippet-list">';
        foreach ($results as $row) {
            if ($row['visibility'] == 'private' && $row['user_id'] == $user_loggedin_id) {
                $html_content .= '<a href="view_snippet.php?id='.$row['id'].'" class="list-group-item"><span class="snippet-title">'.$row['name'].'</span><span class="by-creator"> by <strong>'.get_username($row['user_id']).'</strong></span> &middot; <span class="created-date">'.get_timeago($row['updated_time']).'</span><br><span class="private-snippet"><i class="fa fa-info-circle"></i> This is your private snippet. Only you can see this!</span></a>';
            } else if ($row['visibility'] == 'public') {
                $html_content .= '<a href="view_snippet.php?id='.$row['id'].'" class="list-group-item"><span class="snippet-title">'.$row['name'].'</span><span class="by-creator"> by <strong>'.get_username($row['user_id']).'</strong></span> &middot; <span class="created-date">'.get_timeago($row['updated_time']).'</span></a>';
            }
        }
        $html_content .= '</div>';
    } else {
        $html_content = '<div class="alert alert-warning" role="alert"><span class="label label-warning">OOPS..</span> No available snippet found under this category.</div>';
    }
  }
} else {
  // ID not numeric
  header('Location: 404.php?error=invalid_category');
  exit;
}

$category_title = get_category_title($snippet_cat);
$category_color = get_category_color($snippet_cat);

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
            <a class="navbar-brand" href="index.php"><i class="fa fa-code"></i> <?php echo APP_NAME; ?></a>
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
            <?php if ($authenticated) { ?>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> Signed in as <strong><?php echo $loggedInUser; ?></strong> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <?php html_user_nav(); ?>
                    </ul>
                </li>
            </ul>
            <?php } else { ?>
            <div class="navbar-text pull-right">
                <a href="login.php"><strong>Login</strong></a> / <a href="register.php"><strong>Register</strong></a>
            </div>
            <?php } ?>
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
                        <i class="fa fa-list-alt"></i> Snippet list for Category: <span class="label <?php echo $category_color; ?>"><i class="fa fa-folder"></i> <?php echo $category_title; ?></span>
                    </div>
                    <div class="panel-body" id="content">
                    	<?php echo $html_content; ?>
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
    </script>

</body>

</html>