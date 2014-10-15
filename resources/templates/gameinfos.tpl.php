<section class="cadre">
	<h1><?php echo Utils::t('Game information'); ?></h1>
	<form method="post" action="?p=<?php echo USER_PAGE; ?>">
		<div class="content gameinfos">
			<?php if (AdminServAdminLevel::hasPermission(array('gameinfos_general_gamemode', 'gameinfos_general_warmup', 'gameinfos_general_options'))): ?>
				<?php echo AdminServUI::getTemplate('gameinfos-general'); ?>
			<?php endif; ?>
			
			<?php if (SERVER_VERSION_NAME == 'ManiaPlanet' && AdminServ::checkDisplayTeamMode($data['gameInfos']['next']['GameMode'], $data['gameInfos']['next']['ScriptName']) && AdminServAdminLevel::hasPermission('gameinfos_teams_options')): ?>
				<fieldset class="gameinfos_teaminfos">
					<legend><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/16/players.png" alt="" /><?php echo Utils::t('Team infos'); ?></legend>
					<table>
						<tr>
							<td class="key"><label for="teamInfo1Name"><?php echo Utils::t('Team 1'); ?></label></td>
							<td class="value">
								<input class="text width2" type="text" name="teamInfo1Name" id="teamInfo1Name" value="<?php echo $data['teamInfo']['team1']['name']; ?>" />
								<div class="colorSelectorWrapper">
									<div id="colorPickerTeam1" class="colorSelector" title="<?php echo Utils::t('Color'); ?>"></div>
									<input type="hidden" name="teamInfo1Color" id="teamInfo1Color" value="<?php echo $data['teamInfo']['team1']['color']; ?>" />
									<input type="hidden" name="teamInfo1ColorHex" id="teamInfo1ColorHex" value="<?php echo $data['teamInfo']['team1']['colorhex']; ?>" />
								</div>
							</td>
							<td class="value">
								<input class="text width2" type="text" name="teamInfo1Country" id="teamInfo1Country" value="<?php echo $data['teamInfo']['team1']['country']; ?>" />
							</td>
							<td class="preview"></td>
						</tr>
						<tr>
							<td class="key"><label for="teamInfo2Name"><?php echo Utils::t('Team 2'); ?></label></td>
							<td class="value">
								<input class="text width2" type="text" name="teamInfo2Name" id="teamInfo2Name" value="<?php echo $data['teamInfo']['team2']['name']; ?>" />
								<div class="colorSelectorWrapper">
									<div id="colorPickerTeam2" class="colorSelector" title="<?php echo Utils::t('Color'); ?>"></div>
									<input type="hidden" name="teamInfo2Color" id="teamInfo2Color" value="<?php echo $data['teamInfo']['team2']['color']; ?>" />
									<input type="hidden" name="teamInfo2ColorHex" id="teamInfo2ColorHex" value="<?php echo $data['teamInfo']['team2']['colorhex']; ?>" />
								</div>
							</td>
							<td class="value">
								<input class="text width2" type="text" name="teamInfo2Country" id="teamInfo2Country" value="<?php echo $data['teamInfo']['team2']['country']; ?>" />
							</td>
							<td class="preview"></td>
						</tr>
					</table>
				</fieldset>
			<?php endif; ?>
			
			<?php if (AdminServAdminLevel::hasPermission('gameinfos_gamemode_options')): ?>
				<?php echo AdminServUI::getTemplate('gameinfos-gamemode'); ?>
			<?php endif; ?>
		</div>
		
		<?php if (SERVER_MATCHSET && AdminServAdminLevel::hasPermission('maps_matchsettings_save')): ?>
			<div class="fleft options-checkbox">
				<input class="text inline" type="checkbox" name="SaveCurrentMatchSettings" id="SaveCurrentMatchSettings"<?php if (AdminServConfig::AUTOSAVE_MATCHSETTINGS === true): echo ' checked="checked"'; endif; ?> value="" /><label for="SaveCurrentMatchSettings" title="<?php echo SERVER_MATCHSET; ?>"><?php echo Utils::t('Save the current MatchSettings'); ?></label>
			</div>
		<?php endif; ?>
		<div class="fright save">
			<input class="button light" type="submit" name="savegameinfos" id="savegameinfos" value="<?php echo Utils::t('Save'); ?>" />
		</div>
	</form>
</section>