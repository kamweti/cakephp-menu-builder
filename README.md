Menubuilder
================

Dead Simple Custom Menus in CakePHP

## Requirements

 CakePHP 1.3


## Installation

* Clone the repo to a folder app/plugins/

* Rename the plugin folder to 'menubuilder'

* If you have the ACL plugin installed build the new ACOs else skip this step

* Run the sql schema menubuilder/config/schema/schema.sql in your database to created required tables

* Add the Menu helper in your controller file, preferably in app_controller.php

		var $helpers=array('Menubuilder.Menu');

* Assuming you have an admin prefix set to 'admin', visit <your site address>/admin/menubuilder/menus/ to build new menus

* Tag your menus with a slug. You'll use while displaying

* Display your menu anywhere in your views as follows

		<?php $this->Menu->display('yourmenuslug'); ?>

-	Profit!
