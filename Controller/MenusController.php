<?php
/**
 *
 * @author   Muriuki <kamweti.muriuki@squaddigital.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link
 */


App::import('Config','Menubuilder.MenuSettings');
App::uses('Folder', 'Utility');
App::uses('Sanitize', 'Utility');

class MenusController extends MenubuilderAppController{

	var $components = array(
		'Acl',
		'Auth',
		'Session'
	);

	var $uses = array(
		'Menubuilder.Menu',
		'Menubuilder.Menuitem',
	);

	var $menuconfig = array(
		'defaults' => array(
			'include_plugin_controllers' => false
		)
	);


	function beforeFilter(){
	  parent :: beforeFilter();
		$this->Auth->allow();
	}


	function index(){

		//fetch all menus,contains menu and Menuitem models
		$allmenus=$this->Menu->find('all', array('order'=>'Menu.id desc'));

		$menulist=array(); //array to return containing menu id,name

		foreach($allmenus as $key=>$menu){
			$menulist[$key]['id']=$menu['Menu']['id']; //menu id
			$menulist[$key]['name']=$menu['Menu']['name']; //menu name
			$menulist[$key]['slug']=$menu['Menu']['slug']; //menu name
		}

		$this->set('menus', $menulist);
	}

	function add(){

		//fetch all app controllers
		$appcontrollers=$this->get_all_app_controllers();

		//fetch app controller actions
		foreach($appcontrollers as $key=>$controller){
			$controller_class_name = $controller['name'] . 'Controller';
			$appcontrollers[$key]['actions']=$this->get_controller_actions($controller_class_name);
		}


		if( $this->menuconfig['defaults']['include_plugin_controllers'] ){
			//fetch all plugin controllers

			$plugincontrollers=$this->get_all_plugins_controllers();
			//fetch plugin controller actions
			foreach($plugincontrollers as $key=>$controller){
				$controller_class_name = $controller['name'] . 'Controller';
				$plugincontrollers[$key]['actions']=$this->get_plugin_controllers_actions($controller);
			}
			$appcontrollers = array_merge($plugincontrollers,$appcontrollers);  //merge all controllers to one array and return

		}

		$this->set('controllers',$appcontrollers); //return only application controllers
	}

	/*
	 * discard the menu
	 * note: not actual drop of field but setting status to 0,
	 * @params: menu id
	 * */
	function edit( $menuid = null ){

    if ( is_null($menuid) || is_nan($menuid)) {
      $this->Session->setFlash ( __ ( 'Invalid menu id', true ), 'messages/error');
      $this->redirect ( array ('controller' => 'menus', 'action' => 'index', 'plugin' => 'menubuilder' ));
    }

		//fetch the menu contains: menu, Menuitems
		$menu = $this->Menu->find(
			'first',
			array(
				'conditions'	=> array(
					'Menu.id' => $menuid
				)
			)
		);

		$this->set('menu',$menu);

		//fetch all app controllers
		$appcontrollers=$this->get_all_app_controllers();
		foreach($appcontrollers as $key=>$controller){
			$controller_class_name = $controller['name'] . 'Controller';
			$appcontrollers[$key]['actions']=$this->get_controller_actions($controller_class_name);
		}

		if( $this->menuconfig['defaults']['include_plugin_controllers'] ){
			//fetch all plugin controllers
			$plugincontrollers=$this->get_all_plugins_controllers();
			foreach($plugincontrollers as $key=>$controller){
				$controller_class_name = $controller['name'] . 'Controller';
				$plugincontrollers[$key]['actions']=$this->get_plugin_controllers_actions($controller);
			}
			$appcontrollers = array_merge($plugincontrollers,$appcontrollers);
		}

		$this->set('controllers',$appcontrollers);

	}


/**
 * delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Menu->id = $id;
		if (!$this->Menu->exists()) {
			throw new NotFoundException(__('Invalid menu'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Menu->delete()) {
			$this->Session->setFlash(__('Menu deleted'), 'messages/success' );
		} else {
			$this->Session->setFlash(__('Menu was not deleted'), 'messages/error');
		}

		$this->redirect(array('action' => 'index'));
	}

	/*
	 * shows a preview of the menu,
	 * @params: id the menu id
	 * */
	function preview($menuid=null){


	}




