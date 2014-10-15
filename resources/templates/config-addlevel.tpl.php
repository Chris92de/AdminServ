<section class="cadre">
	<h1><?php echo (defined('IS_LEVEL_EDITION')) ? Utils::t('Edit level') : Utils::t('Add level'); ?></h1>
	<form method="post" action="?p=<?php echo USER_PAGE; if ($args['id'] !== -1): echo '&id='.$args['id']; endif; ?>">
		<div class="content">
			<fieldset>
				<legend><?php echo Utils::t('Admin level'); ?></legend>
				<table>
					<tr>
						<td class="key"><label for="addLevelName"><?php echo Utils::t('Level name'); ?></label></td>
						<td class="value">
							<input class="text width3" type="text" name="addLevelName" id="addLevelName" value="<?php echo $data['name']; ?>" />
						</td>
						<td class="info"></td>
					</tr>
					<tr>
						<td class="key"><label for="addLevelType"><?php echo Utils::t('Level type'); ?></label></td>
						<td class="value">
							<?php if (!empty($data['types'])): ?>
								<select name="addLevelType" id="addLevelType">
									<?php foreach ($data['types'] as $type): ?>
										<option value="<?php echo $type; ?>"<?php echo (defined('IS_LEVEL_EDITION') && $type == $data['adminlevel']['type']) ? ' selected="selected"' : ''; ?>><?php echo $type; ?></option>
									<?php endforeach; ?>
								</select>
							<?php endif; ?>
						</td>
						<td class="info"></td>
					</tr>
				</table>
			</fieldset>
			
			<fieldset class="adminlevelSortableList">
				<legend><?php echo Utils::t('Access'); ?></legend>
				<table>
					<tr>
						<td class="key"><?php echo Utils::t('Add access'); ?></td>
						<td class="value">
							<h3><?php echo Utils::t('All access'); ?></h3>
							<ul id="defaultAccess" class="adminlevelAccessList">
								<?php if (!empty($data['access']['default'])): ?>
									<?php foreach ($data['access']['default'] as $accessName): ?>
										<li class="defaultName"><?php echo $accessName; ?></li>
									<?php endforeach; ?>
								<?php endif; ?>
							</ul>
						</td>
						<td class="value">
							<h3><?php echo Utils::t('Selected access'); ?></h3>
							<ul id="selectedAccess" class="adminlevelAccessList">
								<?php if (!empty($data['access']['selected'])): ?>
									<?php foreach ($data['access']['selected'] as $accessName): ?>
										<li class="selectedName"><?php echo $accessName; ?></li>
									<?php endforeach; ?>
								<?php endif; ?>
							</ul>
							<input type="hidden" name="selectedAccessSortList" id="selectedAccessSortList" value="" />
						</td>
					</tr>
				</table>
			</fieldset>
			
			<fieldset class="adminlevelSortableList">
				<legend><?php echo Utils::t('Permissions'); ?></legend>
				<table>
					<tr>
						<td class="key"><?php echo Utils::t('Add permission'); ?></td>
						<td class="value">
							<h3><?php echo Utils::t('All permissions'); ?></h3>
							<ul id="defaultPermission" class="adminlevelPermissionList">
								<?php if (!empty($data['permission']['default'])): ?>
									<?php foreach ($data['permission']['default'] as $permissionName): ?>
										<li class="defaultName"><?php echo $permissionName; ?></li>
									<?php endforeach; ?>
								<?php endif; ?>
							</ul>
						</td>
						<td class="value">
							<h3><?php echo Utils::t('Selected permissions'); ?></h3>
							<ul id="selectedPermission" class="adminlevelPermissionList">
								<?php if (!empty($data['permission']['selected'])): ?>
									<?php foreach ($data['permission']['selected'] as $permissionName): ?>
										<li class="selectedName"><?php echo $permissionName; ?></li>
									<?php endforeach; ?>
								<?php endif; ?>
							</ul>
							<input type="hidden" name="selectedPermissionSortList" id="selectedPermissionSortList" value="" />
						</td>
					</tr>
				</table>
			</fieldset>
		</div>
		
		<div class="fright save">
			<input class="button light" type="submit" name="savelevel" id="savelevel" value="<?php echo Utils::t('Save'); ?>" />
		</div>
	</form>
</section>