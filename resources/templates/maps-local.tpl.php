<section class="maps hasMenu hasFolders">
	<section class="cadre left menu">
		<?php echo AdminServUI::getMapsMenuList(); ?>
	</section>
	
	<section class="cadre middle folders">
		<?php echo AdminServUI::getMapsDirectoryList($data['currentDir'], $args['directory']); ?>
	</section>
	
	<section class="cadre right local">
		<h1><?php echo Utils::t('Local'); ?></h1>
		<div class="title-detail">
			<ul>
				<li><div class="path"><?php echo $data['mapsDirectoryPath'].$args['directory']; ?></div></li>
				<li class="last"><input type="checkbox" name="checkAll" id="checkAll" value=""<?php if (!is_array($data['maps']['lst'])): echo ' disabled="disabled"'; endif; ?> /></li>
			</ul>
		</div>
		
		<form method="post" action="?p=<?php echo USER_PAGE; if ($args['directory']): echo '&amp;d='.$args['directory']; endif; ?>">
		<div id="maplist">
			<table>
				<thead>
					<tr>
						<th class="thleft"><a href="?p=<?php echo USER_PAGE; if ($args['directory']): echo '&amp;d='.$args['directory']; endif; ?>&amp;sort=name"><?php echo Utils::t('Map'); ?></a></th>
						<th><a href="?p=<?php echo USER_PAGE; if ($args['directory']): echo '&amp;d='.$args['directory']; endif; ?>&amp;sort=env"><?php echo Utils::t('Environment'); ?></a></th>
						<th><a href="?p=<?php echo USER_PAGE; if ($args['directory']): echo '&amp;d='.$args['directory']; endif; ?>&amp;sort=type"><?php echo Utils::t('Type'); ?></a></th>
						<th><a href="?p=<?php echo USER_PAGE; if ($args['directory']): echo '&amp;d='.$args['directory']; endif; ?>&amp;sort=author"><?php echo Utils::t('Author'); ?></a></th>
						<th class="thright"></th>
					</tr>
				</thead>
				<tbody>
					<tr class="table-separation"><td colspan="4"></td></tr>
					<?php if ($data['maps']['nbm']['count'] > 0): ?>
						<?php $i = 0; ?>
						<?php foreach ($data['maps']['lst'] as $id => $map): ?>
							<?php
								// Map sur le serveur
								if($map['OnServer']){
									$mapImg = 'loadmap';
									$mapClass = ' onserver';
								}
								else{
									$mapImg = 'map';
									$mapClass = null;
								}
								// Map récente
								if($map['Recent']){
									$mapClass .= ' recent';
								}
							?>
							<tr class="<?php echo ($i%2) ? 'even' : 'odd'; echo $mapClass; ?>">
								<td class="imgleft"><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/16/<?php echo $mapImg; ?>.png" alt="" /><span title="<?php echo $map['FileName']; ?>"><?php echo $map['Name']; ?></span></td>
								<td class="imgcenter"><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/env/<?php echo strtolower($map['Environment']); ?>.png" alt="" /><?php echo $map['Environment']; ?></td>
								<td><span title="<?php echo $map['Type']['FullName']; ?>"><?php echo $map['Type']['Name']; ?></span></td>
								<td><?php echo $map['Author']; ?></td>
								<td class="checkbox"><input type="checkbox" name="map[]" value="<?php echo $map['FileName']; ?>" /></td>
							</tr>
							<?php $i++; ?>
						<?php endforeach; ?>
					<?php else: ?>
						<tr class="no-line">
							<td class="center" colspan="4">
								<?php if (is_array($data['maps'])): ?>
									<?php echo $data['maps']['lst']; ?>
								<?php else: ?>
									<?php echo $data['maps']; ?>
								<?php endif; ?>
							</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		
		<div class="options" data-mapisused="<?php echo Utils::t('The map,is currently used by the server.'); ?>">
			<div class="fleft">
				<span class="nb-line"><?php if (is_array($data['maps'])): echo $data['maps']['nbm']['count'].' '.$data['maps']['nbm']['title']; endif; ?></span>
			</div>
			<?php if (AdminServAdminLevel::hasPermission(array('maps_local_add', 'maps_local_insert', 'maps_local_download', 'maps_local_rename', 'maps_local_move', 'maps_local_delete'))): ?>
				<div class="fright">
					<div class="selected-files-label locked">
						<span class="selected-files-title"><?php echo Utils::t('For the selection'); ?></span>
						<span class="selected-files-count">(0)</span>
						<div class="selected-files-option">
							<?php if (AdminServAdminLevel::hasPermission('maps_local_delete')): ?>
								<input class="button dark" type="submit" name="deleteMap" id="deleteMap" value="<?php echo Utils::t('Delete'); ?>" data-confirm="<?php echo Utils::t('Do you really want to remove this selection?'); ?>" />
							<?php endif; ?>
							<?php if (AdminServAdminLevel::hasPermission('maps_local_move')): ?>
								<input class="button dark" type="button" name="moveMap" id="moveMap" value="<?php echo Utils::t('Move'); ?>" />
							<?php endif; ?>
							<?php if (AdminServAdminLevel::hasPermission('maps_local_rename')): ?>
								<input class="button dark" type="button" name="renameMap" id="renameMap" value="<?php echo Utils::t('Rename'); ?>" />
							<?php endif; ?>
							<?php if (AdminServAdminLevel::hasPermission('maps_local_download')): ?>
								<input class="button dark" type="submit" name="downloadMap" id="downloadMap" value="<?php echo Utils::t('Download'); ?>" />
							<?php endif; ?>
							<?php if (AdminServAdminLevel::hasPermission('maps_local_insert')): ?>
								<input class="button dark" type="submit" name="insertMap" id="insertMap" value="<?php echo Utils::t('Insert'); ?>" />
							<?php endif; ?>
							<?php if (AdminServAdminLevel::hasPermission('maps_local_add')): ?>
								<input class="button dark" type="submit" name="addMap" id="addMap" value="<?php echo Utils::t('Add'); ?>" />
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<?php if (AdminServAdminLevel::hasPermission('maps_local_rename')): ?>
				<div id="form-rename-map" class="option-form" hidden="hidden" data-cancel="<?php echo Utils::t('Cancel'); ?>" data-rename="<?php echo Utils::t('Rename'); ?>" data-autorename="<?php echo Utils::t('Replace the special characters'); ?>"></div>
			<?php endif; ?>
			<?php if (AdminServAdminLevel::hasPermission('maps_local_move')): ?>
				<div id="form-move-map" class="option-form" hidden="hidden" data-cancel="<?php echo Utils::t('Cancel'); ?>" data-move="<?php echo Utils::t('Move'); ?>" data-inthefolder="<?php echo Utils::t('in the folder:'); ?>" data-root="<?php echo Utils::t('Root'); ?>"></div>
			<?php endif; ?>
		</div>
		<?php if (SERVER_MATCHSET && AdminServAdminLevel::hasPermission('maps_matchsettings_save')): ?>
			<div class="fleft options-checkbox">
				<input class="text inline" type="checkbox" name="SaveCurrentMatchSettings" id="SaveCurrentMatchSettings"<?php if (AdminServConfig::AUTOSAVE_MATCHSETTINGS === true): echo ' checked="checked"'; endif; ?> value="" /><label for="SaveCurrentMatchSettings" title="<?php echo SERVER_MATCHSET; ?>"><?php echo Utils::t('Save the current MatchSettings'); ?></label>
			</div>
		<?php endif; ?>
		</form>
	</section>
</section>