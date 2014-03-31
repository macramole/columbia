 $(function() {
	$('header .login a').click( function(e) {
		 e.preventDefault();
		 $formulario = $('#' + $(this).attr('rel') );
		 
		 if ( $formulario.hasClass('abierto') )
			 $formulario.removeClass('abierto');
		 else
		 {
			 $('#registro, #ingresar').removeClass('abierto');
			 $formulario.addClass('abierto');
		 }
	 });
	 
	 $('section#registrarse #frmRegistro').submit( function() {
		$this = $(this);
		
		$button = $('button', $this);
		$button.text('enviando...');
		
		$('input', $this).removeClass('error');
		$('div.error', $this).removeClass('visible').text('');
		
		$.ajax({
			url: 'usuarios/registro',
			type: 'POST',
			dataType: 'json',
			data: $this.serialize(),
			success: function(data) {
				if ( data.status == 'ok' )
				{
					$button.text('redirigiendo...');
					document.location.href = 'usuarios/registro_exitoso';
				}
				else
				{
					$button.text('Registrarse');
					
					$.each( data.fields, function(index, value) {
						$('input[name="' + index + '"]', $this).addClass('error');
						
						if ( (index == 'pass' || index == 'email') && value != 'requerido' )
							$('div.error', $this).addClass('visible').text(value);
					});
				}
			}
		});
		
		return false;
	 });
	 
	 $('section#registrarse #frmRegistro input').focus(function(){
		$(this).removeClass('error');
		if ( $(this).attr('name') == 'pass' )
			$('#registro span').addClass('hidden').text('');
	 });
	 
	 $('#ingresar form, section#registrarse #frmLogin').submit( function(e) {
		e.preventDefault();
		
		$form = $(this);
		$button = $('button', $form);
		$button.text('ingresando...');
		
		$('input', $form).removeClass('error');
		$('span, div.error', $form).addClass('hidden').text('');
		
		$.ajax({
			url: 'usuarios/ingresar',
			type: 'POST',
			dataType: 'json',
			data: $form.serialize(),
			success: function(data) {
				if ( data.status == 'ok' )
				{
					$button.text('redirigiendo...');
					
					if ( $form.data('goto') )
						document.location.href = $form.data('goto');
					else
						document.location.href = 'usuarios/panel';//location.reload();
				}
				else
				{
					$button.text('ingresar');
					$('input', $form).addClass('error');
					$('span, div.error', $form).removeClass('hidden').html(data.error);
				}
			}
		});
	 });
	 
	 $('#ingresar input, section#registrarse #frmLogin input').focus(function(){
		$('#ingresar input, section#registrarse #frmLogin input').removeClass('error');
		$('#ingresar span, section#registrarse #frmLogin div.error').addClass('hidden').text('');
	 });
	 
	 
	 $('a.inscribirse.nolog').click( function(e) {
		 e.preventDefault();
		 $('#ingresar form').data('goto', $(this).attr('rel'));
		 $('header .login a.ingresar').click();
	 })
 });
 