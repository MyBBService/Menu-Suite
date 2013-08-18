<?php
if(!defined("IN_MYBB")) {
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

global $cache;
if(!isset($pluginlist))
    $pluginlist = $cache->read("plugins");

//ACP Hooks
if(is_array($pluginlist['active']) && in_array("mybbservice", $pluginlist['active'])) {
	$plugins->add_hook("mybbservice_actions", "menu_suite_mybbservice_actions");
	$plugins->add_hook("mybbservice_permission", "menu_suite_admin_config_permissions");
} else {
	$plugins->add_hook("admin_config_menu", "menu_suite_admin_config_menu");
	$plugins->add_hook("admin_config_action_handler", "menu_suite_admin_config_action_handler");
	$plugins->add_hook("admin_config_permissions", "menu_suite_admin_config_permissions");
}

//Hook für Custom Message
$plugins->add_hook("admin_config_plugins_activate_commit", "ms_activated");

//Schnellzugriff Funktion
$plugins->add_hook("admin_home_menu_quick_access", "ms_quick_access");

//Frontend Funktion
$plugins->add_hook("global_start", "ms_frontend");

//Import Funktion
$plugins->add_hook("admin_style_themes_import_commit", "ms_import");

function menu_suite_info()
{
	return array(
		"name"			=> "Men&uuml; Suite",
		"description"	=> "Mit diesem Plugin kannst du deine Men&uuml;s verwalten",
		"website"		=> "http://mybbservice.de",
		"author"		=> "MyBBService",
		"authorsite"	=> "http://mybbservice.de",
		"version"		=> "1.0.1",
		"guid"			=> "",
		"compatibility" => "16*",
		"dlcid"			=> "16"
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
				`sort`			int(11)			NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`) ) ENGINE=MyISAM {$col}");

	$db->query('INSERT INTO '.TABLE_PREFIX.'ms_acp
			(title, link, sort)
		VALUES
			("$lang->add_new_forum",	"index.php?module=forum-management&action=add",	1),
			("$lang->search_for_users",	"index.php?module=user-users&action=search",	2),
			("$lang->themes",			"index.php?module=style-themes",				3),
			("$lang->templates",		"index.php?module=style-templates",				4),
			("$lang->plugins",			"index.php?module=config-plugins",				5),
			("$lang->database_backups",	"index.php?module=tools-backupdb",				6)
		');
	
	$db->add_column("templatesets", "ms_style", "varchar(200) NOT NULL DEFAULT ''");
	
	$db->query("CREATE TABLE `".TABLE_PREFIX."ms_menu` (
				`id`			int(11)			NOT NULL AUTO_INCREMENT,
				`name`			varchar(50)		NOT NULL,
				`link`			varchar(100)	NOT NULL,
				`img`			varchar(100)	NOT NULL DEFAULT '',
				`css`			varchar(50)		NOT NULL DEFAULT '',
				`sid`			varchar(50)		NOT NULL DEFAULT '',
				`gids`			varchar(50)		NOT NULL DEFAULT '',
				`sort`			int(11)			NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`) ) ENGINE=MyISAM {$col}");

}

function menu_suite_is_installed() {
	global $db;
	return $db->table_exists("ms_acp");
}

function menu_suite_activate() {
	global $db, $debug;

	$fixed = array(-2);

	//First fix all Templates that need to be (global, already changed)
	$query = $db->simple_select("templates", "tid, sid, template", "title = 'header' AND sid > -2");
	while($template = $db->fetch_array($query)) {
		$debug[$template['sid']]['success'] = fix_template($template['sid'], $template, $debug);
		$fixed[] = $template['sid'];
	}
	
	//Now add new Templates if needed
	$query = $db->simple_select("templates", "tid, template", "title = 'header' AND sid='-2'");
	$master_template = $db->fetch_array($query);
	$return = fix_raw_template($master_template['template'], $ldebug, true);
	if($return['success'] && $return['template'] != $master_template['template']) {
		$query = $db->simple_select("templatesets", "sid", "sid NOT IN (".implode(',', $fixed).")");
		while($template = $db->fetch_array($query)) {
			$insert = array(
				"title" => "header",
				"template" => $db->escape_string($return['template']),
				"sid" => $template['sid'],
				"version" => $mybb->version_code,
				"status" => '',
				"dateline" => TIME_NOW
			);
			$db->insert_query("templates", $insert);
			
			$db->update_query("templatesets", array("ms_style" => $db->escape_string($ldebug['style'])), "sid='{$template['sid']}'");
			add_menu_points($template['sid'], $ldebug['menu']);

			$debug[$template['sid']] = $ldebug;
			$debug[$template['sid']]['success'] = true;
			$debug[$template['sid']]['added'] = true;
		}
	}
}

