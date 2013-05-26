<?php
App::import('Model', 'Menubuilder.Menu');
App::uses('Sanitize', 'Utility');

App::uses('AppHelper', 'View/Helper');

class MenuHelper extends AppHelper{

	var $Menu;
	var $Acl;

	var $helpers = array(
		'Html',
		'Session'
	);

	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
		$this->Menu = new Menu();
	}

	/*
	 * Outputs an html structure of a <ul> and nested <li> elements
	 * that make the current users navigation
	 *
	 * @params $slug lowercase menu slug
	 **/
	function display( $slug = null, $options = array() ){
		if ($slug == null) return '';
		$slug = Sanitize::clean($slug);

		$menuarray=$this->get($slug);

		if(empty($menuarray)){
			echo "menu with slug ' ".$slug." ' was not found!";
		}else{
			echo $this->make_nav_lists(json_encode($menuarray), $options ); //convert Menuitems array to json object
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


		//fetch the menu contains: menu,Menuitems
		$menu = $this->Menu->find('first',
			array(
				'conditions' => array(
					'Menu.slug' => $slug
				)
			)
		);
		if( isset($menu['Menuitem']) ) return $menu['Menuitem'];

	}


	/**
	 * Make Navigation list elements
	 *
	 * @params $Menuitems array() an array pf menu items
	 *
	 * generate list elements,recurse if menu has a second level
	 */	private function make_nav_lists($menuobj, $options = array()){
		$Menuitems=json_decode($menuobj); //decode json string of children

		if( array_key_exists('menu_class', $options ) ) {
			$navlists = '<ul class="'. $options['menu_class'] . '">';
		} else {
			$navlists="<ul>";
		}

		foreach($Menuitems as $menu){

			$navlists.='<li '.$this->is_current_controller($menu->controller).'>';
			$navlists.='<a href="'. $this->base. $menu->url.'" '. $this->is_current_action($menu->controller,$menu->action) .'><span>'.$menu->label.'</span></a>';

			//if menu element has a children note: $menu->children is stored as a json string
			if(isset($menu->children) && $menu->children !='' ){
				$navlists.=$this->make_nav_lists($menu->children);
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
		$urlcontroller = strtolower($urlcontroller);

		if( $this->request->params['controller'] == $urlcontroller ){
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
		$urlcontroller = strtolower($urlcontroller);
		$urlaction = strtolower($urlaction);
		if( $this->request->params['controller'] == $urlcontroller && $this->request->params['action']==$urlaction ){
			return "class='active'";
		}else{
			return "";
		}
	}



}


?>
