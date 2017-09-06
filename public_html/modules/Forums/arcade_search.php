<?php
/************************************************************************/
/* Platinum Nuke Pro: Expect to be impressed                  COPYRIGHT */
/*                                                                      */
/* Copyright (c) 2004 - 2006 by http://www.techgfx.com                  */
/*     Techgfx - Graeme Allan                       (goose@techgfx.com) */
/*                                                                      */
/* Copyright (c) 2004 - 2006 by http://www.nukeplanet.com               */
/*     Loki / Teknerd - Scott Partee           (loki@nukeplanet.com)    */
/*                                                                      */
/* Copyright (c) 2007 - 2017 by http://www.platinumnukepro.com          */
/*                                                                      */
/* Refer to platinumnukepro.com for detailed information on this CMS    */
/*******************************************************************************/
/* This file is part of the PlatinumNukePro CMS - http://platinumnukepro.com   */
/*                                                                             */
/* This program is free software; you can redistribute it and/or               */
/* modify it under the terms of the GNU General Public License                 */
/* as published by the Free Software Foundation; either version 2              */
/* of the License, or any later version.                                       */
/*                                                                             */
/* This program is distributed in the hope that it will be useful,             */
/* but WITHOUT ANY WARRANTY; without even the implied warranty of              */
/* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               */
/* GNU General Public License for more details.                                */
/*                                                                             */
/* You should have received a copy of the GNU General Public License           */
/* along with this program; if not, write to the Free Software                 */
/* Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. */
/*******************************************************************************/

if ( !defined('MODULE_FILE') )
{
   die("You can't access this file directly...");
}



if ($popup != "1") {

	$module_name = basename(dirname(__FILE__));

	require_once("modules/".$module_name."/nukebb.php");

} else {

	$phpbb_root_path = 'modules/Forums/';

}



define('IN_PHPBB', true);

$phpbb_root_path = 'modules/Forums/';

include_once($phpbb_root_path . 'extension.inc');

include_once($phpbb_root_path . 'common.'.$phpEx);

require_once( $phpbb_root_path . 'gf_funcs/gen_funcs.' . $phpEx );

include_once('includes/constants.'. $phpEx);



//

// Start session management

//

$userdata = session_pagestart($user_ip, PAGE_ARCADES, $nukeuser);

init_userprefs($userdata);

//

// End session management

//

include_once('includes/functions_arcade.' . $phpEx);

//

// Start auth check

//

if (!$userdata['session_logged_in']) {

		$header_location = ( @preg_match("/Microsoft|WebSTAR|Xitami/", getenv("SERVER_SOFTWARE")) ) ? "Refresh: 0; URL=" : "Location: ";

		header($header_location . append_sid("login.$phpEx?redirect=arcade.$phpEx", true));

		exit;

}

//

// End of auth check

//





$x = intval($_GET['x']);

//Games user hasn't played

if($x == 1)

