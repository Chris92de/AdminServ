<section class="cadre">
	<h1><?php echo Utils::t('Servers list'); ?></h1>
	<form method="post" action="?p=<?php echo USER_PAGE; ?>">
	<table id="serverList">
		<thead>
			<tr>
				<th class="thleft"><?php echo Utils::t('Server name'); ?></th>
				<th><?php echo Utils::t('Address'); ?></th>
				<th><?php echo Utils::t('Port'); ?></th>
				<th><?php echo Utils::t('MatchSettings'); ?></th>
				<th><?php echo Utils::t('SuperAdmin level'); ?></th>
				<th><?php echo ucwords( Utils::t('Admin level') ); ?></th>
				<th><?php echo Utils::t('User level'); ?></th>
				<th class="thright"><?php echo Utils::t('Manage'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr class="table-separation"><td colspan="8"></td></tr>
			<?php if($data['count'] > 0): ?>
				<?php $i = 0; ?>
				<?php foreach ($data['servers'] as $serverName => $serverData): ?>
					<?php
						// MatchSettings
						$matchSettings = ($serverData['matchsettings']) ? $serverData['matchsettings'] : Utils::t('None');
						
						// Niveaux admins
						$adminLevelsStatus = array();
						foreach($data['adminLevelsType'] as $level){
							if( array_key_exists($level, $serverData['adminlevel']) ){
								if( is_array($serverData['adminlevel'][$level]) ){
									$adminLevelsStatus[] = Utils::t('IP address');
								}
								else if($serverData['adminlevel'][$level] === 'local'){
									$adminLevelsStatus[] = Utils::t('Local network');
								}
								else if($serverData['adminlevel'][$level] === 'all'){
									$adminLevelsStatus[] = Utils::t('All');
								}
								else if($serverData['adminlevel'][$level] === 'none'){
									$adminLevelsStatus[] = Utils::t('Removed');
								}
								else{
									$adminLevelsStatus[] = Utils::t('Missing');
								}
							}
							else{
								$adminLevelsStatus[] = Utils::t('Missing');
							}
						}
					?>
					<tr class="<?php echo ($i%2) ? 'even' : 'odd'; ?>">
						<td class="imgleft"><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/16/servers.png" alt="" /><?php echo $serverName; ?></td>
						<td><?php echo $serverData['address']; ?></td>
						<td><?php echo $serverData['port']; ?></td>
						<td><?php echo $matchSettings; ?></td>
						<td><?php echo $adminLevelsStatus[0]; ?></td>
						<td><?php echo $adminLevelsStatus[1]; ?></td>
						<td><?php echo $adminLevelsStatus[2]; ?></td>
						<td class="checkbox"><input type="radio" name="server[]" value="<?php echo $serverName; ?>" /></td>
					</tr>
					<?php $i++; ?>
				<?php endforeach; ?>
			<?php else: ?>
				<tr class="no-line">
					<td class="center" colspan="8"><?php echo Utils::t('No server'); ?></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
	
	<div class="options">
		<div class="fleft">
			<span class="nb-line">
				<?php echo $data['count'].' '.(($data['count'] > 1) ? Utils::t('servers') : Utils::t('server')); ?>
			</span>
		</div>
		<div class="fright">
			<div class="selected-files-label locked">
				<span class="selected-files-title"><?php echo Utils::t('For the selection'); ?></span>
				<div class="selected-files-option">
					<input class="button dark" type="submit" name="deleteserver" id="deleteserver" data-confirm-text="<?php echo Utils::t('Do you really want to remove this selection?'); ?>" value="<?php echo Utils::t('Delete'); ?>" />
					<input class="button dark" type="submit" name="duplicateserver" id="duplicateserver" value="<?php echo Utils::t('Duplicate'); ?>" />
					<input class="button dark" type="submit" name="editserver" id="editserver" value="<?php echo Utils::t('Modify'); ?>" />
				</div>
			</div>
		</div>
	</div>
	</form>
</section>