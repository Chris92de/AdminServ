<section class="cadre">
	<h1><?php echo Utils::t('Change password'); ?></h1>
	<form method="post" action="?p=<?php echo USER_PAGE; ?>">
		<div class="content">
			<fieldset>
				<legend><?php echo Utils::t('Password'); ?></legend>
				<table>
					<tr>
						<td class="key"><label for="changePasswordCurrent"><?php echo Utils::t('Current'); ?></label></td>
						<td class="value"><input class="text width3" type="password" name="changePasswordCurrent" id="changePasswordCurrent" value="" /></td>
					</tr>
					<tr>
						<td class="key"><label for="changePasswordNew"><?php echo Utils::t('New'); ?></label></td>
						<td class="value"><input class="text width3" type="password" name="changePasswordNew" id="changePasswordNew" value="" /></td>
					</tr>
				</table>
			</fieldset>
		</div>
		
		<div class="fright save">
			<input class="button light" type="submit" name="savepassword" id="savepassword" value="<?php echo Utils::t('Save'); ?>" />
		</div>
	</form>
</section>