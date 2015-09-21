<?php

// view_snippet.php is public.

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
  $user_loggedin_id = 0;
} else {
  $session = new Session();
  $loggedInUser = $session->getItem('username');
  $loggedInLevel = $session->getItem('userlevel');
  $user_loggedin_id = get_userid($loggedInUser);
  $authenticated = true;
}

if (isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])) {
  $db = DB::getInstance();
  $snippet_id = $db->filter($_GET['id']);
  $query = "SELECT user_id, category_tag, name, visibility, description, snippet, created_time, updated_time, view_count FROM ".DBTABLE_SNIPPETS." WHERE id='".$snippet_id."'";
  if ($db->num_rows($query) > 0) {
    list($user_id, $category_tag, $name, $visibility, $description, $snippet, $created_time, $updated_time, $view_count) = $db->get_row($query);
    if ($visibility == 'private' && $user_id != $user_loggedin_id) {
      // Visibility is private AND logged in user is not the owner of the snippet
      header('Location: 404.php?error=user_not_owner');
    } else {
      $snippet_user_id = $user_id;
      $snippet_name = $name;
      $snippet_cat = get_category_title($category_tag);
      $snippet_cat_color = get_category_color($category_tag);
      $snippet_desc = $description;
      if ($snippet_cat == 'plain-text') $snippet_output = '<div id="plainOutput"><pre class="plain"><code>'.$snippet.'</code></pre></div>';
      else $snippet_output = '<div id="codeOutput"><pre class="highlighted"><code>'.$snippet.'</code></pre></div>';
      $snippet_created_time = get_timeago($created_time);
      $snippet_updated_time = get_timeago($updated_time);
      $snippet_creator = get_username($user_id);
      $snippet_view_count = $view_count + 1;
      snippet_view_increment($snippet_id, $snippet_view_count);
    }
  } else {
    // ID not found
    header('Location: 404.php?error=id_not_found');
    exit;
  }
} else {
  // ID not numeric
  header('Location: 404.php?error=id_not_numeric');
  exit;
}

// Get total snippets for each category
$plain_text = get_total_bycategory('plain-text');
$html = get_total_bycategory('html');
$css = get_total_bycategory('css');
$javascript = get_total_bycategory('javascript');
$php = get_total_bycategory('php');
$mysql = get_total_bycategory('mysql');
$dos = get_total_bycategory('dos');
$csharp = get_total_bycategory('csharp');

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
    <title><?php echo $snippet_name; ?></title>
    <meta name="description" content="<?php echo $snippet_desc; ?>">
    <meta name="author" content="<?php echo $snippet_creator; ?>">
    <meta name="keyword" content="<?php echo APP_KEYWORD; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href='http://fonts.googleapis.com/css?family=Source+Code+Pro' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/bootstrap-select.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/sweet-alert.css">
    <link rel="stylesheet" href="js/highlight.styles/tomorrow.css">
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
                    <div class="panel-heading">
                        <button type="button" class="btn btn-inverse" id="leftSideToggle" title="Show/Hide Snippet Categories"><i class="fa fa-outdent" id="leftSideToggleIcon"></i>
                        </button>
                        <?php if ($snippet_user_id == $user_loggedin_id) { ?>
                          <a href="edit_snippet.php?id=<?php echo $snippet_id; ?>" class="btn btn-primary" type="button" id="btnEditSnippet"><i class="fa fa-pencil-square-o"></i> Edit Snippet</a>
                        <?php } ?>
                    </div>
                    <div class="panel-body">

                        <div id="infobox">
                            <h3 id="staticSnippetName"><?php echo $snippet_name; ?></h3>
                            <span class="label <?php echo $snippet_cat_color; ?>" id="staticSnippetCategory"><i class="fa fa-folder"></i> <span id="snippetCategoryText"><?php echo $snippet_cat; ?></span></span>
                            <pre id="staticSnippetDesc"><?php echo $snippet_desc; ?></pre>
                        </div>

                        <div class="form-group" id="snippetOutput">
                            <?php echo $snippet_output; ?>
                            <div class="codeStats"><span id="charCount">0 characters</span></div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <small><strong>Created on:</strong> <span id="createdTime"><?php echo $snippet_created_time; ?></span> &middot; <strong>Last updated:</strong> <span id="updatedTime"><?php echo $snippet_updated_time; ?></span> &middot; <strong>Creator:</strong> <span id="creator"><?php echo $snippet_creator; ?></span> &middot; <strong>Views:</strong> <span id="viewCount"><?php echo $snippet_view_count; ?></span></small>
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
        viewSnippet();
    </script>

</body>

</html>