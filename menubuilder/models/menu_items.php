<?php
class MenuItem extends MenuBuilderAppModel{
	var $name='MenuItem';
	var $belongsTo=array(
		'Menu'=>array()
	);
	
}
?>