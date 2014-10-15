<?php if (isset($_SESSION['adminserv']['check_password']) && $_SESSION['adminserv']['check_password'] === true): // Demande de password ?>
<section class="config-servers">
	<form method="post" action="./config/">
		<fieldset>
			<legend><?php echo Utils::t('Servers configuration'); ?></legend>
			<div class="connection-label">
				<label for="checkPassword"><?php echo Utils::t('Password'); ?> :</label>
				<input class="text" type="password" name="checkPassword" id="checkPassword" value="" />
			</div>
			<div class="connection-login">
				<input class="button light" type="submit" name="configcheckpassword" id="configcheckpassword" value="<?php echo Utils::t('Connection'); ?>" />
			</div>
			<div class="connection-cancel">
				<a class="button light" href="./?logout"><?php echo Utils::t('Cancel'); ?></a>
			</div>
		</fieldset>
	</form>
</section>
<?php elseif (isset($_SESSION['adminserv']['get_password']) && $_SESSION['adminserv']['get_password'] === true): // Demande de création password ?>
<section class="config-servers no-server">
	<form method="post" action="./config/">
		<fieldset>
			<legend><?php echo Utils::t('Online configuration'); ?></legend>
			<div class="connection-label">
				<label for="savePassword"><?php echo Utils::t('Password'); ?> :</label>
				<input class="text" type="password" name="savePassword" id="savePassword" value="" />
			</div>
			<div class="connection-login">
				<input class="button light" type="submit" name="configsavepassword" id="configsavepassword" value="<?php echo Utils::t('Save'); ?>" />
			</div>
		</fieldset>
	</form>
</section>
<?php else: // Affichage de DisplayServ ?>
<section class="displayserv">
	<?php if (AdminServConfig::USE_DISPLAYSERV): ?>
		<link rel="stylesheet" href="<?php echo AdminServConfig::$PATH_RESOURCES; ?>css/displayserv.css" />
		<script src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>js/displayserv.js"></script>
		<script>
			$(document).ready(function(){
				$('#displayserv').displayServ({
					color: '<?php echo AdminServUI::getThemeColor(); ?>'
				});
			});
		</script>
		<div id="displayserv"></div>
	<?php endif; ?>
</section>
<?php endif; ?>