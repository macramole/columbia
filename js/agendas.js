function hideZoom()
{
	$('section#agenda_especial table td .expandir').fadeIn();
			
	if ( fullCssAnimations )
		$('section#agenda_especial .zoom').css('animation-play-state', 'running');
	else
		$('section#agenda_especial .zoom').fadeOut('fast', function() {$(this).remove()} );
}

$(function() {
	$('section.agenda ul.filtros input').click( function() {
		$this = $(this);
		id = $this.val();
		
		if ( $this.attr('checked') )
			$('section.agenda table a[rel="' + id + '"]').fadeIn('fast');
		else
			$('section.agenda table a[rel="' + id + '"]').fadeOut('fast');
	});
	
	$agendaEspecial = $('section#agenda_especial');
	if ( $agendaEspecial.length > 0 )
	{	
		//$('.expandir', $('table td .wrapper').overflowing().parent()).css('display', 'block');
		
		$('.agenda table', $agendaEspecial).first().addClass('active');
		
		$('ul.meses a', $agendaEspecial).click( function(e) {
			e.preventDefault();
			$this = $(this);
			$table = $('table[rel="' + $this.text() + '"]');
			
			$('.agendaWrapper', $agendaEspecial).scrollTo($table, 1000);
			$('ul.meses li', $agendaEspecial).removeClass('active');
			$this.parent().addClass('active');
			
			$('.agenda table', $agendaEspecial).removeClass('active');
			$table.addClass('active');
			
			$('.zoom', $agendaEspecial).click();
		});

		$('table td .expandir', $agendaEspecial).click( function(e) {
			e.preventDefault();
			$('.zoom', $agendaEspecial).click();
			$(this).fadeOut();
			$zoom = $(this).prev().clone();
			$zoom.addClass('zoom').offset( $(this).parent().position() ).css('position', 'absolute');
			$zoom.appendTo($agendaEspecial);
			$('.drag', $zoom).remove();
			
			if ( fullCssAnimations )
			{
				$zoom.bind('animationiteration webkitAnimationIteration oanimationiteration MSAnimationIteration', function() {
					$zoom.css('animation-play-state', 'paused');
				});

				$zoom.bind('animationend webkitAnimationEnd oanimationend MSAnimationEnd', function() {
					$zoom.remove();
				});
				
				$('.zoom', $agendaEspecial).live('click', hideZoom);
				$('body').click(hideZoom);
				
			}
			else
			{
				$zoom.animate({
					height: '400px',
					left: '170px',
					top: '250px',
					width: '600px',
					opacity: 1
				},800, function() {
					$('.zoom', $agendaEspecial).live('click', hideZoom);
					$('body').click(hideZoom);
				});
			}
		});
		
		
	}
 });
 