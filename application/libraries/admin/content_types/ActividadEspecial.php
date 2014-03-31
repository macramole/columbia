<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once('application/libraries/admin/content_types/Disciplina.php');

class ActividadEspecial extends ContentType {
	
	const MODALIDAD_MONTO_FIJO = 'Monto fijo';
	const MODALIDAD_MONTO_MENSUAL = 'Mensual';
	
	function __construct($id = null)
	{
		/*** Datos básicos ***/
		$this->name = "Actividad Especial";
		$this->table = "disciplinas_actividades_especiales";
		$this->hayPaginaIndividual = true;
		$this->returnURL = 'agenda/{title}';
		
		/*** Fields ***/
		$this->fields['idDisciplina'] = new DatabaseSelect( new Disciplina() );
		$this->fields['idDocentes'] = new DatabaseMultiSelect( new Docente(), 'disciplinas_actividades_especiales_docentes');
		$this->fields['title'] = new Textbox('Nombre');
		$this->fields['imagenes'] = new FileUpload();
		$this->fields['lugar'] = new Textbox();
		$this->fields['fecha'] = new DatePicker('Fecha desde');
		$this->fields['fechaHasta'] = new DatePicker('Fecha hasta');
		$this->fields['horaDesde'] = new HourPicker('Desde las');
		$this->fields['horaHasta'] = new HourPicker('Hasta las');
		$this->fields['frecuencia'] = new Textbox();
		$this->fields['modalidadPago'] = new SimpleSelect('Modalidad de pago');
		
		$this->fields['precio'] = new Textbox();
		$this->fields['precioReserva'] = new Textbox('Valor de reserva');
		$this->fields['precioDescuento1'] = new Textbox('Primer precio de descuento');
		$this->fields['precioDescuento2'] = new Textbox('Segundo precio de descuento');
		$this->fields['hayDescuento'] = new Checkbox('Descuento poblacional');
		
		$this->fields['vacantes'] = new Textbox();
		$this->fields['textoDestacado'] = new TextEditor('Texto destacado');
		$this->fields['cuerpo'] = new TextEditor('Descripción');
		
		
		/*** Extras ***/
		$this->fields['imagenes']->isImage();
		$this->fields['imagenes']->dimensionesRecomendadas = '380x285';
		$this->fields['cuerpo']->extraTags = array('h2');
		$this->fields['modalidadPago']->setValues( array( self::MODALIDAD_MONTO_FIJO => self::MODALIDAD_MONTO_FIJO, self::MODALIDAD_MONTO_MENSUAL => self::MODALIDAD_MONTO_MENSUAL) );
		$this->fields['idDisciplina']->addDefaultOption = '-- Ninguna --';
		
		
		$this->setListableFields(array('idDisciplina', 'title', 'fecha', 'horaDesde', 'horaHasta'));
		
		parent::__construct($id);
	}
	
	function validate()
	{
		parent::validate();
		
		$this->ci->form_validation->set_rules('title','','required');
		$this->ci->form_validation->set_rules('fecha','','required');
		
		if ( $_POST['fecha'] )
		{
			$fechaDesde = strtotime($_POST['fecha']);
			$fechaHasta = strtotime($_POST['fechaHasta']);
			
			if ( $fechaHasta < $fechaDesde )
			{
				$this->ci->form_validation->set_error('debe ser igual o mayor', 'fechaHasta');
			}
		}
		
		if ($this->ci->form_validation->run() == true)
			return null;
		else
		{
			return $this->ci->form_validation->get_error_array();
		}
	}
}