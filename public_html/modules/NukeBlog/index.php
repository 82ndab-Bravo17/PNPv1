<?php 
// ///////////////////////////////////////////////////////////////////////////////////
// NukeBlog by Trevor Scott					                                        //
// ///////////////////////////////////////////////////////////////////////////////////
if ( !defined('MODULE_FILE') ) {
    die ('You can\'t access this file directly...');
}
// ///////////////////////////////////////////////////////////////////////////////////
// Take care of PHPNuke Framework.													//
// ///////////////////////////////////////////////////////////////////////////////////
require_once("mainfile.php");
$module_name = basename(dirname(__FILE__));
get_lang($module_name);
$self = "modules.php?name=" . $module_name;

// ///////////////////////////////////////////////////////////////////////////////////
// Include core and index function libraries.										//
// ///////////////////////////////////////////////////////////////////////////////////
include_once("modules/$module_name/functions/functions_common.php");
include_once("modules/$module_name/functions/functions_index.php");

// ///////////////////////////////////////////////////////////////////////////////////
// Turn right side blocks on or off through NukeBlog Admin Panel.					//
// ///////////////////////////////////////////////////////////////////////////////////
if (get_config("right_blocks")=="1"){
define('INDEX_FILE', true);
}
// ///////////////////////////////////////////////////////////////////////////////////
// Block against non-site members.													//
// ///////////////////////////////////////////////////////////////////////////////////
if (!$user) {
    include_once("header.php");
    opentable();
    center("<span class=\"title\">" . _NB_RESTRICTED . "</span>");
    closetable();
    br();
    opentable();
    center(_NB_MEMREQ);
    closetable();
    include_once("footer.php");
    include_once("includes/counter.php");
    die();
} 

// ///////////////////////////////////////////////////////////////////////////////////
// Condition user identification variable into a "better" format.					//
// ///////////////////////////////////////////////////////////////////////////////////
$temp_user = base64_decode($user);
$temp_cookie = explode(":", $temp_user);
$sql = "SELECT user_id, username FROM " . $user_prefix . "_users WHERE username='" . $temp_cookie[1] . "'";
$result = $db->sql_query($sql);
while ($row = $db->sql_fetchrow($result)) {
    $nb_user[user_id] = $row[user_id];
    $nb_user[username] = $row[username];
} 

// ///////////////////////////////////////////////////////////////////////////////////
// Auto-set $offset to the number stored in the database.							//
// ///////////////////////////////////////////////////////////////////////////////////
if (!$offset) {
    $offset = 0;
} 

// ///////////////////////////////////////////////////////////////////////////////////
// Include site header file.														//
// ///////////////////////////////////////////////////////////////////////////////////
include_once("header.php");

// ///////////////////////////////////////////////////////////////////////////////////
// Standard blog user menu.															//
// ///////////////////////////////////////////////////////////////////////////////////
user_menu();

// ///////////////////////////////////////////////////////////////////////////////////
// Traffic control switch/case for the browsing and interacting with other's blogs.	//
// ///////////////////////////////////////////////////////////////////////////////////
switch ($op) {

    case "fetch_author":
        fetch_author($user_id,$offset);
        break;

    case "blog_members":
        blog_members();
        break;

    case "comment_save":
        comment_save($blog_id,$comm_body);
        break;

    case "comment_add":
        comment_add($blog_id);
        break;

    case "fetch_blog":
        fetch_blog($blog_id);
        break;

    case "blog_alert_save":
        blog_alert_save($form);
        break;

    case "admin_blog_alert":
        admin_blog_alert($blog_id);
        break;

    case "blog_list":
        blog_list($offset);
        break;

    default:
        blog_list($offset);
        break;
} 

// ///////////////////////////////////////////////////////////////////////////////////
// PHPNuke Footer.																	//
// ///////////////////////////////////////////////////////////////////////////////////
include_once("footer.php");
include_once("includes/counter.php");
die();

?>