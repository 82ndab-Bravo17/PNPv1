<?php
/************************************************************************/
/* Nuke HoneyPot - Antibot Script                                       */
/* ==============================                                       */
/*                                                                      */
/* Copyright (c) 2013 coRpSE			                                */
/* http://www.headshotdomain.net                                        */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
if ( !defined('ADMIN_FILE') )
{
	die ('Access Denied');
}
global $admin_file;
if ($radminsuper==1) {
    adminmenu($admin_file . '.php?op=honeypot', 'Nuke Honeypot', 'Nuke_Honeypot.png');
}

?>