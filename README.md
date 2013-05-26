Menubuilder
================

Dead Simple Custom Menus in CakePHP

## Requirements

 CakePHP 2.2.x


## Installation

* Clone the repository to your Plugin folder `git clone git://github.com/kamweti/cakephp-menu-builder.git Menubuilder`

* cd to your application directory and generate required menu tables

  ``$ cake schema create --plugin Menubuilder``

* If you have the ACL plugin installed build the new ACOs else skip this step

* Add the Menu helper to your AppController.php file

		var $helpers = array('Menubuilder.Menu');

* All is well, visit <your site address>/menubuilder/menus/ to build new menus

* Tag your menus with a slug.

* Use the menu slug to dispaly your menu anywhere in your views files

		<?php $this->Menu->display('yourmenuslug'); ?>

*	Profit!
