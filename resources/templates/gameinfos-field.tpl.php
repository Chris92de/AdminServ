<tr>
	<td class="key"><label for="Next<?php echo $data['gameInfosField']['id']; ?>"><?php echo Utils::t($data['gameInfosField']['name']); ?></label></td>
	<?php if ($data['gameInfosField']['gameInfos']['curr'] != null): ?>
		<td class="value">
			<input class="text width2" type="text" name="Curr<?php echo $data['gameInfosField']['id']; ?>" id="Curr<?php echo $data['gameInfosField']['id']; ?>" readonly="readonly" value="<?php if (isset($data['gameInfosField']['gameInfos']['curr'][$data['gameInfosField']['id']])): echo $data['gameInfosField']['gameInfos']['curr'][$data['gameInfosField']['id']]; endif; ?>" />
		</td>
	<?php endif; ?>
	<td class="value">
		<input class="text width2" type="<?php echo (isset($data['gameInfosField']['gameInfos']['next'][$data['gameInfosField']['id']]) && is_numeric($data['gameInfosField']['gameInfos']['next'][$data['gameInfosField']['id']])) ? 'number" min="0"' : 'text'; ?>" name="Next<?php echo $data['gameInfosField']['id']; ?>" id="Next<?php echo $data['gameInfosField']['id']; ?>" value="<?php if (isset($data['gameInfosField']['gameInfos']['next'][$data['gameInfosField']['id']])): echo $data['gameInfosField']['gameInfos']['next'][$data['gameInfosField']['id']]; endif; ?>" />
	</td>
	<td class="preview"></td>
</tr>