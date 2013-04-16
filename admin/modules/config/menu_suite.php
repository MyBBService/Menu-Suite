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
if($mybb->input['action'] == "acp_order") {
	foreach($mybb->input['disporder'] as $id => $sort) {
		$id = (int)$id; $sort = (int)$sort;
		$db->update_query("ms_acp", array("sort"=>$sort), "id='{$id}'");
	}

	flash_message($lang->ms_order_new, 'success');
	admin_redirect("index.php?module=config-menu_suite&action=acp");
}
if($mybb->input['action'] == "acp") {
	$page->output_header($lang->ms_acp);
	generate_tabs("acp");

	$form = new Form("index.php?module=config-menu_suite&amp;action=acp_order", "post");
	$form_container = new FormContainer($lang->ms_acp."<span style=\"float: right;\"><a href=\"index.php?module=config-menu_suite&amp;action=acp_add\">{$lang->ms_add}</a></span>");

	$form_container->output_row_header($lang->ms_title);
	$form_container->output_row_header($lang->ms_url);
	$form_container->output_row_header($lang->order);
	$form_container->output_row_header($lang->controls, array("class" => "align_center", "colspan" => 2));

	$query = $db->simple_select("ms_acp", "*", "", array("order_by"=>"sort"));
	if($db->num_rows($query) > 0)
	{
		while($link = $db->fetch_array($query))
		{
			$form_container->output_cell($link['title']);
			$form_container->output_cell($link['link']);
			$form_container->output_cell("<input type=\"text\" name=\"disporder[".$link['id']."]\" value=\"".$link['sort']."\" class=\"text_input align_center\" style=\"width: 80%; font-weight: bold;\" />", array('width' => '5%'));
			$form_container->output_cell("<a href=\"index.php?module=config-menu_suite&amp;action=acp_edit&amp;id={$link['id']}\">{$lang->edit}</a>", array('class' => 'align_center', 'width' => '10%'));
			$form_container->output_cell("<a href=\"index.php?module=config-menu_suite&amp;action=acp_delete&amp;id={$link['id']}\">{$lang->delete}</a>", array('class' => 'align_center', 'width' => '10%'));
			$form_container->construct_row();
		}
	} else {
		$form_container->output_cell($lang->no_links, array('class' => 'align_center', 'colspan' => 4));
		$form_container->construct_row();
	}
	$form_container->end();
	$buttons[] = $form->generate_submit_button($lang->ms_save);
	$buttons[] = $form->generate_reset_button($lang->reset);
	$form->output_submit_wrapper($buttons);
	$form->end();
}
if($mybb->input['action'] == "style_do_edit") {
	if(!strlen(trim($mybb->input['id'])))
	{
		flash_message($lang->menu_suite_no_id, 'error');
		admin_redirect("index.php?module=config-menu_suite&action=style");
	}
	$id=(int)$mybb->input['id'];

    if(!verify_post_check($mybb->input['my_post_key']))
	{
		flash_message($lang->invalid_post_verify_key2, 'error');
		admin_redirect("index.php?module=config-menu_suite&action=style");
	}

	if(!strlen(trim($mybb->input['style'])))
        $errors[] = $lang->missing_style;

	if(!$errors) {
		$update = array(
			"ms_style"		=> $db->escape_string($mybb->input['style']),
		);
		$db->update_query("templatesets", $update, "sid={$id}");

		flash_message($lang->edit_success, 'success');
		admin_redirect("index.php?module=config-menu_suite&action=style");
	} else {
		$mybb->input['action'] = "style_edit";
	}
}
if($mybb->input['action'] == "style_edit") {
	if(!strlen(trim($mybb->input['id'])))
	{
		flash_message($lang->menu_suite_no_id, 'error');
		admin_redirect("index.php?module=config-menu_suite&action=style");
	}
	$id=(int)$mybb->input['id'];
	$query = $db->simple_select("templatesets", "*", "sid='{$id}'");
	if($db->num_rows($query) != 1)
	{
		flash_message($lang->menu_suite_wrong_id, 'error');
		admin_redirect("index.php?module=config-menu_suite&action=style");
	}
	$theme = $db->fetch_array($query);

	$page->add_breadcrumb_item($lang->ms_edit_style.": ".$theme['title'], "index.php?module=config-menu_suite&action=style_edit&id={$id}");
	$page->output_header($lang->ms_edit_style);
	generate_tabs("styles");

	if($errors) {
		$page->output_inline_error($errors);
		$style = $mybb->input['style'];
	} else {
		$style = trim($theme['ms_style']);
	}

	$form = new Form("index.php?module=config-menu_suite&amp;action=style_do_edit", "post");
	$form_container = new FormContainer($lang->ms_edit_style);

	$add_style = $form->generate_text_box("style", $style);
	$form_container->output_row($lang->style." <em>*</em>", $lang->ms_style_desc, $add_style);

	$form_container->end();

	echo $form->generate_hidden_field("id", $id);
	$buttons[] = $form->generate_submit_button($lang->ms_save);
	$buttons[] = $form->generate_reset_button($lang->reset);
	$form->output_submit_wrapper($buttons);
	$form->end();
}
if($mybb->input['action'] == "style") {
	$page->output_header($lang->ms_styles);
	generate_tabs("styles");

	$table = new Table;

	$table->construct_header($lang->theme);
	$table->construct_header($lang->style);
	$table->construct_header($lang->controls, array('class' => 'align_center'));

	$query = $db->simple_select("templatesets", "*", "", array("order_by"=>"title"));
	if($db->num_rows($query) > 0)
	{
		while($theme = $db->fetch_array($query))
		{
			$table->construct_cell("<a href=\"index.php?module=style-templates&amp;sid={$theme['sid']}\">{$theme['title']}</a>");
			$table->construct_cell(htmlentities($theme['ms_style']));
			$table->construct_cell("<a href=\"index.php?module=config-menu_suite&amp;action=style_edit&amp;id={$theme['sid']}\">{$lang->edit}</a>", array('class' => 'align_center', 'width' => '10%'));
			$table->construct_row();
		}
	} else {
		$table->construct_cell($lang->error, array('class' => 'align_center', 'colspan' => 4));
		$table->construct_row();
	}
	$table->output($lang->ms_styles);
}
if($mybb->input['action'] == "do_add") {
	if(!verify_post_check($mybb->input['my_post_key']))
	{
		flash_message($lang->invalid_post_verify_key2, 'error');
		admin_redirect("index.php?module=config-menu_suite&action=add");
	}

	if(!strlen(trim($mybb->input['name'])))
        $errors[] = $lang->missing_title;

	if(!strlen(trim($mybb->input['link'])))
        $errors[] = $lang->missing_url;

	if(!$errors) {
		$insert = array(
			"name"		=> $db->escape_string($mybb->input['name']),
			"link"		=> $db->escape_string($mybb->input['link']),
			"img"		=> $db->escape_string($mybb->input['img']),
			"css"		=> $db->escape_string($mybb->input['css']),
			"sid"		=> $db->escape_string(implode(",", $mybb->input['sids'])),
			"gids"		=> $db->escape_string(implode(",", $mybb->input['gids'])),
		);
		$db->insert_query("ms_menu", $insert);

		flash_message($lang->add_success, 'success');
		admin_redirect("index.php?module=config-menu_suite");
	} else {
		$mybb->input['action'] = "add";
	}
}
if($mybb->input['action'] == "add") {
	$page->add_breadcrumb_item($lang->ms_add, "index.php?module=config-menu_suite&action=add");
	$page->output_header($lang->ms_add);
	generate_tabs("toplinks");

	if($errors) {
		$page->output_inline_error($errors);
		$name = $mybb->input['name'];
		$link = $mybb->input['link'];
		$img = $mybb->input['img'];
		$css = $mybb->input['css'];
		$sid = $mybb->input['sids'];
		$gid = $mybb->input['gids'];
	} else {
		$name = "";
		$link = "";
		$img = "";
		$css = "";
		$sid = array();
		$gid = array();
	}

	$form = new Form("index.php?module=config-menu_suite&amp;action=do_add", "post");
	$form_container = new FormContainer($lang->ms_add);

	$add_name = $form->generate_text_box("name", $name);
	$form_container->output_row($lang->ms_title." <em>*</em>", $lang->ms_title_desc, $add_name);

	$add_link = $form->generate_text_box("link", $link);
	$form_container->output_row($lang->ms_url." <em>*</em>", $lang->ms_url_desc, $add_link);

	$add_img = $form->generate_text_box("img", $img);
	$form_container->output_row($lang->ms_img, $lang->ms_img_desc, $add_img);

	$add_css = $form->generate_text_box("css", $css);
	$form_container->output_row($lang->ms_css, $lang->ms_css_desc, $add_css);

	$query = $db->simple_select("templatesets", "sid, title", "", array("order_by"=>"title"));
	$themes = array();
	while($theme = $db->fetch_array($query))
	    $themes[$theme['sid']] = $theme['title'];
	$add_sid = $form->generate_select_box("sids[]", $themes, $sid, array("multiple"=>true));
	$form_container->output_row($lang->ms_sid, $lang->ms_sid_desc, $add_sid);

	$add_gid = $form->generate_group_select("gids[]", $gid, array("multiple"=>true));
	$form_container->output_row($lang->ms_gid, $lang->ms_gid_desc, $add_gid);

	$form_container->end();

	$buttons[] = $form->generate_submit_button($lang->ms_save);
	$buttons[] = $form->generate_reset_button($lang->reset);
	$form->output_submit_wrapper($buttons);
	$form->end();
}
if($mybb->input['action']=="delete") {
	if(!strlen(trim($mybb->input['id'])))
	{
		flash_message($lang->menu_suite_no_id, 'error');
		admin_redirect("index.php?module=config-menu_suite");
	}
	$id=(int)$mybb->input['id'];

	if($mybb->input['no'])
		admin_redirect("index.php?module=config-menu_suite");
	else {
		if($mybb->request_method == "post") {
			$db->delete_query("ms_menu", "id='{$id}'");
			flash_message($lang->ms_deleted, 'success');
			admin_redirect("index.php?module=config-menu_suite");
		} else
			$page->output_confirm_action("index.php?module=config-menu_suite&action=delete&id={$id}", $lang->ms_delete_confirm);
	}
}
if($mybb->input['action'] == "do_edit") {
	if(!strlen(trim($mybb->input['id'])))
	{
		flash_message($lang->menu_suite_no_id, 'error');
		admin_redirect("index.php?module=config-menu_suite");
	}
	$id=(int)$mybb->input['id'];

    if(!verify_post_check($mybb->input['my_post_key']))
	{
		flash_message($lang->invalid_post_verify_key2, 'error');
		admin_redirect("index.php?module=config-menu_suite");
	}

	if(!strlen(trim($mybb->input['name'])))
        $errors[] = $lang->missing_title;

	if(!strlen(trim($mybb->input['link'])))
        $errors[] = $lang->missing_url;

	if(!$errors) {
		$update = array(
			"name"		=> $db->escape_string($mybb->input['name']),
			"link"		=> $db->escape_string($mybb->input['link']),
			"img"		=> $db->escape_string($mybb->input['img']),
			"css"		=> $db->escape_string($mybb->input['css']),
			"sid"		=> $db->escape_string(implode(",", $mybb->input['sids'])),
			"gids"		=> $db->escape_string(implode(",", $mybb->input['gids'])),
		);
		$db->update_query("ms_menu", $update, "id={$id}");

		flash_message($lang->edit_success, 'success');
		admin_redirect("index.php?module=config-menu_suite");
	} else {
		$mybb->input['action'] = "edit";
	}
}
if($mybb->input['action'] == "edit") {
	if(!strlen(trim($mybb->input['id'])))
	{
		flash_message($lang->menu_suite_no_id, 'error');
		admin_redirect("index.php?module=config-menu_suite");
	}
	$id=(int)$mybb->input['id'];
	$query = $db->simple_select("ms_menu", "*", "id='{$id}'");
	if($db->num_rows($query) != 1)
	{
		flash_message($lang->menu_suite_wrong_id, 'error');
		admin_redirect("index.php?module=config-menu_suite");
	}
	$point = $db->fetch_array($query);

	$page->add_breadcrumb_item($lang->ms_edit, "index.php?module=config-menu_suite&action=edit&id={$id}");
	$page->output_header($lang->ms_edit);
	generate_tabs("toplinks");

	if($errors) {
		$page->output_inline_error($errors);
		$name = $mybb->input['name'];
		$link = $mybb->input['link'];
		$img = $mybb->input['img'];
		$css = $mybb->input['css'];
		$sid = $mybb->input['sids'];
		$gid = $mybb->input['gids'];
	} else {
		$name = $point['name'];
		$link = $point['link'];
		$img = $point['img'];
		$css = $point['css'];
		$sid = explode(",", $point['sid']);
		$gid = explode(",", $point['gids']);
	}

	$form = new Form("index.php?module=config-menu_suite&amp;action=do_edit", "post");
	$form_container = new FormContainer($lang->ms_edit);

	$add_name = $form->generate_text_box("name", $name);
	$form_container->output_row($lang->ms_title." <em>*</em>", $lang->ms_title_desc, $add_name);

	$add_link = $form->generate_text_box("link", $link);
	$form_container->output_row($lang->ms_url." <em>*</em>", $lang->ms_url_desc, $add_link);

	$add_img = $form->generate_text_box("img", $img);
	$form_container->output_row($lang->ms_img, $lang->ms_img_desc, $add_img);

	$add_css = $form->generate_text_box("css", $css);
	$form_container->output_row($lang->ms_css, $lang->ms_css_desc, $add_css);

	$query = $db->simple_select("templatesets", "sid, title", "", array("order_by"=>"title"));
	$themes = array();
	while($theme = $db->fetch_array($query))
	    $themes[$theme['sid']] = $theme['title'];
	$add_sid = $form->generate_select_box("sids[]", $themes, $sid, array("multiple"=>true));
	$form_container->output_row($lang->ms_sid, $lang->ms_sid_desc, $add_sid);

	$add_gid = $form->generate_group_select("gids[]", $gid, array("multiple"=>true));
	$form_container->output_row($lang->ms_gid, $lang->ms_gid_desc, $add_gid);

	$form_container->end();

	echo $form->generate_hidden_field("id", $id);
	$buttons[] = $form->generate_submit_button($lang->ms_save);
	$buttons[] = $form->generate_reset_button($lang->reset);
	$form->output_submit_wrapper($buttons);
	$form->end();
}
if($mybb->input['action'] == "order") {
	foreach($mybb->input['disporder'] as $id => $sort) {
		$id = (int)$id; $sort = (int)$sort;
		$db->update_query("ms_menu", array("sort"=>$sort), "id='{$id}'");
	}

	flash_message($lang->ms_order_new, 'success');
	admin_redirect("index.php?module=config-menu_suite");
}
if($mybb->input['action'] == "") {
	$page->output_header($lang->menu_suite);
	generate_tabs("toplinks");

	$form = new Form("index.php?module=config-menu_suite&amp;action=order", "post");
	$form_container = new FormContainer($lang->menu_suite."<span style=\"float: right;\"><a href=\"index.php?module=config-menu_suite&amp;action=add\">{$lang->ms_add}</a></span>");

	$form_container->output_row_header($lang->ms_title);
	$form_container->output_row_header($lang->ms_url);
	$form_container->output_row_header($lang->order);
	$form_container->output_row_header($lang->controls, array("class" => "align_center", "colspan" => 2));

	$query = $db->simple_select("ms_menu", "*", "", array("order_by"=>"sort"));
	if($db->num_rows($query) > 0)
	{
		while($link = $db->fetch_array($query))
		{
			$form_container->output_cell($link['name']);
			$form_container->output_cell($link['link']);
			$form_container->output_cell("<input type=\"text\" name=\"disporder[".$link['id']."]\" value=\"".$link['sort']."\" class=\"text_input align_center\" style=\"width: 80%; font-weight: bold;\" />", array('width' => '5%'));
			$form_container->output_cell("<a href=\"index.php?module=config-menu_suite&amp;action=edit&amp;id={$link['id']}\">{$lang->edit}</a>", array('class' => 'align_center', 'width' => '10%'));
			$form_container->output_cell("<a href=\"index.php?module=config-menu_suite&amp;action=delete&amp;id={$link['id']}\">{$lang->delete}</a>", array('class' => 'align_center', 'width' => '10%'));
			$form_container->construct_row();
		}
	} else {
		$form_container->output_cell($lang->no_links, array('class' => 'align_center', 'colspan' => 4));
		$form_container->construct_row();
	}
	$form_container->end();
	$buttons[] = $form->generate_submit_button($lang->ms_save);
	$buttons[] = $form->generate_reset_button($lang->reset);
	$form->output_submit_wrapper($buttons);
	$form->end();
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
	$sub_tabs['styles'] = array(
		'title' => $lang->ms_styles,
		'link' => "index.php?module=config-menu_suite&amp;action=style",
		'description' => $lang->ms_styles_desc
	);
	$sub_tabs['acp'] = array(
		'title' => $lang->ms_acp,
		'link' => "index.php?module=config-menu_suite&amp;action=acp",
		'description' => $lang->ms_acp_desc
	);

	$page->output_nav_tabs($sub_tabs, $selected);
}
?>