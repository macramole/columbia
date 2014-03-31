<section id="modificarDatos">
	<div class="wrapper">
		<h2>Modificar mis datos</h2>
		<form id="frmModificarDatos" action="usuarios/grabarDatos" method="POST">
			<fieldset>
				<label>Nombre</label>
				<input type="text" name="nombre" value="<?= $user['nombre'] ?>" />
				<label>Apellido</label>
				<input type="text" name="apellido" value="<?= $user['apellido'] ?>" />
				<label>Correo electrónico</label>
				<input type="text" name="email" value="<?= $user['email'] ?>" />
				<label>Celular</label>
				<input type="text" name="celular" value="<?= $user['celular'] ?>" />
				<p>Pedimos este dato para poder notificarte en caso de cancelacion, cambios, etc. y asi darte un mejor servicio.</p>
				<div class="nuevaPass">
					<p>Llená estos campos sólo si querés modificar tu contraseña actual.</p>
					
					<label>Nueva contraseña</label>
					<input type="password" name="pass" />
					<label>Repita nueva contraseña</label>
					<input type="password" name="pass2" />
				</div>
				<button>Guardar cambios</button>
				<p class="error">
					Los datos ingresados no son correctos.
				</p>
			</fieldset>
		</form>
	</div>
</section>
<script>
	$( function() {
		
		$('#frmModificarDatos').submit( function(e) {
			e.preventDefault();
			
			$this = $(this);
			$button = $('button', $this);
			$button.data('texto', $button.text());
			
			$button.text('Guardando...');
			$button.attr('disabled', true);
			
			$('p.error', $this).removeClass('visible');
			
			$.post( $this.attr('action'), $this.serialize(), function(data) {
				
				if ( data.status == 'ok' )
				{
					alert('Tus datos fueron guardados');
					$button.text('redirigiendo...');
					location.href = 'usuarios/panel';
				}
				else
				{
					
					$.each( data.fields, function(index, value) {
						$('#frmModificarDatos input[name="' + index + '"]').addClass('error');
						
						if ( (index == 'pass' || index == 'email') && value != 'requerido' )
							$('#frmModificarDatos p.error').addClass('visible').text(value);
					});
					
					$button.text( $button.data('texto') );
					$button.attr('disabled', false);
				}
			}, 'json');
			
		} );
		
		$('#frmModificarDatos input').focus( function() {
			$(this).removeClass('error');
		});
	});
</script>