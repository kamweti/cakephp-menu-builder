Menubuilder 0.2
================

Simple plugin to build and output custom menus in CakePHP

## Requirements
 CakePHP 1.3

 
## Installation
 
Copy folder menubuilder to app/plugins

If you have the ACL plugin installed build the new ACOs else skip this step

### Database Tables
	
Menubuilder depends on 2 tables 'menu' and 'menu_items',run the following mysql queries to add these tables to your database

		CREATE TABLE `menus` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(100) DEFAULT NULL,
		  `slug` varchar(100) DEFAULT NULL,
		  `status` int(11) DEFAULT '1' COMMENT '1=>active 0=>disabled',
		  `created` datetime DEFAULT NULL,
		  `deleted` datetime DEFAULT NULL,
		  PRIMARY KEY (`id`)
		);

		CREATE TABLE `menu_items` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `menu_id` int(11) DEFAULT NULL COMMENT 'id of menu',
		  `label` varchar(100) DEFAULT NULL,
		  `url` varchar(80) DEFAULT NULL,
		  `controller` varchar(100) DEFAULT NULL,
		  `action` varchar(100) DEFAULT NULL,
		  `children` text COMMENT 'menuitem id of menu',
		  `created` datetime DEFAULT NULL,
		  `modified` datetime DEFAULT NULL,
		  PRIMARY KEY (`id`)
		)

	
- 	Add the Menu helper in your controller file, preferably in app_controller.php

	var $helpers=array('Menubuilder.Menu');

-	Go to the plugin dashboard at <your site address>/admin/menubuilder/menus/ to build new menus

-	Tag your menus by a slug which you'll use while displaying
		
-  	Display the menu anywhere in your views by calling the helper method display and passing the 

		<?php $this->Menu->display('yourmenuslug'); ?>
	

-	Profit!
a