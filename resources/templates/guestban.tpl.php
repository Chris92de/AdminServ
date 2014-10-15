<section class="cadre left">
	<form method="post" action="?p=<?php echo USER_PAGE; ?>">
	<div id="banlist">
		<h1>Banlist<?php if ($data['banlist']['count'] > 0): ' ('.$data['banlist']['count'].')'; endif; ?></h1>
		<div class="title-detail">
			<ul>
				<li><a class="cleanList" href="?p=<?php echo USER_PAGE; ?>&amp;clean=banlist" data-empty="<?php echo Utils::t('The list is already empty.'); ?>"><?php echo Utils::t('Clean the list'); ?></a></li>
				<li class="last"><input type="checkbox" name="checkAllBanlist" id="checkAllBanlist" value=""<?php if ($data['banlist']['count'] == 0): echo ' disabled="disabled"'; endif; ?> /></li>
			</ul>
		</div>
		<table>
			<thead>
				<tr>
					<th class="thleft"><?php echo Utils::t('Login'); ?></th>
					<th><?php echo Utils::t('IP address'); ?></th>
					<th><?php echo Utils::t('Client'); ?></th>
					<th class="thright"></th>
				</tr>
			</thead>
			<tbody>
				<tr class="table-separation"><td colspan="4"></td></tr>
				<?php if ($data['banlist']['count'] > 0): ?>
					<?php $i = 0; ?>
					<?php foreach ($data['banlist']['list'] as $player): ?>
						<tr class="<?php echo ($i%2) ? 'even' : 'odd'; ?>">
							<td class="imgleft"><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/16/solo.png" alt="" /><?php echo $player['Login']; ?></td>
							<td><?php echo $player['IPAddress']; ?></td>
							<td><?php echo $player['ClientName']; ?></td>
							<td class="checkbox"><input type="checkbox" name="banlist[]" value="<?php echo $player['Login']; ?>" /></td>
						</tr>
						<?php $i++; ?>
					<?php endforeach; ?>
				<?php else: ?>
					<tr class="no-line">
						<td class="center" colspan="4"><?php echo Utils::t('No player'); ?></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	
	<div id="blacklist">
		<h1>Blacklist<?php if ($data['blacklist']['count'] > 0): echo ' ('.$data['blacklist']['count'].')'; endif; ?></h1>
		<div class="title-detail">
			<ul>
				<li><a class="cleanList" href="?p=<?php echo USER_PAGE; ?>&amp;clean=blacklist" data-empty="<?php echo Utils::t('The list is already empty.'); ?>"><?php echo Utils::t('Clean the list'); ?></a></li>
				<li class="last"><input type="checkbox" name="checkAllBlacklist" id="checkAllBlacklist" value=""<?php if ($data['blacklist']['count'] == 0): echo ' disabled="disabled"'; endif; ?> /></li>
			</ul>
		</div>
		<table>
			<thead>
				<tr>
					<th class="thleft"><?php echo Utils::t('Login'); ?></th>
					<th class="thright"></th>
				</tr>
			</thead>
			<tbody>
				<tr class="table-separation"><td colspan="2"></td></tr>
				<?php if ($data['blacklist']['count'] > 0): ?>
					<?php $i = 0; ?>
					<?php foreach ($data['blacklist']['list'] as $player): ?>
						<tr class="<?php echo ($i%2) ? 'even' : 'odd'; ?>">
							<td class="imgleft"><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/16/solo.png" alt="" /><?php echo $player['Login']; ?></td>
							<td class="checkbox"><input type="checkbox" name="blacklist[]" value="<?php echo $player['Login']; ?>" /></td>
						</tr>
						<?php $i++; ?>
					<?php endforeach; ?>
				<?php else: ?>
					<tr class="no-line">
						<td class="center" colspan="2"><?php echo Utils::t('No player'); ?></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	
	<div id="guestlist">
		<h1>Guestlist<?php if ($data['guestlist']['count'] > 0): echo ' ('.$data['guestlist']['count'].')'; endif; ?></h1>
		<div class="title-detail">
			<ul>
				<li><a class="cleanList" href="?p=<?php echo USER_PAGE; ?>&amp;clean=guestlist" data-empty="<?php echo Utils::t('The list is already empty.'); ?>"><?php echo Utils::t('Clean the list'); ?></a></li>
				<li class="last"><input type="checkbox" name="checkAllGuestlist" id="checkAllGuestlist" value=""<?php if ($data['guestlist']['count'] == 0): echo ' disabled="disabled"'; endif; ?> /></li>
			</ul>
		</div>
		<table>
			<thead>
				<tr>
					<th class="thleft"><?php echo Utils::t('Login'); ?></th>
					<th class="thright"></th>
				</tr>
			</thead>
			<tbody>
				<tr class="table-separation"><td colspan="2"></td></tr>
				<?php if ($data['guestlist']['count'] > 0): ?>
					<?php $i = 0; ?>
					<?php foreach ($data['guestlist']['list'] as $player): ?>
						<tr class="<?php echo ($i%2) ? 'even' : 'odd'; ?>">
							<td class="imgleft"><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/16/solo.png" alt="" /><?php echo $player['Login']; ?></td>
							<td class="checkbox"><input type="checkbox" name="guestlist[]" value="<?php echo $player['Login']; ?>" /></td>
						</tr>
						<?php $i++; ?>
					<?php endforeach; ?>
				<?php else: ?>
					<tr class="no-line">
						<td class="center" colspan="2"><?php echo Utils::t('No player'); ?></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	
	<div id="ignorelist">
		<h1>Ignorelist<?php if ($data['ignorelist']['count'] > 0): echo ' ('.$data['ignorelist']['count'].')'; endif; ?></h1>
		<div class="title-detail">
			<ul>
				<li><a class="cleanList" href="?p=<?php echo USER_PAGE; ?>&amp;clean=ignorelist" data-empty="<?php echo Utils::t('The list is already empty.'); ?>"><?php echo Utils::t('Clean the list'); ?></a></li>
				<li class="last"><input type="checkbox" name="checkAllIgnorelist" id="checkAllIgnorelist" value=""<?php if ($data['ignorelist']['count'] == 0): echo ' disabled="disabled"'; endif; ?> /></li>
			</ul>
		</div>
		<table>
			<thead>
				<tr>
					<th class="thleft"><?php echo Utils::t('Login'); ?></th>
					<th class="thright"></th>
				</tr>
			</thead>
			<tbody>
				<tr class="table-separation"><td colspan="2"></td></tr>
				<?php if ($data['ignorelist']['count'] > 0): ?>
					<?php $i = 0; ?>
					<?php foreach ($data['ignorelist']['list'] as $player): ?>
						<tr class="<?php echo ($i%2) ? 'even' : 'odd'; ?>">
							<td class="imgleft"><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/16/solo.png" alt="" /><?php echo $player['Login']; ?></td>
							<td class="checkbox"><input type="checkbox" name="ignorelist[]" value="<?php echo $player['Login']; ?>" /></td>
						</tr>
						<?php $i++; ?>
					<?php endforeach; ?>
				<?php else: ?>
					<tr class="no-line">
						<td class="center" colspan="2"><?php echo Utils::t('No player'); ?></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	
	<div class="options">
		<div class="fright">
			<div class="selected-files-label locked">
				<span class="selected-files-title"><?php echo Utils::t('For the selection'); ?></span>
				<span class="selected-files-count">(0)</span>
				<div class="selected-files-option">
					<?php if (AdminServAdminLevel::hasPermission('guestban_addplayer')): ?>
						<input class="button dark" type="submit" name="blackListPlayer" id="blackListPlayer" value="<?php echo Utils::t('Blacklist'); ?>" />
					<?php endif; ?>
					<?php if (AdminServAdminLevel::hasPermission('guestban_removeplayer')): ?>
						<input class="button dark" type="submit" name="removeList" id="removeList" value="<?php echo Utils::t('Remove from the list'); ?>" />
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	</form>
</section>

