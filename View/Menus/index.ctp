<div class="row">
	<div class="span12">
		<div class="well cf">
			<div class="span5">
				<h1>All Menus</h1>
			</div>
			<div class="span3 right addnewbutton">
				<a class="btn btn-success" href="<?php echo $this->Html->url(array("plugin"=>"menubuilder","controller" => "menus", "action" => "add"));?>">
					+ add new menu
				</a>
			</div>
		</div>
	</div>
</div>

<div class="row add-top">
	<div class="span12">
		<?php if(isset($menus) && empty($menus)){  ?>
			<h3>
				*Crickets*, you have not added menus yet , <a href="<?php echo $this->Html->url(array("plugin"=>"menubuilder","controller" => "menus", "action" => "add"));?>">click here to add one!</a>
			</h3>
		<?php }else{ ?>
		<table class="table table-striped table-bordered product_list">
			<thead>
				<th style="width:206px;">Menu Name</th>
				<th style="width:306px;">Menu Slug:</th>
				<th>Menu actions</th>
			</thead>
			<tbody>
				<?php foreach($menus as $menu){  ?>
					<tr>
						<td><?php echo $menu['name']; ?></td>
						<td>
							<?php echo $menu['slug']; ?>
						</td>
						<td>
							<a class="btn" href="<?php echo $this->Html->url(array("plugin"=>"menubuilder","controller" => "menus", "action" => "edit", $menu['id']));?>">
								<i class="icon-edit"></i>
								<?php echo __('Edit'); ?>
							</a>
							<?php
								echo $this->Form->postLink(
									__('Delete'),
										array(
										'action' => 'delete',
										$menu['id']
									),
									array('class' => 'btn btn-danger'),
									__('Are you sure you want to delete?')
								);
							?>

						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php } ?>
	</div>
</div>

