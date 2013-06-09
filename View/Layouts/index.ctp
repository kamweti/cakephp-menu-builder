<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" >
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Menu Builder</title>
	<?php
		echo $this->Html->css('/menubuilder/css/bootstrap.min.css');
		echo $this->Html->css('/menubuilder/css/style.css');
	?>
</head>
<body>
<div class="modal" id="myModal"></div>
<div class="navbar navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <a class="brand" href="<?php echo $this->Html->url(array('plugin'=>'menubuilder','controller' => 'menus', 'action' => 'index')); ?>">MenuBuilder</a>
      <div class="nav-collapse">
        <ul class="nav">
          <li>
            <a href="<?php echo $this->Html->url(array('plugin'=>'menubuilder', 'controller'=>'menus', 'action'=>'index')) ?>">
              All menus
            </a>
          </li>
          <li>
            <a href="<?php echo $this->Html->url(array('plugin'=>'menubuilder', 'controller'=>'menus', 'action'=>'add')) ?>">
              Add New Menu
            </a>
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
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<?php echo $this->Html->script('/menubuilder/js/bootstrap-modal.js'); ?>
<?php echo $this->Html->script('/menubuilder/js/bootstrap-tab.js'); ?>
<?php echo $this->Html->script('/menubuilder/js/scripts.js'); ?>
<!-- templates -->
<script type="text/html" id="menuitem_tmpl">
	<li data-label="<%=label%>" data-url="<%=href%>">
		<span class="actions">
      <i class="icon-remove del" title="Remove"></i>
			<i class="icon-chevron-up moveup" title="Move Up"></i>
		</span>
		<span><%=label%></span>
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
	  <!--<a href="#" class="btn btn-primary">Preview Menu</a>-->
	  <a href="#" class="btn" data-dismiss="modal">Close</a>
	</div>
</script>
<div class="row">
	<?php //echo $this->element('sql_dump'); ?>
</div>
</body>
</html>