	/*
	 * fetch all controllers in /app/controllers
	 * @params:
	 * */
	function get_all_app_controllers(){
		$controllers = array();
		$folder = & new Folder();

		$didCD = $folder->cd(APP . 'Controller');

		if(!empty($didCD)){
		    $files = $folder->findRecursive('.*Controller\.php');

		    foreach($files as $fileName)
			{
				$file = basename($fileName);

				// Get the controller name
				$controller_class_name =substr($file, 0, strlen($file) - strlen('Controller.php'));

				if (!App::import('Controller', $controller_class_name))
				{
					debug('Error importing ' . $controller_class_name . ' from APP controllers');
				}
				else
				{
				    $controllers[] = array('file' => $fileName, 'name' => $controller_class_name);
				}
			}
		}

		sort($controllers);
		return $controllers;
	}

	/*
	 * fetch all controllers in /app/plugins
	 * @params:
	 * */
	function get_all_plugins_controllers($filter_default_controller = true){
		$plugin_paths = $this->get_all_plugins_paths();

		$plugins_controllers = array();
		$folder =new Folder();

		// Loop through the plugins
		foreach($plugin_paths as $plugin_path)
		{
			$didCD = $folder->cd($plugin_path . DS . 'controllers');

			if(!empty($didCD))
			{
				$files = $folder->findRecursive('.*_controller\.php');

				$plugin_name = substr($plugin_path, strrpos($plugin_path, DS) + 1);

				foreach($files as $fileName)
				{
					$file = basename($fileName);

					// Get the controller name
					$controller_class_name = Inflector::camelize(substr($file, 0, strlen($file) - strlen('_controller.php')));

					if(!$filter_default_controller || Inflector::humanize($plugin_name) != $controller_class_name)
					{
    					if (!preg_match('/^'. Inflector::humanize($plugin_name) . 'App/', $controller_class_name))
    					{
    						if (!App::import('Controller', $plugin_name . '.' . $controller_class_name))
    						{
    							debug('Error importing ' . $controller_class_name . ' for plugin ' . $plugin_name);
    						}
    						else
    						{
    						    $plugins_controllers[] = array('file' => $fileName, 'name' => Inflector::humanize($plugin_name) . "/" . $controller_class_name);
    						}
    					}
					}
				}
			}
		}

		sort($plugins_controllers);

		return $plugins_controllers;
	}


	public function get_all_plugins_paths(){
		$plugin_names = array();

		$folder =& new Folder();

		$folder->cd(APP . 'plugins');
		$app_plugins = $folder->read();
		foreach($app_plugins[0] as $plugin_name)
		{
			$plugin_names[] = APP . 'plugins' . DS . $plugin_name;
		}

		$folder->cd(ROOT . DS . 'plugins');
		$root_plugins = $folder->read();
		foreach($root_plugins[0] as $plugin_name)
		{
			$plugin_names[] = ROOT . DS . 'plugins' . DS . $plugin_name;
		}

		return $plugin_names;
	}


	public function get_all_plugins_names()
	{
		$plugin_names = array();

		$folder =& new Folder();

		$folder->cd(APP . 'plugins');
		$app_plugins = $folder->read();
		if(!empty($app_plugins))
		{
			$plugin_names = array_merge($plugin_names, $app_plugins[0]);
		}

		$folder->cd(ROOT . DS . 'plugins');
		$root_plugins = $folder->read();
		if(!empty($root_plugins))
		{
			$plugin_names = array_merge($plugin_names, $root_plugins[0]);
		}

		return $plugin_names;
	}


	/**
	 * Return the methods of a given class name.
	 * Depending on the $filter_base_methods parameter, it can return the parent methods.
	 *
	 * @param string $controller_class_name (eg: 'AcosController')
	 * @param boolean $filter_base_methods
	 */
	public function get_controller_actions($controller_classname, $filter_base_methods = true)
	{
	    $controller_classname = $this->get_controller_classname($controller_classname);
		$methods = get_class_methods($controller_classname);

		if(isset($methods) && !empty($methods))
		{
    		if($filter_base_methods)
    		{
    			$baseMethods = get_class_methods('Controller');

    			$ctrl_cleaned_methods = array();
    		    foreach($methods as $method)
    		    {
    		        if(!in_array($method, $baseMethods) && strpos($method, '_') !== 0)
    				{
    				    $ctrl_cleaned_methods[] = $method;
    				}
    		    }

    		    return $ctrl_cleaned_methods;
    		}
    		else
    		{
    			return $methods;
    		}
		}
		else
		{
		    return array();
		}
	}

