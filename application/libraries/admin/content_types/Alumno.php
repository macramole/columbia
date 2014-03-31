<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Alumno extends ContentType {
	
	const MAIL_CONFIRM_HASH = '_&/343aGke$';
	
	function __construct($id = null)
	{
		/*** Datos básicos ***/
		$this->name = "Alumno";
		$this->table = "alumnos";
		$this->hayPaginaIndividual = false;
		$this->returnURL = 'usuarios/verPanel/{id}';
		
		/*** Fields ***/
		$this->fields['nombre'] = new Textbox();
		$this->fields['apellido'] = new Textbox();
		$this->fields['email'] = new Textbox(null,'Si se trata de un alumno nuevo, se le enviará un email con una contraseña generada por el sistema-');
		$this->fields['celular'] = new Textbox();
		//$this->fields['activo'] = new SimpleSelect();
		
		
		
		/*** Extras ***/	
		//$this->fields['activo']->setValues(array( 0 => '0', 1 => '1' ));
		$this->addCustomListButton('Ver pagos', 'pago_24.png', 'usuarios/verPanel/');
		
		$this->setListableFields(array('nombre', 'apellido', 'email'));
		
		parent::__construct($id);
	}
	
	function save()
	{
		if ( $this->getOperation() == self::OPERATION_CREATE )
			$sendEmail = true;
		
		parent::save(); //una vez que guardo el operation me cambia a EDIT...
		
		if ( $sendEmail )
		{
			$pass = $this->generatePassword();
			$this->ci->db->update($this->table, array('pass' => md5($pass)), array('id' => $this->id));

			$this->enviarEmailRegistro($_POST['nombre'] . ' ' . $_POST['apellido'], $pass, $_POST['email']);
		}
	}
	
	function delete()
	{
		parent::delete();
		
		$this->ci->db->delete('alumnos_actividades_dias', array('idAlumno' => $this->id));
	}
	
	function validate()
	{
		parent::validate();
		
		$this->ci->form_validation->set_rules('nombre','','required');
		$this->ci->form_validation->set_rules('apellido','','required');
		$this->ci->form_validation->set_rules('email','','required|valid_email');
		
		if ( trim($_POST['email']) )
		{
			$email = $this->ci->db->get_where('alumnos', array('email' => trim($_POST['email']) ))->row_array();
		
			if ( $email )
			{
				if ( $this->getOperation() == self::OPERATION_CREATE || $email['id'] != $this->id )
				{
					$this->ci->form_validation->set_error('ya registrado', 'email');
				}
			}
			
			
		}
		
		if ($this->ci->form_validation->run() == true)
			return null;
		else
		{
			return $this->ci->form_validation->get_error_array();
		}
	}
	
	private function enviarEmailRegistro($nombre, $pass, $email)
	{
		$mail = $this->ci->load->view('mails/registrado_confirmacion', array(
			'nombre' => $nombre,
			'pass' => $pass,
			'confirmHash' => $this->generateConfirmationHash($email)
		), true);
		
		magico_sendmail($email, 'Fundación Columbia - Registración', $mail, 'info@fundacioncolumbia.org');
	}
	
	private function generatePassword()
	{
		$chars = "abcdefghijkmnopqrstuvwxyz023456789";
		srand((double)microtime()*1000000);
		
		$i = 0;
		$pass = '' ;

		while ($i <= 6) {

			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
			$i++;
		}
		return $pass;
	}
	
	public static function generateConfirmationHash($email)
	{
		return md5($email . self::MAIL_CONFIRM_HASH);
	}

	
	public static function archivarInscripcion($idAlumno, $idDisciplina)
	{
		$ci =& get_instance();
		
		$sqlArchivo = "
			INSERT INTO
				`alumnos_actividades_dias_archivo` ( `idDisciplinaActividadDia`, `idAlumno`, `fechaDesde`, `fechaHasta`, `MP_external_reference`, `fechaReserva` )
			( SELECT
				`idDisciplinaActividadDia`,
				idAlumno,
				fechaDesde,
				fechaHasta,
				MP_external_reference,
				fechaReserva
			FROM
				alumnos_actividades_dias aad
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
				aad.vacante = 1 AND aad.idAlumno = ? )
		";

		$ci->db->query($sqlArchivo, array($idDisciplina, $idAlumno));

		$sqlDelete = "
			DELETE FROM
				alumnos_actividades_dias
			WHERE
				`idDisciplinaActividadDia` IN
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
				vacante = 1 AND idAlumno = ?
		";

		$ci->db->query($sqlDelete, array($idDisciplina, $idAlumno));
	}
	
	public static function archivarInscripcionEspecial($idAlumno, $idDisciplinaActividadEspecial)
	{
		$ci =& get_instance();
		
		$sqlArchivo = "
			INSERT INTO
				`alumnos_actividades_especiales_archivo`
				(idAlumno, idDisciplinaActividadEspecial, MP_external_reference, fechaDesde, fechaHasta, activo, vacante, pagoVacante)
			SELECT
				idAlumno, 
				idDisciplinaActividadEspecial, 
				MP_external_reference, 
				fechaDesde, 
				fechaHasta, 
				activo, 
				vacante, 
				pagoVacante
			FROM
				alumnos_actividades_especiales
			WHERE
				idDisciplinaActividadEspecial = ? AND
				idAlumno = ?

		";

		$ci->db->query($sqlArchivo, array($idDisciplinaActividadEspecial, $idAlumno)); //$this->siteuser->getUserData('id')

		$ci->db->delete('alumnos_actividades_especiales', 
				array('idAlumno' => $idAlumno, 'idDisciplinaActividadEspecial' => $idDisciplinaActividadEspecial));
	}
	
	public static function desactivarInscripcion($idAlumno, $idDisciplina)
	{
		$ci =& get_instance();
		
		$sqlDesactivar = "
			UPDATE
				`alumnos_actividades_dias` aad
			INNER JOIN
				`disciplinas_actividades_dias` dad ON
				dad.`id` = aad.`idDisciplinaActividadDia`
			INNER JOIN
				`disciplinas_actividades` da ON
				da.`id` = dad.`idActividad` 
			SET
				aad.activo = 0
			WHERE
				da.`idDisciplina` = ? AND
				aad.`idAlumno` = ?
		";
		
		$ci->db->query($sqlDesactivar, array($idDisciplina, $idAlumno));
	}
	
	public static function desactivarInscripcionEspecial($idAlumno, $idDisciplinaActividadEspecial)
	{
		$ci =& get_instance();
		
		$sqlDesactivar = "
			UPDATE
				`alumnos_actividades_especiales`
			SET
				activo = 0
			WHERE
				`idDisciplinaActividadEspecial` = ? AND
				`idAlumno` = ?
		";
		
		$ci->db->query($sqlDesactivar, array($idDisciplinaActividadEspecial, $idAlumno));
	}
}