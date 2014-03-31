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
			Para terminar el proceso de registro por favor <a href="<?= base_url("usuarios/confirmar_email/$confirmHash") ?>">hacé click aquí</a> o dirigite a la siguiente dirección:
		</p>
		<p>
			<?= base_url("usuarios/confirmar_email/$confirmHash") ?>
		</p>
		<?php if ( $pass ) : ?>
		<p>
			Tu contraseña es: <?= $pass ?>
		</p>
		<?php endif; ?>
		<br/>
		<?php include('mail_contacto.php')  ?>
	</div>
  </body>
</html>