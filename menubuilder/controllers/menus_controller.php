<?php
/**
 *
 * @author   Muriuki <kamweti.muriuki@squaddigital.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     
 */

 App::import('Sanitize');
 
class MenusController extends MenuBuilderAppController{
	var $name="Menus";
	var $components = array('Acl', 'Auth', 'Session');

	
	function beforeFilter(){
	    parent :: beforeFilter();
		$this->Auth->allow('*');
	}
    

	function admin_index(){
		
		//fetch all menus,contains menu and menuitem models
		$allmenus=$this->Menu->find('all', array('order'=>'Menu.id desc'));
	
		$menulist=array(); //array to return containing menu id,name 

		foreach($allmenus as $key=>$menu){
			$menulist[$key]['id']=$menu['Menu']['id']; //menu id
			$menulist[$key]['name']=$menu['Menu']['name']; //menu name
			$menulist[$key]['slug']=$menu['Menu']['slug']; //menu name
		}
		
		$this->set('menus', $menulist);
	}
	
	function admin_new(){
		
		//fetch all app controllers
		$appcontrollers=$this->get_all_app_controllers();
		
		//fetch app controller actions
		foreach($appcontrollers as $key=>$controller){
			$controller_class_name = $controller['name'] . 'Controller';
			$appcontrollers[$key]['actions']=$this->get_controller_actions($controller_class_name);
		}


		if(Configure::read('exclude_plugin_controllers')==false){
			//fetch all plugin controllers
			
			$plugincontrollers=$this->get_all_plugins_controllers();
			//fetch plugin controller actions
			foreach($plugincontrollers as $key=>$controller){
				$controller_class_name = $controller['name'] . 'Controller';
				$plugincontrollers[$key]['actions']=$this->get_plugin_controllers_actions($controller);
			}	
			$this->set('controllers',array_merge($plugincontrollers,$appcontrollers));  //merge all controllers to one array and return
		
		}else{
			$this->set('controllers',$appcontrollers); //return only application controllers
		}
	}
	
	/*
	 * discard the menu 
	 * note: not actual drop of field but setting status to 0,
	 * @params: menu id
	 * */
	function admin_edit($menuid=null){
		$menuid=(int)$menuid;
		
        if (!$menuid || is_nan($menuid)) {
            $this->Session->setFlash ( __ ( 'Invalid menu id', true ), 'messages/error');
            $this->redirect (array ('action' => 'admin_index' ));
        }
		
		//fetch the menu contains: menu,menuitems
		$menu=$this->Menu->find('first',array('conditions'	=>'Menu.id='.$menuid));
		$this->set('menu',$menu);

		
		
		//fetch all app controllers
		$appcontrollers=$this->get_all_app_controllers();
		foreach($appcontrollers as $key=>$controller){
			$controller_class_name = $controller['name'] . 'Controller';
			$appcontrollers[$key]['actions']=$this->get_controller_actions($controller_class_name);
		}	
		
		if(Configure::read('exclude_plugin_controllers')==false){
			//fetch all plugin controllers
			$plugincontrollers=$this->get_all_plugins_controllers();
			foreach($plugincontrollers as $key=>$controller){
				$controller_class_name = $controller['name'] . 'Controller';
				$plugincontrollers[$key]['actions']=$this->get_plugin_controllers_actions($controller);
			}
			$this->set('controllers',array_merge($plugincontrollers,$appcontrollers));
		}else{
			$this->set('controllers',$appcontrollers); //return only application controllers
		}
	}
	

	/*
	 * discard the menu 
	 * note: not actual drop of field but setting status to 0,
	 * @params: menu id
	 * */
	function admin_delete($menuid=null){
		
	}
	
