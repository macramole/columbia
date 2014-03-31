<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once('MasterControllerColumbia.php');
include_once('application/libraries/admin/content_types/Alumno.php');

class Usuarios extends MasterControllerColumbia
{	
	public function registrarse()
	{
		$this->addContentPage('registrarse');
		$this->show();
	}
	
	public function logout()
	{
		$this->siteuser->logout();
		redirect();
	}
	
	public function registro_exitoso()
	{
		$this->setTitle("Registro exitoso");
		$this->addContentPage('registro_exitoso');
		$this->show();
	}
	
	public function registro() //ajax
	{
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('nombre','','trim|required');
		$this->form_validation->set_rules('apellido','','trim|required');
		$this->form_validation->set_rules('email','','trim|required|valid_email');
		$this->form_validation->set_rules('pass','La contraseña','trim|min_length[6]|matches[pass2]|required');
		
		$email = $this->db->get_where('alumnos', array('email' => trim($_POST['email']) ))->row_array();
		
		if ( $email )
			$this->form_validation->set_error('El email ya está registrado.', 'email');
		 
		if ( $this->form_validation->run() == true )
		{
			$data = array(
				'nombre' => $_POST['nombre'],
				'apellido' => $_POST['apellido'],
				'email' => $_POST['email'],
				'pass' => md5($_POST['pass']),
				'celular' => $_POST['celular'],
				'fechaRegistro' => null	,
				'activo' => 0
			);
			
			if ( $_POST['news'] )
			{
				$this->load->library('mcapi');
				$this->mcapi->listSubscribe('f42c45d1d3', $_POST['email'], null, 'html', false, true);
			}
			
			$this->db->insert('alumnos', $data);
			
			// AUTO LOGIN $this->siteuser->setUserData( $this->db->insert_id() );
			
			$this->enviarEmailRegistro($data['nombre'] . ' ' . $data['apellido'], $_POST['pass'], $data['email']);
			
			echo json_encode(array('status' => 'ok'));
		}
		else
		{
			echo json_encode(array('status' => 'error', 'fields' => $this->form_validation->get_error_array()));
		}
	}
	
	public function ingresar() //ajax
	{
		$loginResult = $this->siteuser->login($_POST['email'], $_POST['pass'], $_POST['recordar']);
		
		switch($loginResult)
		{
			case SiteUser::LOGGED_IN:
				echo json_encode( array('status' => 'ok') );
				break;
			case SiteUser::NOT_LOGGED_IN:
				echo json_encode( array('status' => 'error', 'error' => 'Datos incorrectos.') );
				break;
			case SiteUser::NOT_LOGGED_IN_INVALID_PASS:
				echo json_encode( array('status' => 'error', 'error' => "Datos incorrectos. <br /> <a href='usuarios/olvido?e=$_POST[email]'>¿Olvidaste tu contraseña?</a>") );
				break;
			case SiteUser::NOT_ACTIVE:
				echo json_encode( array('status' => 'error', 'error' => "Aún no confirmaste tu email. <br /> <a href='usuarios/reenviarConfirmacion?e=$_POST[email]'> ¿Reenviar email de confirmación? </a>") );
				break;
			default:
				echo json_encode( array('status' => 'error', 'error' => 'Datos incorrectos.') );
				break;	
		}
	}
	
