Menubuilder
================

Simple custom menus in cakephp

## Requirements

  * PHP 5.3.1 >

  * CakePHP 2.2.x


## Installation

* Clone the repository to your Plugin folder `git clone git://github.com/kamweti/cakephp-menu-builder.git Menubuilder`

* Load the plugin in your `app/Config/bootstrap.php`

    ``CakePlugin::load( 'Menubuilder' );``

* This plugin depends on two tables, use the cake shell to import them

  ``$ cake schema create --plugin Menubuilder``

* If you have the ACL plugin installed build the new ACOs else skip this step

* Add the Menu helper to your AppController.php file

		var $helpers = array('Menubuilder.Menu');

* All is well, visit <your site address>/menubuilder/menus/ to build new menus

* Tag your menus with a slug.

* Use this slug to display your menu anywhere in your views

		<?php $this->Menu->display('yourmenuslug'); ?>

