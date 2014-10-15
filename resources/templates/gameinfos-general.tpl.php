<fieldset class="gameinfos_general">
	<legend><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/16/restartrace.png" alt="" /><?php echo Utils::t('General'); ?></legend>
	<table>
		<?php if (AdminServAdminLevel::hasPermission('gameinfos_general_gamemode')): ?>
			<tr>
				<td class="key"><label for="NextGameMode"><?php echo Utils::t('Game mode'); ?></label></td>
				<?php if ($data['gameInfos']['curr'] != null): ?>
					<td class="value">
						<input class="text width2" type="text" name="CurrGameMode" id="CurrGameMode" readonly="readonly" value="<?php echo AdminServ::getGameModeName($data['gameInfos']['curr']['GameMode']); ?>" />
					</td>
				<?php endif; ?>
				<td class="value">
					<select class="width2" name="NextGameMode" id="NextGameMode">
						<?php echo AdminServUI::getGameModeList($data['gameInfos']['next']['GameMode']); ?>
					</select>
				</td>
				<td class="preview"></td>
			</tr>
		<?php endif; ?>
		<?php if (AdminServAdminLevel::hasPermission('gameinfos_general_options')): ?>
			<tr>
				<td class="key"><label for="NextChatTime"><?php echo Utils::t('Map end time'); ?> <span>(<?php echo Utils::t('sec'); ?>)</span></label></td>
				<?php if ($data['gameInfos']['curr'] != null): ?>
					<td class="value">
						<input class="text width2" type="text" name="CurrChatTime" id="CurrChatTime" readonly="readonly" value="<?php echo TimeDate::millisecToSec($data['gameInfos']['curr']['ChatTime'] + 8000); ?>" />
					</td>
				<?php endif; ?>
				<td class="value">
					<input class="text width2" type="number" min="0" name="NextChatTime" id="NextChatTime" value="<?php echo TimeDate::millisecToSec($data['gameInfos']['next']['ChatTime'] + 8000); ?>" />
				</td>
				<td class="preview"></td>
			</tr>
			<tr>
				<td class="key"><label for="NextFinishTimeout"><?php echo Utils::t('Round/lap end time'); ?> <span>(<?php echo Utils::t('sec'); ?>)</span></label></td>
				<?php if ($data['gameInfos']['curr'] != null): ?>
					<td class="value">
						<input class="text width2" type="text" name="CurrFinishTimeout" id="CurrFinishTimeout" readonly="readonly" value="<?php if ($data['gameInfos']['curr']['FinishTimeout'] == 0): echo Utils::t('Default').' (15'.Utils::t('sec'); elseif ($data['gameInfos']['curr']['FinishTimeout'] == 1): echo Utils::t('Auto (based on map)'); else: echo TimeDate::millisecToSec($data['gameInfos']['curr']['FinishTimeout']); endif; ?>" />
					</td>
				<?php endif; ?>
				<td class="value next">
					<select class="width2" name="NextFinishTimeout" id="NextFinishTimeout"<?php if ($data['gameInfos']['next']['FinishTimeout'] > 1): echo ' hidden="hidden"'; endif; ?>>
						<option value="0"<?php if ($data['gameInfos']['next']['FinishTimeout'] == 0): echo ' selected="selected"'; endif; ?>><?php echo Utils::t('Default');?> (15<?php echo Utils::t('sec'); ?>)</option>
						<option value="1"<?php if ($data['gameInfos']['next']['FinishTimeout'] == 1): echo ' selected="selected"'; endif; ?>><?php echo Utils::t('Auto (based on map)'); ?></option>
						<option value="more"><?php echo Utils::t('Choose time'); ?></option>
					</select>
					<input class="text width2" type="number" min="0" name="NextFinishTimeoutValue" id="NextFinishTimeoutValue" value="<?php if ($data['gameInfos']['next']['FinishTimeout'] > 1): echo TimeDate::millisecToSec($data['gameInfos']['next']['FinishTimeout']); endif; ?>"<?php if ($data['gameInfos']['next']['FinishTimeout'] < 2): echo ' hidden="hidden"'; endif; ?> />
				</td>
				<td class="preview"<?php if ($data['gameInfos']['next']['FinishTimeout'] < 2): echo ' hidden="hidden"'; endif; ?>>
					<a class="returnDefaultValue" href="?p=<?php echo USER_PAGE; ?>"><?php echo Utils::t('Return to the default value'); ?></a>
				</td>
			</tr>
		<?php endif; ?>
			<?php if (AdminServAdminLevel::hasPermission('gameinfos_general_warmup')): ?>
				<?php echo AdminServUI::getGameInfosField($data['gameInfos'], 'All WarmUp duration', 'AllWarmUpDuration'); ?>
			<?php endif; ?>
		<?php if (AdminServAdminLevel::hasPermission('gameinfos_general_options')): ?>
			<tr>
				<td class="key"><label for="NextDisableRespawn"><?php echo Utils::t('Respawn'); ?></label></td>
				<?php if ($data['gameInfos']['curr'] != null): ?>
					<td class="value">
						<input class="text width2" type="text" name="CurrDisableRespawn" id="CurrDisableRespawn" readonly="readonly" value="<?php echo ($data['gameInfos']['curr']['DisableRespawn'] === false) ? Utils::t('Enable') : Utils::t('Disable'); ?>" />
					</td>
				<?php endif; ?>
				<td class="value">
					<input class="text" type="checkbox" name="NextDisableRespawn" id="NextDisableRespawn" value=""<?php if ($data['gameInfos']['next']['DisableRespawn'] === false): echo ' checked="checked"'; endif; ?> />
				</td>
				<td class="preview"></td>
			</tr>
			<tr>
				<td class="key"><label for="NextForceShowAllOpponents"><?php echo Utils::t('Force show of all opponents'); ?></label></td>
				<?php if ($data['gameInfos']['curr'] != null): ?>
					<td class="value">
						<input class="text width2" type="text" name="CurrForceShowAllOpponents" id="CurrForceShowAllOpponents" readonly="readonly" value="<?php if ($data['gameInfos']['curr']['ForceShowAllOpponents'] == 0): echo Utils::t('Let player choose'); elseif ($data['gameInfos']['curr']['ForceShowAllOpponents'] == 1): echo Utils::t('All opponents'); else: echo $data['gameInfos']['curr']['ForceShowAllOpponents'].' '.Utils::t('minimal opponents'); endif; ?>" />
					</td>
				<?php endif; ?>
				<td class="value next">
					<select class="width2" name="NextForceShowAllOpponents" id="NextForceShowAllOpponents"<?php if ($data['gameInfos']['next']['ForceShowAllOpponents'] > 1): echo ' hidden="hidden"'; endif;?>>
						<option value="0"<?php if ($data['gameInfos']['next']['ForceShowAllOpponents'] == 0): echo ' selected="selected"'; endif; ?>><?php echo Utils::t('Let player choose'); ?></option>
						<option value="1"<?php if ($data['gameInfos']['next']['ForceShowAllOpponents'] == 1): echo ' selected="selected"'; endif; ?>><?php echo Utils::t('All opponents'); ?></option>
						<option value="more"><?php echo Utils::t('Choose opponents number'); ?></option>
					</select>
					<input class="text width2" type="text" name="NextForceShowAllOpponentsValue" id="NextForceShowAllOpponentsValue" value="<?php if ($data['gameInfos']['next']['ForceShowAllOpponents'] > 1): echo $data['gameInfos']['next']['ForceShowAllOpponents']; endif; ?>"<?php if ($data['gameInfos']['next']['ForceShowAllOpponents'] < 2): echo ' hidden="hidden"'; endif; ?> />
				</td>
				<td class="preview"<?php if ($data['gameInfos']['next']['ForceShowAllOpponents'] < 2): echo ' hidden="hidden"'; endif; ?>>
					<a class="returnDefaultValue" href="?p=<?php echo USER_PAGE; ?>"><?php echo Utils::t('Return to the default value'); ?></a>
				</td>
			</tr>
		<?php endif; ?>
	</table>
</fieldset>