	public function panel()
	{
		if ( !$this->siteuser->isLogged() )
			redirect();
		
		$sqlHorarios = "
			SELECT
				da.`id` AS idActividad,
				dis.title AS disciplina,
				dis.id as idDisciplina,
				TIME_FORMAT(da.`horaDesde`,'%H:%i') AS horaDesde, 
				TIME_FORMAT(da.`horaHasta`,'%H:%i') AS horaHasta, 
				GROUP_CONCAT(d.title ORDER BY d.id SEPARATOR ', ') AS dia,
				aad.activo,
				aad.fechaDesde,
				aad.fechaHasta,
				f.title as frecuencia,
				cu.url AS disciplinaURL
			FROM
				`disciplinas_actividades` da
			INNER JOIN
				`disciplinas_actividades_dias` dad ON
				dad.`idActividad` = da.`id`
			INNER JOIN
				dias d ON
				d.`id` = dad.`idDia`
			INNER JOIN
				`alumnos_actividades_dias` aad ON
				aad.`idDisciplinaActividadDia` = dad.`id`
			INNER JOIN
				`disciplinas` dis ON
				dis.id = da.`idDisciplina`
			INNER JOIN
				frecuencias f ON
				f.id = da.idFrecuencia
			LEFT JOIN
				`clean_urls` cu ON
				cu.`node_id` = dis.`id` AND cu.`table` = 'disciplinas'
			WHERE
				aad.`idAlumno` = ? AND
				aad.vacante = 1
			GROUP BY
				da.id
			ORDER BY
				dis.title, f.id, dad.`idDia`, da.`horaDesde`
		";
		
		$arrHorarios = $this->db->query($sqlHorarios,array( $this->siteuser->getUserData('id') ))->result_array();
		
		if ( count($arrHorarios) )
		{
			$arrIn = array();
			foreach($arrHorarios as &$horario)
			{
				$arrIn[] = $horario['idActividad'];
			}
			$strIn = implode(',', $arrIn);
			
			$sqlDocentes = "
				SELECT
					dad.`idActividad` ,
					d.`title`,
					cu.`url`
				FROM
					`disciplinas_actividades_docentes` dad
				INNER JOIN
					`docentes` d ON
					d.id = dad.`idDocente`
				INNER JOIN
					`disciplinas_actividades` da ON
					da.`id` = dad.`idActividad`
				INNER JOIN
					`clean_urls` cu ON
					cu.`node_id` = d.`id` AND cu.`table` = 'docentes'
				WHERE
					dad.idActividad IN ($strIn)
			";
			
			$arrDocentes = $this->db->query($sqlDocentes)->result_array();
			
			$docentes = array();
			foreach($arrDocentes as &$docente)
				$docentes[$docente['idActividad']][] = $docente;
			
			$horarios = array();
			foreach($arrHorarios as &$horario)
			{
				if ( $horario['dia'] &&  strrpos($horario['dia'], ', ') )
					$horario['dia'] = substr_replace($horario['dia'], ' y ', strrpos($horario['dia'], ', '), 1 );
				
				$horario['docentes'] = $docentes[$horario['idActividad']];
				
				$arrDisciplinaUrl = explode("/", $horario['disciplinaURL']);
				
				$horario['disciplinaInscripcionURL'] = 'inscripciones/' . $arrDisciplinaUrl[1] . '/' . $arrDisciplinaUrl[2];
				
				$horarios[$horario['disciplina']][] = $horario;
			}
		}
		
		/* ACTIVIDADES ESPECIALES */
		magico_setLocale('es');
		
		$sqlActividadesEspeciales = "
			SELECT
				dae.*,
				aae.pagoVacante,
				aae.activo,
				aae.fechaHasta AS pagoFechaHasta
			FROM
				`disciplinas_actividades_especiales` dae
			INNER JOIN
				`alumnos_actividades_especiales` aae ON
				aae.`idDisciplinaActividadEspecial` = dae.`id`
			WHERE
				aae.`idAlumno` = ? AND
				aae.`vacante` = 1
		";
		
		$arrActividadesEspeciales = $this->db->query($sqlActividadesEspeciales, array( $this->siteuser->getUserData('id') ))->result_array();
		$actividadesEspeciales = array();
		
		if ( count($arrActividadesEspeciales) )
		{
			$arrIn = array();

			foreach ( $arrActividadesEspeciales as &$actividad )
			{
				$time = strtotime($actividad['fecha']);
				$fecha = strftime('%A', $time);
				$fecha = ucfirst( utf8_encode($fecha) );
				$fecha .= strftime(' %#d de %B', $time);
				$fecha .= ', ' . substr($actividad['horaDesde'],0,5) . 'hs';
				
				$actividad['fechaHastaRaw'] = $actividad['fechaHasta'];
				
				if ( $actividad['fechaHasta'] && $actividad['fecha'] != $actividad['fechaHasta']  )
				{
					$time = strtotime($actividad['fechaHasta']);
					$actividad['fechaHasta'] = strftime('%A', $time);
					$actividad['fechaHasta'] = ucfirst( utf8_encode($actividad['fechaHasta']) );
					$actividad['fechaHasta'] .= strftime(' %#d de %B', $time);
					$actividad['fechaHasta'] .= ', ' . substr($actividad['horaDesde'],0,5) . 'hs';

					$fecha .= ' - ' . $actividad['fechaHasta'];
				}

				$actividad['fecha'] = $fecha;
				$actividadesEspeciales[ $actividad['id'] ] = $actividad;

				$arrIn[] = $actividad['id'];
			}

			$strIn = implode(',', $arrIn);

			$sqlDocentesEspeciales = "
				SELECT
					daed.`idActividadEspecial`,
					d.title,
					cu.`url`
				FROM
					docentes d
				INNER JOIN
					`disciplinas_actividades_especiales_docentes` daed ON
					daed.`idDocente` = d.`id`
				INNER JOIN
					`clean_urls` cu ON
					cu.`node_id` = d.`id` AND cu.`table` = 'docentes'
				WHERE
					daed.`idActividadEspecial` IN ( $strIn )
			";

			$arrDocentesEspeciales = $this->db->query($sqlDocentesEspeciales)->result_array();

			foreach ( $arrDocentesEspeciales as $docente )
			{
				$actividadesEspeciales[ $docente['idActividadEspecial'] ]['docentes'][] = $docente;
			}
		}
		
		$this->setTitle("Panel del usuario");
		$this->addContentPage('dashboard', array('horarios' => $horarios, 'actividadesEspeciales' => $actividadesEspeciales));
		$this->show(array(), false, true);
	}
	
