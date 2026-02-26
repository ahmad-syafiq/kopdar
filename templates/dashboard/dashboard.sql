INSERT INTO `bbc_template` (`name`, `installed`, `syncron_to`, `last_copty_to`, `last_copy_from`)
VALUES
	('dashboard', NOW(), 0, 0, 0);

SET @template_id = LAST_INSERT_ID();



INSERT INTO `bbc_module` (`name`, `site_title`, `site_desc`, `site_keyword`, `created`, `protected`, `allow_group`, `order_func_pre`, `order_func_post`, `account_func_pre`, `account_func_post`, `search_func`, `is_config`, `active`)
VALUES
	('dashboard', NULL, NULL, NULL, NOW(), 0, ',all,', NULL, NULL, NULL, NULL, '', 1, 1);

SET @module_id = LAST_INSERT_ID();



INSERT INTO `bbc_block_theme` (`template_id`, `name`, `content`, `active`)
VALUES
	(@template_id, 'none', '[title][content]', 1);

SET @theme_id_none = LAST_INSERT_ID();

INSERT INTO `bbc_block_theme` (`template_id`, `name`, `content`, `active`)
VALUES
	(@template_id, 'dashboard_sidebar_header', '<ul class=\"sidebar-menu\">\r\n  <li class=\"header\">[title]</li>\r\n</ul>\r\n[content]', 1);

SET @theme_id_sidebar = LAST_INSERT_ID();



INSERT INTO `bbc_block` (`template_id`, `block_ref_id`, `position_id`, `show_title`, `link`, `cache`, `theme_id`, `group_ids`, `menu_ids`, `menu_ids_blocked`, `module_ids_allowed`, `module_ids_blocked`, `config`, `orderby`, `active`)
VALUES
	(@template_id, 10, 1, 1, '', 0, @theme_id_sidebar, ',all,', ',all,', '', '', '', '{\"template\":\"dashboard_sidebar\",\"cat_id\":\"4\",\"submenu\":\"bottom+right\"}', 2, 1);

SET @block_id_mainmenu = LAST_INSERT_ID();

INSERT INTO `bbc_block` (`template_id`, `block_ref_id`, `position_id`, `show_title`, `link`, `cache`, `theme_id`, `group_ids`, `menu_ids`, `menu_ids_blocked`, `module_ids_allowed`, `module_ids_blocked`, `config`, `orderby`, `active`)
VALUES
	(@template_id, 9, 7, 0, '', 0, @theme_id_none, ',all,', ',all,', '', '', '', '{\"template\":\"\",\"image\":\"images%2Fuploads%2Fdashboard.png\",\"size\":\"\",\"is_link\":\"1\",\"link\":\"dashboard%2F\",\"title\":\"\",\"attribute\":\"data-lg%3D%22%3Cb%3EAdmin%3C%2Fb%3ELTE%22+data-sm%3D%22%3Cb%3EA%3C%2Fb%3ELT%22\"}', 1, 1);

SET @block_id_logo = LAST_INSERT_ID();

INSERT INTO `bbc_block` (`template_id`, `block_ref_id`, `position_id`, `show_title`, `link`, `cache`, `theme_id`, `group_ids`, `menu_ids`, `menu_ids_blocked`, `module_ids_allowed`, `module_ids_blocked`, `config`, `orderby`, `active`)
VALUES
	(@template_id, 6, 8, 0, '', 0, @theme_id_none, ',all,', ',all,', '', '', '', '{\"template\":\"Navigation\",\"caption\":\"\"}', 1, 1);

SET @block_id_navigation = LAST_INSERT_ID();

INSERT INTO `bbc_block` (`template_id`, `block_ref_id`, `position_id`, `show_title`, `link`, `cache`, `theme_id`, `group_ids`, `menu_ids`, `menu_ids_blocked`, `module_ids_allowed`, `module_ids_blocked`, `config`, `orderby`, `active`)
VALUES
	(@template_id, 10, 5, 0, '', 0, @theme_id_none, ',logged,', ',all,', '', '', '', '{\"template\":\"dashboard_user\",\"cat_id\":\"3\",\"submenu\":\"bottom+right\"}', 1, 1);

SET @block_id_usermenu = LAST_INSERT_ID();



INSERT INTO `bbc_block_text` (`block_id`, `title`, `lang_id`)
VALUES
	(@block_id_mainmenu, 'MAIN MENU', 1),
	(@block_id_logo, 'Logo', 1),
	(@block_id_navigation, 'Navigation', 1),
	(@block_id_usermenu, 'User Menu', 1);



INSERT INTO `bbc_menu_cat` (`name`, `orderby`)
VALUES
	('dashboard', 4);

SET @menu_cat_id = LAST_INSERT_ID();



INSERT INTO `bbc_menu` (`par_id`, `module_id`, `seo`, `link`, `orderby`, `cat_id`, `protected`, `is_admin`, `is_shortcut`, `is_content`, `is_content_cat`, `content_cat_id`, `content_id`, `active`)
VALUES
	(0, @module_id, 'dashboard', 'index.php?mod=dashboard.main', 1, @menu_cat_id, 1, 0, 0, 0, 0, 0, 0, 1);

SET @menu_id = LAST_INSERT_ID();



INSERT INTO `bbc_menu_text` (`menu_id`, `title`, `lang_id`)
VALUES
	(@menu_id, 'Dashboard', 1);


