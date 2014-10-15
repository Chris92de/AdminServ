<section class="cadre">
	<h1><?php echo Utils::t('Admin levels list'); ?></h1>
	<form method="post" action="?p=<?php echo USER_PAGE; ?>">
	<table id="levelList">
		<thead>
			<tr>
				<th class="thleft"><?php echo Utils::t('Name'); ?></th>
				<th><?php echo Utils::t('Type'); ?></th>
				<th><?php echo Utils::t('Access'); ?></th>
				<th><?php echo Utils::t('Permissions'); ?></th>
				<th class="thright"><?php echo Utils::t('Manage'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr class="table-separation"><td colspan="8"></td></tr>
			<?php if($data['count'] > 0): ?>
				<?php $i = 0; ?>
				<?php foreach ($data['levels'] as $levelData): ?>
					<tr class="<?php echo ($i%2) ? 'even' : 'odd'; ?>">
						<td class="imgleft"><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/16/players.png" alt="" /><?php echo $levelData['name']; ?></td>
						<td><?php echo $levelData['type']; ?></td>
						<td><?php echo $levelData['allowed_access']; ?></td>
						<td><?php echo $levelData['allowed_permissions']; ?></td>
						<td class="checkbox"><input type="radio" name="level[]" value="<?php echo $levelData['name']; ?>" /></td>
					</tr>
					<?php $i++; ?>
				<?php endforeach; ?>
			<?php else: ?>
				<tr class="no-line">
					<td class="center" colspan="8"><?php echo Utils::t('No admin level'); ?></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
	
	<div class="options">
		<div class="fleft">
			<span class="nb-line">
				<?php echo $data['count'].' '.(($data['count'] > 1) ? Utils::t('admin levels') : Utils::t('admin level')); ?>
			</span>
		</div>
		<div class="fright">
			<div class="selected-files-label locked">
				<span class="selected-files-title"><?php echo Utils::t('For the selection'); ?></span>
				<div class="selected-files-option">
					<input class="button dark" type="submit" name="deletelevel" id="deletelevel" data-confirm-text="<?php echo Utils::t('Do you really want to remove this selection?'); ?>" value="<?php echo Utils::t('Delete'); ?>" />
					<input class="button dark" type="submit" name="duplicatelevel" id="duplicatelevel" value="<?php echo Utils::t('Duplicate'); ?>" />
					<input class="button dark" type="submit" name="editlevel" id="editlevel" value="<?php echo Utils::t('Modify'); ?>" />
				</div>
			</div>
		</div>
	</div>
	</form>
</section>