	function get_plugin_controllers_actions($plugin_controller,$filter_default_controller = true)
	{

		$plugin_controllers_actions = array();

		$plugin_name     = $this->getPluginName($plugin_controller['name']);
		$controller_name = $this->getPluginControllerName($plugin_controller['name']);

		if(!$filter_default_controller || $plugin_name != $controller_name)
		{
			$controller_class_name = $controller_name . 'Controller';

			$ctrl_cleaned_methods = $this->get_controller_actions($controller_class_name);

			foreach($ctrl_cleaned_methods as $action)
			{
				$plugin_controllers_actions[] = $action;
			}
		}

		sort($plugin_controllers_actions);

		return $plugin_controllers_actions;
	}



	function get_controller_classname($controller_name){

	    if(strrpos($controller_name, 'Controller') !== strlen($controller_name) - strlen('Controller'))
	    {
	        /*
	         * If $controller does not already end with 'Controller'
	         */

    	    if(stripos($controller_name, '/') === false)
    	    {
    	        $controller_classname = $controller_name . 'Controller';
    	    }
    	    else
    	    {
    	        /*
    	         * Case of plugin controller
    	         */
    	        $controller_classname = substr($controller_name, strripos($controller_name, '/') + 1) . 'Controller';
    	    }

    	    return $controller_classname;
	    }
	    else
	    {
	        return $controller_name;
	    }
	}

	function getPluginName($ctrlName = null)
	{
		$arr = String::tokenize($ctrlName, '/');
		if (count($arr) == 2) {
			return $arr[0];
		} else {
			return false;
		}
	}

	function getPluginControllerName($ctrlName = null)
	{
		$arr = String::tokenize($ctrlName, '/');
		if (count($arr) == 2) {
			return $arr[1];
		} else {
			return false;
		}
	}


	/*
	 * save menu,triggered via ajax
	 * return success or error
	 * */
	function ajax_save(){
		$data = array();

		$slug = strtolower($this->request->data['slug']); //slug always in lowercase

		//build menu
		$data['Menu'] = array(
			'name' => $this->request->data['name'],
			'slug' => $slug
		);

		if(isset($this->request->data['menuid'])){
			//form in edit state read the menu data
			$this->Menu->read(null,$this->request->data['menuid']);
			$data['Menu']['id'] = $this->request->data['menuid'];

			//clear previous menu items before insert
			$this->Menuitem->deleteAll(array(
				"Menuitem.menu_id" => $this->request->data['menuid']
				),
				false
			); //do not cascade

		}


		//get routing prefixes
		$routing_prefixes = Configure::read('Routing.prefixes');

		//build menu items
		$Menuitems=array();

		if(!isset($this->request->data['menuitems']))  $this->request->data['menuitems'] = array();

		foreach( $this->request->data['menuitems'] as $key=>$menu){
			// match prefix and construct a prefixed url
			if( $i = strpos( $menu['action'], '_') ) {
				$prefix = substr($menu['action'], 0, $i );
				$action = substr( $menu['action'], $i + 1,  strlen($menu['action']) );
				if( in_array( $prefix, $routing_prefixes ) ) {
					$menu['url'] = $this->base .'/'. $prefix .'/'. $menu['controller'] .'/'. $action;
				}
			}

			$Menuitems[$key]=array(
				'label'=>$menu['label'],
				'url' => $menu['url'],
				'controller'=>$menu['controller'],
				'action'=>$menu['action']
			);

			//children
			if(isset($menu['children'])){
				$Menuitems[$key]['children']=json_encode($menu['children']);
			}
		}

		$data['Menuitem'] = $Menuitems;

		//do insert
		if( $this->Menu->saveAll($data, array('validate' => false)) ) {
			echo 'success';
			exit;
		} else {
			//debug($this->validationErrors); die();
		}


	}



}



