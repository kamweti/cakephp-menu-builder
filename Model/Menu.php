<?php

class Menu extends MenubuilderAppModel{
  public $recursive = 1;

	public $hasMany = array('Menubuilder.Menuitem');


}

?>
