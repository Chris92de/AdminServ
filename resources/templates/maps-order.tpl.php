<section class="maps hasMenu">
	<section class="cadre left menu">
		<?php echo AdminServUI::getMapsMenuList(); ?>
	</section>
	
	<section class="cadre right order">
		<h1><?php echo Utils::t('Order'); ?></h1>
		<form method="post" action="?p=<?php echo USER_PAGE; ?>">
			<h2><?php echo Utils::t('Automatic sort'); ?></h2>
			<div class="autoSortMode options-radio-inline">
				<ul>
					<li class="ui-state-default">
						<input class="text" type="radio" name="sortMode" id="sortModeName" value="name" />
						<div class="name"><?php echo Utils::t('Name'); ?></div>
						<div class="icon">
							<span class="ui-icon ui-icon-arrowthick-1-n"></span>
							<span class="ui-icon ui-icon-arrowthick-1-s"></span>
						</div>
					</li>
					<li class="ui-state-default">
						<input class="text" type="radio" name="sortMode" id="sortModeEnv" value="env" />
						<div class="name"><?php echo Utils::t('Environment'); ?></div>
						<div class="icon">
							<span class="ui-icon ui-icon-arrowthick-1-n"></span>
							<span class="ui-icon ui-icon-arrowthick-1-s"></span>
						</div>
					</li>
					<li class="ui-state-default">
						<input class="text" type="radio" name="sortMode" id="sortModeAuthor" value="author" />
						<div class="name"><?php echo Utils::t('Author'); ?></div>
						<div class="icon">
							<span class="ui-icon ui-icon-arrowthick-1-n"></span>
							<span class="ui-icon ui-icon-arrowthick-1-s"></span>
						</div>
					</li>
					<li class="ui-state-default">
						<input class="text" type="radio" name="sortMode" id="sortModeRand" value="rand" />
						<div class="name"><?php echo Utils::t('Random'); ?></div>
					</li>
				</ul>
			</div>
			
			<h2><?php echo Utils::t('Manual sort'); ?></h2>
			<div class="content">
				<ul id="sortableMapList">
					<?php if (!empty($data['maps'])): ?>
						<?php foreach ($data['maps']['lst'] as $id => $map): ?>
							<li class="ui-state-default">
								<div class="ui-icon ui-icon-arrowthick-2-n-s"></div>
								<div class="order-map-name" title="<?php echo $map['FileName']; ?>"><?php echo $map['Name']; ?></div>
								<div class="order-map-env"><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/env/<?php echo strtolower($map['Environment']); ?>.png" alt="" /><?php echo $map['Environment']; ?></div>
								<div class="order-map-author"><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/16/mapauthor.png" alt="" /><?php echo $map['Author']; ?></div>
							</li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>
			</div>
			
			<?php if (SERVER_MATCHSET && AdminServAdminLevel::hasPermission('maps_matchsettings_save')): ?>
				<div class="fleft options-checkbox">
					<input class="text inline" type="checkbox" name="SaveCurrentMatchSettings" id="SaveCurrentMatchSettings"<?php if (AdminServConfig::AUTOSAVE_MATCHSETTINGS === true): echo ' checked="checked"'; endif; ?> value="" /><label for="SaveCurrentMatchSettings" title="<?php echo SERVER_MATCHSET; ?>"><?php echo Utils::t('Save the current MatchSettings'); ?></label>
				</div>
			<?php endif; ?>
			<div class="fright save">
				<input class="button light" type="button" id="reset" name="reset" value="<?php echo Utils::t('Reset'); ?>" />
				<input class="button light" type="submit" id="save" name="save" value="<?php echo Utils::t('Save'); ?>" />
				<input type="hidden" id="list" name="list" value="" />
				<input type="hidden" id="jsonlist" name="jsonlist" value="<?php echo htmlspecialchars( json_encode($data['maps']) ); ?>" />
			</div>
		</form>
	</section>
</section>