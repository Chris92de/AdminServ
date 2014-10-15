<section class="cadre">
	<h1><?php echo Utils::t('Servers order'); ?></h1>
	<form method="post" action="?p=<?php echo USER_PAGE; ?>">
		<div class="content">
			<ul id="sortableServersList">
			<?php foreach ($data['servers'] as $serverName => $serverData): ?>
				<li class="ui-state-default">
					<div class="ui-icon ui-icon-arrowthick-2-n-s"></div>
					<div class="order-server-name"><?php echo $serverName; ?></div>
					<div class="order-server-addr-port"><?php echo $serverData['address'].' / '.$serverData['port']; ?></div>
				</li>
			<?php endforeach; ?>
			</ul>
		</div>
		
		<div class="fright save">
			<input class="button light" type="button" id="reset" name="reset" value="<?php echo Utils::t('Reset'); ?>" />
			<input class="button light" type="submit" id="save" name="save" value="<?php echo Utils::t('Save'); ?>" />
			<input type="hidden" id="list" name="list" value="" />
		</div>
	</form>
</section>