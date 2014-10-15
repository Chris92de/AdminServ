<section class="cadre">
	<h1><?php echo Utils::t('Server options'); ?></h1>
	<form method="post" action="?p=<?php echo USER_PAGE; ?>">
		<div class="content">
			<?php if (AdminServAdminLevel::hasPermission(array('srvopts_general_name', 'srvopts_general_comment', 'srvopts_general_serverpassword', 'srvopts_general_spectatorpassword', 'srvopts_general_nbplayers', 'srvopts_general_nbspectators'))): ?>
				<fieldset class="srvopts_general">
					<legend><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/16/servers.png" alt="" /><?php echo Utils::t('General'); ?></legend>
					<table>
						<?php if (AdminServAdminLevel::hasPermission('srvopts_general_name')): ?>
							<tr class="serverName">
								<td class="key"><label for="ServerName"><?php echo Utils::t('Server name'); ?></label></td>
								<td class="value" colspan="3">
									<input class="text width3" type="text" name="Name" id="ServerName" maxlength="75" value="<?php echo $data['srvOpt']['Name']; ?>" />
								</td>
								<td class="preview">[<span id="serverNameHtml"><?php echo $data['srvOpt']['NameHtml']; ?></span>]</td>
							</tr>
						<?php endif; ?>
						<?php if (AdminServAdminLevel::hasPermission('srvopts_general_comment')): ?>
							<tr class="serverComment">
								<td class="key"><label for="ServerComment"><?php echo Utils::t('Comment'); ?></label></td>
								<td class="value" colspan="3">
									<textarea class="width3" name="Comment" id="ServerComment" maxlength="255"><?php echo $data['srvOpt']['Comment']; ?></textarea>
								</td>
								<td class="preview">[<span id="serverCommentHtml"><?php echo $data['srvOpt']['CommentHtml']; ?></span>]</td>
							</tr>
						<?php endif; ?>
						<?php if (AdminServAdminLevel::hasPermission('srvopts_general_playerpassword')): ?>
							<tr>
								<td class="key"><label for="ServerPassword"><?php echo Utils::t('Player password'); ?></label></td>
								<td class="value" colspan="3">
									<input class="text width3" type="text" name="Password" id="ServerPassword" value="<?php echo $data['srvOpt']['Password']; ?>" />
								</td>
								<td class="preview"></td>
							</tr>
						<?php endif; ?>
						<?php if (AdminServAdminLevel::hasPermission('srvopts_general_spectatorpassword')): ?>
							<tr>
								<td class="key"><label for="SpectatorPassword"><?php echo Utils::t('Spectator password'); ?></label></td>
								<td class="value" colspan="3">
									<input class="text width3" type="text" name="PasswordForSpectator" id="SpectatorPassword" value="<?php echo $data['srvOpt']['PasswordForSpectator']; ?>" />
								</td>
								<td class="preview"></td>
							</tr>
						<?php endif; ?>
						<?php if (AdminServAdminLevel::hasPermission('srvopts_general_nbplayers')): ?>
							<tr>
								<td class="key"><label for="NextMaxPlayers"><?php echo Utils::t('Nb max of players'); ?></label></td>
								<td class="value col2">
									<input class="text width1" type="text" name="CurrentMaxPlayers" id="CurrentMaxPlayers" readonly="readonly" value="<?php echo $data['srvOpt']['CurrentMaxPlayers']; ?>" />
								</td>
								<td class="key col3"><label for="NextMaxPlayers"><?php echo Utils::t('Next value'); ?></label></td>
								<td class="value">
									<input class="text width1" type="number" min="0" name="NextMaxPlayers" id="NextMaxPlayers" value="<?php echo $data['srvOpt']['NextMaxPlayers']; ?>" />
								</td>
								<td class="preview"></td>
							</tr>
						<?php endif; ?>
						<?php if (AdminServAdminLevel::hasPermission('srvopts_general_nbspectators')): ?>
							<tr>
								<td class="key"><label for="NextMaxSpectators"><?php echo Utils::t('Nb max of spectators'); ?></label></td>
								<td class="value col2">
									<input class="text width1" type="text" name="CurrentMaxSpectators" id="CurrentMaxSpectators" readonly="readonly" value="<?php echo $data['srvOpt']['CurrentMaxSpectators']; ?>" />
								</td>
								<td class="key col3"><label for="NextMaxSpectators"><?php echo Utils::t('Next value'); ?></label></td>
								<td class="value">
									<input class="text width1" type="number" min="0" name="NextMaxSpectators" id="NextMaxSpectators" value="<?php echo $data['srvOpt']['NextMaxSpectators']; ?>" />
								</td>
								<td class="preview"></td>
							</tr>
						<?php endif; ?>
					</table>
				</fieldset>
			<?php endif; ?>
			
			<?php if (AdminServAdminLevel::hasPermission('srvopts_advanced')): ?>
				<fieldset class="srvopts_advanced">
					<legend><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/16/options.png" alt="" /><?php echo Utils::t('Advanced'); ?></legend>
					<table>
						<tr>
							<td class="key"><label for="IsP2PUpload"><?php echo Utils::t('P2P Upload'); ?></label></td>
							<td class="value col2">
								<input class="text" type="checkbox" name="IsP2PUpload" id="IsP2PUpload"<?php if ($data['srvOpt']['IsP2PUpload'] != 0): echo ' checked="checked"'; endif; ?> value="" />
							</td>
							<td class="key col3"><label for="IsP2PDownload"><?php echo Utils::t('P2P Download'); ?></label></td>
							<td class="value">
								<input class="text" type="checkbox" name="IsP2PDownload" id="IsP2PDownload"<?php if ($data['srvOpt']['IsP2PDownload'] != 0): echo ' checked="checked"'; endif; ?> value="" />
							</td>
							<td class="preview"></td>
						</tr>
						<tr>
							<td class="key"><label for="NextLadderMode"><?php echo Utils::t('Ladder mode'); ?></label></td>
							<td class="value col2">
								<input class="text width1" type="text" name="CurrentLadderMode" id="CurrentLadderMode" readonly="readonly" value="<?php echo $data['srvOpt']['CurrentLadderModeName']; ?>" />
							</td>
							<td class="key col3"><label for="NextLadderMode"><?php echo Utils::t('Next value'); ?></label></td>
							<td class="value">
								<select class="width1" name="NextLadderMode" id="NextLadderMode">
									<option value="0"<?php if ($data['srvOpt']['NextLadderMode'] == 0): echo ' selected="selected"'; endif; ?>><?php echo Utils::t('Inactive'); ?></option>
									<option value="1"<?php if ($data['srvOpt']['NextLadderMode'] == 1): echo ' selected="selected"'; endif; ?>><?php echo Utils::t('Forced'); ?></option>
								</select>
							</td>
							<td class="preview"></td>
						</tr>
						<tr>
							<td class="key"><label for="NextVehicleNetQuality"><?php echo Utils::t('Vehicles quality'); ?></label></td>
							<td class="value col2">
								<input class="text width1" type="text" name="CurrentVehicleNetQuality" id="CurrentVehicleNetQuality" readonly="readonly" value="<?php echo $data['srvOpt']['CurrentVehicleNetQualityName']; ?>" />
							</td>
							<td class="key col3"><label for="NextVehicleNetQuality"><?php echo Utils::t('Next value'); ?></label></td>
							<td class="value">
								<select class="width1" name="NextVehicleNetQuality" id="NextVehicleNetQuality">
									<option value="0"<?php if ($data['srvOpt']['NextVehicleNetQuality'] == 0): echo ' selected="selected"'; endif; ?>><?php echo Utils::t('Fast'); ?></option>
									<option value="1"<?php if ($data['srvOpt']['NextVehicleNetQuality'] == 1): echo ' selected="selected"'; endif; ?>><?php echo Utils::t('High'); ?></option>
								</select>
							</td>
							<td class="preview"></td>
						</tr>
						<tr>
							<td class="key"><label for="NextCallVoteTimeOut"><?php echo Utils::t('Vote expiration'); ?> <span>(<?php echo Utils::t('sec'); ?>)</span></label></td>
							<td class="value col2">
								<input class="text width1" type="text" name="CurrentCallVoteTimeOut" id="CurrentCallVoteTimeOut" readonly="readonly" value="<?php echo TimeDate::millisecToSec($data['srvOpt']['CurrentCallVoteTimeOut']); ?>" />
							</td>
							<td class="key col3"><label for="NextCallVoteTimeOut"><?php echo Utils::t('Next value'); ?></label></td>
							<td class="value">
								<input class="text width1" type="number" min="0" name="NextCallVoteTimeOut" id="NextCallVoteTimeOut" value="<?php echo TimeDate::millisecToSec($data['srvOpt']['NextCallVoteTimeOut']); ?>" />
							</td>
							<td class="preview"></td>
						</tr>
						<tr>
							<td class="key"><label for="CallVoteRatio"><?php echo Utils::t('Vote ratio'); ?></label></td>
							<td class="value" colspan="4">
								<select name="callVoteRatioDisabled" id="callVoteRatioDisabled"<?php if ($data['srvOpt']['CallVoteRatio'] > -1): echo ' hidden="hidden"'; endif; ?>>
									<option value="-1"<?php if ($data['srvOpt']['CallVoteRatio'] == -1): echo ' selected="selected"'; endif; ?>><?php echo Utils::t('Disable'); ?></option>
									<option value="0"<?php if ($data['srvOpt']['CallVoteRatio'] > -1): echo ' selected="selected"'; endif; ?>><?php echo Utils::t('Enable'); ?></option>
								</select>
								<input class="text" type="number" min="0" max="1" step=".1" name="CallVoteRatio" id="CallVoteRatio" value="<?php echo $data['srvOpt']['CallVoteRatio']; ?>"<?php if ($data['srvOpt']['CallVoteRatio'] == -1): echo ' hidden="hidden"'; endif; ?> />
								<a class="returnDefaultValue" id="resetCallVoteRatio" href="?p=<?php echo USER_PAGE; ?>"<?php if ($data['srvOpt']['CallVoteRatio'] == -1): echo ' hidden="hidden"'; endif; ?>><?php echo Utils::t('Disable vote ratio'); ?></a>
							</td>
						</tr>
						<?php if (SERVER_VERSION_NAME == 'ManiaPlanet'): ?>
							<tr>
								<td class="key"><label for="ClientInputsMaxLatency"><?php echo Utils::t('Client inputs max latency'); ?></label></td>
								<td class="value" colspan="4">
									<select name="ClientInputsMaxLatency" id="ClientInputsMaxLatency"<?php if ($data['srvOpt']['ClientInputsMaxLatency'] > 0): echo ' hidden="hidden"'; endif; ?>>
										<option value="0"><?php echo Utils::t('Automatic'); ?></option>
										<option value="more"><?php echo Utils::t('Choose number'); ?></option>
									</select>
									<input class="text" type="number" min="0" name="ClientInputsMaxLatencyValue" id="ClientInputsMaxLatencyValue" value="<?php echo $data['srvOpt']['ClientInputsMaxLatency']; ?>"<?php if ($data['srvOpt']['ClientInputsMaxLatency'] == 0): echo ' hidden="hidden"'; endif; ?> />
									<a class="returnDefaultValue" id="resetClientInputsMaxLarency" href="?p=<?php echo USER_PAGE; ?>"<?php if ($data['srvOpt']['ClientInputsMaxLatency'] == 0): echo ' hidden="hidden"'; endif; ?>><?php echo Utils::t('Return to the default value'); ?></a>
								</td>
							</tr>
						<?php endif; ?>
						<tr>
							<td class="key"><label for="HideServer"><?php echo Utils::t('Hidden server'); ?></label></td>
							<td class="value" colspan="4">
								<input class="text" type="checkbox" name="HideServer" id="HideServer"<?php if ($data['srvOpt']['HideServer'] != 0): echo ' checked="checked"'; endif; ?> value="" />
							</td>
						</tr>
						<tr>
							<td class="key"><label for="AllowMapDownload"><?php echo Utils::t('Map download'); ?></label></td>
							<td class="value" colspan="4">
								<input class="text" type="checkbox" name="AllowMapDownload" id="AllowMapDownload"<?php if (SERVER_VERSION_NAME == 'TmForever' && $data['srvOpt']['AllowChallengeDownload'] != 0): echo ' checked="checked"'; elseif (SERVER_VERSION_NAME == 'ManiaPlanet' && $data['srvOpt']['AllowMapDownload'] != 0): echo ' checked="checked"'; endif; ?> value="" />
							</td>
						</tr>
						<tr<?php if (AdminServAdminLevel::isType('Admin') && !AdminServAdminLevel::isType('SuperAdmin')): echo ' hidden="hidden"'; endif; ?>>
							<td class="key"><label for="AutoSaveReplays"><?php echo Utils::t('Replays auto save'); ?></label></td>
							<td class="value" colspan="4">
								<input class="text" type="checkbox" name="AutoSaveReplays" id="AutoSaveReplays"<?php if ($data['srvOpt']['AutoSaveReplays'] != 0): echo ' checked="checked"'; endif; ?> value="" />
							</td>
						</tr>
						<tr>
							<td class="key"><label for="BuddyNotification"><?php echo Utils::t('Buddy notification'); ?></label></td>
							<td class="value" colspan="4">
								<input class="text" type="checkbox" name="BuddyNotification" id="BuddyNotification"<?php if ($data['srvOpt']['BuddyNotification'] != 0): echo ' checked="checked"'; endif; ?> value="" />
							</td>
						</tr>
						<?php if (SERVER_VERSION_NAME == 'ManiaPlanet'): ?>
							<tr>
								<td class="key"><label for="DisableHorns"><?php echo Utils::t('Disable horns'); ?></label></td>
								<td class="value" colspan="4">
									<input class="text" type="checkbox" name="DisableHorns" id="DisableHorns"<?php if ($data['srvOpt']['DisableHorns'] != 0): echo ' checked="checked"'; endif; ?> value="" />
								</td>
							</tr>
						<?php endif; ?>
					</table>
				</fieldset>
			<?php endif; ?>
			
			<?php if (AdminServAdminLevel::hasPermission('srvopts_adminlevelpassword', USER_ADMINLEVEL, 'SuperAdmin')): ?>
				<fieldset class="srvopts_changeauthpassword">
					<legend><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/16/players.png" alt="" /><?php echo Utils::t('Change authentication password'); ?></legend>
					<table>
						<tr>
							<td class="key"><label for="ChangeAuthLevel"><?php echo Utils::t('Admin level'); ?></label></td>
							<td class="value col2">
								<select name="ChangeAuthLevel" id="ChangeAuthLevel">
									<?php if (isset($data['adminLevels']['levels']) && !empty($data['adminLevels']['levels'])): ?>
										<?php foreach ($data['adminLevels']['levels'] as $levelId => $levelName): ?>
											<option value="<?php echo $levelName; ?>"><?php echo $levelName; ?></option>
										<?php endforeach; ?>
									<?php endif; ?>
								</select>
								<span class="changeauthpassword-arrow"> </span>
								<input class="text" type="password" name="ChangeAuthPassword" id="ChangeAuthPassword" value="" />
							</td>
						</tr>
					</table>
				</fieldset>
			<?php endif; ?>
			
			<?php if (defined('IS_LOCAL') && IS_LOCAL && AdminServAdminLevel::hasPermission('srvopts_importexport')): ?>
				<fieldset class="srvopts_importexport">
					<legend><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/16/rt_team.png" alt="" /><?php echo Utils::t('Manage server options'); ?></legend>
					<table>
						<tr>
							<td class="key"><label for="srvoptsImport"><?php echo Utils::t('Import'); ?></label></td>
							<td class="value col2">
								<input class="text" type="radio" name="srvoptsImportExport" id="srvoptsImport" value="Import" />
								<select name="srvoptsImportName" id="srvoptsImportName" hidden="hidden">
									<?php if (isset($data['srvoptsConfigFiles']['files']) && !empty($data['srvoptsConfigFiles']['files'])): ?>
										<?php foreach ($data['srvoptsConfigFiles']['files'] as $file): ?>
											<option value="<?php echo $file['filename']; ?>"><?php echo substr($file['filename'], 0, -4); ?></option>
										<?php endforeach; ?>
									<?php else: ?>
										<option value="none"><?php echo Utils::t('No export available'); ?></option>
									<?php endif; ?>
								</select>
							</td>
						</tr>
						<tr>
							<td class="key"><label for="srvoptsExport"><?php echo Utils::t('Export'); ?></label></td>
							<td class="value col2">
								<input class="text" type="radio" name="srvoptsImportExport" id="srvoptsExport" value="Export" />
								<input class="text" hidden="hidden" type="text" name="srvoptsExportName" id="srvoptsExportName" value="<?php echo SERVER_LOGIN; ?>" />
							</td>
						</tr>
					</table>
				</fieldset>
			<?php endif; ?>
		</div>
		<div class="fright save">
			<input class="button light" type="submit" name="savesrvopts" id="savesrvopts" value="<?php echo Utils::t('Save'); ?>" />
		</div>
	</form>
</section>