	public function modificarPago()
	{
		if ( !$this->adminuser->isLogged() )
			exit;
		
		if ( $_POST['idDisciplina'] ) //Actividad regular
		{
			if ( $_POST['desde'] && $_POST['hasta'] )
			{
				$arrDesde = explode('/', $_POST['desde']);
				$desde = $arrDesde[2] . '-' . $arrDesde[1] . '-' . $arrDesde[0];

				$arrHasta = explode('/', $_POST['hasta']);
				$hasta = $arrHasta[2] . '-' . $arrHasta[1] . '-' . $arrHasta[0];

				if ( strtotime($desde) > strtotime($hasta) )
					echo json_encode (array('status' => 'error', 'error' => 'La fecha inicial debe ser menor a la fecha final'));
				else if ( strtotime($hasta) < time() )
					echo json_encode (array('status' => 'error', 'error' => 'La fecha final debe ser mayor a hoy'));
				else
				{	
					$sql = "
						UPDATE
							alumnos_actividades_dias aad
						SET
							fechaDesde = ?,
							fechaHasta = ?,
							activo = 1
						WHERE
							aad.`idDisciplinaActividadDia` IN
								(
									SELECT
										dad.id
									FROM
										`disciplinas_actividades_dias` dad
									INNER JOIN
										`disciplinas_actividades` da ON
										da.`id` = dad.`idActividad`
									WHERE
										da.`idDisciplina` = ?
								)
							AND
							aad.`vacante` = 1 AND
							aad.idAlumno = ?
					";

					$this->db->query($sql, array($desde, $hasta, $_POST['idDisciplina'], $this->siteuser->getUserData('id')));

					$this->enviarEmailRegular('approved', $_POST['idDisciplina'], array($_POST['desde'], $_POST['hasta']));

					echo json_encode (array('status' => 'ok'));
				}
			}
			else
			{
				if ( !$_POST['desde'] )
					echo json_encode (array('status' => 'error', 'error' => 'Debe ingresar la fecha inicial'));

				if ( !$_POST['hasta'] )
					echo json_encode (array('status' => 'error', 'error' => 'Debe ingresar la fecha final'));
			}
		}
		elseif ( $_POST['idActividad'] ) //Actividad especial
		{
			if ( $_POST['hasta'] )
			{
				$arrHasta = explode('/', $_POST['hasta']);
				$hasta = $arrHasta[2] . '-' . $arrHasta[1] . '-' . $arrHasta[0];
				
				$sql = "
					UPDATE
						alumnos_actividades_especiales
					SET
						fechaHasta = ?,
						pagoVacante = ?,
						activo = 1
					WHERE
						idDisciplinaActividadEspecial = ? AND
						idAlumno = ? AND
						`vacante` = 1
				";

				$this->db->query($sql, array($hasta, $_POST['soloVacante'], $_POST['idActividad'], $this->siteuser->getUserData('id')));

				$this->enviarEmailEspecial('approved', $_POST['idActividad'], $_POST['soloVacante'], $hasta );

				echo json_encode (array('status' => 'ok'));
			}
			else
			{
				echo json_encode (array('status' => 'error', 'error' => 'Debe ingresar la fecha hasta'));
			}
		}
		else
		{
			echo json_encode (array('status' => 'error', 'error' => 'Ocurrió un error. Por favor recargue la página y vuelva a intentarlo'));
		}
		
	}
	
