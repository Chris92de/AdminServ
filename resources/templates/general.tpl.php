<section class="cadre left<?php if ($data['isTeamGameMode']): echo ' isTeamGameMode'; endif; ?>">
	<h1><?php echo Utils::t('Current map'); ?></h1>
	<form method="post" action=".">
	<div class="content">
		<table class="current_map">
			<tr>
				<td class="key"><?php echo Utils::t('Name'); ?></td>
				<td class="value" id="map_name"><?php echo $data['serverInfo']['map']['name']; ?></td>
			</tr>
			<tr>
				<td class="key"><?php echo Utils::t('Author'); ?></td>
				<td class="value" id="map_author"><?php echo $data['serverInfo']['map']['author']; ?></td>
			</tr>
			<tr>
				<td class="key"><?php echo Utils::t('Environment'); ?></td>
				<td class="value" id="map_enviro"><?php echo $data['serverInfo']['map']['enviro']; ?><img src="<?php echo AdminServConfig::$PATH_RESOURCES .'images/env/'.strtolower($data['serverInfo']['map']['enviro']); ?>.png" alt="" /></td>
			</tr>
			<tr>
				<td class="key"><?php echo Utils::t('Map UId'); ?></td>
				<td class="value" id="map_uid"><?php echo $data['serverInfo']['map']['uid']; ?></td>
			</tr>
			<tr>
				<td class="key"><?php echo Utils::t('Game mode'); ?></td>
				<td class="value <?php echo strtolower($data['serverInfo']['srv']['gameModeName']); ?>" id="map_gamemode">
					<?php if (isset($data['serverInfo']['srv']['gameModeScriptName'])): ?>
						<?php echo $data['serverInfo']['srv']['gameModeScriptName']; ?> <span class="scriptName">(<?php echo $data['serverInfo']['srv']['gameModeName']; ?>)</span>
					<?php else: ?>
						<?php echo $data['serverInfo']['srv']['gameModeName']; ?>
					<?php endif; ?>
				</td>
			</tr>
			<?php if ($data['serverInfo']['map']['callvote']['login'] && AdminServAdminLevel::hasPermission('cancel_vote')): ?>
				<tr>
					<td class="key"><?php echo Utils::t('Current vote'); ?></td>
					<td class="value" id="map_currentcallvote">
						<?php echo $data['serverInfo']['map']['callvote']['login'].' : '.$data['serverInfo']['map']['callvote']['cmdname'].' ('.$data['serverInfo']['map']['callvote']['cmdparam'].')'; ?>
						<input class="button light" type="submit" name="CancelVote" id="CancelVote" value="<?php echo Utils::t('Cancel vote'); ?>" />
					</td>
				</tr>
			<?php endif; ?>
			<?php if (AdminServ::isGameMode('Team', $data['serverInfo']['srv']['gameModeId']) && AdminServAdminLevel::hasPermission('force_scores')): ?>
				<tr>
					<td class="key"><?php echo Utils::t('Scores'); ?></td>
					<td class="value" id="map_teamscore">
						<span class="team_0" title="<?php echo Utils::t('Blue team'); ?>"></span>
						<input class="text" type="number" min="0" name="ScoreTeamBlue" id="ScoreTeamBlue" value="<?php echo $data['serverInfo']['map']['scores']['blue']; ?>" />
						<span class="team_1" title="<?php echo Utils::t('Red team'); ?>"></span>
						<input class="text" type="number" min="0" name="ScoreTeamRed" id="ScoreTeamRed" value="<?php echo $data['serverInfo']['map']['scores']['red']; ?>" />
						<input class="button light" type="submit" name="ForceScores" id="ForceScores" value="<?php echo Utils::t('Force the scores'); ?>" />
					</td>
				</tr>
			<?php endif; ?>
		</table>
		<?php if ($data['serverInfo']['map']['thumb'] != null): ?>
			<div id="map_thumbnail" data-text-thumbnail="<?php echo Utils::t('No thumbnail'); ?>">
				<img src="data:image/jpeg;base64,<?php echo $data['serverInfo']['map']['thumb']; ?>" alt="<?php echo Utils::t('No thumbnail'); ?>" />
			</div>
		<?php endif; ?>
	</div>
	</form>
	
	<h1><?php echo Utils::t('Server'); ?></h1>
	<div class="content">
		<table>
			<tr>
				<td class="key"><?php echo Utils::t('Server name'); ?></td>
				<td class="value" id="server_name"><?php echo $data['serverInfo']['srv']['name']; ?></td>
			</tr>
			<tr>
				<td class="key"><?php echo Utils::t('Status'); ?></td>
				<td class="value" id="server_status"><?php echo $data['serverInfo']['srv']['status']; ?></td>
			</tr>
			<tr>
				<td class="key"><?php echo Utils::t('Server login'); ?></td>
				<td class="value"><?php echo SERVER_LOGIN; ?></td>
			</tr>
			<tr>
				<td class="key"><?php echo Utils::t('Connected on'); ?></td>
				<td class="value" id="srv_version_name">
					<?php if (defined('IS_RELAY') && IS_RELAY && isset($data['mainServerLogin']) && $data['mainServerLogin'] !== null): ?>
						<?php echo $data['mainServerLogin']; ?> (<span class="<?php echo strtolower(SERVER_VERSION_NAME); ?>"><?php echo SERVER_VERSION_NAME; ?></span>)
					<?php else: ?>
						<span class="<?php echo strtolower(SERVER_VERSION_NAME); ?>"><?php echo SERVER_VERSION_NAME; ?></span>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo Utils::t('Dedicated version'); ?></td>
				<td class="value"><?php echo SERVER_BUILD.' ('. SERVER_VERSION .')'; ?></td>
			</tr>
		</table>
	</div>
	
	<?php if (AdminServAdminLevel::isType('SuperAdmin')): ?>
		<h1><?php echo Utils::t('Statistics'); ?></h1>
		<div class="content last">
			<table>
				<tr>
					<td class="key"><?php echo Utils::t('Uptime'); ?></td>
					<td class="value" id="network_uptime"><?php echo $data['serverInfo']['net']['uptime']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo Utils::t('Number of connections'); ?></td>
					<td class="value" id="network_nbrconnection"><?php echo $data['serverInfo']['net']['nbrconnection']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo Utils::t('Average connection time'); ?></td>
					<td class="value" id="network_meanconnectiontime"><?php echo $data['serverInfo']['net']['meanconnectiontime']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo Utils::t('Average number of players'); ?></td>
					<td class="value" id="network_meannbrplayer"><?php echo $data['serverInfo']['net']['meannbrplayer']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo Utils::t('Recv net rate'); ?></td>
					<td class="value" id="network_recvnetrate"><?php echo $data['serverInfo']['net']['recvnetrate']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo Utils::t('Send net rate'); ?></td>
					<td class="value" id="network_sendnetrate"><?php echo $data['serverInfo']['net']['sendnetrate']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo Utils::t('Total receiving size'); ?></td>
					<td class="value" id="network_totalreceivingsize"><?php echo $data['serverInfo']['net']['totalreceivingsize']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo Utils::t('Total sending size'); ?></td>
					<td class="value" id="network_totalsendingsize"><?php echo $data['serverInfo']['net']['totalsendingsize']; ?></td>
				</tr>
			</table>
		</div>
	<?php endif; ?>
