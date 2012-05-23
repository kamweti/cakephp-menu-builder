<?php
class Menu extends MenubuilderAppModel{
	var $name='Menu';
	var $recursive=4;
	var $actsAs=array('Containable');
	
	var $hasMany=array(
		'MenuItem'=>array(
			'foreignKey' => 'menu_id',
		) 
	);
}

?>