{



//Reads your ACP Arcade settings...

$arcade_config = array();

$arcade_config = read_arcade_config();



//Sets the order by for the games based on you ACP settings

$order_by = '';

switch ( $arcade_config['game_order']) {

		case 'Alpha':

			$order_by = ' game_name ASC ';

			break;



		case 'Popular':

			$order_by = ' game_set DESC ';

			break;



		case 'Fixed':

			$order_by = ' game_order ASC ';

			break;



		case 'Random':

			$order_by = ' RAND() ';

			break;



		case 'News':

			$order_by = ' game_id DESC ';

			break;



		default :

			$order_by = ' game_order ASC ';

			break;

}





$template->set_filenames(array(

		'body' => 'arcade_body.tpl')

);



$template->assign_vars(array(

		'URL_ARCADE' => '<nobr><a class="cattitle" href="' . append_sid("arcade.$phpEx") . '">' . $lang['lib_arcade'] . '</a></nobr> ',

		'URL_BESTSCORES' => '<nobr><a class="cattitle" href="' . append_sid("toparcade.$phpEx") . '">' . $lang['best_scores'] . '</a></nobr> ',

		'URL_SCOREBOARD' => '<nobr><a class="cattitle" href="' . append_sid("scoreboard.$phpEx?gid=$gid") . '">' . $lang['scoreboard'] . '</a></nobr> ',

		'MANAGE_COMMENTS' => '<nobr><a class="cattitle" href="' . append_sid("comments_list.$phpEx") . '">' . $lang['comments'] . '</a></nobr> ',

		'CATTITLE' => 'Games I haven\'t played',

		'NAV_DESC' => '<a class="nav" href="' . append_sid("arcade.$phpEx") . '">' . $lang['arcade'] . '</a> ' ,

		'L_GAME' => $lang['games'],

		'ARCADE_COL' => ($arcade_config['use_fav_category'])? 6:5,

		'ARCADE_COL1' => ($arcade_config['use_fav_category'])? 2:1,

		'FAV' => $lang['fav'],

		'L_HIGHSCORE' => $lang['highscore'],

		'L_YOURSCORE' => $lang['yourbestscore'],

		'L_DESC' => $lang['desc_game'],

		'L_ARCADE' => $lang['lib_arcade'])

);



$sql = "SELECT g.*, u.username, u.user_id, u.user_color_gc, s.score_game, s.score_date FROM " . GAMES_TABLE . " g left join " . USERS_TABLE . " u on g.game_highuser = u.user_id left join " . SCORES_TABLE . " s on s.game_id = g.game_id and s.user_id = " . $userdata['user_id'] . " ORDER BY $order_by";



if( !($result = $db->sql_query($sql)) ) {

		message_die(GENERAL_ERROR, "Unable to retrieve game and score data", '', __LINE__, __FILE__, $sql);

}



$total_match_count = 0;



while( $row = $db->sql_fetchrow($result) ) {

        //Displays on the games that you have no score/haven't played

        if($row['score_game'] == 0)

         {

                $total_match_count++;

                $row['username'] = CheckUsernameColor($row['user_color_gc'], $row['username']);

		$template->assign_block_vars('gamerow', array(

				'GAMENAME' => $row['game_name'],

				'GAMEPIC' => ( $row['game_pic'] != '' ) ? "<a href='" . append_sid("games.$phpEx?gid=" . $row['game_id'] ) . "'><img src='".$phpbb_root_path ."games/pics/" . $row['game_pic'] . "' align='absmiddle' border='0' width='30' height='30' alt='" . $row['game_name'] . "' ></a>" : '' ,

				'GAMESET' => ( $row['game_set'] != 0  ) ? $lang['game_actual_nbset'] . $row['game_set'] : '',

				'GAMEDESC' => $row['game_desc'],

				'HIGHSCORE' => number_format($row['game_highscore']),

				'YOURHIGHSCORE' => number_format($row['score_game']),

				'CLICKPLAY' => '<a href="' . append_sid("games.$phpEx?gid=" . $row['game_id'] ) . '">Click to Play!</a>',

				'NORECORD' => ( $row['game_highscore'] == 0 ) ? $lang['no_record'] : '',

				'HIGHUSER' => ( $row['game_highuser'] != 0 ) ? '(' . $row['username'] . ')' : '' ,

				'URL_SCOREBOARD' => '<nobr><a class="cattitle" href="' . append_sid("scoreboard.$phpEx?gid=" . $row['game_id'] ) . '">' . "<img src='".$phpbb_root_path ."templates/" . $theme['template_name'] . "/images/scoreboard.gif' align='absmiddle' border='0' alt='" . $lang['scoreboard'] . " " . $row['game_name'] . "'>" . '</a></nobr> ',

				'GAMEID' => $row['game_id'],

				'DATEHIGH' => "<nobr>" . create_date( $board_config['default_dateformat'] , $row['game_highdate'] , $board_config['board_timezone'] ) . "</nobr>",

				'YOURDATEHIGH' => "<nobr>" . create_date( $board_config['default_dateformat'] , $row['score_date'] , $board_config['board_timezone'] ) . "</nobr>",

				'IMGFIRST' => ( $row['game_highuser'] == $userdata['user_id'] ) ? "&nbsp;&nbsp;<img src='".$phpbb_root_path ."templates/" . $theme['template_name'] . "/images/couronne.gif' align='absmiddle'>" : "" ,

				'ADD_FAV' => ($arcade_config['use_fav_category'])?'<td class="row1" width="25" align="center" valign="center"><a href="' . append_sid("arcade.$phpEx?favori=" . $row['game_id'] ) .'"><img src="modules/Forums/templates/subSilver/images/favs.gif" border=0 alt="'.$lang['add_fav'].'"></a></td>':'',

				'GAMEPOPUPLINK' => "<a href='javascript:Arcade_Popup(\"".append_sid("gamespopup.$phpEx?gid=".$row['game_id'] )."\", \"New_Window\",\"".$row['game_width']."\",\"".$row['game_height']."\", \"no\")'>New Window</a>",

				'GAMELINK' => '<nobr><a href="' . append_sid("games.$phpEx?gid=" . $row['game_id'] ) . '">' . $row['game_name'] . '</a></nobr> ' )

		);



		if ( $row['game_highscore'] !=0 ) {

				$template->assign_block_vars('gamerow.recordrow',array());

		}



		if ( $row['score_game'] !=0 ) {

				$template->assign_block_vars('gamerow.yourrecordrow',array());

		}

		else

		{

			$template->assign_block_vars('gamerow.playrecordrow',array()) ;

		}

         }

}





//Sets the number of total search results to be displayed.

$l_search_matches = ( $total_match_count == 1 ) ? sprintf($lang['Found_search_match'], $total_match_count) : sprintf($lang['Found_search_matches'], $total_match_count);



$template->assign_block_vars('arcade_search', array(

                'L_SEARCH_MATCHES' => $l_search_matches));









//

// Output page header

include_once($phpbb_root_path . 'headingarcade.'.$phpEx);

include_once($phpbb_root_path . 'whoisplaying.'.$phpEx);

$page_title = $lang['arcade'];

include_once('includes/page_header.'.$phpEx);

$template->pparse('body');

include_once('includes/page_tail.'.$phpEx);



}



