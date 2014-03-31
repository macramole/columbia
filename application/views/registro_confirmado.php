<section id="inscripcion">
	<?php if ( $ok )  : ?>
	<h1>Registro confirmado</h1>
	<p>
		Gracias por ser parte de Fundación Columbia.
	</p>
	<p>
		Tu cuenta ha sido activada. Ya puedes <a class="ingresar" href="#">ingresar</a> a la web utilizando el email y la contraseña elegida.
	</p>
	<?php else : ?>
	<h1>Error en registro</h1>
	<p>
		El código de inscripción ingresado es erróneo o ya ha sido activado. Por favor, volvé a intentarlo.
	</p>
	<?php endif; ?>
	
	<a href="<?= base_url() ?>">
		&lt;&lt; Volver
	</a>
	<script>
		$( function() {
			$('section#inscripcion a.ingresar').click( function(e) {
				e.preventDefault();
				$('header .login a.ingresar').click();
			});
		});
	</script>
</section>