<section class="cadre">
	<h1><?php echo Utils::t('Chat'); ?></h1>
	<div class="title-detail">
		<ul>
			<li class="last"><a id="checkServerLines" href="?p=chat" data-val="0" data-txt="<?php echo Utils::t('Show server lines'); ?>"><?php echo Utils::t('Hide server lines'); ?></a></li>
		</ul>
	</div>
	
	<div id="chat"><?php echo $data['serverLines']; ?></div>
	
	<?php if (AdminServAdminLevel::hasPermission('chat_sendmessage')): ?>
		<div class="options">
			<input class="text" type="text" name="chatNickname" id="chatNickname" value="<?php echo $data['nickname']; ?>" data-default-value="<?php echo Utils::t('Nickname'); ?>" />
			<select name="chatColor" id="chatColor" title="<?php echo Utils::t('Default color: yellow'); ?>">
				<?php echo $data['colorOptions']; ?>
			</select>
			<input class="text" type="text" name="chatMessage" id="chatMessage" value="<?php echo Utils::t('Message'); ?>" data-default-value="<?php echo Utils::t('Message'); ?>" />
			<select name="chatDestination" id="chatDestination" title="<?php echo $data['destination']['title']; ?>">
				<option value="server"><?php echo Utils::t('Destination'); ?></option>
				<?php echo $data['destination']['list']; ?>
			</select>
			<input class="button dark" type="submit" name="chatSend" id="chatSend" value="<?php echo Utils::t('Send'); ?>" />
		</div>
	<?php endif; ?>
</section>