//Newest Games

if($x == 2)

{



//Reads your ACP Arcade settings...

$arcade_config = array();

$arcade_config = read_arcade_config();



//Total number of Newest Games to display

$total_match_count = 25;



$template->set_filenames(array(

		'body' => 'arcade_body.tpl')

);



$template->assign_vars(array(

		'URL_ARCADE' => '<nobr><a class="cattitle" href="' . append_sid("arcade.$phpEx") . '">' . $lang['lib_arcade'] . '</a></nobr> ',

		'URL_BESTSCORES' => '<nobr><a class="cattitle" href="' . append_sid("toparcade.$phpEx") . '">' . $lang['best_scores'] . '</a></nobr> ',

		'URL_SCOREBOARD' => '<nobr><a class="cattitle" href="' . append_sid("scoreboard.$phpEx?gid=$gid") . '">' . $lang['scoreboard'] . '</a></nobr> ',

		'MANAGE_COMMENTS' => '<nobr><a class="cattitle" href="' . append_sid("comments_list.$phpEx") . '">' . $lang['comments'] . '</a></nobr> ',

		'CATTITLE' => $total_match_count. ' Newest Games',

		'NAV_DESC' => '<a class="nav" href="' . append_sid("arcade.$phpEx") . '">' . $lang['arcade'] . '</a> ' ,

		'L_GAME' => $lang['games'],

		'ARCADE_COL' => ($arcade_config['use_fav_category'])? 6:5,

		'ARCADE_COL1' => ($arcade_config['use_fav_category'])? 2:1,

		'FAV' => $lang['fav'],

		'L_HIGHSCORE' => $lang['highscore'],

		'L_YOURSCORE' => $lang['yourbestscore'],

		'L_DESC' => $lang['desc_game'],

		'L_ARCADE' => $lang['lib_arcade'])

);



$sql = "SELECT g.*, u.username, u.user_id, u.user_color_gc, s.score_game, s.score_date FROM " . GAMES_TABLE . " g left join " . USERS_TABLE . " u on g.game_highuser = u.user_id left join " . SCORES_TABLE . " s on s.game_id = g.game_id and s.user_id = " . $userdata['user_id'] . " ORDER BY g.game_order DESC LIMIT 0,$total_match_count";



if( !($result = $db->sql_query($sql)) ) {

		message_die(GENERAL_ERROR, "Unable to retrieve game and score data", '', __LINE__, __FILE__, $sql);

}



while( $row = $db->sql_fetchrow($result) ) {

        //Displays on the games that you have no score/haven't played

                $row['username'] = CheckUsernameColor($row['user_color_gc'], $row['username']);

		$template->assign_block_vars('gamerow', array(

				'GAMENAME' => $row['game_name'],

				'GAMEPIC' => ( $row['game_pic'] != '' ) ? "<a href='" . append_sid("games.$phpEx?gid=" . $row['game_id'] ) . "'><img src='".$phpbb_root_path ."games/pics/" . $row['game_pic'] . "' align='absmiddle' border='0' width='30' height='30' alt='" . $row['game_name'] . "' ></a>" : '' ,

				'GAMESET' => ( $row['game_set'] != 0  ) ? $lang['game_actual_nbset'] . $row['game_set'] : '',

				'GAMEDESC' => $row['game_desc'],

				'HIGHSCORE' => number_format($row['game_highscore']),

				'YOURHIGHSCORE' => number_format($row['score_game']),

				'CLICKPLAY' => '<a href="' . append_sid("games.$phpEx?gid=" . $row['game_id'] ) . '">Click to Play!</a>',

				'NORECORD' => ( $row['game_highscore'] == 0 ) ? $lang['no_record'] : '',

				'HIGHUSER' => ( $row['game_highuser'] != 0 ) ? '(' . $row['username'] . ')' : '' ,

				'URL_SCOREBOARD' => '<nobr><a class="cattitle" href="' . append_sid("scoreboard.$phpEx?gid=" . $row['game_id'] ) . '">' . "<img src='".$phpbb_root_path ."templates/" . $theme['template_name'] . "/images/scoreboard.gif' align='absmiddle' border='0' alt='" . $lang['scoreboard'] . " " . $row['game_name'] . "'>" . '</a></nobr> ',

				'GAMEID' => $row['game_id'],

				'DATEHIGH' => "<nobr>" . create_date( $board_config['default_dateformat'] , $row['game_highdate'] , $board_config['board_timezone'] ) . "</nobr>",

				'YOURDATEHIGH' => "<nobr>" . create_date( $board_config['default_dateformat'] , $row['score_date'] , $board_config['board_timezone'] ) . "</nobr>",

				'IMGFIRST' => ( $row['game_highuser'] == $userdata['user_id'] ) ? "&nbsp;&nbsp;<img src='".$phpbb_root_path ."templates/" . $theme['template_name'] . "/images/couronne.gif' align='absmiddle'>" : "" ,

				'ADD_FAV' => ($arcade_config['use_fav_category'])?'<td class="row1" width="25" align="center" valign="center"><a href="' . append_sid("arcade.$phpEx?favori=" . $row['game_id'] ) .'"><img src="modules/Forums/templates/subSilver/images/favs.gif" border=0 alt="'.$lang['add_fav'].'"></a></td>':'',

				'GAMEPOPUPLINK' => "<a href='javascript:Arcade_Popup(\"".append_sid("gamespopup.$phpEx?gid=".$row['game_id'] )."\", \"New_Window\",\"".$row['game_width']."\",\"".$row['game_height']."\", \"no\")'>New Window</a>",

                                'GAMELINK' => '<nobr><a href="' . append_sid("games.$phpEx?gid=" . $row['game_id'] ) . '">' . $row['game_name'] . '</a></nobr> ' )

		);



		if ( $row['game_highscore'] !=0 ) {

				$template->assign_block_vars('gamerow.recordrow',array());

		}



		if ( $row['score_game'] !=0 ) {

				$template->assign_block_vars('gamerow.yourrecordrow',array());

		}

		else

		{

			$template->assign_block_vars('gamerow.playrecordrow',array()) ;

		}

         }







//Sets the number of total search results to be displayed.

$l_search_matches = ( $total_match_count == 1 ) ? sprintf($lang['Found_search_match'], $total_match_count) : sprintf($lang['Found_search_matches'], $total_match_count);



$template->assign_block_vars('arcade_search', array(

                'L_SEARCH_MATCHES' => $l_search_matches));









//

// Output page header

include_once($phpbb_root_path . 'headingarcade.'.$phpEx);

include_once($phpbb_root_path . 'whoisplaying.'.$phpEx);

$page_title = $lang['arcade'];

include_once('includes/page_header.'.$phpEx);

$template->pparse('body');

include_once('includes/page_tail.'.$phpEx);



}

        //Arcade Search is called

        //Return Search string and format it.

        $srchstring = $_POST['srchstring'];

        $srchstring="*".$srchstring."*";

	if (trim($srchstring) <> "**")

        {

        // replace wildcards */? to use with mysql "LIKE"

        $search = str_replace("_", "\\_", $srchstring);

        $search = str_replace("%", "\\%", $search);

        $search = str_replace("*", "%" , $search);

        $search = str_replace("?", "_" , $search);

        }

        

        //Sets search by game name or description

        $searchin = $_POST['searchin'];

        if($searchin == 'name')

        {

        $where_search= "WHERE game_name LIKE '$search'";

        }

        elseif($searchin == 'desc')

        {

        $where_search= "WHERE game_desc LIKE '$search'";

        }



