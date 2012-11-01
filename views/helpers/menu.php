<?php
App::import('Model', 'Menubuilder.Menu');
App::import('Sanitize');
class MenuHelper extends AppHelper{

	var $Menu;
	var $Acl;

	var $helpers = array('Html', 'Session');

	function __construct(){

		$this->Menu=new Menu();
		parent::__construct();
	}
	/*
	 * Outputs an html structure of a <ul> and nested <li> elements
	 * that make the current users navigation
	 *
	 * @params $slug lowercase menu slug
	 **/
	function display( $slug = null ){
		if ($slug == null) return '';
		$slug = Sanitize::clean($slug);

		$menuarray=$this->get($slug);

		if(empty($menuarray)){
			echo "menu with slug ' ".$slug." ' was not found!";
		}else{
			echo $this->make_nav_lists(json_encode($menuarray)); //convert menuItems array to json object
		}
	}

	/*
	 * Returns an associative array containing
	 * menu items,accepts a slug of the menu
	 *
	 * @params $slug lowercase menu slug
	 **/
	function get($slug = null){
		if ($slug == null) return '';

		$slug = Sanitize::clean($slug);

		$current_controller=$this->params['controller'];
		$current_action=$this->params['action'];


		//fetch the menu contains: menu,menuitems
		$menu=$this->Menu->find('first',array('conditions'=>'Menu.slug="'.$slug.'"','contain'=>array('MenuItem')));

		$menuitems=$menu['MenuItem'];

		return $menuitems;
	}


	/**
	 * Make Navigation list elements
	 *
	 * @params $menuitems array() an array pf menu items
	 *
	 * generate list elements,recurse if menu has a second level
	 */
	function make_nav_lists($menuobj){
		$menuitems=json_decode($menuobj); //decode json string of children



		$navlists="<ul>";

		$user=$this->Session->read('Auth.User.username'); //get current user

		foreach($menuitems as $menu){

			$navlists.="<li ".$this->is_current_controller($menu->controller).">";
			$navlists.="<a href='".$menu->url."'><b ".$this->is_current_action($menu->controller,$menu->action).">".$menu->label."</b></a>";

			//if menu element has a children note: $menu->children is stored as a json string
			if(isset($menu->children) && $menu->children !='' ){

				$navlists.="<ul>";
				$navlists.=$this->make_nav_lists($menu->children);
				$navlists.="</ul>";
			}
			$navlists.="</li>";
		}

		$navlists .= '</ul>';

		return $navlists;
	}


	/**
	 * Is Current Controller
	 *
	 * @params $urlcontroller
	 * checks if controller for the current link matches current controller,
	 * then add class current to highlight it
	 */
	function is_current_controller($urlcontroller=""){
		if($this->params['controller']==$urlcontroller){
			return "class='current'";
		}else{
			return "";
		}
	}


	/**
	 * Is Current Action
	 *
	 * @params $urlcontroller
	 * checks if controller for the current link matches current controller,
	 * then add class current to highlight it
	 */
	function is_current_action($urlcontroller="",$urlaction=""){
		if($this->params['controller']==$urlcontroller && $this->params['action']==$urlaction){
			return "class='active'";
		}else{
			return "";
		}
	}



}


?>
