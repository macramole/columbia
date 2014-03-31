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
			Tu pago ha sido acreditado. Estás inscripto en:
		</p>
		<?php include('inscripcion_regular_horarios.inc.php') ?>
		Esta inscripción tiene validez entre el <?= $rangoFechas[0] ?> y el <?= $rangoFechas[1] ?>. 
		Cerca de la fecha de vencimiento te enviaremos un mail para su renovación.<br/><br/>
		
		<?php include('mail_contacto.php')  ?>
	</div>
  </body>
</html>