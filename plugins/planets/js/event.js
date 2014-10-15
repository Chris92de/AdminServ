$(document).ready(function(){
	// Default values
	$('#serverToServerLogin, #serverToPlayerMessage, #serverToPlayerLogin2').click(function(){
		if( $(this).val() == $(this).data('default-value') ){
			$(this).val('');
		}
	});
	$('#serverToServerLogin, #serverToPlayerMessage, #serverToPlayerLogin2').blur(function(){
		if( $(this).val() == '' ){
			$(this).val( $(this).data('default-value') );
		}
	});
	
	// Server > Player
	$('#serverToPlayerLogin').change(function(){
		if( $(this).val() == 'more' ){
			$(this).fadeOut('fast');
			$('#serverToPlayerLogin2').fadeIn('fast');
			$(this).attr('hidden', true);
			$('#serverToPlayerLogin2').removeAttr('hidden');
		}
	});
});