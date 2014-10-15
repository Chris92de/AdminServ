<section class="maps hasMenu">
	<section class="cadre left menu">
		<?php echo AdminServUI::getMapsMenuList(); ?>
	</section>
	
	<section class="cadre right list">
		<h1><?php echo Utils::t('List'); ?></h1>
		<div class="title-detail">
			<ul>
				<?php if ($data['maps']['nbm']['count'] > 25): ?>
					<li><a id="scrollToCurrentMap" href="#currentMap"><?php echo Utils::t('Go to the current map'); ?></a></li>
				<?php endif; ?>
				<li><a id="detailMode" href="." data-statusmode="<?php echo USER_MODE_MAPS; ?>" data-textdetail="<?php echo Utils::t('Detailed mode'); ?>" data-textsimple="<?php echo Utils::t('Simple mode'); ?>"><?php echo (USER_MODE_MAPS == 'detail') ? Utils::t('Simple mode') : Utils::t('Detailed mode'); ?></a></li>
				<li class="last"><input type="checkbox" name="checkAll" id="checkAll" value=""<?php if (!is_array($data['maps']['lst'])): echo ' disabled="disabled"'; endif; ?> /></li>
			</ul>
		</div>
		
		<form method="post" action="?p=<?php echo USER_PAGE; ?>">
		<div id="maplist">
			<table>
				<thead>
					<tr>
						<th class="thleft"><?php echo Utils::t('Map'); ?></th>
						<th><?php echo Utils::t('Environment'); ?></th>
						<th><?php echo Utils::t('Author'); ?></th>
						<th class="detailModeTh"<?php if (USER_MODE_MAPS == 'simple'): echo ' hidden="hidden"'; endif; ?>><?php echo Utils::t('Gold time'); ?></th>
						<th class="detailModeTh"<?php if (USER_MODE_MAPS == 'simple'): echo ' hidden="hidden"'; endif; ?>><?php echo Utils::t('Cost'); ?></th>
						<th class="thright"></th>
					</tr>
				</thead>
				<tbody>
					<tr class="table-separation"><td colspan="6"></td></tr>
					<?php if ($data['maps']['nbm']['count'] > 0 ): ?>
						<?php $i = 0;  ?>
						<?php foreach ($data['maps']['lst'] as $id => $map): ?>
							<tr<?php if ($id == $data['maps']['cid']): echo ' id="currentMap"'; endif; ?> class="<?php echo ($i%2) ? 'even' : 'odd'; if ($id == $data['maps']['cid']): echo ' current'; endif; ?>">
								<td class="imgleft">
									<img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/16/map.png" alt="" />
									<span title="<?php echo $map['FileName']; ?>"><?php echo $map['Name']; ?></span>
									<?php if (USER_MODE_MAPS == 'detail'): ?>
										<span class="detailModeTd"><?php echo $map['UId']; ?></span>
									<?php endif; ?>
								</td>
								<td class="imgcenter"><img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/env/<?php echo strtolower($map['Environment']); ?>.png" alt="" /><?php echo $map['Environment']; ?></td>
								<td><?php echo $map['Author']; ?></td>
								<td<?php if (USER_MODE_MAPS == 'simple'): echo ' hidden="hidden"'; endif; ?>><?php echo $map['GoldTime']; ?></td>
								<td<?php if (USER_MODE_MAPS == 'simple'): echo ' hidden="hidden"'; endif; ?>><?php echo $map['CopperPrice']; ?></td>
								<td class="checkbox">
									<?php if ($id != $data['maps']['cid']): ?>
										<input type="checkbox" name="map[]" value="<?php echo $map['FileName']; ?>" />
									<?php endif; ?>
								</td>
							</tr>
							<?php $i++; ?>
						<?php endforeach; ?>
					<?php else: ?>
						<tr class="no-line">
							<td class="center" colspan="6"><?php echo $data['maps']['lst']; ?></td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		
		<div class="options">
			<div class="fleft">
				<span class="nb-line"><?php echo $data['maps']['nbm']['count'].' '.$data['maps']['nbm']['title']; ?></span>
			</div>
			<?php if (AdminServAdminLevel::hasPermission(array('maps_list_moveaftercurrent', 'maps_list_removetolist'))): ?>
				<div class="fright">
					<div class="selected-files-label locked">
						<span class="selected-files-title"><?php echo Utils::t('For the selection'); ?></span>
						<span class="selected-files-count">(0)</span>
						<div class="selected-files-option">
							<?php if (AdminServAdminLevel::hasPermission('maps_list_removetolist')): ?>
								<input class="button dark" type="submit" name="removeMap" id="removeMap" value="<?php echo Utils::t('Delete'); ?>" />
							<?php endif; ?>
							<?php if (AdminServAdminLevel::hasPermission('maps_list_moveaftercurrent')): ?>
								<input class="button dark" type="submit" name="chooseNextMap" id="chooseNextMap" value="<?php echo Utils::t('Move after the current map'); ?>" />
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
		
		<input type="hidden" id="currentSort" name="currentSort" value="" />
		</form>
	</section>
</section>