<section class="cadre right">
	<?php if (AdminServAdminLevel::hasPermission('guestban_addplayer')): ?>
		<h1><?php echo Utils::t('Add'); ?></h1>
		<div class="content last addPlayer">
			<form method="post" action="?p=<?php echo USER_PAGE; ?>">
				<div>
					<select class="width2" name="addPlayerList" id="addPlayerList"<?php if ($data['players']['count'] == 0): echo ' hidden="hidden"'; endif; ?>>
						<option value="none"><?php echo Utils::t('Select a player'); ?></option>
						<?php echo $data['players']['listOptions']; ?>
						<option value="more"><?php echo Utils::t('Enter another login'); ?></option>
					</select>
					<input class="text width2" type="text" name="addPlayerLogin" id="addPlayerLogin" data-default-value="<?php echo Utils::t('Player login'); ?>" value="<?php echo Utils::t('Player login'); ?>"<?php if ($data['players']['count'] != 0): echo ' hidden="hidden"'; endif; ?> />
					<select class="addPlayerTypeList" name="addPlayerTypeList" id="addPlayerTypeList">
						<option value="none"><?php echo Utils::t('Add in the'); ?></option>
						<option value="guestlist">Guestlist</option>
						<option value="blacklist">Blacklist</option>
					</select>
					<input class="button light" type="submit" name="addPlayer" id="addPlayer" value="<?php echo Utils::t('Add'); ?>" />
				</div>
			</form>
		</div>
	<?php endif; ?>
	
	<?php if (isset($data['playlistDirectory']) && $data['playlistDirectory'] != 'Not directory'): ?>
		<div id="playlists">
			<form method="post" action="?p=<?php echo USER_PAGE; ?>">
				<h1>
					<?php echo Utils::t('Playlists'); ?>
					<?php if (AdminServAdminLevel::hasPermission('guestban_playlist_new')): ?>
						<span id="form-new-playlist" hidden="hidden">
							<select name="createPlaylistType" id="createPlaylistType">
								<option value="none"><?php echo Utils::t('Type'); ?></option>
								<option value="guestlist">Guestlist</option>
								<option value="blacklist">Blacklist</option>
							</select>
							<input class="text" type="text" name="createPlaylistName" id="createPlaylistName" data-playlistname="<?php echo Utils::t('Playlist name'); ?>" value="<?php echo Utils::t('Playlist name'); ?>" />
							<input class="button light" type="submit" name="createPlaylistValid" id="createPlaylistValid" value="<?php echo Utils::t('Create'); ?>" />
						</span>
					<?php endif; ?>
				</h1>
			</form>
			<div class="title-detail">
				<ul>
					<?php if (AdminServAdminLevel::hasPermission('guestban_playlist_new')): ?>
						<li><a id="clickNewPlaylist" href="" data-cancel="<?php echo Utils::t('Cancel'); ?>" data-newplaylist="<?php echo Utils::t('New playlist'); ?>"><?php echo Utils::t('New playlist'); ?></a></li>
					<?php endif; ?>
					<li class="last"><input type="checkbox" name="checkAllPlaylists" id="checkAllPlaylists" value="" /></li>
				</ul>
			</div>
			
			<form method="post" action="?p=<?php echo USER_PAGE; ?>">
			<table>
				<thead>
					<tr>
						<th class="thleft"><?php echo Utils::t('Playlist'); ?></th>
						<th><?php echo Utils::t('Type'); ?></th>
						<th><?php echo Utils::t('Contains'); ?></th>
						<th><?php echo Utils::t('Modified'); ?></th>
						<th class="thright"></th>
					</tr>
				</thead>
				<tbody>
					<tr class="table-separation"><td colspan="5"></td></tr>
					<?php if (isset($data['playlistDirectory']['files']) && !empty($data['playlistDirectory']['files'])): ?>
						<?php
							$i = 0;
							$defaultFilename = array(
								'guestlist.txt',
								'blacklist.txt',
								'guestlist.xml',
								'blacklist.xml',
							);
						?>
						<?php foreach ($data['playlistDirectory']['files'] as $file): ?>
							<?php if (in_array($file['filename'], $defaultFilename) || ($isDoubleExt = in_array(File::getDoubleExtension($file['filename']), AdminServConfig::$PLAYLIST_EXTENSION))): ?>
								<?php
									// Playlist data
									$playlistData = AdminServ::getPlaylistData($data['gameDataDirectory'].'Config/'.$file['filename']);
									if( isset($playlistData['logins']) ){
										$countDataLogins = count($playlistData['logins']);
										$nbPlayers = ($countDataLogins > 1) ? $countDataLogins.' '.Utils::t('players') : '1 '.Utils::t('player');
									}
									else{
										$nbPlayers = '0 '.Utils::t('player');
									}
									
									// Filename
									$parseExtIndex = ($isDoubleExt) ? -13 : -4;
									$filename = substr($file['filename'], 0, $parseExtIndex);
								?>
								<tr class="<?php echo ($i%2) ? 'even' : 'odd'; ?>">
									<td class="imgleft"><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/16/finishgrey.png" alt="" /><span title="<?php echo $file['filename']; ?>"><?php echo $filename; ?></span></td>
									<td class="center"><?php echo ucfirst($playlistData['type']); ?></td>
									<td class="center"><?php echo $nbPlayers; ?></td>
									<td class="center"><?php echo date('d-m-Y', $file['mtime']); ?></td>
									<td class="checkbox">
										<input type="checkbox" name="playlist[]" value="<?php echo $playlistData['type'].'|'.$file['filename']; ?>" />
									</td>
								</tr>
								<?php $i++; ?>
							<?php endif; ?>
						<?php endforeach; ?>
					<?php else: ?>
						<tr class="no-line">
							<td class="center" colspan="5"><?php echo Utils::t('No playlist'); ?></td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
			
			<?php if (AdminServAdminLevel::hasPermission(array('guestban_playlist_save', 'guestban_playlist_load', 'guestban_playlist_delete'))): ?>
				<div class="options">
					<div class="fright">
						<div class="selected-files-label locked">
							<span class="selected-files-title"><?php echo Utils::t('For the selection'); ?></span>
							<span class="selected-files-count">(0)</span>
							<div class="selected-files-option">
								<?php if (AdminServAdminLevel::hasPermission('guestban_playlist_delete')): ?>
									<input class="button dark" type="submit" name="deletePlaylist" id="deletePlaylist" value="<?php echo Utils::t('Delete'); ?>" />
								<?php endif; ?>
								<?php if (AdminServAdminLevel::hasPermission('guestban_playlist_load')): ?>
									<input class="button dark" type="submit" name="loadPlaylist" id="loadPlaylist" value="<?php echo Utils::t('Load'); ?>" />
								<?php endif; ?>
								<?php if (AdminServAdminLevel::hasPermission('guestban_playlist_save')): ?>
									<input class="button dark" type="submit" name="savePlaylist" id="savePlaylist" value="<?php echo Utils::t('Save '); ?>" />
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>
			</form>
		</div>
	<?php endif; ?>
</section>