function ms_activated() {
	global $codename, $debug, $message, $lang;

	if(!$lang->menu_suite)
	    $lang->load("menu_suite");

	if($codename == "menu_suite") {
		$success = 0; $failed = 0;

		foreach($debug as $debug_menu) {
			if($debug_menu['success'])
			    $success++;
			else
				$failed++;
		}
		
		if($success > 0) {
			$message .= "<br />".$lang->sprintf($lang->ms_activated_success, $success);
		}

		if($failed > 0) {
			$message .= "<br />".$lang->sprintf($lang->ms_activated_failed, $failed);
		}
	}
}

function tprint(&$item, $key) {
	if(!is_array($item)) {
		$item = htmlentities(trim($item));
	} else {
		array_walk($item, "tprint");
	}
}

function menu_suite_deactivate() {
	global $db;

	$query = $db->simple_select("templates", "tid, sid, template", "title = 'header'");
	while($template = $db->fetch_array($query)) {
		$menu_suite = generate_menu($template['sid'], "", false, false);
		$new_template = str_replace('{$menu_suite}', $menu_suite, $template['template']);
		$db->update_query("templatesets", array("ms_style" => ""), "sid='{$template['sid']}'");
		$db->update_query("templates", array("template" => $db->escape_string($new_template)), "tid='{$template['tid']}'");
	}
}

function menu_suite_uninstall()
{
	global $db;
	
	$db->drop_table("ms_acp");
	$db->drop_column("templatesets", "ms_style");
	$db->drop_table("ms_menu");
}

