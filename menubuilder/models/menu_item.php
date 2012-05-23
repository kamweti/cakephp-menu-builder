<?php
class MenuItem extends MenubuilderAppModel{
	var $name='MenuItem';
	var $belongsTo=array(
		'Menu'=>array()
	);
	
}
?>