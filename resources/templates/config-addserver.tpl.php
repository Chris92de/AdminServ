<section class="cadre">
	<h1><?php echo (defined('IS_SERVER_EDITION')) ? Utils::t('Edit server') : Utils::t('Add server'); ?></h1>
	<form method="post" action="?p=<?php echo USER_PAGE; if ($args['id'] !== -1): echo '&id='.$args['id']; endif; ?>">
		<div class="content">
			<fieldset>
				<legend><?php echo Utils::t('Connection information'); ?></legend>
				<table>
					<tr>
						<td class="key"><label for="addServerName"><?php echo Utils::t('Server name'); ?></label></td>
						<td class="value">
							<input class="text width3" type="text" name="addServerName" id="addServerName" value="<?php echo $data['name']; ?>" />
						</td>
						<td class="info">
							<?php echo Utils::t('Server name without color'); ?>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="addServerAddress"><?php echo Utils::t('Address'); ?></label></td>
						<td class="value">
							<input class="text width3" type="text" name="addServerAddress" id="addServerAddress" value="<?php echo $data['address']; ?>" />
						</td>
						<td class="info">
							<?php echo Utils::t('IP address or domain name'); ?>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="addServerPort"><?php echo Utils::t('XMLRPC port'); ?></label></td>
						<td class="value">
							<input class="text width3" type="number" name="addServerPort" id="addServerPort" value="<?php echo $data['port']; ?>" />
						</td>
						<td class="info">
							<?php echo Utils::t('Port for remote control'); ?>
						</td>
					</tr>
                    <tr>
                        <td class="key"><label for="addDisplayServPassword"><?php echo Utils::t('DisplayServ Password'); ?></label></td>
                        <td class="value">
                            <input class="text width3" type="text" name="addDisplayServPassword" id="addDisplayServPassword" value="<?php echo $data['ds_pw']; ?>" />
                        </td>
                        <td class="info">
                            <?php echo Utils::t('User password from dedicated config'); ?>
                        </td>
                    </tr>
				</table>
			</fieldset>
			
			<fieldset>
				<legend><?php echo Utils::t('Optionnal information'); ?></legend>
				<table>
					<tr>
						<td class="key"><label for="addServerMapsBasePath"><?php echo Utils::t('Maps base folder'); ?></label></td>
						<td class="value">
							<input class="text width3" type="text" name="addServerMapsBasePath" id="addServerMapsBasePath" value="<?php echo $data['mapsbasepath']; ?>" />
						</td>
						<td class="info">
							<?php echo Utils::t('Default path from maps folder for listing maps'); ?>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="addServerMatchSet"><?php echo Utils::t('Server MatchSettings'); ?></label></td>
						<td class="value">
							<input class="text width3" type="text" name="addServerMatchSet" id="addServerMatchSet" value="<?php echo $data['matchsettings']; ?>" />
						</td>
						<td class="info">
							<?php echo Utils::t('Current server MatchSettings name'); ?>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="addServerAdmLvlSA"><?php echo Utils::t('SuperAdmin level'); ?></label></td>
						<td class="value">
							<input class="text width3" type="text" name="addServerAdmLvlSA" id="addServerAdmLvlSA" value="<?php echo $data['adminlevel']['SuperAdmin']; ?>" />
						</td>
						<td rowspan="3" class="info">
							<?php echo Utils::t('Possible values for the admin level:'); ?><br />
							<?php echo Utils::t('all => all access'); ?><br />
							<?php echo Utils::t('local => local network access'); ?><br />
							<?php echo Utils::t('192.168.0.1, 192.168.0.2 => access to one or more IP address'); ?><br />
							<?php echo Utils::t('none => removed from the access list'); ?>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="addServerAdmLvlADM"><?php echo ucwords(Utils::t('Admin level')); ?></label></td>
						<td class="value">
							<input class="text width3" type="text" name="addServerAdmLvlADM" id="addServerAdmLvlADM" value="<?php echo $data['adminlevel']['Admin']; ?>" />
						</td>
					</tr>
					<tr>
						<td class="key"><label for="addServerAdmLvlUSR"><?php echo Utils::t('User level'); ?></label></td>
						<td class="value">
							<input class="text width3" type="text" name="addServerAdmLvlUSR" id="addServerAdmLvlUSR" value="<?php echo $data['adminlevel']['User']; ?>" />
						</td>
					</tr>
				</table>
			</fieldset>
		</div>
		
		<div class="fright save">
			<input class="button light" type="submit" name="saveserver" id="saveserver" value="<?php echo Utils::t('Save'); ?>" />
		</div>
	</form>
</section>