</section>

<section class="cadre right<?php if ($data['isTeamGameMode']): echo ' isTeamGameMode'; endif; ?>">
	<h1><?php echo Utils::t('Players'); ?></h1>
	<div class="title-detail">
		<ul>
			<li><a id="detailMode" href="." data-statusmode="<?php echo USER_MODE_GENERAL; ?>" data-textdetail="<?php echo Utils::t('Detailed mode'); ?>" data-textsimple="<?php echo Utils::t('Simple mode'); ?>"><?php echo (USER_MODE_GENERAL == 'detail') ? Utils::t('Simple mode') : Utils::t('Detailed mode'); ?></a></li>
			<li class="last"><input type="checkbox" name="checkAll" id="checkAll" value=""<?php if (!is_array($data['serverInfo']['ply'])): echo ' disabled="disabled"'; endif; ?> /></li>
		</ul>
	</div>
	
	<!-- Liste des joueurs -->
	<form method="post" action=".">
	<div id="playerlist">
		<table>
			<thead>
				<tr>
					<?php if ($data['isTeamGameMode']): ?>
						<th class="detailModeTh thleft"<?php if (USER_MODE_GENERAL == 'simple'): echo ' hidden="hidden"'; endif; ?>><a href="?sort=team"><?php echo Utils::t('Team'); ?></a></th>
					<?php endif; ?>
					<th class="firstTh <?php if (USER_MODE_GENERAL == 'simple' || !$data['isTeamGameMode']): echo 'thleft'; endif; ?>"><a href="?sort=nickname"><?php echo Utils::t('Nickname'); ?></a></th>
					<?php if (!$data['isTeamGameMode']): ?>
						<th class="detailModeTh"<?php if (USER_MODE_GENERAL == 'simple'): echo ' hidden="hidden"'; endif; ?>><a href="?sort=ladder"><?php echo Utils::t('Ladder'); ?></a></th>
					<?php endif; ?>
					<th><a href="?sort=login"><?php echo Utils::t('Login'); ?></a></th>
					<th><a href="?sort=status"><?php echo Utils::t('Status'); ?></a></th>
					<th class="thright"></th>
				</tr>
			</thead>
			<tbody>
				<tr class="table-separation"><td colspan="<?php echo ($data['isTeamGameMode']) ? '6' : '5'; ?>"></td></tr>
				<?php if ($data['serverInfo']['nbp'] > 0): ?>
					<?php $i = 0; ?>
					<?php foreach ($data['serverInfo']['ply'] as $player): ?>
						<tr class="<?php echo ($i%2) ? 'even' : 'odd'; ?>">
							<?php if ($data['isTeamGameMode'] && USER_MODE_GENERAL == 'detail'): ?>
								<td class="detailModeTd imgleft"><span class="team_<?php echo $player['TeamId']; ?>" title="<?php echo $player['TeamName']; ?>">&nbsp;</span><?php echo $player['TeamName']; ?></td>
							<?php endif; ?>
							<td class="imgleft"><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/16/solo.png" alt="" /><?php echo $player['NickName']; ?></td>
							<?php if (!$data['isTeamGameMode']): ?>
								<td class="detailModeTd imgleft"<?php if (USER_MODE_GENERAL == 'simple'): ' hidden="hidden"'; endif; ?>><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/16/leagueladder.png" alt="" /><?php echo $player['LadderRanking']; ?></td>
							<?php endif; ?>
							<td><?php echo $player['Login']; ?></td>
							<td><?php echo $player['PlayerStatus']; ?></td>
							<td class="checkbox"><input type="checkbox" name="player[]" value="<?php echo $player['Login']; ?>" /></td>
						</tr>
						<?php $i++; ?>
					<?php endforeach; ?>
				<?php else: ?>
					<tr class="no-line">
						<td class="center" colspan="<?php echo ($data['isTeamGameMode']) ? '6' : '5'; ?>"><?php echo $data['serverInfo']['ply']; ?></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	
	<div class="options">
		<div class="fleft">
			<span class="nb-line"><?php echo $data['serverInfo']['nbp']; ?></span>
		</div>
		<div class="fright">
			<div class="selected-files-label locked">
				<span class="selected-files-title"><?php echo Utils::t('For the selection'); ?></span>
				<span class="selected-files-count">(0)</span>
				<div class="selected-files-option">
					<?php if (AdminServAdminLevel::hasPermission('player_ignore')): ?>
						<input class="button dark" type="submit" name="IgnoreLoginList" id="IgnoreLoginList" value="<?php echo Utils::t('Ignore'); ?>" />
					<?php endif; ?>
					<?php if (AdminServAdminLevel::hasPermission('player_guest')): ?>
						<input class="button dark" type="submit" name="GuestLoginList" id="GuestLoginList" value="<?php echo Utils::t('Guest'); ?>" />
					<?php endif; ?>
					<?php if (AdminServAdminLevel::hasPermission('player_ban')): ?>
						<input class="button dark" type="submit" name="BanLoginList" id="BanLoginList" value="<?php echo Utils::t('Ban'); ?>" />
					<?php endif; ?>
					<?php if (AdminServAdminLevel::hasPermission('player_kick')): ?>
						<input class="button dark" type="submit" name="KickLoginList" id="KickLoginList" value="<?php echo Utils::t('Kick'); ?>" />
					<?php endif; ?>
					<?php if ($data['isTeamGameMode'] && AdminServAdminLevel::hasPermission('player_changeteam')): ?>
						<input class="button dark" type="submit" name="ForceBlueTeam" id="ForceBlueTeam" value="<?php echo Utils::t('Blue team'); ?>" />
						<input class="button dark" type="submit" name="ForceRedTeam" id="ForceRedTeam" value="<?php echo Utils::t('Red team'); ?>" />
					<?php endif; ?>
					<?php if (AdminServAdminLevel::hasPermission('player_forcetospectator')): ?>
						<input class="button dark" type="submit" name="ForceSpectatorList" id="ForceSpectatorList" value="<?php echo Utils::t('Spectator'); ?>" />
					<?php endif; ?>
					<?php if (AdminServAdminLevel::hasPermission('player_forcetoplayer')): ?>
						<input class="button dark" type="submit" name="ForcePlayerList" id="ForcePlayerList" value="<?php echo Utils::t('Player'); ?>" />
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	
	<input type="hidden" id="currentSort" name="currentSort" value="" />
	<input type="hidden" id="isTeamGameMode" name="isTeamGameMode" value="<?php echo $data['isTeamGameMode']; ?>" />
	</form>
</section>