//Reads your ACP Arcade settings...

$arcade_config = array();

$arcade_config = read_arcade_config();



//Gets total games from you search results and set the number of total search results.

$sql = "SELECT COUNT(*) as total_games FROM " . GAMES_TABLE . " $where_search";



		if( !($result = $db->sql_query($sql)) ) {

				message_die(GENERAL_ERROR, "Impossible d\acceder � la tables des jeux", '', __LINE__, __FILE__, $sql);

		}





		$row = $db->sql_fetchrow($result);

		$total_match_count = $row['total_games'];

		$l_search_matches = ( $total_match_count == 1 ) ? sprintf($lang['Found_search_match'], $total_match_count) : sprintf($lang['Found_search_matches'], $total_match_count);



//Sets the order by for the games based on you ACP settings

$order_by = '';

switch ( $arcade_config['game_order']) {

		case 'Alpha':

			$order_by = ' game_name ASC ';

			break;



		case 'Popular':

			$order_by = ' game_set DESC ';

			break;



		case 'Fixed':

			$order_by = ' game_order ASC ';

			break;



		case 'Random':

			$order_by = ' RAND() ';

			break;



		case 'News':

			$order_by = ' game_id DESC ';

			break;



		default :

			$order_by = ' game_order ASC ';

			break;

}





