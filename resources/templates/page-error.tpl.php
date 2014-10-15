<section class="cadre">
	<h1><?php echo $data['errorTitle']; ?></h1>
	
	<?php if ($data['errorMessage']): ?>
		<div class="content">
			<p><?php echo $data['errorMessage']; ?></p>
		</div>
	<?php endif; ?>
</section>