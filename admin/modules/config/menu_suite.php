<?php
if(!defined("IN_MYBB"))
{
	header("HTTP/1.0 404 Not Found");
	exit;
}

$page->add_breadcrumb_item($lang->menu_suite, "index.php?module=config-menu_suite");

if($mybb->input['action'] == "acp_do_add") {
	if(!verify_post_check($mybb->input['my_post_key']))
	{
		flash_message($lang->invalid_post_verify_key2, 'error');
		admin_redirect("index.php?module=config-menu_suite&action=acp_add");
	}

	if(!strlen(trim($mybb->input['title'])))
        $errors[] = $lang->missing_title;

	if(!strlen(trim($mybb->input['url'])))
        $errors[] = $lang->missing_url;

	if(!$errors) {
		$insert = array(
			"title"		=> $db->escape_string($mybb->input['title']),
			"link"		=> $db->escape_string($mybb->input['url']),
		);
		$db->insert_query("ms_acp", $insert);

		flash_message($lang->add_success, 'success');
		admin_redirect("index.php?module=config-menu_suite&action=acp");
	} else {
		$mybb->input['action'] = "acp_add";
	}
}
if($mybb->input['action'] == "acp_add") {
	$page->add_breadcrumb_item($lang->ms_add, "index.php?module=config-menu_suite&action=acp_add");
	$page->output_header($lang->ms_add);
	generate_tabs("acp");

	if($errors) {
		$page->output_inline_error($errors);
		$title = $mybb->input['title'];
		$url = $mybb->input['url'];
	} else {
		$title = "";
		$url = "";
	}

	$form = new Form("index.php?module=config-menu_suite&amp;action=acp_do_add", "post");
	$form_container = new FormContainer($lang->ms_add);

	$add_title = $form->generate_text_box("title", $title);
	$form_container->output_row($lang->ms_title." <em>*</em>", $lang->ms_acp_title_desc.$lang->ms_lang, $add_title);

	$add_url = $form->generate_text_box("url", $url);
	$form_container->output_row($lang->ms_url." <em>*</em>", $lang->ms_acp_url_desc, $add_url);

	$form_container->end();

	$buttons[] = $form->generate_submit_button($lang->ms_save);
	$buttons[] = $form->generate_reset_button($lang->reset);
	$form->output_submit_wrapper($buttons);
	$form->end();
}
if($mybb->input['action']=="acp_delete") {
	if(!strlen(trim($mybb->input['id'])))
	{
		flash_message($lang->menu_suite_no_id, 'error');
		admin_redirect("index.php?module=config-menu_suite&action=acp");
	}
	$id=(int)$mybb->input['id'];

	if($mybb->input['no'])
		admin_redirect("index.php?module=config-menu_suite&action=acp");
	else {
		if($mybb->request_method == "post") {
			$db->delete_query("ms_acp", "id='{$id}'");
			flash_message($lang->ms_deleted, 'success');
			admin_redirect("index.php?module=config-menu_suite&action=acp");
		} else
			$page->output_confirm_action("index.php?module=config-menu_suite&action=acp_delete&id={$id}", $lang->ms_delete_confirm);
	}
}
if($mybb->input['action'] == "acp_do_edit") {
	if(!strlen(trim($mybb->input['id'])))
	{
		flash_message($lang->menu_suite_no_id, 'error');
		admin_redirect("index.php?module=config-menu_suite&action=acp");
	}
	$id=(int)$mybb->input['id'];

    if(!verify_post_check($mybb->input['my_post_key']))
	{
		flash_message($lang->invalid_post_verify_key2, 'error');
		admin_redirect("index.php?module=config-menu_suite&action=acp");
	}

	if(!strlen(trim($mybb->input['title'])))
        $errors[] = $lang->missing_title;

	if(!strlen(trim($mybb->input['url'])))
        $errors[] = $lang->missing_url;

	if(!$errors) {
		$update = array(
			"title"		=> $db->escape_string($mybb->input['title']),
			"link"		=> $db->escape_string($mybb->input['url']),
		);
		$db->update_query("ms_acp", $update, "id={$id}");

		flash_message($lang->edit_success, 'success');
		admin_redirect("index.php?module=config-menu_suite&action=acp");
	} else {
		$mybb->input['action'] = "acp_edit";
	}
}
if($mybb->input['action'] == "acp_edit") {
	if(!strlen(trim($mybb->input['id'])))
	{
		flash_message($lang->menu_suite_no_id, 'error');
		admin_redirect("index.php?module=config-menu_suite&action=acp");
	}
	$id=(int)$mybb->input['id'];
	$query = $db->simple_select("ms_acp", "*", "id='{$id}'");
	if($db->num_rows($query) != 1)
	{
		flash_message($lang->menu_suite_wrong_id, 'error');
		admin_redirect("index.php?module=config-menu_suite&action=acp");
	}
	$point = $db->fetch_array($query);

	$page->add_breadcrumb_item($lang->ms_edit, "index.php?module=config-menu_suite&action=acp_edit&id={$id}");
	$page->output_header($lang->ms_edit);
	generate_tabs("acp");

	if($errors) {
		$page->output_inline_error($errors);
		$title = $mybb->input['title'];
		$url = $mybb->input['url'];
	} else {
		$title = $point['title'];
		$url = $point['link'];
	}

	$form = new Form("index.php?module=config-menu_suite&amp;action=acp_do_edit", "post");
	$form_container = new FormContainer($lang->ms_edit);

	$add_title = $form->generate_text_box("title", $title);
	$form_container->output_row($lang->ms_title." <em>*</em>", $lang->ms_acp_title_desc.$lang->ms_lang, $add_title);

	$add_url = $form->generate_text_box("url", $url);
	$form_container->output_row($lang->ms_url." <em>*</em>", $lang->ms_acp_url_desc, $add_url);

	$form_container->end();

	echo $form->generate_hidden_field("id", $id);
	$buttons[] = $form->generate_submit_button($lang->ms_save);
	$buttons[] = $form->generate_reset_button($lang->reset);
	$form->output_submit_wrapper($buttons);
	$form->end();
}
if($mybb->input['action'] == "acp") {
	$page->output_header($lang->ms_acp);
	generate_tabs("acp");

	$table = new Table;

	$table->construct_header($lang->ms_title);
	$table->construct_header($lang->ms_url);
	$table->construct_header($lang->controls, array("class" => "align_center", "colspan" => 2));

	$query = $db->simple_select("ms_acp", "*", "", array("order_by"=>"title"));
	if($db->num_rows($query) > 0)
	{
		while($link = $db->fetch_array($query))
		{
			$table->construct_cell($link['title']);
			$table->construct_cell($link['link']);
			$table->construct_cell("<a href=\"index.php?module=config-menu_suite&amp;action=acp_edit&amp;id={$link['id']}\">{$lang->edit}</a>", array('class' => 'align_center', 'width' => '10%'));
			$table->construct_cell("<a href=\"index.php?module=config-menu_suite&amp;action=acp_delete&amp;id={$link['id']}\">{$lang->delete}</a>", array('class' => 'align_center', 'width' => '10%'));
			$table->construct_row();
		}
	} else {
		$table->construct_cell($lang->no_links, array('class' => 'align_center', 'colspan' => 4));
		$table->construct_row();
	}
	$table->output($lang->ms_acp."<span style=\"float: right;\"><a href=\"index.php?module=config-menu_suite&amp;action=acp_add\">{$lang->ms_add}</a></span>");
}
if($mybb->input['action'] == "") {
	$page->output_header($lang->menu_suite);
	generate_tabs("toplinks");
}

$page->output_footer();

function generate_tabs($selected)
{
	global $lang, $page;

	$sub_tabs = array();
	$sub_tabs['toplinks'] = array(
		'title' => $lang->ms_toplinks,
		'link' => "index.php?module=config-menu_suite",
		'description' => $lang->ms_toplinks_desc
	);
	$sub_tabs['acp'] = array(
		'title' => $lang->ms_acp,
		'link' => "index.php?module=config-menu_suite&amp;action=acp",
		'description' => $lang->ms_acp_desc
	);

	$page->output_nav_tabs($sub_tabs, $selected);
}
?>