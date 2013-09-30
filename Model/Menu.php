<?php
class Menu extends MenubuilderAppModel{
  public $recursive = 1;

	public $hasMany = array('Menubuilder.Menuitem');

  public $validate = array(
    'name' => array(
      array(
        'rule'    => 'notEmpty',
        'message' => 'Menu name cannot be blank'
      ),
      array(
        'rule'    => '/^[a-z0-9\s]+$/i',
        'message' => 'Menu name must contain alphabets and numbers only'
      )
    ),
    'slug' => array(
      array(
        'rule'    => 'notEmpty',
        'message' => 'Menu slug cannot be blank'
      ),
      array(
        'rule'    => '/^[a-z0-9_-]+$/i',
        'message' => 'Menu slug can only contain letters numbers or an underscore or hyphen'
      ),
      array(
        'rule'    => 'isUnique',
        'message' => 'This menu slug is already taken'
      )
    )
  );
}