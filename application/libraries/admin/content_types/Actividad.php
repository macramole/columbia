<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once('application/libraries/admin/content_types/Disciplina.php');
include_once('application/libraries/admin/content_types/Frecuencia.php');
include_once('application/libraries/admin/content_types/Dia.php');

class Actividad extends ContentType {
	
	function __construct($id = null)
	{
		/*** Datos básicos ***/
		$this->name = "Actividad";
		$this->table = "disciplinas_actividades";
		$this->hayPaginaIndividual = false;
		$this->returnURL = '{idDisciplina}';
		
		/*** Fields ***/
		$this->fields['idDisciplina'] = new DatabaseSelect( new Disciplina(), null, false, 'Disciplina' );
		$this->fields['idDocentes'] = new DatabaseMultiSelect( new Docente(), 'disciplinas_actividades_docentes');
		$this->fields['dia'] = new DatabaseChecklist(new Dia(), 'disciplinas_actividades_dias');
		$this->fields['idFrecuencia'] = new DatabaseSelect( new Frecuencia() );
		$this->fields['horaDesde'] = new HourPicker('Desde las');
		$this->fields['horaHasta'] = new HourPicker('Hasta las');
		$this->fields['idSala'] = new DatabaseSelect( new Sala(), null, false, 'Sala' );
		$this->fields['precio'] = new Textbox('Precio','Se usará este valor en vez de el de la configuración general y la disciplina');
		$this->fields['vacantes'] = new Textbox();
		
		/*** Extras ***/
		$this->fields['dia']->addNew = false;
		
		$this->setListableFields(array('idDisciplina', 'horaDesde', 'horaHasta', 'vacantes'));
		
		parent::__construct($id);
	}
	
	function delete()
	{
		//checkeo que no esté sacando una actividad con algun alumno anotado
		
		$sqlDias = "
			SELECT
				dad.*
			FROM
				alumnos_actividades_dias aad
			INNER JOIN
				disciplinas_actividades_dias dad ON
				aad.idDisciplinaActividadDia = dad.id
			WHERE
				dad.idActividad = ? AND
				aad.activo = 1
		";

		$arrDias = $this->ci->db->query($sqlDias, array($this->id))->result_array();
		
		if ( count($arrDias) )
			$foo->tirarEerror(); //super trucho pero funciona. TODO: hacerlo bien !
		else
		{
			parent::delete();
			
			$alumnosActividadesDiasDeleteSQL = "
				DELETE FROM
					alumnos_actividades_dias
				WHERE
					idDisciplinaActividadDia IN (
				SELECT
					id
				FROM
					disciplinas_actividades_dias dad
				WHERE
					idActividad = ?
				)
			";
			
			$this->ci->db->query($alumnosActividadesDiasDeleteSQL, array($this->id));
			$this->ci->db->delete('disciplinas_actividades_dias', array('idActividad' => $this->id) );
		}
	}
	
	function validate()
	{
		parent::validate();
		
		//checkeo que no esté sacando algun día que algun alumno esté anotado
		if ( $this->getOperation() == self::OPERATION_EDIT )
		{
			$sqlDias = "
				SELECT
					dad.*
				FROM
					alumnos_actividades_dias aad
				INNER JOIN
					disciplinas_actividades_dias dad ON
					aad.idDisciplinaActividadDia = dad.id
				WHERE
					dad.idActividad = ? AND
					aad.activo = 1
			";
			
			$arrDias = $this->ci->db->query($sqlDias, array($this->id))->result_array();
			$arrDiasChecked = count($_POST['dia']) ? $_POST['dia'] : array();
			
			if ( count($arrDias) )
			{
				$error = false;
				
				foreach( $arrDias as $dia )
				{
					if ( !in_array($dia['idDia'], $arrDiasChecked) )
					{
						$error = true;
						break;
					}
				}
				
				if ( $error )
				{
					$this->ci->form_validation->set_error( 'hay alumnos anotados en días eliminados', 'dia' );
				}
			}
		}
		
		$this->ci->form_validation->set_rules('dia','','required');
		$this->ci->form_validation->set_rules('idDocentes','','required');
		
		if ($this->ci->form_validation->run() == true)
			return null;
		else
		{
			return $this->ci->form_validation->get_error_array();
		}
	}
}