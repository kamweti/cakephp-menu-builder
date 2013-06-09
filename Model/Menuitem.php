<?php

class Menuitem extends MenubuilderAppModel{

  public $hasMany = array('Menubuilder.Menu');



  /**
   * Creates a menu tree
   * @param  array $menu_items
   * @return array
   */
  public function createMenuTree($menu_items, $parentid = null){

    $menu_structure = array();
    foreach($menu_items as $item) {
      if( $item['Menuitem']['nested'] == 0 && is_null($parentid) ) {
        //find children for this menu
        $kids = $this->createMenuTree($menu_items, $item['Menuitem']['id']);
        if( ! empty($kids) ) $item['Menuitem']['children'] = $kids ;

        $menu_structure[] = $item['Menuitem'];
      } else{

        // this is a child,
        if( $item['Menuitem']['parent_id'] == $parentid ) {
          //does the child have nested children
          $kids = $this->createMenuTree($menu_items, $item['Menuitem']['id']);
          //unset this menu
          if( ! empty($kids) ) $item['Menuitem']['children'] = $kids ;

          $menu_structure[] = $item['Menuitem'];
        }

      }
    }

    return $menu_structure;
  }


  /**
   * Make a nested unordered list of menu items
   * the li should match the 'menuitem_tmpl' in layouts/index.ctp
   *
   * @param  array $menu_items
   * @return array
   */
  public function make_ul_list($menu_items = array()){

    $navlists = '<ul>';
    foreach($menu_items as $item){
      $navlists.='<li data-id="'.$item['id'].'" data-label="'.$item['label'].'" data-url="'.$item['url'].'">';
      $navlists.= '<span class="actions"><i class="icon-remove del" title="Remove"></i><i class="icon-chevron-up moveup" title="Move Up"></i></span>';
      $navlists.= '<span>'.$item['label'].'</span>';

      //recurse if item has childrens
      if(array_key_exists('children', $item) ){
        $navlists .= $this->make_ul_list($item['children']);
      }
      $navlists.="</li>";
    }

    return $navlists .'</ul>';
  }

  /**
   * Make a nested unordered list of menu items
   * the li should match the 'menuitem_tmpl' in layouts/index.ctp
   *
   * @param  array $menu_items
   * @return array
   */
  public function make_options_list($menu_items = array(), $nest = ''){
    $optionlist = '';
    foreach($menu_items as $item){
      if(array_key_exists('children', $item) ){
        $optionlist.='<option class="parent">' . $nest . $item['label'] ."</option>";
        $optionlist .= $this->make_options_list($item['children'], $nest.'&nbsp;');
      } else {
        $optionlist.='<option value="'.$item['id'].'">'. $nest . $item['label'] . '</option>';
      }
    }
    return $optionlist;
  }


}
?>
