$(function() {
	/** 
	 * MAGICO PAGOS 
	 **/
	
	$('ul.magico_pagos li.pagar a').click( function(e) {
		e.preventDefault();
		$this = $(this).parent();
		
		if ( !$this.hasClass('expanded') )
			$this.addClass('expanded');
		else
			$this.removeClass('expanded');
	} );
	
	$('ul.magico_pagos li.pagar input[type="text"]').datepicker({
		dateFormat: 'dd/mm/yy'
	});

	$('ul.magico_pagos li.pagar input').datepicker('option', $.datepicker.regional[ "es" ] );
	
	$('ul.magico_pagos li.pagar button').click(function() {
		$this = $(this);
		$this.text('...');
		$this.attr('disabled',true);
		sendData = $this.parent().serialize();
		
		$.post(
			'usuarios/modificarPago',
			sendData,
			function(data) {
				if ( data.status == 'ok' )
				{
					showMessage('El pago fue modificado correctamente');
					$this.parents('li').first().removeClass('expanded');
					
					if ( $('input[name="idDisciplina"]', $this.prev()).val() ) //es actividad regular
					{
						$('.pagoPendiente', $this.parents('ul.actuales > li')).addClass('no');
						
						$validez = $('.validez', $this.parents('ul.actuales > li'));
						$('span', $validez).text( $('input[name="hasta"]', $this.prev()).val() );
						$validez.removeClass('no');
					}
					else //es actividad especial
					{
						if ( !$('input[name="soloVacante"]', $this.prev()).is(':checked') )
						{
							$('.pagoPendiente', $this.parents('ul.actuales > li')).addClass('no');

							if ( $('input[name="modalidadPago"]', $this.prev()).val() == 'Mensual' )
							{
								$validez = $('.validez', $this.parents('ul.actuales > li'));
								$('span', $validez).text( $('input[name="hasta"]', $this.prev()).val() );
								$validez.removeClass('no');
							}
						}
						else
						{
							$('.pagoPendiente', $this.parents('ul.actuales > li')).removeClass('no').text('Vacante reservada');
							$('.validez', $this.parents('ul.actuales > li')).addClass('no');
						}
					}
				}	
				else
				{
					alert(data.error);
				}
				$this.attr('disabled',false);
				$this.text('Aceptar');
			},
			'json'
		);
	});
	
	$('ul.magico_pagos li.eliminar a').click( function(e) {
		e.preventDefault();
		$this = $(this);
		$magico_pagos = $this.parents('ul').first();
		id = $magico_pagos.attr('rel');
		type = $magico_pagos.hasClass('magico_pagos_especiales') ? 'especial' : 'regular';
		
		if ( confirm('¿Está seguro que desea eliminar esta actividad del usuario') )
			$.post(
				'usuarios/eliminarPago',
				{ 'id' : id, 'type' : type },
				function(data) {
					if ( data.status == 'ok' )
					{
						showMessage('La actividad fue eliminada correctamente');
						$this.parents('li').last().fadeOut();
					}	
					else
					{
						alert(data.error);
					}
				},
				'json'
			);
	});
	
	/** FIN MAGICO PAGOS **/
	
	$('body').on('magico-draggable-start', function() {
		$('#mainContent').addClass('noOutline');
	});
	
	$('body').on('magico-draggable-stop', function() {
		$('#mainContent').removeClass('noOutline');
	});
});

function updateDraggables(selector)
{
	$(selector).magico_draggable();
}

function onDeleteContent(item)
{
	if ( $puertasEntrada.length > 0 && $(item).data('type') == 'Disciplina' )
	{
		$this = $(item);
		$('.textoWrapper .texto[rel="' + $this.data('id') + '"]').fadeOut(500, function(){ $(this).remove() });
		puertasEntrada_setHeight();
	}
}