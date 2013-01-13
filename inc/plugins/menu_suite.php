<?php
if(!defined("IN_MYBB")) {
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

//ACP Hooks
$plugins->add_hook("admin_config_menu", "menu_suite_admin_config_menu");
$plugins->add_hook("admin_config_action_handler", "menu_suite_admin_config_action_handler");
$plugins->add_hook("admin_config_permissions", "menu_suite_admin_config_permissions");


function menu_suite_info()
{
	return array(
		"name"			=> "Men&uuml; Suite",
		"description"	=> "Mit diesem Plugin kannst du deine Men&uuml;s verwalten",
		"website"		=> "http://mybbservice.de",
		"author"		=> "MyBBService",
		"authorsite"	=> "http://mybbservice.de",
		"version"		=> "1.0 Beta",
		"guid"			=> "",
		"compatibility" => "16*",
	);
}


function menu_suite_install()
{
	global $db;
	
	//Datenbank Tabelle
	$col = $db->build_create_table_collation();
	$db->query("CREATE TABLE `".TABLE_PREFIX."ms_acp` (
				`id`			int(11)			NOT NULL AUTO_INCREMENT,
				`title`			varchar(50)		NOT NULL,
				`link`			varchar(100)	NOT NULL,
	PRIMARY KEY (`id`) ) ENGINE=MyISAM {$col}");
}

function menu_suite_is_installed() {
	global $db;
	return $db->table_exists("ms_acp");
}

function menu_suite_activate() {}

function menu_suite_deactivate() {}

function menu_suite_uninstall()
{
	global $db;
	
	$db->drop_table("ms_acp");
}

function menu_suite_admin_config_menu($sub_menu)
{
	global $lang;

	$lang->load("menu_suite");

	$sub_menu[] = array("id" => "menu_suite", "title" => $lang->menu_suite, "link" => "index.php?module=config-menu_suite");

	return $sub_menu;
}

function menu_suite_admin_config_action_handler($actions)
{
	$actions['menu_suite'] = array(
		"active" => "menu_suite",
		"file" => "menu_suite.php"
	);

	return $actions;
}

function menu_suite_admin_config_permissions($admin_permissions)
{
	global $lang;

	$lang->load("menu_suite");

	$admin_permissions['menu_suite'] = $lang->menu_suite_permission;

	return $admin_permissions;
}
?>