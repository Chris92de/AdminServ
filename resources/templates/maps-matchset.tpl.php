<section class="maps hasMenu hasFolders">
	<section class="cadre left menu">
		<?php echo AdminServUI::getMapsMenuList(); ?>
	</section>
	
	<section class="cadre middle folders">
		<?php echo AdminServUI::getMapsDirectoryList($data['currentDir'], $args['directory']); ?>
	</section>
	
	<section class="cadre right matchset">
		<h1><?php echo Utils::t('MatchSettings'); ?></h1>
		<div class="title-detail">
			<ul>
				<li class="path"><?php echo $data['mapsDirectoryPath'].$args['directory']; ?></li>
				<li class="last"><input type="checkbox" name="checkAll" id="checkAll" value="" /></li>
			</ul>
		</div>
		
		<form method="post" action="?p=<?php echo USER_PAGE; if ($args['directory']): echo '&amp;d='.$args['directory']; endif; ?>">
		<div id="matchsetlist">
			<table>
				<thead>
					<tr>
						<th class="thleft"><a href="?sort=name"><?php echo Utils::t('Name'); ?></a></th>
						<th><a href="?sort=nbm"><?php echo Utils::t('Contains'); ?></a></th>
						<th><a href="?sort=mtime"><?php echo Utils::t('Modified'); ?></a></th>
						<th class="thright"></th>
					</tr>
				</thead>
				<tbody>
					<tr class="table-separation"><td colspan="4"></td></tr>
					<?php if ($data['matchsettingsList']['nbm']['count'] > 0): ?>
						<?php $i = 0; ?>
						<?php foreach ($data['matchsettingsList']['lst'] as $id => $matchset): ?>
							<tr class="<?php echo ($i%2) ? 'even' : 'odd'; if ($matchset['Recent']): echo ' recent'; endif; ?>">
								<td class="imgleft"><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/16/finishgrey.png" alt="" /><span title="<?php echo $matchset['FileName']; ?>"><?php echo $matchset['Name']; ?></span></td>
								<td><?php echo $matchset['Nbm']; ?></td>
								<td><?php echo date('d/m/Y', $matchset['Mtime']); ?></td>
								<td class="checkbox"><input type="checkbox" name="matchset[]" value="<?php echo $matchset['FileName']; ?>" /></td>
							</tr>
							<?php $i++; ?>
						<?php endforeach; ?>
					<?php else: ?>
						<tr class="no-line">
							<td class="center" colspan="4"><?php echo $data['matchsettingsList']['lst']; ?></td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		
		<div class="options">
			<div class="fleft">
				<span class="nb-line"><?php if (is_array($data['matchsettingsList']['nbm'])): echo $data['matchsettingsList']['nbm']['count'].' '.$data['matchsettingsList']['nbm']['title']; endif; ?></span>
			</div>
			<?php if (AdminServAdminLevel::hasPermission(array('maps_matchsettings_save', 'maps_matchsettings_load', 'maps_matchsettings_add', 'maps_matchsettings_insert', 'maps_matchsettings_edit', 'maps_matchsettings_delete'))): ?>
				<div class="fright">
					<div class="selected-files-label locked">
						<span class="selected-files-title"><?php echo Utils::t('For the selection'); ?></span>
						<span class="selected-files-count">(0)</span>
						<div class="selected-files-option">
							<?php if (AdminServAdminLevel::hasPermission('maps_matchsettings_delete')): ?>
								<input class="button dark" type="submit" name="deleteMatchset" id="deleteMatchset" value="<?php echo Utils::t('Delete'); ?>" />
							<?php endif; ?>
							<?php if (AdminServAdminLevel::hasPermission('maps_matchsettings_edit')): ?>
								<input class="button dark" type="submit" name="editMatchset" id="editMatchset" value="<?php echo Utils::t('Edit'); ?>" />
							<?php endif; ?>
							<?php if (AdminServAdminLevel::hasPermission('maps_matchsettings_insert')): ?>
								<input class="button dark" type="submit" name="insertMatchset" id="insertMatchset" value="<?php echo Utils::t('Insert'); ?>" />
							<?php endif; ?>
							<?php if (AdminServAdminLevel::hasPermission('maps_matchsettings_add')): ?>
								<input class="button dark" type="submit" name="addMatchset" id="addMatchset" value="<?php echo Utils::t('Add'); ?>" />
							<?php endif; ?>
							<?php if (AdminServAdminLevel::hasPermission('maps_matchsettings_load')): ?>
								<input class="button dark" type="submit" name="loadMatchset" id="loadMatchset" value="<?php echo Utils::t('Load'); ?>" />
							<?php endif; ?>
							<?php if (AdminServAdminLevel::hasPermission('maps_matchsettings_save')): ?>
								<input class="button dark" type="submit" name="saveMatchset" id="saveMatchset" value="<?php echo Utils::t('Save '); ?>" />
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
		</form>
	</section>
</section>