$template->set_filenames(array(

		'body' => 'arcade_body.tpl')

);



$template->assign_block_vars('arcade_search', array(

                'L_SEARCH_MATCHES' => $l_search_matches));



$template->assign_vars(array(

		'URL_ARCADE' => '<nobr><a class="cattitle" href="' . append_sid("arcade.$phpEx") . '">' . $lang['lib_arcade'] . '</a></nobr> ',

		'URL_BESTSCORES' => '<nobr><a class="cattitle" href="' . append_sid("toparcade.$phpEx") . '">' . $lang['best_scores'] . '</a></nobr> ',

		'URL_SCOREBOARD' => '<nobr><a class="cattitle" href="' . append_sid("scoreboard.$phpEx?gid=$gid") . '">' . $lang['scoreboard'] . '</a></nobr> ',

		'MANAGE_COMMENTS' => '<nobr><a class="cattitle" href="' . append_sid("comments_list.$phpEx") . '">' . $lang['comments'] . '</a></nobr> ',

		'CATTITLE' => 'Arcade Search',

		'NAV_DESC' => '<a class="nav" href="' . append_sid("arcade.$phpEx") . '">' . $lang['arcade'] . '</a> ' ,

		'L_GAME' => $lang['games'],

		'ARCADE_COL' => ($arcade_config['use_fav_category'])? 6:5,

		'ARCADE_COL1' => ($arcade_config['use_fav_category'])? 2:1,

		'FAV' => $lang['fav'],

		'L_HIGHSCORE' => $lang['highscore'],

		'L_YOURSCORE' => $lang['yourbestscore'],

		'L_DESC' => $lang['desc_game'],

		'L_ARCADE' => $lang['lib_arcade'])

);

$sql = "SELECT g.*, u.username, u.user_id, u.user_color_gc, s.score_game, s.score_date FROM " . GAMES_TABLE . " g left join " . USERS_TABLE . " u on g.game_highuser = u.user_id left join " . SCORES_TABLE . " s on s.game_id = g.game_id and s.user_id = " . $userdata['user_id'] . " $where_search ORDER BY $order_by";