	public function eliminarPago()
	{
		if ( !$this->adminuser->isLogged() )
			exit;
		
		if ( $_POST['id'] && $_POST['type'] == 'regular' )
		{	
			Alumno::archivarInscripcion($this->siteuser->getUserData('id'), $_POST['id']);
			
			echo json_encode (array('status' => 'ok'));
		}
		elseif ( $_POST['id'] && $_POST['type'] == 'especial' ) //Actividad especial
		{
			
			Alumno::archivarInscripcionEspecial($this->siteuser->getUserData('id'), $_POST['id']);
			
			echo json_encode (array('status' => 'ok'));
		}
		else
			echo json_encode (array('status' => 'error', 'error' => 'Ocurrió un error. Por favor recargue la página y vuelva a intentarlo'));
	}
	
	public function verPanel($id)
	{
		if ( !$this->adminuser->isLogged() )
			exit;
		
		$this->siteuser->forceLogin($id);
		redirect('usuarios/panel');
	}
	
	public function modificarDatos()
	{
		if ( !$this->siteuser->isLogged() )
			redirect();
		
		$user = $this->siteuser->getUserData();
		
		$this->setTitle("Modificar mis datos");
		$this->addContentPage('modificar_datos', array( 'user' => $user ) );
		$this->show(array(), false, true);
	}
	
	public function grabarDatos()
	{
		if ( !$this->siteuser->isLogged() )
			redirect();
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('nombre','','trim|required');
		$this->form_validation->set_rules('apellido','','trim|required');
		$this->form_validation->set_rules('email','','trim|required|valid_email');
		
		if ( $_POST['pass'] )
			$this->form_validation->set_rules('pass','La nueva contraseña','trim|min_length[6]|matches[pass2]');
		
		if ( trim($_POST['email']) != $this->siteuser->getUserData('email') )
		{
			$email = $this->db->get_where('alumnos', array('email' => trim($_POST['email']) ))->row_array();

			if ( $email )
				$this->form_validation->set_error('El email ya está registrado.', 'email');
		}
		
		
		if ( $this->form_validation->run() == true )
		{
			$data = array(
				'nombre' => $_POST['nombre'],
				'apellido' => $_POST['apellido'],
				'email' => $_POST['email'],
				'pass' => md5($_POST['pass']),
				'celular' => $_POST['celular']
			);
			
			$this->db->update('alumnos', $data, array('id' => $this->siteuser->getUserData('id')));
			
			echo json_encode(array('status' => 'ok'));
		}
		else
		{
			echo json_encode(array('status' => 'error', 'fields' => $this->form_validation->get_error_array()));
		}
	}
	
	public function olvido()
	{
		$email = $this->db->get_where('alumnos', array('email' => trim($_GET['e']) ))->row_array();
		$mensaje = '¡ No te preocupes ! <br /><br /> Te hemos enviado un correo electrónico a tu casilla para que puedas recuperarla.';
		
		if ( $email )
		{
			$newPass = $this->siteuser->resetPassword($email['id']);
			$this->enviarEmailOlvido($email['nombre'], $newPass, $email['email']);
		}
		else
		{
			redirect('/');
		}
		
		
		$this->addContentPage('olvido', array('mensaje' => $mensaje));
		$this->show();
	}
	
	public function reenviarConfirmacion()
	{
		$email = $this->db->get_where('alumnos', array('email' => trim($_GET['e']) ))->row_array();
		
		if ( $email && !$email['activo'] )
		{
			$this->enviarEmailRegistro($email['nombre'] . ' ' . $email['apellido'], null, $email['email']);
		}
		else
		{
			redirect('/');
		}
		
		$this->addContentPage('reenviar_confirmacion');
		$this->show();
	}
	
	public function confirmar_email($hash)
	{
		$sql = "
			SELECT
				*
			FROM
				alumnos
			WHERE
				MD5(CONCAT(email,?)) = ? AND activo = 0
			LIMIT 1
		";
		
		$user = $this->db->query($sql, array(Alumno::MAIL_CONFIRM_HASH, $hash ))->row_array();
		$ok = false;
		
		if ( $user )
		{
			$ok = true;
			$this->db->update('alumnos', array('activo' => 1, 'fechaRegistro' =>  date('Y-m-d H:i:s')), array('id' => $user['id']));
		}
			
		
		$this->setTitle("Confirmación de datos");
		$this->addContentPage('registro_confirmado', array('ok' => $ok));
		$this->show();
		
	}
	
