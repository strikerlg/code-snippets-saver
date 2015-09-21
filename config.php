<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Set up MySQLi database
define( 'DB_HOST', 'localhost' ); // set database host (common: localhost)
define( 'DB_USER', 'root' ); // set database user (common: root)
define( 'DB_PASS', 'toor' ); // set database password
define( 'DB_NAME', 'db' ); // set database name
define( 'SEND_EMAIL', false ); //send errors to email?
define( 'SEND_ERRORS_TO', 'hnrird@gmail.com' ); //set email notification email address
define( 'DISPLAY_DEBUG', true ); //display db errors?

// Define database tables used
define( 'DBTABLE_SNIPPETS', 'csc_snippets' );
define( 'DBTABLE_USERS', 'csc_users' );

// Define application info
define( 'APP_NAME', 'Code Snippets Saver v1.x' );
define( 'APP_DESC', 'Save your precious snippets and share with friends.' );
define( 'APP_AUTHOR', 'Heiswayi Nrird' );
define( 'APP_KEYWORD', 'code snippets saver, php pastebin, snippy app, paste app, personal snippets app, private snippets saver' );
define( 'APP_VERSION', '1.0' );
define( 'APP_URL', 'http://wayi.me/app/snippets' );
define( 'APP_EMAIL', 'codesnippetssaver@wayi.me' );

// Configurations for HNAuthLib
$hndb['table'] = DBTABLE_USERS; // user database table name used in HNAuthLib
$hnauth['salt'] = "Yu23ds09*d?"; // password salt
$hnauth['default_userlevel'] = 1; // user level (default: 1)
$loc = 'en'; // English
$hnmail['from'] = APP_EMAIL; // HNAuthLib email settings: FROM address
$hnmail['from_name'] = APP_NAME; // HNAuthLib email settings: FROM name
$hnmail['site_url'] = APP_URL; // HNAuthLib email settings: app site URL
$hnsite['name'] = APP_NAME; // app site name

$snippet_category = array('plain-text', 'html', 'css', 'javascript', 'php', 'mysql', 'csharp');

function html_main_nav() {
	echo '<li><a href="recent_snippets.php"><i class="fa fa-clock-o"></i> Recent Snippets</a></li>';
    echo '<li><a href="popular_snippets.php"><i class="fa fa-bookmark"></i> Popular Snippets</a></li>';
    echo '<li><a href="add_snippet.php"><i class="fa fa-plus"></i> Submit a Snippet</a></li>';
}

function html_user_nav() {
	echo '<li><a href="my_snippets.php"><i class="fa fa-code"></i> My Saved Snippets</a></li>';
	echo '<li class="divider"></li>';
	echo '<li><a href="change_password.php"><i class="fa fa-asterisk"></i> Change Password</a></li>';
    echo '<li><a href="change_email.php"><i class="fa fa-envelope"></i> Change Email</a></li>';
    echo '<li class="divider"></li>';
    echo '<li><a href="logout.php"><i class="fa fa-sign-out"></i> Sign Out</a></li>';
}

function html_footer() {
	echo '<p class="copyright">&copy; '.APP_NAME.'<br>Handcraft with &hearts; by <a href="https://wayi.me/about">Heiswayi Nrird</a></p>';
}

?>