function menu_suite_mybbservice_actions($actions)
{
	global $page, $lang, $info;
	$lang->load("menu_suite");

	$actions['menu_suite'] = array(
		"active" => "menu_suite",
		"file" => "../config/menu_suite.php"
	);

	$sub_menu = array();
	$sub_menu['10'] = array("id" => "menu_suite", "title" => $lang->menu_suite, "link" => "index.php?module=mybbservice-menu_suite");
	$sidebar = new SidebarItem($lang->menu_suite);
	$sidebar->add_menu_items($sub_menu, $actions[$info]['active']);

	$page->sidebar .= $sidebar->get_markup();

	return $actions;
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

function get_title($title)
{
	$start = 0;
	if(substr($title, 0, 1) == "{")
	    $start = 1;

	if(strpos($title, "$") == $start && substr($title, $start, 7) == '$lang->') {
	    //Sprachvariable
		global $lang;
		eval ("\$title = \"$title\";");
	}
	return $title;
}

function ms_quick_access($menu)
{
	global $db;
	$menu = array();
	
	$query = $db->simple_select("ms_acp", "*", "", array("order_by" => "sort"));
	while($link = $db->fetch_array($query)) {
		$menu[] = array("title" => get_title($link['title']), "link" => $link['link']);
	}
	return $menu;
}

function ms_frontend()
{
	global $mybb, $db, $current_page, $menu_suite, $theme;

	$loadstyle = '';
	$load_from_forum = 0;
	$style = array();

	if(isset($mybb->user['style']) && intval($mybb->user['style']) != 0)
	{
		$loadstyle = "tid='".$mybb->user['style']."'";
	}

	$valid = array(
		"showthread.php", 
		"forumdisplay.php",
		"newthread.php",
		"newreply.php",
		"ratethread.php",
		"editpost.php",
		"polls.php",
		"sendthread.php",
		"printthread.php",
		"moderation.php"
	);

	if(in_array($current_page, $valid))
	{
		cache_forums();

		if($mybb->input['pid'])
		{
			$query = $db->simple_select("posts", "fid", "pid = '".intval($mybb->input['pid'])."'", array("limit" => 1));
			$fid = $db->fetch_field($query, "fid");

			if($fid)
			{
				$style = $forum_cache[$fid];
				$load_from_forum = 1;
			}
		}
		else if($mybb->input['tid'])
		{
			$query = $db->simple_select("threads", "fid", "tid = '".intval($mybb->input['tid'])."'", array("limit" => 1));
			$fid = $db->fetch_field($query, "fid");

			if($fid)
			{
				$style = $forum_cache[$fid];
				$load_from_forum = 1;
			}
		}
		else if($mybb->input['fid'])
		{
			$style = $forum_cache[intval($mybb->input['fid'])];
			$load_from_forum = 1;
		}
	}
	unset($valid);

	if(isset($style['style']) && $style['style'] > 0)
	{
		if($style['overridestyle'] == 1 || !isset($mybb->user['style']))
		{
			$loadstyle = "tid='".intval($style['style'])."'";
		}
	}

	if(empty($loadstyle))
	{
		$loadstyle = "def='1'";
	}

	$query = $db->simple_select("themes", "properties", $loadstyle, array('limit' => 1));
	$theme = unserialize($db->fetch_field($query, "properties"));

	if(!$theme)
	{
		$query = $db->simple_select("themes", "properties", "", array("order_by" => "tid", "limit" => 1));
		$theme = unserialize($db->fetch_field($query, "properties"));
	}
	
	$menu_suite = generate_menu($theme['templateset']);
	unset($theme);
}

function ms_import()
{
	global $theme_id, $db, $lang;

	$query = $db->simple_select("themes", "properties", "tid='{$theme_id}'");
	$theme = unserialize($db->fetch_field($query, "properties"));

	$success = fix_template($theme['templateset']);
	
	log_admin_action($theme_id);

	if($success)
		flash_message($lang->success_imported_theme."<br />".$lang->ms_import_success, 'success');
	else
		flash_message($lang->success_imported_theme."<br />".$lang->ms_import_failed, 'success');
	
	admin_redirect("index.php?module=style-themes&action=edit&tid=".$theme_id);
}

function fix_template($sid, $template = array(), &$debug = array()) {
	global $db;
	
	if(empty($template)) {
		$query = $db->simple_select("templates", "tid, sid, template", "title = 'header' AND sid = '".$sid."'");
		$template = $db->fetch_array($query);
	}
	
	$return = fix_raw_template($template['template'], $debug[$sid]);
	
	if(!$return['success'])
	    return false;
	
	//echo htmlentities($debug[$sid]['template']); exit();
	
	//Now insert everything...
	//First the style
	$db->update_query("templatesets", array("ms_style" => $db->escape_string($debug[$sid]['style'])), "sid='{$sid}'");
	//Then update the template
	$db->update_query("templates", array("template" => $db->escape_string($debug[$sid]['template'])), "sid='{$sid}' AND title='header'");
	//Add the menu points to our very nice database
	add_menu_points($sid, $debug[$sid]['menu']);
	
	return true;
}

function fix_raw_template($template, &$debug=array()) {
	//Let's search our Menu
	$debug['menu_type'] = "class";
	$start = strpos($template, "<div class=\"menu\">");
	$end = strpos($template, "</div>", $start);
	if($start === false || $end === false) {
		//Probably we don't have a class but an id?
		$debug['menu_type'] = "id";
		$start = strpos($template, "<div id=\"menu\">");
		$end = strpos($template, "</div>", $start);
		if($start === false || $end === false) {
			$debug['status'] = "Menu not found";
			return array("success"=>"false");
		}
	}

	$menu = substr($template, $start, $end-$start);
	if($menu == "") {
		$debug['status'] = "Menu empty";
		return array("success"=>"false");
	}

	$menus = explode("\n", $menu);
	$debug['menu'] = array();
	foreach($menus as $menup) {
		//Let's do the hard work - fetching the style of the menu
		preg_match('#href="(.*?)"#i', $menup, $test);
		$info['link'] = $test[1];
		$style = preg_replace("#href=\"(.*?)\"#i", "href={link}", $menup);
		preg_match("#src=\"(.*?)\"#i", $style, $test);
		$info['img'] = $test[1];
		$style = preg_replace("#src=\"(.*?)\"#i", "src={img}", $style);
		preg_match("#class=\"(.*?)\"#i", $style, $test);
		$info['css'] = $test[1];
		$style = preg_replace("#class=\"(.*?)\"#i", "class={css}", $style);
		$info['name'] = trim(strip_tags($menup));
		$style = str_replace($info['name'], "{name}", $style);

		if(!empty($info) && $info['name'] != "" && $info['link'] != "")
			$debug['menu'][] = $info;

		if(isset($styles[$style]))
		    $styles[$style]++;
		else
			$styles[$style] = 1;

	}

	//echo "<pre>"; var_dump($debug['menu']); echo "</pre>"	;
	if(empty($debug['menu'])) {
		$debug['status'] = "Menu not fetched";
		return array("success"=>"false");
	}

	//Let's select which style we need
	natsort($styles);
	$styles = array_reverse($styles);

	$styles = array_keys($styles);
	$style = $styles[0];

	$debug['style'] = $style;

	//Generate the whole menu
	//Generate first menu point
	$first = generate_menu_point(0, $debug['menu'][0], $debug['style'], false);
	$first = strpos($template, $first);

	$menup = end($debug['menu']);
	$last = generate_menu_point(0, $menup, $debug['style'], false);
	$last = strpos($template, $last) + strlen($last);

	if($first === false || $last === false) {
		$debug['status'] = "Menu style not right";
		return array("success"=>"false");
	}

	$start = substr($template, 0, $first);
	$end = substr($template, $last);

	$new_template = $start."{\$menu_suite}".$end;

	$debug['template'] = $new_template;	

	return array("success"=>"true", "template" => $new_template);
}

function add_menu_points($sid, $menu) {
	global $db, $cache;
	
	$groups = $cache->read("usergroups");
	foreach($groups as $group) {
		$gids[] = $group['gid'];
	}
	$max_groups = implode(",", $gids);
	
	foreach($menu as $menup) {
		//First check whether we have this point already
		$whether = array(
			"link='".$db->escape_string($menup['link'])."'",
			"name='".$db->escape_string($menup['name'])."'",
			"img='".$db->escape_string($menup['img'])."'",
			"css='".$db->escape_string($menup['css'])."'"
		);
		$whether = implode(" AND ", $whether);
		$whether .= " AND (gids='' OR gids='{$max_groups}')";
		
		$query = $db->simple_select("ms_menu", "id, sid", $whether);
		if($db->num_rows($query) == 1) {
			$smenu = $db->fetch_array($query);
			$smenu['sid'] .= ",{$sid}";
			$db->update_query("ms_menu", array("sid" => $smenu['sid']), "id='{$smenu['id']}'");
		} else {
			$menu = array(
				"sid" => (int)$sid,
				"link" => $db->escape_string($menup['link']),
				"name" => $db->escape_string($menup['name']),
				"img" => $db->escape_string($menup['img']),
				"css" => $db->escape_string($menup['css'])
			);
			$db->insert_query("ms_menu", $menu);
		}
	}
}

function generate_menu($sid, $style = "", $use_groups = true, $eval = true) {
	global $db, $mybb;
	
	if($style == "") {
		$query = $db->simple_select("templatesets", "ms_style", "sid='{$sid}'");
		$style = $db->fetch_field($query, "ms_style");
	}

	$menu = "";
	$query = $db->simple_select("ms_menu", "*", "", array("order_by"=>"sort"));
	while($menus = $db->fetch_array($query)) {
		//First check if it's in our SID
		if($menus['sid'] != "" && !in_array($sid, explode(",",$menus['sid'])))
		    continue;
		
		//Let's check the group
		if($use_groups && $menus['gids'] != "") {
			$groups = explode(",", $menus['gids']);
		
		    $memberships = explode(',', $mybb->user['additionalgroups']);
		    $memberships[] = $mybb->user['usergroup'];
		
			if(sizeof(array_intersect($groups, $memberships)) == 0)
				continue;
		}
		
		//Ok let's add this Menu Point
		
		$menu .= "\n".generate_menu_point($sid, $menus, $style, $eval);
	}
	
	return $menu;
}

function generate_menu_point($sid, $menu, $style = "", $eval = true) {
	global $db, $mybb, $lang, $theme;
	
	if($style == "") {
		$query = $db->simple_select("templatesets", "ms_style", "sid='{$sid}'");
		$style = $db->fetch_field($query, "ms_style");
	}
		
	if($eval)
	    $menu['name'] = get_title($menu['name']);
	
	$point = str_replace("{link}", "\"{$menu['link']}\"", $style);
	$point = str_replace("{img}", "\"{$menu['img']}\"", $point);
	$point = str_replace("{css}", "\"{$menu['css']}\"", $point);
	$point = str_replace("{name}", $menu['name'], $point);
	
	if($eval) {
		$point = str_replace("\\'", "'", addslashes($point));
		eval ("\$point = \"$point\";");
	}

	return $point;
}

?>