
<div class="row well">
	<div class="span5">
		<h1>Edit Menu</h1>
	</div>
</div>


<div class="row">
	<form class="span12 add-top new_menu" action="">
		<input type="hidden" class="menuid" value="<?php echo $menu['Menu']['id']; ?>" />
		<input type="hidden" class="ajax_target" value="<?php echo $this->base; ?>/menubuilder/menus/ajax_save" />
		<label class="left">
			<span>Menu Name</span>
			<input type="text" class="smalltext menu_name" name="menu_name" value="<?php echo $menu['Menu']['name']; ?>" placeholder="e.g Level 3 menu">
		</label>
		<label class="add-left left">
			<span class="block">Menu slug</span>
			<input type="text" class="smalltext menu_slug left" name="menu_slug" value="<?php echo $menu['Menu']['slug']; ?>" placeholder="e.g frontend">
			<pre class="left add-left" style="padding: 4px 8px;">usage: &lt;?php $this-&gt;Menu-&gt;display('slug'); ?&gt; </pre>
		</label>

		<div class="double-top double-bottom cf">
			<h4>Add Navigation Items To This Menu</h4>
			<div class="left">
					<label class="add-top cf">
						<span>Menu Item Label</span>
						<input type="text" name="menu_label" class="menu_label" />
					</label>
					<label class="left">
						<span>Controller</span>
						<select class="select_controller">
							<option value="null" selected>none</option>
							<?php foreach($controllers as $controller): ?>
								<option actions='<?php echo json_encode($controller['actions']); ?>'>
									<?php echo $controller['name']; ?>
								</option>
							<?php endforeach; ?>
						</select>
					</label>
					<label class="add-left left">
						<span>Action</span>
						<select class="select_action">
							<option value="null" selected>none</option>
						</select>
					</label>

					<label class="cf">
						<span>Parent Menu Item</span>
						<select class="the_parent">
							<option value="null" selected>none</option>
							<?php foreach($menu['Menuitem'] as $key=>$m): ?>?>
								<option index="<?php echo $key; ?>">
									<?php echo $m['label']; ?>
								</option>
							<?php endforeach; ?>
						</select>
					</label>
					<input value="Add Item To Menu" class="add_menu_item add-top btn cf" style="width:120px;" />

			</div>
			<div class="add-top double-left left well">
				<b class="block add-bottom">Menu Structure Preview </b>
				<ul class="nav nav-list menulistprvw">
					<?php foreach($menu['Menuitem'] as $menu) { ?>
						<li>
							<span class="actions" style="display: none; ">
								<i class="icon-remove del" title="Remove"></i>
								<i class="icon-chevron-up moveup" title="Move Up"></i>
							</span>
							<a controller="<?php echo $menu['controller']; ?>" action="<?php echo $menu['action']; ?>" href="<?php echo $menu['url']; ?>" target="_blank"><?php echo $menu['label']; ?></a>
							<ul class="nav nav-list">
								<?php
									if(strlen(trim($menu['children']))>0){
										$children=json_decode(($menu['children'])); //json object of children
								?>
									<?php foreach($children as $menu){ //$menu is now an object ?>
										<li>
											<a controller="<?php echo $menu->controller; ?>" action="<?php echo $menu->action; ?>" href="<?php echo $menu->url; ?>">- <?php echo $menu->label; ?></a>
										</li>
									<?php } ?>
								<?php } ?>
							</ul>
						</li>
					<?php } ?>
				</ul>
				<input class="btn btn-primary save_menu block add-top" value="Save Menu" style="width: 100px;" />

			</div>
		</div>


	</form>
</div>
