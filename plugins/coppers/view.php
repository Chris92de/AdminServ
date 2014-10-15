<link rel="stylesheet" href="<?php echo $path; ?>styles/coppers.css" />
<script src="<?php echo $path; ?>js/event.js"></script>

<h2><?php echo Utils::t('Infos'); ?></h2>
<div class="content">
	<p><?php echo Utils::t('Number of coppers:'); ?> <b><?php echo $nbCoppers; ?></b></p>
	<p><?php echo Utils::t('Transfer state:').' '.$transferState; ?></p>
</div>

<h2><?php echo Utils::t('Transfers'); ?></h2>
<form method="post" action="">
<div class="content">
	<fieldset>
		<legend><?php echo Utils::t('Server').' &gt; '.Utils::t('Server'); ?></legend>
		<table>
			<tr>
				<td class="key">
					<label for="serverToServerAmout"><?php echo Utils::t('Amount'); ?></label>
				</td>
				<td class="value">
					<input class="text width2" type="number" min="0" name="serverToServerAmout" id="serverToServerAmout" value="" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="serverToServerLogin"><i><?php echo SERVER_LOGIN; ?></i> →</label>
				</td>
				<td class="value">
					<input class="text width2" type="text" name="serverToServerLogin" id="serverToServerLogin" data-default-value="Login serveur" value="Login serveur" />
				</td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset>
		<legend><?php echo Utils::t('Server').' &gt; '.Utils::t('Player'); ?></legend>
		<table>
			<tr>
				<td class="key">
					<label for="serverToPlayerAmount"><?php echo Utils::t('Amount'); ?></label>
				</td>
				<td class="value">
					<input class="text width2" type="number" min="0" name="serverToPlayerAmount" id="serverToPlayerAmount" value="" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="serverToPlayerMessage"><?php echo Utils::t('Message'); ?></label>
				</td>
				<td class="value">
					<input class="text width2" type="text" name="serverToPlayerMessage" id="serverToPlayerMessage" data-default-value="<?php echo Utils::t('Optionnal'); ?>" value="<?php echo Utils::t('Optionnal'); ?>" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="serverToPlayerLogin"><i><?php echo SERVER_LOGIN; ?></i> →</label>
				</td>
				<td class="value">
					<select class="width2" name="serverToPlayerLogin" id="serverToPlayerLogin"<?php if($playerCount == 0){ echo ' hidden="hidden"'; } ?>>
						<?php echo $getPlayerListUI; ?>
						<option value="more"><?php echo Utils::t('Enter another login'); ?></option>
					</select>
					<input class="text width2" type="text" name="serverToPlayerLogin2" id="serverToPlayerLogin2" data-default-value="<?php echo Utils::t('Player login'); ?>" value="<?php echo Utils::t('Player login'); ?>"<?php if($playerCount != 0){ echo ' hidden="hidden"'; } ?> />
				</td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset>
		<legend><?php echo Utils::t('Server').' &lt; '.Utils::t('Player'); ?></legend>
		<table>
			<tr>
				<td class="key">
					<label for="playerToServerAmount"><?php echo Utils::t('Amount'); ?></label>
				</td>
				<td class="value">
					<input class="text width1" type="number" min="0" name="playerToServerAmount" id="playerToServerAmount" value="" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="playerToServerLogin"><i><?php echo SERVER_LOGIN; ?></i> ←</label>
				</td>
				<td class="value">
					<select class="width1" name="playerToServerLogin" id="playerToServerLogin">
						<?php echo $getPlayerListUI; ?>
					</select>
				</td>
				<td class="info">
					<?php echo Utils::t('Confirmation from a player on the server is necessary.'); ?>
				</td>
			</tr>
		</table>
	</fieldset>
</div>
<div class="fright save">
	<input class="button light" type="submit" name="transfercoppers" id="transfercoppers" value="<?php echo Utils::t('Transfer'); ?>" />
</div>
</form>