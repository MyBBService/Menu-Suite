<?php
if(!defined("IN_MYBB"))
{
	header("HTTP/1.0 404 Not Found");
	exit;
}

$page->add_breadcrumb_item($lang->menu_suite, "index.php?module=config-menu_suite");

if($mybb->input['action'] == "") {
	$page->output_header($lang->menu_suite);
	generate_tabs("list");
}

$page->output_footer();

function generate_tabs($selected)
{
	global $lang, $page;

	$sub_tabs = array();
	$sub_tabs['toplink'] = array(
		'title' => $lang->menu_suite,
		'link' => "index.php?module=config-menu_suite",
		'description' => $lang->menu_suite
	);
	$sub_tabs['acp'] = array(
		'title' => $lang->menu_suite,
		'link' => "index.php?module=config-menu_suite&amp;action=acp",
		'description' => $lang->menu_suite
	);

	$page->output_nav_tabs($sub_tabs, $selected);
}
?>