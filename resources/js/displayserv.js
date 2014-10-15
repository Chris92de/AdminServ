(function($){
	$.fn.displayServ = function(options){
		var selector = $(this);
		
		// Options
		var settings = {
			config: 'config/servers.cfg.php',
			resources: 'resources/',
			timeout: 3,
			refresh: 30,
			color: '',
			links: {
				join: true,
				spectate: true,
				addfavourite: false
			}
		}
		$.extend(settings, options);
		settings.refresh = parseInt(settings.refresh * 1000);
		if(settings.refresh < 10000){
			settings.refresh = 10000;
		}
		if(settings.color){
			settings.color = ' style="color: '+settings.color+';"';
		}
		
		// Refresh
		selector.initialize(settings);
		setInterval(function(){
			selector.initialize(settings);
		}, settings.refresh);
	};
})(jQuery);

(function($){
	$.fn.initialize = function(settings){
		var selector = $(this);
		
		// 1ère étape - Initialiser DisplayServ en créant le html
		$.getJSON(settings.resources+'ajax/ds_initialize.php', {cfg: settings.config}, function(data){
			if(data != null){
				var out = '<ul class="ds-servers-list loading">';
					if(data.servers){
						for(var i = 0; i < data.servers; i++){
							out += '<li id="ds-server-'+i+'" class="ds-server">'
								+ '<table>'
									+ '<tr class="ds-header">'
										+ '<th class="first"'+settings.color+'>'+data.label.server+' n°'+(i+1)+'</th>'
										+ '<th class="middle"></th>'
										+ '<th class="last"'+settings.color+'>'+data.label.players+'</th>'
									+ '</tr>'
									+ '<tr class="ds-space"><td colspan="3"></td></tr>'
									+ '<tr class="ds-content">'
										+ '<td class="ds-part-left">'
											+ '<ul>'
												+ '<li>'+data.label.name+'</li>'
												+ '<li>'+data.label.login+'</li>'
												+ '<li>'+data.label.connect+'</li>'
												+ '<li>'+data.label.status+'</li>'
												+ '<li>'+data.label.gamemode+'</li>'
												+ '<li>'+data.label.currentmap+'</li>'
												+ '<li>'+data.label.players+'</li>'
											+ '</ul>'
										+ '</td>'
										+ '<td class="ds-part-middle">'
											+ '<ul>'
												+ '<li class="ds-server-name"></li>'
												+ '<li class="ds-server-login"></li>'
												+ '<li class="ds-server-connect"></li>'
												+ '<li class="ds-server-protocol"></li>'
												+ '<li class="ds-server-status"></li>'
												+ '<li class="ds-server-gamemode"></li>'
												+ '<li class="ds-server-currentmap"></li>'
												+ '<li class="ds-server-players-count"></li>'
											+ '</ul>'
										+ '</td>'
										+ '<td class="ds-part-right">'
											+ '<div class="ds-servers-players-list"></div>'
										+ '</td>'
									+ '</tr>'
								+ '</table>'
								+ '<div class="ds-server-join-wrap">'
										+ '<ul>';
											if(settings.links.addfavourite){
												out += '<li class="ds-server-favourite"><a href=".">'+data.label.addfavourite+'</a></li>';
											}
											if(settings.links.spectate){
												out += '<li class="ds-server-spectate"><a href=".">'+data.label.accessserverspectate+'</a></li>';
											}
											if(settings.links.join){
												out += '<li class="ds-server-join"><a href=".">'+data.label.accessserverplayer+'</a></li>';
											}
										out += '</ul>'
								+ '</div>'
							+ '</li>';
						}
					}
				out += '</ul>';
				
				// Affichage
				selector.find('.ds-servers-list').remove();
				selector.html(out);
				
				// Calcul de la taille max
				var maxsize = selector.find('.ds-servers-list').width();
				if(maxsize < 380){
					selector.find('.ds-servers-list').addClass("max-width-380");
					if(settings.links.addfavourite){
						selector.find('.ds-servers-list .ds-server-favourite').remove();
					}
					if(settings.links.spectate){
						selector.find('.ds-servers-list .ds-server-spectate').remove();
					}
				}
				else if(maxsize < 580){
					selector.find('.ds-servers-list').addClass('max-width-580');
					if(settings.links.addfavourite){
						selector.find('.ds-servers-list .ds-server-favourite').remove();
					}
				}
				
				// 2ème étape - Récupérer les données serveur
				$.getJSON(settings.resources+'ajax/ds_getservers.php', {cfg: settings.config, rsc: settings.resources}, function(data){
					if(data != null){
						if(data.servers){
							for(var i = 0; i < data.servers.length; i++){
								var serverId = $('#ds-server-'+i);
								
								// Server infos
								if(data.servers[i].error){
									serverId.find('.ds-server-name').html(data.servers[i].error);
									serverId.find('.ds-server-join-wrap').remove();
								}
								else{
									serverId.find('.ds-server-name').html(data.servers[i].name);
									serverId.find('.ds-server-login').html(data.servers[i].serverlogin);
									serverId.find('.ds-server-connect').html(data.servers[i].version.name);
									serverId.find('.ds-server-connect').addClass(data.servers[i].version.name.toLowerCase());
									serverId.find('.ds-server-status').html(data.servers[i].status);
									serverId.find('.ds-server-gamemode').html(data.servers[i].gamemode);
									serverId.find('.ds-server-gamemode').addClass(data.servers[i].gamemode.toLowerCase());
									var envImg = '';
									if(data.servers[i].map.env.filename != null){
										var envImg = ' <img src="'+data.servers[i].map.env.filename+'" alt="('+data.servers[i].map.env.name+')" title="'+data.servers[i].map.env.name+'" />';
									}
									serverId.find('.ds-server-currentmap').html(data.servers[i].map.name + envImg);
									serverId.find('.ds-server-players-count').html(data.players[i].count.current+' / '+data.players[i].count.max);
									
									// Join
									var title = '';
									if(data.servers[i].version.title){
										title += '@'+data.servers[i].version.title;
									}
									if(settings.links.join){
										serverId.find('.ds-server-join a').attr('href', data.servers[i].version.protocol+'://#join='+data.servers[i].serverlogin+title);
									}
									if(settings.links.spectate){
										serverId.find('.ds-server-spectate a').attr('href', data.servers[i].version.protocol+'://#spectate='+data.servers[i].serverlogin+title);
									}
									if(settings.links.addfavourite){
										serverId.find('.ds-server-favourite a').attr('href', data.servers[i].version.protocol+'://#addfavourite='+data.servers[i].serverlogin+title);
									}
									
									// Players
									var playerListTable = '<table>';
										if(data.players[i].count.current > 0){
											$.each(data.players[i].list, function(idPlayer, dataPlayer){
												var teamSpan = '';
												if(data.servers[i].gamemode == 'Team'){
													teamSpan = '<span class="team_'+dataPlayer.teamId+'" title="'+dataPlayer.teamName+'">&nbsp;</span>';
												}
												playerListTable += '<tr>'
													+ '<td>'+teamSpan+dataPlayer.name+'</td>'
													+ '<td>'+dataPlayer.status+'</td>'
												+ '</tr>';
											});
										}
										else{
											playerListTable += '<td class="no-player" colspan="2">'+data.players[i].list+'</td>';
										}
									playerListTable += '</table>';
									serverId.find('.ds-servers-players-list').html(playerListTable);
								}
							}
						}
						
						selector.find('.ds-servers-list').removeClass('loading');
					}
				});
			}
		});
	};
})(jQuery);