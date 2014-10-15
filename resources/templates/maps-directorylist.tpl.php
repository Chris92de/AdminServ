<!-- Titre + nouveau dossier -->
<form id="createFolderForm" method="post" action="?p=<?php echo USER_PAGE; ?>&amp;d=<?php echo $data['currentPath']; ?>">
	<h1>
		<?php echo Utils::t('Dossiers'); ?>
		<?php if ($data['showOptions'] && AdminServAdminLevel::hasPermission('folder_new')): ?>
			<span id="form-new-folder" hidden="hidden">
				<input class="text" type="text" name="newFolderName" id="newFolderName" value="" />
				<input class="button light" type="submit" name="newFolderValid" id="newFolderValid" value="ok" />
			</span>
		<?php endif; ?>
	</h1>
	<?php if ($data['showOptions'] && AdminServAdminLevel::hasPermission('folder_new')): ?>
		<div class="title-detail"><a href="." id="newfolder" data-cancel="<?php echo Utils::t('Cancel'); ?>" data-new="<?php echo Utils::t('New'); ?>"><?php echo Utils::t('New'); ?></a></div>
	<?php endif; ?>
</form>

<!-- Liste des dossiers -->
<div class="folder-list">
	<ul>
		<!-- Dossier parent  -->
		<?php if ($data['currentPath']): ?>
			<li>
				<a href="./?p=<?php echo USER_PAGE.(($data['parentPath']) ? '&amp;d='.$data['parentPath'] : ''); ?>">
					<img src="<?php echo AdminServConfig::$PATH_RESOURCES; ?>images/16/back.png" alt="" />
					<span class="dir-name"><?php echo Utils::t('Parent folder'); ?></span>
				</a>
			</li>
		<?php endif; ?>
		
		<!-- Dossiers -->
		<?php if (!empty($data['folders'])): ?>
			<?php foreach ($data['folders'] as $dir => $values): ?>
				<li>
					<a href="./?p=<?php echo USER_PAGE; ?>&amp;d=<?php echo urlencode($data['currentPath'].$dir); ?>/">
						<span class="dir-name"><?php echo $dir; ?></span>
						<span class="dir-info"><?php echo $values['nb_file']; ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		<?php else: ?>
			<li class="no-result"><?php echo Utils::t('No folder'); ?></li>
		<?php endif; ?>
	</ul>
</div>

<!-- Options de dossier -->
<?php if ($data['showOptions'] && $data['currentPath'] && AdminServAdminLevel::hasPermission(array('folder_new', 'folder_rename', 'folder_move', 'folder_delete'))): ?>
	<?php $currentDir = basename($data['currentPath']); ?>
	<form id="optionFolderForm" method="post" action="?p=<?php echo USER_PAGE; ?>&amp;d=<?php echo $data['currentPath']; ?>">
		<div class="option-folder-list">
			<h3><?php echo Utils::t('Folder options'); ?><span class="arrow-down">&nbsp;</span></h3>
			<ul hidden="hidden">
				<?php if (AdminServAdminLevel::hasPermission('folder_rename')): ?>
					<li><a class="button light rename" id="renameFolder" href="."><?php echo Utils::t('Rename'); ?></a></li>
				<?php endif; ?>
				<?php if (AdminServAdminLevel::hasPermission('folder_move')): ?>
					<li><a class="button light move" id="moveFolder" href="."><?php echo Utils::t('Move'); ?></a></li>
				<?php endif; ?>
				<?php if (AdminServAdminLevel::hasPermission('folder_delete')): ?>
					<li><a class="button light delete" id="deleteFolder" href="." data-confirm-text="<?php echo Utils::t('Do you really want to remove this folder !currentDir?', array('!currentDir' => $currentDir)); ?>>"><?php echo Utils::t('Delete'); ?></a></li>
				<?php endif; ?>
			</ul>
		</div>
		<input type="hidden" name="optionFolderHiddenFieldAction" id="optionFolderHiddenFieldAction" value="" />
		<input type="hidden" name="optionFolderHiddenFieldValue" id="optionFolderHiddenFieldValue" value="" />
		<div id="renameFolderForm" class="option-form" hidden="hidden" data-title="<?php echo Utils::t('Rename folder'); ?>" data-cancel="<?php echo Utils::t('Cancel'); ?>" data-rename="<?php echo Utils::t('Rename'); ?>">
			<ul>
				<li>
					<span class="rename-map-name"><?php echo $currentDir; ?></span>
					<span class="rename-map-arrow">&nbsp;</span>
					<input class="text width2" type="text" name="renameFolderNewName" id="renameFolderNewName" value="<?php echo $currentDir; ?>" />
				</li>
			</ul>
		</div>
		<div id="moveFolderForm" class="option-form" hidden="hidden" data-title="<?php echo Utils::t('Move folder'); ?>" data-cancel="<?php echo Utils::t('Cancel'); ?>" data-move="<?php echo Utils::t('Move'); ?>" data-root="<?php echo Utils::t('Root'); ?>" data-movethefolder="<?php echo Utils::t('Move folder <b>!currentDir</b> in:', array('!currentDir' => $currentDir)); ?>"></div>
	</form>
<?php endif; ?>