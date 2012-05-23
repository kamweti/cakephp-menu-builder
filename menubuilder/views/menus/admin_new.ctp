
<div class="row well">
	<div class="span5">
		<h1>Add New Menu</h1>
	</div>
</div>

<div class="row">
	<form class="span12 add-top new_menu" action="">
		<input type="hidden" class="ajax_target" value="<?php echo $this->base; ?>/menubuilder/menus/ajax_save" />
		<label class="left">
			<span>Menu Name</span>
			<input type="text" class="smalltext menu_name" name="menu_name" value="" placeholder="e.g Level 3 menu">
		</label>
		<label class="add-left left">
			<span class="block">Menu slug</span>
			<input type="text" class="smalltext menu_slug left" name="menu_name" value="" placeholder="e.g frontend">
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
									<?php echo strtolower($controller['name']); ?>
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
						</select>
					</label>
					<input value="Add" class="add_menu_item add-top btn cf" style="width:40px;" />
			</div>
			<div class="add-top double-left left well">
				<b class="block add-bottom">Menu Structure Preview </b>
				<ul class="nav nav-list menulistprvw"></ul>
			</div>
		</div>
		
		<input class="btn btn-primary save_menu" value="Save Menu" style="width: 100px;" />
	</form>
</div>
