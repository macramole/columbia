<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  </head>
  <body>
    <a href="http://www.fundacioncolumbia.org/"><img src="http://www.fundacioncolumbia.org/images/logo_fundacion_columbia_mail.jpg" alt="Fundación Columbia"/></a>
	<div class="info" style="background-color: #f4f4ec;padding: 25px;color: #818086;width: 550px;font-family: Arial, sans-serif;font-size: 13px">
		<?= $nombre ?>:
		<p style="margin-top: 0">
			Gracias por ser parte de Fundación Columbia. <br/>
			Tu pago ha sido acreditado.<br /> 
			<?php if ( $actividad['modalidadPago'] == 'Mensual' && !$soloVacante ) : ?>
			El mismo tiene validez hasta el <?= $fechaHasta ?>, 
			te enviaremos un mail cuando se acerque su plazo de renovación
			<br />
			<?php endif; ?>
			<?php if ( !$soloVacante ) : ?>
			<br />Estás inscripto en:
			<?php else : ?>
			<br />Has reservado vacante para:
			<?php endif; ?>
		</p>
		<?php include('inscripcion_especial_horarios.inc.php') ?>
		<?php if ( $soloVacante ) : ?>
			Debes abonar el monto restante para poder asistir a la misma. <br /><br />
		<?php endif; ?>
		<?php include('mail_contacto.php')  ?>
	</div>
  </body>
</html>