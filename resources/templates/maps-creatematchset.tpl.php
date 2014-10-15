<section class="maps hasMenu hasFolders">
	<section class="cadre left menu">
		<?php echo AdminServUI::getMapsMenuList(); ?>
	</section>
	
	<section class="cadre middle folders">
		<?php echo AdminServUI::getMapsDirectoryList($data['currentDir'], $args['directory']); ?>
	</section>
	
	<form method="post" action="?p=<?php echo USER_PAGE.$data['hasDirectory']; ?>">
	<section class="cadre right creatematchset">
		<h1><?php echo $data['pageTitle'].' '.Utils::t('a MatchSettings'); ?></h1>
		<div class="title-detail">
			<ul>
				<li class="last path"><?php echo $data['mapsDirectoryPath'].$args['directory']; ?></li>
			</ul>
		</div>
		
		<h2><?php echo Utils::t('MatchSettings name'); ?></h2>
		<input class="text" type="text" name="matchSettingName" id="matchSettingName" value="<?php echo $data['matchSettings']['name']; ?>" />
		<p class="ui-state-error" id="matchSettingNameExists" hidden="hidden"><span class="ui-icon ui-icon-alert"></span><?php echo Utils::t('The MatchSettings name already exist! It will be overwritten.'); ?></p>
		
		<h2><?php echo Utils::t('Maps'); ?></h2>
		<div class="content maps">
			<fieldset>
				<div class="mapsSelection">
					<select name="mapsDirectoryList" id="mapsDirectoryList">
						<option value="currentServerSelection"><?php echo Utils::t('Server selection'); ?></option>
						<option value="<?php echo $data['mapsDirectoryPath']; ?>"><?php echo Utils::t('Root'); ?></option>
						<?php if (!empty($data['directoryList'])): ?>
							<?php foreach ($data['directoryList'] as $dir): ?>
								<option value="<?php echo $dir['path']; ?>"><?php echo $dir['level'].$dir['name']; ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
					<input class="button light" type="button" name="mapImportSelection" id="mapImportSelection" value="<?php echo Utils::t('Make selection'); ?>" />
					<input class="button light" type="button" name="mapImport" id="mapImport" value="<?php echo Utils::t('Import all folder'); ?>" />
					<div id="mapImportSelectionDialog" data-title="<?php echo Utils::t('Make selection'); ?>" data-select="<?php echo Utils::t('Select'); ?>" hidden="hidden">
						<table>
							<thead>
								<tr>
									<th class="thleft"><?php echo Utils::t('Map'); ?></th>
									<th><?php echo Utils::t('Environment'); ?></th>
									<th><?php echo Utils::t('Type'); ?></th>
									<th><?php echo Utils::t('Author'); ?></th>
									<th class="thright"><input type="checkbox" name="checkAllMapImport" id="checkAllMapImport" value="" /></th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
				
				<div class="mapsSelected">
					<p><?php echo Utils::t('MatchSettings selected maps:'); ?> <span id="nbMapSelected"><?php echo $data['matchSettings']['nbm']; ?></span></p>
					<input class="button light" type="button" name="mapSelection" id="mapSelection" value="<?php echo Utils::t('View the MatchSettings selection'); ?>" />
					<div id="mapSelectionDialog" data-title="<?php echo Utils::t('MatchSettings selection'); ?>" data-remove="<?php echo Utils::t('Remove map from the selection'); ?>" data-close="<?php echo Utils::t('Close'); ?>" hidden="hidden">
						<table>
							<thead>
								<tr>
									<th class="thleft"><?php echo Utils::t('Map'); ?></th>
									<th><?php echo Utils::t('Environment'); ?></th>
									<th><?php echo Utils::t('Type'); ?></th>
									<th><?php echo Utils::t('Author'); ?></th>
									<th class="thright"></th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</fieldset>
		</div>
		
		<h2><?php echo Utils::t('Game information'); ?></h2>
		<div class="content gameinfos">
			<?php echo AdminServUI::getTemplate('gameinfos-general'); ?>
			<?php echo AdminServUI::getTemplate('gameinfos-gamemode'); ?>
		</div>
		
		<h2><?php echo Utils::t('HotSeat'); ?></h2>
		<div class="content">
			<fieldset>
				<table>
					<tr>
						<td class="key"><label for="hotSeatGameMode"><?php echo Utils::t('Game mode'); ?></label></td>
						<td class="value">
							<select class="width2" name="hotSeatGameMode" id="hotSeatGameMode">
								<?php echo AdminServUI::getGameModeList($data['matchSettings']['hotseat']['GameMode']); ?>
							</select>
						</td>
						<td class="preview"></td>
					</tr>
					<tr>
						<td class="key"><label for="hotSeatTimeLimit"><?php echo Utils::t('Time limit'); ?></label></td>
						<td class="value">
							<input class="text width2" type="number" min="0" name="hotSeatTimeLimit" id="hotSeatTimeLimit" value="<?php echo TimeDate::millisecToSec($data['matchSettings']['hotseat']['TimeLimit']); ?>" />
						</td>
						<td class="preview"></td>
					</tr>
					<tr>
						<td class="key"><label for="hotSeatCountRound"><?php echo Utils::t('Rounds count'); ?></label></td>
						<td class="value">
							<input class="text width2" type="number" min="0" name="hotSeatCountRound" id="hotSeatCountRound" value="<?php echo $data['matchSettings']['hotseat']['RoundsCount']; ?>" />
						</td>
						<td class="preview"></td>
					</tr>
				</table>
			</fieldset>
		</div>
		
		<h2><?php echo Utils::t('Filter'); ?></h2>
		<div class="content">
			<fieldset>
				<table>
					<tr>
						<td class="key"><label for="filterIsLan"><?php echo Utils::t('Lan'); ?></label></td>
						<td class="value">
							<input class="text" type="checkbox" name="filterIsLan" id="filterIsLan"<?php if ($data['matchSettings']['filter']['IsLan']): echo ' checked="checked"'; endif; ?> value="" />
						</td>
						<td class="preview"></td>
					</tr>
					<tr>
						<td class="key"><label for="filterIsInternet"><?php echo Utils::t('Internet'); ?></label></td>
						<td class="value">
							<input class="text" type="checkbox" name="filterIsInternet" id="filterIsInternet"<?php if ($data['matchSettings']['filter']['IsInternet']): echo ' checked="checked"'; endif; ?> value="" />
						</td>
						<td class="preview"></td>
					</tr>
					<tr>
						<td class="key"><label for="filterIsSolo"><?php echo Utils::t('Solo'); ?></label></td>
						<td class="value">
							<input class="text" type="checkbox" name="filterIsSolo" id="filterIsSolo"<?php if ($data['matchSettings']['filter']['IsSolo']): echo ' checked="checked"'; endif; ?> value="" />
						</td>
						<td class="preview"></td>
					</tr>
					<tr>
						<td class="key"><label for="filterIsHotSeat"><?php echo Utils::t('HotSeat'); ?></label></td>
						<td class="value">
							<input class="text" type="checkbox" name="filterIsHotSeat" id="filterIsHotSeat"<?php if ($data['matchSettings']['filter']['IsHotseat']): echo ' checked="checked"'; endif; ?> value="" />
						</td>
						<td class="preview"></td>
					</tr>
					<tr>
						<td class="key"><label for="filterSortIndex"><?php echo Utils::t('Sort index'); ?></label></td>
						<td class="value">
							<input class="text width2" type="number" min="0" name="filterSortIndex" id="filterSortIndex" value="<?php echo $data['matchSettings']['filter']['SortIndex']; ?>" />
						</td>
						<td class="preview"></td>
					</tr>
					<tr>
						<td class="key"><label for="filterRandomMaps"><?php echo Utils::t('Random map order'); ?></label></td>
						<td class="value">
							<input class="text" type="checkbox" name="filterRandomMaps" id="filterRandomMaps"<?php if ($data['matchSettings']['filter']['RandomMapOrder']): echo ' checked="checked"'; endif; ?> value="" />
						</td>
						<td class="preview"></td>
					</tr>
					<tr>
						<td class="key"><label for="filterDefaultGameMode"><?php echo Utils::t('Default game mode'); ?></label></td>
						<td class="value">
							<select class="width2" name="filterDefaultGameMode" id="filterDefaultGameMode">
								<?php echo AdminServUI::getGameModeList($data['matchSettings']['filter']['ForceDefaultGameMode']); ?>
							</select>
						</td>
						<td class="preview"></td>
					</tr>
				</table>
			</fieldset>
		</div>
		
		<div class="fright save">
			<input class="button light" type="submit" name="savematchsetting" id="savematchsetting" data-nomap="<?php echo Utils::t('No map selected for the MatchSettings.'); ?>" value="<?php echo Utils::t('Save'); ?>" />
		</div>
	</section>
	</form>
</section>