	private function enviarEmailRegular( $status, $idDisciplina, $rangoFechas )
	{
		$user = $this->siteuser->getUserData();

		$sqlHorarios = "
			SELECT
				dis.title AS disciplina,
				TIME_FORMAT(da.`horaDesde`,'%H:%i') AS horaDesde, 
				TIME_FORMAT(da.`horaHasta`,'%H:%i') AS horaHasta, 
				GROUP_CONCAT(d.title ORDER BY d.id SEPARATOR ', ') AS dia
			FROM
				`disciplinas_actividades` da
			INNER JOIN
				`disciplinas_actividades_dias` dad ON
				dad.`idActividad` = da.`id`
			INNER JOIN
				dias d ON
				d.`id` = dad.`idDia`
			INNER JOIN
				`alumnos_actividades_dias` aad ON
				aad.`idDisciplinaActividadDia` = dad.`id`
			INNER JOIN
				`disciplinas` dis ON
				dis.id = da.`idDisciplina`
			WHERE
				aad.`idAlumno` = ? AND dis.id = ?
			GROUP BY
				horaDesde, horaHasta
			ORDER BY
				dis.title, dad.`idDia`, da.`horaDesde`
		";

		$arrHorarios = $this->db->query($sqlHorarios, array( $user['id'], $idDisciplina ))->result_array();
		$horarios = array();

		foreach( $arrHorarios as $horario )
		{
			if ( $horario['dia'] &&  strrpos($horario['dia'], ', ') )
				$horario['dia'] = substr_replace($horario['dia'], ' y ', strrpos($horario['dia'], ', '), 1 );

			$horarios[] = "$horario[dia] de $horario[horaDesde] a $horario[horaHasta] hs";
		}

		$mail = $this->load->view('mails/inscripcion_regular_' . $status, array(
			'horarios' => $horarios,
			'disciplina' => $arrHorarios[0]['disciplina'],
			'nombre' => $user['nombre'] . ' ' . $user['apellido'],
			'rangoFechas' => $rangoFechas
		), true);
		
		magico_sendmail($user['email'], 'Fundación Columbia - Pago acreditado', $mail, 'info@fundacioncolumbia.org');
	}

	private function enviarEmailEspecial( $status, $idActividadEspecial, $soloVacante, $fechaHasta )
	{
		$user = $this->siteuser->getUserData();
		magico_setLocale('es');
				
		$sqlActividad = "
			SELECT
				*
			FROM 
				`disciplinas_actividades_especiales`
			WHERE
				`id` = ?
		";

		$actividad = $this->db->query($sqlActividad, array( $idActividadEspecial ))->row_array();

		$time = strtotime($actividad['fecha']);
		$actividad['fecha'] = strftime('%A', $time);
		$actividad['fecha'] = ucfirst( utf8_encode($actividad['fecha']) );
		$actividad['fecha'] .= strftime(' %#d de %B', $time);
		$actividad['fecha'] .= ', ' . substr($actividad['horaDesde'],0,5) . 'hs';
		
		$mail = $this->load->view('mails/inscripcion_especial_' . $status, array(
			'actividad' => $actividad,
			'nombre' => $user['nombre'] . ' ' . $user['apellido'],
			'soloVacante' => $soloVacante,
			'fechaHasta' => date('d/m/Y', strtotime($fechaHasta) )
		), true);

		magico_sendmail($user['email'], 'Fundación Columbia - Aviso de pago', $mail, 'info@fundacioncolumbia.org');
	}
	
	private function enviarEmailRegistro($nombre, $pass, $email)
	{
		
		$mail = $this->load->view('mails/registrado_confirmacion', array(
			'nombre' => $nombre,
			'pass' => $pass,
			'confirmHash' => Alumno::generateConfirmationHash($email)
		), true);
		
		magico_sendmail($email, 'Fundación Columbia - Registración', $mail, 'info@fundacioncolumbia.org');
	}
	
	private function enviarEmailOlvido($nombre, $pass, $email)
	{
		
		$mail = $this->load->view('mails/olvido_pass', array(
			'nombre' => $nombre,
			'pass' => $pass
		), true);
		
		magico_sendmail($email, 'Fundación Columbia - Recuperar contraseña', $mail, 'info@fundacioncolumbia.org');
	}
}
