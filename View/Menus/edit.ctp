
<div class="row">
	<div class="span5">
		<h1>Edit menu</h1>
	</div>
</div>

<div class="row">
	<div class="span12 add-top">
  	<input type="hidden" class="menu_id" value="<?php echo $this->request->data['Menu']['id'] ?>" />
    <div class="well cf">
    	<h4>Menu name</h4>
      <label class="left">
        <input type="text" class="smalltext menu_name" name="menu_name" value="<?php echo $this->request->data['Menu']['name']; ?>" />
      </label>
      <pre style="float:right;margin-top: -5px;"> Use it in your view:  &lt;?php $this-&gt;Menu-&gt;display('<?php echo $this->request->data['Menu']['slug'] ?>'); ?&gt;</pre>
    </div>
    <form method="post" action="" class="well edit_menu cf">
      <div class="left">
        <h4>Add items to this menu</h4>
        <label class="add-top cf control-group">
          <h5>Label</h5>
          <input type="text" name="menu_label" class="menu_label" placeholder="e.g Products" />
        </label>
        <h5>Specify where this menu item points to</h5>
        <ul class="nav nav-pills menu_points_to_tabs">
          <li class="active">
            <a href="#controller_action">Controller Action</a>
          </li>
          <li>
            <a href="#plugin_action">Plugin Action</a>
          </li>
          <li>
            <a href="#custom_url">Custom URL</a>
          </li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="controller_action">
            <label>
              <span class="block">Controller</span>
              <select class="select_controller">
                <?php foreach($app_controllers as $controller): ?>
                  <option actions='<?php echo json_encode($controller['actions']); ?>'><?php echo $controller['name']; ?></option>
                <?php endforeach; ?>
              </select>
            </label>
            <label class="select_action">
              <span class="block">Action</span>
              <select><!-- will be populated with actions on change --></select>
            </label>
          </div>
          <div class="tab-pane" id="plugin_action">
            <label>
              <span class="block">Plugin Controller</span>
              <select class="select_controller">
                <?php foreach($plugin_controllers as $controller): ?>
                  <option actions='<?php echo json_encode($controller['actions']); ?>'><?php echo $controller['name']; ?></option>
                <?php endforeach; ?>
              </select>
            </label>
            <label class="select_action">
              <span class="block">Plugin Action</span>
              <select><!-- will be populated with actions on change --></select>
            </label>
          </div>
          <div class="tab-pane" id="custom_url">
            <label>
              <input type="url" class="input-block-level url" placeholder="http://www.youtube.com/watch?v=N9qYF9DZPdw">
            </label>
          </div>
        </div>
        <h5>Does this menu item have a parent?</h5>
        <label class="radio">
          <input type="radio" name="has_parent" value="no" class="has_parent" checked/> No
        </label>
        <label class="radio">
          <input type="radio" name="has_parent" value="yes" class="has_parent" /> Yes
          <div style="display:none;" class="specify_parent_container">
            <span>Specify</span>
            <select class="the_parent"><?php echo $menu_options_list; ?></select>
          </div>
        </label>
        <label>
          <input type="button" value="Add to menu &rarr;" class="add_menu_item add-top btn cf" />
        </label>
      </div>
      <div class="double-left left">
        <h4 class="block add-bottom">Preview</h4>
        <div class="menulistprvw">
					<?php echo $menu_ul_list; ?>
        </div>
      </div>
    </form>
	</div>
</div>

