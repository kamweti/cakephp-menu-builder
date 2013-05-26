<?php if($this->Session->check('Message.flash')): ?>
    <div class="span12">
		<div class="alert alert-success">
			<a class="close" data-dismiss="alert">&times;</a>
			<h4 class="alert-heading">Success!</h4>
			<p><?php echo $message; ?></p>
		</div>
    </div>
<?php endif; ?>