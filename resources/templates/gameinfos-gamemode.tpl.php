<?php if (SERVER_VERSION_NAME == 'ManiaPlanet'): ?>
	<fieldset id="gameMode-script" class="gameinfos_script" hidden="hidden">
		<legend><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/16/options.png" alt="" /><?php echo AdminServ::getGameModeName(0); ?></legend>
		<table class="game_infos">
			<tr>
				<td class="key"><label for="NextScriptName"><?php echo Utils::t('Script name'); ?></label></td>
				<?php if ($data['gameInfos']['curr'] != null): ?>
					<td class="value">
						<input class="text width2" type="text" name="CurrScriptName" id="CurrScriptName" readonly="readonly" value="<?php echo $data['gameInfos']['curr']['ScriptName']; ?>" />
					</td>
				<?php endif; ?>
				<?php if ($data['gameInfos']['next'] != null): ?>
					<td class="value">
						<input class="text width2" type="text" name="NextScriptName" id="NextScriptName" value="<?php echo $data['gameInfos']['next']['ScriptName']; ?>" />
					</td>
					<td class="preview">
					<?php if ($data['gameInfos']['next']['GameMode'] == 0): ?>
						<a id="getScriptSettings" href="" data-infotext="<?php echo Utils::t('Script settings updated.'); ?>"><?php echo Utils::t('Script settings'); ?></a>
					<?php endif; ?>
					</td>
				<?php endif; ?>
			</tr>
		</table>
	</fieldset>
	<?php if ($data['gameInfos']['next']['GameMode'] == 0): ?>
		<div id="getScriptSettingsDialog" data-title="<?php echo Utils::t('Script settings'); ?>" data-cancel="<?php echo Utils::t('Cancel'); ?>" data-save="<?php echo Utils::t('Save'); ?>" hidden="hidden">
			<div id="dialogScriptInfo">
				<h2><?php echo Utils::t('Script info'); ?></h2>
				<div class="content">
					<table>
						<tbody>
							<tr>
								<td class="key"><?php echo Utils::t('Name'); ?></td>
								<td class="value" id="dialogScriptInfoName"></td>
							</tr>
							<tr>
								<td class="key"><?php echo Utils::t('Compatible map types'); ?></td>
								<td class="value" id="dialogScriptInfoCompatibleMapTypes"></td>
							</tr>
							<tr class="dialogScriptInfoDesc" hidden="hidden">
								<td class="key"><?php echo Utils::t('Description'); ?></td>
								<td class="value" id="dialogScriptInfoDesc"></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div id="dialogScriptSettings">
				<h2><?php echo Utils::t('Script parameters'); ?></h2>
				<table>
					<thead>
						<tr>
							<th class="thleft"><?php echo Utils::t('Name'); ?></th>
							<th><?php echo Utils::t('Value'); ?></th>
							<th class="thright"><?php echo Utils::t('Description'); ?></th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	<?php endif; ?>
<?php endif; ?>

<fieldset id="gameMode-rounds" class="gameinfos_round" hidden="hidden">
	<legend><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/16/rt_rounds.png" alt="" /><?php echo AdminServ::getGameModeName(1, true); ?></legend>
	<table class="game_infos">
		<tr>
			<td class="key"><label for="NextRoundsUseNewRules"><?php echo Utils::t('Use new rules'); ?></label></td>
			<?php if ($data['gameInfos']['curr'] != null): ?>
				<td class="value">
					<input class="text width2" type="text" name="CurrRoundsUseNewRules" id="CurrRoundsUseNewRules" readonly="readonly" value="<?php echo ($data['gameInfos']['curr']['RoundsUseNewRules'] != null) ? Utils::t('Enable') : Utils::t('Disable'); ?>" />
				</td>
			<?php endif; ?>
			<td class="value">
				<input class="text" type="checkbox" name="NextRoundsUseNewRules" id="NextRoundsUseNewRules" value=""<?php if ($data['gameInfos']['next']['RoundsUseNewRules'] != null): echo ' checked="checked"'; endif; ?> />
			</td>
			<td class="preview"></td>
		</tr>
		<?php echo AdminServUI::getGameInfosField($data['gameInfos'], 'Points limit', 'RoundsPointsLimit'); ?>
		<?php echo AdminServUI::getGameInfosField($data['gameInfos'], 'Custom points limit', 'RoundCustomPoints'); ?>
		<?php echo AdminServUI::getGameInfosField($data['gameInfos'], 'Force laps', 'RoundsForcedLaps'); ?>
	</table>
