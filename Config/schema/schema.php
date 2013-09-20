<?php
class MenubuilderSchema extends CakeSchema {

	public function before($event = array()) {
		return true;
	}

	public $menuitems = array(
		'id'              => array('type' => 'integer',  'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'menu_id'         => array('type' => 'integer',  'null' => true, 'default' => null, 'comment' => 'id of menu'),
		'label'           => array('type' => 'string',   'null' => true, 'default' => null, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'url'             => array('type' => 'string',   'null' => true, 'default' => null, 'length' => 80, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'nested'          => array('type' => 'integer',  'null' => true, 'default' => '0'),
		'parent_id'       => array('type' => 'integer',  'null' => true, 'default' => null),
		'created'         => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes'         => array(
			'PRIMARY'         => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $menus = array(
		'id'              => array('type' => 'integer',  'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'name'            => array('type' => 'string',   'null' => true, 'default' => null, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'slug'            => array('type' => 'string',   'null' => true, 'default' => null, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'status'          => array('type' => 'integer',  'null' => true, 'default' => '1', 'comment' => '1=>active 0=>disabled'),
		'created'         => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes'         => array(
			'PRIMARY'         => array('column' => 'id', 'unique' => 1)
			),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);
}