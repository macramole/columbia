<section id="registrarse">
	<h1>¿No tenés cuenta?</h1>
	
	<p>
		<strong>Las ventajas son:</strong>
	</p>
	<ul class="intro">
		<li>· Manejar la agenda de tus cursos</li>
		<li>· Saber el estado de pago y deudas</li>
		<li>· Recibir material de los cursos</li>
		<li>· Recibir nuestro "Boletín de novedades"</li>
	</ul>
	
	
	<div class="forms">
		<form id="frmRegistro">
			<fieldset class="registro">
				<label>Nombre:</label>
				<input type="text" name="nombre" />
				<label>Apellido:</label>
				<input type="text" name="apellido" />
				<label>Correo electrónico:</label>
				<input type="text" name="email" />
				<label>Celular:</label>
				<input type="text" name="celular" />
				<p>Pedimos este dato para avisarte en caso de cancelaciones o cambios, no es obligatorio.</p>
				<label>Contraseña:</label>
				<input type="password" name="pass" />
				<label>Repetir contraseña:</label>
				<input type="password" name="pass2" />
				<div class="check">
					<input type="checkbox" name="news" checked="checked" />
					<label>Deseo recibir noticias de la Fundación.</label>
				</div>
				<div class="clear"></div>
				
				<div class="error"></div>
				<button type="submit">Registrarse</button>
			</fieldset>
		</form>
		<form id="frmLogin">
			<fieldset class="login">
				<h2>¡Tengo Cuenta!</h2>
				<label>E.mail:</label>
				<input type="text" name="email" />
				<label>Contraseña:</label>
				<input type="password" name="pass" />

				<div class="error hidden"></div>
				<button type="submit">ingresar</button>
			</fieldset>
		</form>
	</div>

	<p class="outro">Ingresar la próxima vez por <strong>¡Tengo cuenta!</strong></p>
</section>