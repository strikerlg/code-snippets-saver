<?php

// edit_snippet.php is NOT public.

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

if (isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])) {
  $db = DB::getInstance();
  $snippet_id = $db->filter($_GET['id']);
  $query = "SELECT user_id, category_tag, name, visibility, description, snippet, created_time, updated_time, view_count FROM ".DBTABLE_SNIPPETS." WHERE id='".$snippet_id."'";
  if ($db->num_rows($query) > 0) {
    list($user_id, $category_tag, $name, $visibility, $description, $snippet, $created_time, $updated_time, $view_count) = $db->get_row($query);
    $snippet_user_id = $user_id;
    if ($snippet_user_id != $user_loggedin_id) {
      // User is NOT the owner of this snippet
      header('Location: 404.php?error=user_not_owner');
    } else {
      $snippet_visibility = $visibility;
      $snippet_name = htmlspecialchars(htmlspecialchars_decode($name));
      $snippet_cat = $category_tag;
      $snippet_cat_color = get_category_color($category_tag);
      $snippet_desc = htmlspecialchars_decode($description);
      $snippet_output = htmlspecialchars_decode($snippet);
      $snippet_created_time = get_timeago($created_time);
      $snippet_updated_time = get_timeago($updated_time);
      $snippet_creator = get_username($user_id);
      $snippet_view_count = $view_count;
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
    <title>
        <?php echo APP_NAME; ?>
    </title>
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
                    <div class="panel-heading">
                        <button type="button" class="btn btn-inverse" id="leftSideToggle" title="Show/Hide Snippet Categories"><i class="fa fa-outdent" id="leftSideToggleIcon"></i>
                        </button>
                        <button class="btn btn-primary" type="button" id="btnUpdateSnippet"><i class="fa fa-save"></i> Update Snippet</button>
                        <a href="#" class="btn btn-default back">Cancel</a>
                        <button class="btn btn-danger pull-right" type="button" id="btnDeleteSnippet">DELETE</button>
                    </div>
                    <div class="panel-body">

                        <div class="form-group">
                            <form class="form-inline" id="inputForm">
                                <div class="form-group">
                                    <input class="form-control" id="snippetName" type="text" placeholder="Enter snippet name.." maxlength="140" value="<?php echo $snippet_name; ?>">
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-folder"></i> Category:</span>
                                        <select class="form-control" id="snippetCategory">
                                            <option class="palette-silver-light" style="color:#000" value="plain-text" <?php if ($snippet_cat=='plain-text' ) echo ' selected="selected"'; ?>>Plain Text</option>
                                            <option class="palette-red-light" value="html" <?php if ($snippet_cat=='html' ) echo ' selected="selected"'; ?>>HTML</option>
                                            <option class="palette-orange-light" value="css" <?php if ($snippet_cat=='css' ) echo ' selected="selected"'; ?>>CSS</option>
                                            <option class="palette-yellow-light" value="javascript" <?php if ($snippet_cat=='javascript' ) echo ' selected="selected"'; ?>>JavaScript</option>
                                            <option class="palette-blue-light" value="php" <?php if ($snippet_cat=='php' ) echo ' selected="selected"'; ?>>PHP</option>
                                            <option class="palette-green-light" value="mysql" <?php if ($snippet_cat=='mysql' ) echo ' selected="selected"'; ?>>MySQL</option>
                                            <option class="palette-black-light" value="dos" <?php if ($snippet_cat=='dos' ) echo ' selected="selected"'; ?>>DOS</option>
                                            <option class="palette-purple-light" value="csharp" <?php if ($snippet_cat=='csharp' ) echo ' selected="selected"'; ?>>C#</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-eye"></i> Visibility:</span>
                                        <select class="form-control" id="snippetVisibility">
                                            <option value="public" <?php if ($snippet_visibility=='public' ) echo ' selected="selected"'; ?>>Public</option>
                                            <option value="private" <?php if ($snippet_visibility=='private' ) echo ' selected="selected"'; ?>>Private</option>
                                        </select>
                                    </div>
                                </div>

                            </form>
                        </div>

                        <div class="form-group">
                            <textarea id="codeDesc" class="form-control" rows="3" placeholder="Enter snippet description..."><?php echo $snippet_desc; ?></textarea>
                        </div>

                        <div class="form-group">
                            <textarea id="codeInput" class="form-control" rows="10" placeholder="Write or paste your code snippet here.."><?php echo $snippet; ?></textarea>
                            <div class="codeStats">Line: <span id="caretLine">0</span> / <span id="lineCount">0</span> <span id="charCount">0 characters</span>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <small><strong>Created on:</strong> <span id="createdTime"><?php echo $snippet_created_time; ?></span> &middot; <strong>Last updated:</strong> <span id="updatedTime"><?php echo $snippet_updated_time; ?></span> &middot; <strong>Creator:</strong> <span id="creator" data-userid="<?php echo $snippet_user_id; ?>"><?php echo $snippet_creator; ?></span> &middot; <strong>Views:</strong> <span id="viewCount"><?php echo $snippet_view_count; ?></span></small>
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
        editSnippet();
        $('#btnUpdateSnippet').click(function() {
            $('#btnUpdateSnippet').prop('disabled', true).html('<i class="fa fa-save fa-spin"></i> Saving...');
            var snippetName = $('input#snippetName').val();
            var snippetCategory = $('#snippetCategory option:selected').val();
            var snippetVisibility = $('#snippetVisibility option:selected').val();
            var snippetDesc = $('textarea#codeDesc').val();
            var snippetCode = $('textarea#codeInput').val();
            var snippetUserID = $('#creator').data('userid');

            // Validations
            if (snippetName.length == 0) {
                sweetAlert("Oops...", 'Snippet name cannot be empty.', "error");
                $('#btnUpdateSnippet').prop('disabled', false).html('<i class="fa fa-save"></i> Update Snippet');
                return;
            }
            //var regex = /[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
            var regex = /[`~!@#^*|=?;:'",<>\{\}\[\]\\\/]/gi;
            var isSplChar = regex.test(snippetName);
            if (isSplChar) {
                sweetAlert("Oops...", 'Special characters are not allowed in snippet name.', "error");
                $('#btnUpdateSnippet').prop('disabled', false).html('<i class="fa fa-save"></i> Update Snippet');
                return;
            }
            if (snippetCode.length == 0) {
                sweetAlert("Oops...", 'Please write/paste your snippet.', "error");
                $('#btnUpdateSnippet').prop('disabled', false).html('<i class="fa fa-save"></i> Update Snippet');
                return;
            }

            postData = {
                'action': 'edit',
                'snippetName': snippetName,
                'snippetCategory': snippetCategory,
                'snippetVisibility': snippetVisibility,
                'snippetDesc': snippetDesc,
                'snippetCode': snippetCode,
                'userID': snippetUserID,
                'snippetID': urlencode(getQueryVariable('id'))
            };

            $.post('processing.php', postData, function(response) {
                var arraydata = response.split("|");
                if (arraydata[0] == 'OK') {
                    window.location.assign("view_snippet.php?id=" + arraydata[1]);
                } else {
                    sweetAlert("Oops...", arraydata[1], "error");
                }
            });

            setTimeout(function() {
                $('#btnUpdateSnippet').prop('disabled', false).html('<i class="fa fa-save"></i> Update Snippet');
            }, 1000);

        });

        $('#btnDeleteSnippet').click(function() {
            $('#btnDeleteSnippet').prop('disabled', true).html('DELETE <i class="fa fa-spinner fa-spin"></i>');
            sweetAlert({
                title: "Are you sure?",
                text: "You will not be able to recover this snippet!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false
            }, function(isConfirm) {
                if (isConfirm) {
                    var snippetUserID = $('#creator').data('userid');
                    postData = {
                        'action': 'delete',
                        'userID': snippetUserID,
                        'snippetID': urlencode(getQueryVariable('id'))
                    };
                    $.post('processing.php', postData, function(response) {
                        var arraydata = response.split("|");
                        if (arraydata[0] == 'OK') {
                            sweetAlert("Deleted!", "Your snippet has been deleted.", "success");
                            window.location.assign("my_snippets.php");
                        } else {
                            sweetAlert("Oops...", arraydata[1], "error");
                        }
                    });
                } else {
                    setTimeout(function() {
                        $('#btnDeleteSnippet').prop('disabled', false).html('DELETE');
                    }, 1000);
                }
            });

        });
    </script>

</body>

</html>