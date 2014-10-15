<section class="maps hasMenu<?php if (defined('IS_LOCAL') && IS_LOCAL): echo ' hasFolders'; endif; ?>">
	<section class="cadre left menu">
		<?php echo AdminServUI::getMapsMenuList(); ?>
	</section>
	
	<?php if (defined('IS_LOCAL') && IS_LOCAL): ?>
		<section class="cadre middle folders">
			<?php echo AdminServUI::getMapsDirectoryList($data['currentDir'], $args['directory']); ?>
		</section>
	<?php endif; ?>
	
	<section class="cadre right upload">
		<h1><?php echo Utils::t('Send'); ?></h1>
		<div class="title-detail path"><?php echo $data['mapsDirectoryPath'].$args['directory']; ?></div>
		
		<h2><?php echo Utils::t('Transfer mode'); ?></h2>
		<div class="transferMode options-radio-inline">
			<ul>
				<?php if (AdminServAdminLevel::hasPermission('maps_upload_add')): ?>
					<li class="selected">
						<input class="text" type="radio" name="transferMode" id="transferModeAdd" value="add" checked="checked" />
						<div class="name"><span><?php echo Utils::t('Add'); ?></span> <?php echo Utils::t('at the end of list'); ?></div>
					</li>
				<?php endif; ?>
				<?php if (AdminServAdminLevel::hasPermission('maps_upload_insert')): ?>
					<li>
						<input class="text" type="radio" name="transferMode" id="transferModeInsert" value="insert" />
						<div class="name"><span><?php echo Utils::t('Insert'); ?></span> <?php echo Utils::t('after current map'); ?></div>
					</li>
				<?php endif; ?>
				<?php if (AdminServAdminLevel::hasPermission('maps_upload_folder')): ?>
					<li>
						<input class="text" type="radio" name="transferMode" id="transferModeLocal" value="local" />
						<div class="name"><span><?php echo Utils::t('Send'); ?></span> <?php echo Utils::t('only in the folder'); ?></div>
					</li>
				<?php endif; ?>
			</ul>
		</div>
		
		<h2><?php echo Utils::t('Options'); ?></h2>
		<div class="options-checkbox">
			<ul>
				<?php if (SERVER_MATCHSET && AdminServAdminLevel::hasPermission('maps_matchsettings_save')): ?>
					<li>
						<input class="text inline" type="checkbox" name="SaveCurrentMatchSettings" id="SaveCurrentMatchSettings"<?php if (AdminServConfig::AUTOSAVE_MATCHSETTINGS === true): echo ' checked="checked"'; endif; ?> value="" />
						<label for="SaveCurrentMatchSettings" title="<?php echo SERVER_MATCHSET; ?>"><?php echo Utils::t('Save the current MatchSettings'); ?></label>
					</li>
				<?php endif; ?>
				<li>
					<input class="text inline" type="checkbox" name="GotoListMaps" id="GotoListMaps" value="maps" checked="checked" />
					<label for="GotoListMaps"><?php echo Utils::t('Go to the maps list when upload is complete'); ?></label>
				</li>
			</ul>
		</div>
		
		<h2><?php echo Utils::t('Upload'); ?></h2>
		<div id="formUpload" class="loader" data-mapspagename="maps-list" data-cancel="<?php echo Utils::t('Cancel'); ?>" data-failed="<?php echo Utils::t('Failed'); ?>" data-uploadfile="<?php echo Utils::t('Upload a file'); ?>" data-dropfiles="<?php echo Utils::t('Drop files here to upload'); ?>" data-uploadnotfinished="<?php echo Utils::t('Upload not finished'); ?>" data-from="<?php echo Utils::t('from'); ?>" data-kb="<?php echo Utils::t('Kb'); ?>" data-mb="<?php echo Utils::t('Mb'); ?>" data-type-error="<?php echo Utils::t('{file} has invalid extension. Only {extensions} are allowed.'); ?>" data-size-error="<?php echo Utils::t('{file} is too large, maximum file size is {sizeLimit}.'); ?>" data-minsize-error="<?php echo Utils::t('{file} is too small, minimum file size is {minSizeLimit}.'); ?>" data-empty-error="<?php echo Utils::t('{file} is empty, please select files again without it.'); ?>" data-onleave="<?php echo Utils::t('The file was not uploaded. Upload has been cancelled or a server error occurred.'); ?>"></div>
	</section>
</section>