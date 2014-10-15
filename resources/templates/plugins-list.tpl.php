<section class="plugins hasMenu">
	<section class="cadre left menu">
		<?php echo AdminServPlugin::getMenuList(); ?>
	</section>
	
	<section class="cadre right">
		<h1><?php echo Utils::t('Plugins'); ?></h1>
		<div class="title-detail">
			<ul>
				<li class="last">
					<?php echo $data['nbplugins']['count'].' '.$data['nbplugins']['title']; ?>
				</li>
			</ul>
		</div>
		<div class="content">
			<p><?php echo Utils::t('Plugins are extensions to add features to Adminserv.'); ?></p>
		</div>
		
		<h2><?php echo Utils::t('Install a plugin'); ?></h2>
		<div class="content">
			<p><?php echo Utils::t('See all plugins:'); ?> <a href="http://dl.zone-kev717.info/adminserv/plugins"><?php echo Utils::t('Click here'); ?></a></p>
			<p>- <?php echo Utils::t('Unzip the plugin and place its contents into the &laquo; plugins &raquo; folder of Adminserv.'); ?><br />
			- <?php echo Utils::t('In the Configuration Extension, add the name of the plugin folder previously added.'); ?></p>
			<p>
				<code>
					public static $PLUGINS = array(<br />
					&nbsp;&nbsp;&nbsp;&nbsp;'PluginName',<br />
					);
				</code>
			</p>
		</div>
		
		<h2><?php echo Utils::t('Create a new plugin'); ?></h2>
		<div class="content">
			<?php echo Utils::t('<p>To create a plugin, go to the plugins folder and duplicate file _newplugin. Replace the values ​​in the config.ini file and folder name.<br />Then there are two PHP files:</p><ul><li>script.php : this file is executed before the header of the site. This is where all plugin script will be placed.</li><li>view.php : this file is the display of the plugin executed after the header. This is where everything will be placed the html code.</li></ul><p>You need to create resources (classes, js, css) then include these files.</p>'); ?>
		</div>
	</section>
</section>