if( !($result = $db->sql_query($sql)) ) {

		message_die(GENERAL_ERROR, "Could not read from the games/users table", '', __LINE__, __FILE__, $sql);

}



while( $row = $db->sql_fetchrow($result) ) {

                $row['username'] = CheckUsernameColor($row['user_color_gc'], $row['username']);

		$template->assign_block_vars('gamerow', array(

				'GAMENAME' => $row['game_name'],

				'GAMEPIC' => ( $row['game_pic'] != '' ) ? "<a href='" . append_sid("games.$phpEx?gid=" . $row['game_id'] ) . "'><img src='".$phpbb_root_path ."games/pics/" . $row['game_pic'] . "' align='absmiddle' border='0' width='30' height='30' alt='" . $row['game_name'] . "' ></a>" : '' ,

				'GAMESET' => ( $row['game_set'] != 0  ) ? $lang['game_actual_nbset'] . $row['game_set'] : '',

				'GAMEDESC' => $row['game_desc'],

				'HIGHSCORE' => number_format($row['game_highscore']),

				'YOURHIGHSCORE' => number_format($row['score_game']),

				'CLICKPLAY' => '<a href="' . append_sid("games.$phpEx?gid=" . $row['game_id'] ) . '">Click to Play!</a>',

				'NORECORD' => ( $row['game_highscore'] == 0 ) ? $lang['no_record'] : '',

				'HIGHUSER' => ( $row['game_highuser'] != 0 ) ? '(' . $row['username'] . ')' : '' ,

				'URL_SCOREBOARD' => '<nobr><a class="cattitle" href="' . append_sid("scoreboard.$phpEx?gid=" . $row['game_id'] ) . '">' . "<img src='".$phpbb_root_path ."templates/" . $theme['template_name'] . "/images/scoreboard.gif' align='absmiddle' border='0' alt='" . $lang['scoreboard'] . " " . $row['game_name'] . "'>" . '</a></nobr> ',

				'GAMEID' => $row['game_id'],

				'DATEHIGH' => "<nobr>" . create_date( $board_config['default_dateformat'] , $row['game_highdate'] , $board_config['board_timezone'] ) . "</nobr>",

				'YOURDATEHIGH' => "<nobr>" . create_date( $board_config['default_dateformat'] , $row['score_date'] , $board_config['board_timezone'] ) . "</nobr>",

				'IMGFIRST' => ( $row['game_highuser'] == $userdata['user_id'] ) ? "&nbsp;&nbsp;<img src='".$phpbb_root_path ."templates/" . $theme['template_name'] . "/images/couronne.gif' align='absmiddle'>" : "" ,

				'ADD_FAV' => ($arcade_config['use_fav_category'])?'<td class="row1" width="25" align="center" valign="center"><a href="' . append_sid("arcade.$phpEx?favori=" . $row['game_id'] ) .'"><img src="modules/Forums/templates/subSilver/images/favs.gif" border=0 alt="'.$lang['add_fav'].'"></a></td>':'',

				'GAMEPOPUPLINK' => "<a href='javascript:Arcade_Popup(\"".append_sid("gamespopup.$phpEx?gid=".$row['game_id'] )."\", \"New_Window\",\"".$row['game_width']."\",\"".$row['game_height']."\", \"no\")'>New Window</a>",

                                'GAMELINK' => '<nobr><a href="' . append_sid("games.$phpEx?gid=" . $row['game_id'] ) . '">' . $row['game_name'] . '</a></nobr> ' )

		);



		if ( $row['game_highscore'] !=0 ) {

				$template->assign_block_vars('gamerow.recordrow',array());

		}



		if ( $row['score_game'] !=0 ) {

				$template->assign_block_vars('gamerow.yourrecordrow',array());

		}

		else

		{

			$template->assign_block_vars('gamerow.playrecordrow',array()) ;

		}

}



//

// Output page header

include_once($phpbb_root_path . 'headingarcade.'.$phpEx);

include_once($phpbb_root_path . 'whoisplaying.'.$phpEx);

$page_title = $lang['arcade'];

include_once('includes/page_header.'.$phpEx);

$template->pparse('body');

include_once('includes/page_tail.'.$phpEx);

?>

