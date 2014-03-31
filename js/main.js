var fullCssAnimations = Modernizr.cssanimations && !( Modernizr.touch && !Modernizr.webgl );
var hayMasNovedades = true;

function getMasNovedades(force)
{
	if ( !hayMasNovedades )
		return;
	
	if ( force == true || $(window).scrollTop() >= $(document).height() - $(window).height())
	{
		$('section#novedades .cargarMas a').text('cargando...');
		
		$.get('novedades/ajaxMasNotas/' + $('section#novedades ul li').last().attr('rel'), function(data) {
			
			if ( data.trim() != '' )
			{
				$('section#novedades ul').append(data);
				$('section#novedades .cargarMas a').text('ver anteriores');

				if ( typeof(updateDraggables) == 'function' )
				{
					updateDraggables('section#novedades ul');
				}
			}
			else
			{
				hayMasNovedades = false;
				$('section#novedades .cargarMas a').text('no hay más novedades');
			}
		});
	}	
}

function setDashHeight()
{
	$dashboard = $('section#dashboard');
	if ( $dashboard.length > 0 )
	{
		leftHeight = $('.left', $dashboard).height();
		rightHeight = $('.right', $dashboard).height();
		dashHeight = leftHeight > rightHeight ? leftHeight : rightHeight;
		$('.left, .right', $dashboard).height(dashHeight);
	}
}

$(function() {
	$('#mainContent .left .puertas .puerta').each( function()  {
		var puertaHeight = 0;
		
		$('li', $(this)).not('.hidden').each( function() {
			puertaHeight += $(this).outerHeight() + 1;
		});
		
		$(this).css('height', puertaHeight);
	});
	
	$('#mainContent .puertas .verMas a').click( function(e) {
		e.preventDefault();
		$this = $(this);
		$puerta = $this.parent().parent();
		$hiddens = $this.parent().siblings('.hidden');
		$hiddens.removeClass('hidden');
		
		var puertaHeight = 0;
		
		$('li', $puerta).not('.verMas').each( function() {
			puertaHeight += $(this).outerHeight() + 1;
		});
		
		$puerta.css('height', puertaHeight );
		$this.addClass('hidden');
	});
	
	$(window).load( function() {
		$('#mainContent > .left .puertas .puerta').css('transition', 'height 0.5s ease');
	});
	
	 $('#coin-slider').coinslider({
		 width: 750,
		 height: 290,
		 sDelay: 50
	 });
	 
	  $('#coin-slider-novedad').coinslider({
		 width: 380,
		 height: 285,
		 sDelay: 50
	 });
	 
	 $('#coin-slider-casa').coinslider({
		 width: 340,
		 height: 220,
		 sDelay: 50
	 });
	 
	 $('#coin-slider a[href=""]').live('click', function(e) {
		e.preventDefault(); 
	 });
	 
	 setDashHeight();
	 $(window).load(setDashHeight);
	 
	if ( $('section#novedades').length > 0 )
	{
		$('section#novedades .cargarMas a').click( function(e) {
			e.preventDefault();
			getMasNovedades(true);
		});
		$(window).scroll($.debounce( 250, getMasNovedades ));
	}
	
	if ( $('section#disciplina').length > 0 )
	{
		$('.puerta.' + $('section#disciplina').attr('class') + ' li.verMas a').click();
	}
	
	$('#frmNewsletter input').focus( function () {
		$(this).val('');
	});
	
	
	$('#mainContent .left .sponsors .bjqsWrapper').bjqs({
		width: 100,
		height: 130,
		showcontrols: false,
		showmarkers: false,
		keyboardnav: false,
		usecaptions: false,
		animduration: 2000
	});
	
	
	$('#frmNewsletter button').click(function() {
		$('#frmNewsletter .wrapper').fadeOut('fast', function() {
			$('#frmNewsletter .message').text('Un momento por favor...').fadeIn('fast');

			$.post('common/subscribeNews', { email : $('#frmNewsletter input').val() }, function(data) {
				if ( data == 'cool' )
					$('#frmNewsletter .message').text('Has sido subscripto, muchas gracias.')
				else
				{
					$('#frmNewsletter .message').text('Email invalido').fadeOut(1500, function() {
						$('#frmNewsletter .wrapper').fadeIn('fast');
					});
				}
			} );
			
		});
		
	});
		
});