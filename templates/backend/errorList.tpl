<?php ?>
<div class="xhsErrors">
	<h1><?php $this->label('error'); ?>:</h1>
	<p><?php $this->hint('sorry_errors'); ?></p>
	<ul>
		<?php foreach($this->errors['file_errors'] as $error){ ?>
		<li><?php $this->label($error[1]); ?>: <?php echo $error[0]; ?></li>
		<?php } ?>
	</ul>
</div>