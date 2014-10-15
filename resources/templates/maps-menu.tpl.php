<?php if (!empty($data['menuList'])): ?>
	<nav class="vertical-nav">
		<ul>
			<?php foreach ($data['menuList'] as $page => $title): ?>
				<?php if (AdminServAdminLevel::hasAccess($page)): ?>
					<li><a <?php if (USER_PAGE == $page): echo 'class="active"'; endif; ?>href="?p=<?php echo $page; if ($args['directory']): echo '&amp;d='.$args['directory']; endif; ?>"><?php echo Utils::t($title); ?></a></li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	</nav>
<?php endif; ?>