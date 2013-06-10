<?php
/**
 *
 * @author   Muriuki <kamweti.muriuki@squaddigital.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link
 */

App::import('Config', 'Menubuilder.MenuSettings');
App::uses('Folder',   'Utility');
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

	function beforeFilter(){
	  parent :: beforeFilter();

		$this->Auth->allow();
	}

	function index(){
		// fetch all menus,contains menu and Menuitem models
		$allmenus = $this->Menu->find('all', array('order'=>'Menu.id desc'));

		$menulist = array(); //array to return containing menu id,name

		foreach($allmenus as $key=>$menu){
			$menulist[$key]['id']   = $menu['Menu']['id']; //menu id
			$menulist[$key]['name'] = $menu['Menu']['name']; //menu name
			$menulist[$key]['slug'] = $menu['Menu']['slug']; //menu name
		}

		$this->set('menus', $menulist);
	}

	function add(){

		if ($this->request->is('post')) {
			$this->Menu->create();
			if ($this->Menu->save($this->request->data)) {
				$this->Session->setFlash(__('Your menu has been created, you can now add items'), 'alert/success');
				$this->redirect(array('action' => 'edit', $this->Menu->id));
			} else {
				$this->set('errors', $this->Menu->validationErrors);
				$this->Session->setFlash(__('The menu could not be saved. Please, try again.'), 'alert/error');
			}
		}

	}

	/*
	 * discard the menu
	 * note: not actual drop of field but setting status to 0,
	 * @params: menu id
	 * */
	function edit( $id = null ){

		if (!$this->Menu->exists($id)) {
			throw new NotFoundException(__('Invalid menu'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Menu->save($this->request->data)) {
				$this->Session->setFlash(__('The menu has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The menu could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Menu.' . $this->Menu->primaryKey => $id));
			$this->request->data = $this->Menu->find('first', $options);
		}
    //fetch all app controllers
    $appcontrollers=$this->get_all_app_controllers();

    //fetch app controller actions
    foreach($appcontrollers as $key=>$controller){
      $controller_class_name = $controller['name'] . 'Controller';
      $appcontrollers[$key]['actions']=$this->get_controller_actions($controller_class_name);
    }

    //fetch all plugin controllers
    $plugincontrollers=$this->get_all_plugins_controllers();
    //fetch plugin controller actions
    foreach($plugincontrollers as $key=>$controller){
      $controller_class_name = $controller['name'] . 'Controller';

      // we have no business showing controllers
      // with no actions, beats the purpose
      $actions = $this->get_plugin_controllers_actions($controller);
      if( empty( $actions ) )  {
        unset($plugincontrollers[$key]);
      } else {
        $plugincontrollers[$key]['actions'] = $actions;
      }
    }
    $this->set('plugin_controllers', $plugincontrollers );
    $this->set('app_controllers',$appcontrollers); //return only application controllers

		$this->Menuitem->recursive = -1;
		$menu_tree = $this->Menuitem->createMenuTree(
			$this->Menuitem->find('all', array(
					'conditions' => array( 'menu_id' => $id )
				)
			)
		);
		$this->set('menu_ul_list', $this->Menuitem->make_ul_list($menu_tree) );
		$this->set('menu_options_list', $this->Menuitem->make_options_list($menu_tree) );
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
			$this->Session->setFlash(__('Menu deleted'), 'alert/success' );
		} else {
			$this->Session->setFlash(__('Menu was not deleted'), 'alert/error');
		}

		$this->redirect(array('action' => 'index'));
	}

	/*
	 * shows a preview of the menu,
	 * @params: id the menu id
	 * */
	function preview( $menuid=null ){
	}

	/*
	 * fetch all controllers in /app/Controller
	 * @params:
	 * */
	function get_all_app_controllers(){
		$controllers = array();
		$folder      = & new Folder();

		$didCD = $folder->cd(APP . 'Controller');

		if(!empty($didCD)){
	    $files = $folder->findRecursive('.*Controller\.php');

	    foreach($files as $fileName) {
				$file = basename($fileName);

				// Get the controller name
				$controller_class_name =substr($file, 0, strlen($file) - strlen('Controller.php'));

				if (!App::import('Controller', $controller_class_name)) {
					debug('Error importing ' . $controller_class_name . ' from APP controllers');
				} else {
				   $controllers[] = array('file' => $fileName, 'name' => $controller_class_name);
				}
			}
		}

		sort($controllers);
		return $controllers;
	}

	/*
	 * fetch all controllers in /app/Plugin
	 * note: this will fetch controllers for plugins that have been enabled
	 * @params:
	 * */
	function get_all_plugins_controllers($filter_default_controller = true){
		$plugin_paths = $this->get_all_plugins_paths();

		$plugins_controllers = array();
		$folder              = new Folder();

		// Loop through the plugins
		foreach($plugin_paths as $plugin_path) {
			$didCD = $folder->cd($plugin_path . DS . 'Controller');

			if(!empty($didCD)) {
				$files = $folder->findRecursive('.*Controller\.php');

				$plugin_name = substr($plugin_path, strrpos($plugin_path, DS) + 1);

				foreach($files as $fileName) {
					$file = basename($fileName);

					// Get the controller name
					$controller_class_name = Inflector::camelize(substr($file, 0, strlen($file) - strlen('Controller.php')));

					// if this is a default controller
					if ( preg_match('/^'. Inflector::humanize($plugin_name) . 'App/', $controller_class_name) ) {
						if( $filter_default_controller ) { continue; }
					} else {
						// only add it to menu list if the import is successful,
						// meaning the plugin has been loaded in app/Config/bootstrap.php
						if ( App::import('Controller', $plugin_name . '.' . $controller_class_name)) {
							$plugins_controllers[] = array('file' => $fileName, 'name' => Inflector::humanize($plugin_name) . "/" . $controller_class_name);
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

		$folder->cd(APP . 'Plugin');
		$app_plugins = $folder->read();

		foreach($app_plugins[0] as $plugin_name) {
			$plugin_names[] = APP . 'Plugin' . DS . $plugin_name;
		}

		$folder->cd(ROOT . DS . 'plugins'); // root folder plugin directory name is `plugins`
		$root_plugins = $folder->read();

		foreach($root_plugins[0] as $plugin_name) {
			$plugin_names[] = ROOT . DS . 'plugins' . DS . $plugin_name;
		}

		return $plugin_names;
	}


	public function get_all_plugins_names() {
		$plugin_names = array();

		$folder =& new Folder();

		$folder->cd(APP . 'Plugin');
		$app_plugins = $folder->read();
		if(!empty($app_plugins))
		{
			$plugin_names = array_merge($plugin_names, $app_plugins[0]);
		}

		$folder->cd(ROOT . DS . 'Plugin');
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
	public function get_controller_actions($controller_classname, $filter_base_methods = true) {
		$controller_classname = $this->get_controller_classname($controller_classname);
		$methods              = get_class_methods($controller_classname);

		if(isset($methods) && !empty($methods)) {
  		if($filter_base_methods) {
  			$baseMethods = get_class_methods('Controller');

  			$ctrl_cleaned_methods = array();
  		   
				foreach($methods as $method) {
				
					if(!in_array($method, $baseMethods) && strpos($method, '_') !== 0) {
						$ctrl_cleaned_methods[] = $method;
					}
				}
				return $ctrl_cleaned_methods;
  		} else {
  			return $methods;
  		}
		}
		else {
			return array();
		}
	}

	function get_plugin_controllers_actions($plugin_controller,$filter_default_controller = true) {
		$plugin_controllers_actions = array();

		$plugin_name     = $this->getPluginName($plugin_controller['name']);
		$controller_name = $this->getPluginControllerName($plugin_controller['name']);

		if(!$filter_default_controller || $plugin_name != $controller_name) {
			$controller_class_name = $controller_name . 'Controller';

			$ctrl_cleaned_methods = $this->get_controller_actions($controller_class_name);

			foreach($ctrl_cleaned_methods as $action) {
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
	    if(stripos($controller_name, '/') === false) {
	      $controller_classname = $controller_name . 'Controller';
	    } else {
        /*
         * Case of plugin controller
         */
        $controller_classname = substr($controller_name, strripos($controller_name, '/') + 1) . 'Controller';
	    }

	    return $controller_classname;
    } else {
    	return $controller_name;
    }
	}

	function getPluginName($ctrlName = null) {
		$arr = String::tokenize($ctrlName, '/');
		if (count($arr) == 2) {
			return $arr[0];
		} else {
			return false;
		}
	}

	function getPluginControllerName($ctrlName = null) {
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
		$this->render(false, false);

		if( $this->request->isPost() ) {
			if( $this->Menu->exists($this->request->data['id']) ) {
				//save the menu name
				$menu = $this->Menu->read(null, $this->request->data['id']);
				
				$this->Menu->save(array('name' =>  $this->request->data['name']));

				// save the menu items
				$get_items = function( $items, $menu_id, $parent_id = '', $nested = false) use (&$get_items) {
					$_items = array();

					foreach( $items as $item ) {
						$_items[] = array(
							'id' 				=> isset($item['id']) ? $item['id'] : false ,
							'menu_id' 	=> $menu_id,
							'label' 		=> $item['label'],
							'url' 		  => $item['url'],
							'parent_id' => (int) $parent_id,
							'nested' 		=> (int) $nested
						);
						if( array_key_exists('children', $item) ) {
							$_items = array_merge($_items, $get_items($item['children'], $menu_id, $item['id'], true));
						}
					}
					return $_items;
				};

	      //clear previous menu items before insert
	      $this->Menuitem->recursive = -1;
	      if( $this->Menuitem->deleteAll(array("menu_id" => $this->Menu->id), false) ) {
	      	$this->Menuitem->saveAll(
	      		$get_items($this->request->data['items'], $this->Menu->id )
	      	);

				  $this->Menuitem->recursive = -1;
					$menu_tree = $this->Menuitem->createMenuTree(
						$this->Menuitem->find('all', array(
								'conditions' => array( 'menu_id' => $this->Menu->id )
							)
						)
					);

					echo json_encode(
						array(
							'menu_name' => $menu['Menu']['name'],
							'menu_ul_list' => $this->Menuitem->make_ul_list($menu_tree),
							'menu_options_list' => $this->Menuitem->make_options_list($menu_tree)
						)
					);
	      }
			}
		}
	}
}