</fieldset>

<fieldset id="gameMode-timeattack" class="gameinfos_timeattack" hidden="hidden">
	<legend><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/16/rt_timeattack.png" alt="" /><?php echo AdminServ::getGameModeName(2, true); ?></legend>
	<table class="game_infos">
		<tr>
			<td class="key"><label for="NextTimeAttackLimit"><?php echo Utils::t('Time limit'); ?> <span>(<?php echo Utils::t('sec'); ?>)</span></label></td>
			<?php if ($data['gameInfos']['curr'] != null): ?>
				<td class="value">
					<input class="text width2" type="text" name="CurrTimeAttackLimit" id="CurrTimeAttackLimit" readonly="readonly" value="<?php echo TimeDate::millisecToSec($data['gameInfos']['curr']['TimeAttackLimit']); ?>" />
				</td>
			<?php endif; ?>
			<td class="value">
				<input class="text width2" type="number" min="0" name="NextTimeAttackLimit" id="NextTimeAttackLimit" value="<?php echo TimeDate::millisecToSec($data['gameInfos']['next']['TimeAttackLimit']); ?>" />
			</td>
			<td class="preview"></td>
		</tr>
		<?php echo AdminServUI::getGameInfosField($data['gameInfos'], 'Start synchronization period', 'TimeAttackSynchStartPeriod'); ?>
	</table>
</fieldset>

<fieldset id="gameMode-team" class="gameinfos_team" hidden="hidden">
	<legend><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/16/rt_team.png" alt="" /><?php echo AdminServ::getGameModeName(3, true); ?></legend>
	<table class="game_infos">
		<tr>
			<td class="key"><label for="NextTeamUseNewRules"><?php echo Utils::t('Use new rules'); ?></label></td>
			<?php if ($data['gameInfos']['curr'] != null): ?>
				<td class="value">
					<input class="text width2" type="text" name="CurrTeamUseNewRules" id="CurrTeamUseNewRules" readonly="readonly" value="<?php echo ($data['gameInfos']['curr']['TeamUseNewRules'] != null) ? Utils::t('Enable') : Utils::t('Disable'); ?>" />
				</td>
			<?php endif; ?>
			<td class="value">
				<input class="text" type="checkbox" name="NextTeamUseNewRules" id="NextTeamUseNewRules" value=""<?php if ($data['gameInfos']['next']['TeamUseNewRules'] != null): echo ' checked="checked"'; endif; ?> />
			</td>
			<td class="preview"></td>
		</tr>
		<?php echo AdminServUI::getGameInfosField($data['gameInfos'], 'Points limit', 'TeamPointsLimit'); ?>
		<?php echo AdminServUI::getGameInfosField($data['gameInfos'], 'Maximal points', 'TeamMaxPoints'); ?>
	</table>
</fieldset>

<fieldset id="gameMode-laps" class="gameinfos_laps" hidden="hidden">
	<legend><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/16/rt_laps.png" alt="" /><?php echo AdminServ::getGameModeName(4, true); ?></legend>
	<table class="game_infos">
		<?php echo AdminServUI::getGameInfosField($data['gameInfos'], 'Number of laps', 'LapsNbLaps'); ?>
		<?php echo AdminServUI::getGameInfosField($data['gameInfos'], Utils::t('Time limit').' <span>('.Utils::t('sec').')</span>', 'LapsTimeLimit'); ?>
	</table>
</fieldset>

<fieldset id="gameMode-cup" class="gameinfos_cup" hidden="hidden">
	<legend><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/16/rt_cup.png" alt="" /><?php echo AdminServ::getGameModeName(5); ?></legend>
	<table class="game_infos">
		<?php echo AdminServUI::getGameInfosField($data['gameInfos'], 'Points limit', 'CupPointsLimit'); ?>
		<?php echo AdminServUI::getGameInfosField($data['gameInfos'], 'Rounds per map', 'CupRoundsPerMap'); ?>
		<?php echo AdminServUI::getGameInfosField($data['gameInfos'], 'Number of winners', 'CupNbWinners'); ?>
		<?php echo AdminServUI::getGameInfosField($data['gameInfos'], 'All WarmUp duration', 'CupWarmUpDuration'); ?>
	</table>
</fieldset>