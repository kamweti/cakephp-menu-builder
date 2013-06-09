<div class="row">
	<form class="span12 add-top new_menu" method="post">
		<h1>Create a new menu</h1>
		<div class="well cf">
			<?php if( isset($errors) ) { ?>
				<ul>
					<?php foreach ($errors as $error): ?>
						<li class="redtext"><?php echo $error[0] ?></li>
					<?php endforeach ?>
				</ul>
			<?php } ?>
			<label class="left">
				<span class="block">Menu name</span>
				<input type="text" class="smalltext menu_name" name="name" value="<?php echo isset($this->request->data['name']) ? $this->request->data['name'] : ''; ?>" placeholder="e.g Level 3 menu">
			</label>
			<label class="add-left left">
				<span class="block">Slug</span>
				<input type="text" class="smalltext menu_slug left" name="slug" value="<?php echo isset($this->request->data['slug']) ? $this->request->data['slug'] : ''; ?>" placeholder="e.g frontend">
				<pre class="left add-left" style="padding: 4px 8px;">usage: &lt;?php $this-&gt;Menu-&gt;display('frontend'); ?&gt; </pre>
			</label>
			<input type="submit" class="btn btn-success" value="Create" />
		</div>
	</form>
</div>