	/*
	 * shows a preview of the menu,
	 * @params: id the menu id
	 * */
	function admin_preview($menuid=null){

		
	}
	
	

	
	/*
	 * fetch all controllers in /app/controllers
	 * @params:
	 * */
	function get_all_app_controllers(){
		$controllers = array();
		$folder =& new Folder();
		
		$didCD = $folder->cd(APP . 'controllers');
		if(!empty($didCD)){
			
		    $files = $folder->findRecursive('.*_controller\.php');
		    
		    foreach($files as $fileName)
			{
				$file = basename($fileName);

				// Get the controller name
				$controller_class_name =substr($file, 0, strlen($file) - strlen('_controller.php'));
				
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
		global $_POST;
		
		//load models
		$this->loadModel('MenuItem');
		
		$post= Sanitize::clean($_POST, array('encode' => false));
		$slug=strtolower($post['slug']); //slug always in lowercase
		
		//build menu
		$this->data['Menu']=array('name'=>$post['name'],'slug'=>$slug);

		if(isset($post['menuid'])){
			//form in edit state read the menu data
			$this->Menu->read(null,$post['menuid']); 
			$this->data['Menu']['id']=$post['menuid'];
			
			//clear previous menu items before insert
			$this->MenuItem->deleteAll(array("MenuItem.menu_id"=>$post['menuid']),false); //do not cascade
		}
		
		
		//build menu items
		$menuitems=array();
		if(!isset($post['menuitems']))  $post['menuitems']=array();
		foreach($post['menuitems'] as $key=>$menu){
			$menuitems[$key]=array(
							'label'=>$menu['label'],
							'url'=>$menu['url'],
							'controller'=>$menu['controller'],
							'action'=>$menu['action']
						);
			//children
			if(isset($menu['children'])){
				$menuitems[$key]['children']=json_encode($menu['children']);
			}
		}
		
		$this->data['MenuItem']=$menuitems;

		

		//do insert
		$this->Menu->saveAll($this->data);

		echo 'success';
		exit;
	}

	/* 
	 * Returns an html structure of a <ul> and nested <li> elements
	 * that make the current users navigation
	 *  
	 * @params $slug lowercase menu slug
	 * 		   $current_controller string the current app controller for the request
	 * 		   $current_action string the current action for the request
	 **/
	function display($slug="",$current_controller="",$current_action=""){
		$slug=Sanitize::clean($slug);
		
		$current_controller=Sanitize::clean($current_controller);
		$current_action=Sanitize::clean($current_action);
		
		//set these variables to $this->params
		$this->params['current_controller']=$current_controller;
		$this->params['current_action']=$current_action; 
		
		
		$this->autoRender=false; //no view rendered for this action
		
		//fetch the menu contains: menu,menuitems
		$menu=$this->Menu->find('first',array('conditions'=>'Menu.slug="'.$slug.'"','contain'=>array('MenuItem')));
		
		$menuitems=$menu['MenuItem'];
		
		$menuitems=json_encode($menuitems); //convert menuItems array to json object
		return $this->make_nav_lists($menuitems);

	}
	
	/**
	 * Make Navigation list elements
	 * 
	 * @params $menuitems array() an array pf menu items
	 *	
	 * generate list elements,recurse if menu has a second level
	 */
	function make_nav_lists($menuitems){
		$menuitems=json_decode($menuitems); //decode json string of children
		$navlists="<ul>";
		
		$user=$this->Session->read('Auth.User.username'); //get current user
		
		foreach($menuitems as $menu){
			
			$authorized = $this->Acl->check($user, $menu->action);
			if(!$authorized) continue; //do not generate menu item if user is not allowed that action
			
	
			$navlists.="<li ".$this->is_current_controller($menu->controller).">";
			$navlists.="<a href='".$menu->url."'><b ".$this->is_current_action($menu->controller,$menu->action).">".$menu->label."</b></a>";
			
			//if menu element has a children note: $menu->children is stored as a json string 
			if(isset($menu->children) && $menu->children!='' ){

				$navlists.="<ul>";
				$navlists.=$this->make_nav_lists($menu->children);
				$navlists.="</ul>";
			}
			$navlists.="</li></ul>";
		}
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
		if($this->params['current_controller']==$urlcontroller){
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
		if($this->params['current_controller']==$urlcontroller && $this->params['current_action']==$urlaction){
			return "class='active'";
		}else{
			return "";
		}
	}
	
}

 
 