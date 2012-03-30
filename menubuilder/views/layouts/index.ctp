<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" >
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Menu Builder</title>
	<link rel="shortcut icon" href="images/favicon.ico">
	<?php
		echo $this->Html->css('/menubuilder/css/bootstrap.css')
			.$this->Html->css('/menubuilder/css/style.css');
	?>
	
	<link type="text/css" rel="stylesheet" href="styles/style.css" />
</head>
<body baseurl='<?php echo $this->base; ?>'>
<div class="modal" id="myModal"></div>
<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<a class="brand " href="#">MenuBuilder</a>
			<div class="nav-collapse">
				<ul class="nav">
					<li>
						<a href="<?php echo $this->Html->url(array("plugin"=>"menubuilder","controller" => "menus", "action" => "index"));?>">
							All menus
						</a>
					</li>
					<li>
						<a href="<?php echo $this->Html->url(array("plugin"=>"menubuilder","controller" => "menus", "action" => "new"));?>">Add New Menu</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="container">
	
	<div class="row">
		<?php echo $this->Session->flash(); ?>
	</div>
	<?php echo $content_for_layout; ?>
	<div class="row">
		<?php echo $this->element('sql_dump'); ?>
	</div>
</div>

<?php echo $this->Html->script('/menubuilder/js/jquery1.7.js'); ?>
<?php echo $this->Html->script('/menubuilder/js/bootstrap-modal.js'); ?>
<?php echo $this->Html->script('/menubuilder/js/scripts.js'); ?>
<!-- templates -->
<script type="text/html" id="menuitem_tmpl">
	<li>
		<span class="actions" style="display: none; ">
			<i class="icon-remove del" title="Remove"></i> 
			<i class="icon-chevron-up moveup" title="Move Up"></i> 
		</span>
		<a action="<%=action%>" controller="<%=controller%>" href="<%=url%>" target="_blank"><%=label%></a>
		<ul class="nav nav-list"></ul>
	</li>
</script>
<script type="text/html" id="menu_child_item_tmpl">
	<li>
		<a action="<%=action%>" controller="<%=controller%>" href="<%=url%>">- <%=label%></a>
	</li>
</script>
<script type="text/html" id="saving_menu">
	<div class="modal-header">
		<h3 class="cf">
			<?php echo $this->Html->image('/menubuilder/img/loading.gif',array('class'=>'left')); ?>
			<span class="left add-left block">Saving Menu...</span>
		</h3>
	</div>
	<div class="modal-body">
		<p>Please wait while your menu gets saved...</p>
	</div>
</script>
<script type="text/html" id="success_menu_saved">
	<div class="modal-header">
		<h3 class="cf">
			<?php echo $this->Html->image('/menubuilder/img/tick.jpg',array('class'=>'left')); ?>
			<span class="left add-left block">Menu Saved...</span>
		</h3>
	</div>
	<div class="modal-footer">
	  <a href="#" class="btn btn-primary">Preview Menu</a>
	  <a href="#" class="btn" data-dismiss="modal">Close</a>
	</div>
</script>
</body>
</html>