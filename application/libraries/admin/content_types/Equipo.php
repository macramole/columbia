<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once('application/libraries/admin/content_types/EquipoCategoria.php');

class Equipo extends ContentType {
	
	function __construct($id = null)
	{
		/*** Datos básicos ***/
		$this->name = "Miembro del equipo";
		$this->table = "equipo";
		$this->hayPaginaIndividual = true;
		$this->returnURL = 'informacion/equipo/{title}';
		
		/*** Fields ***/
		$this->fields['idEquipoCategoria'] = new DatabaseSelect(new EquipoCategoria(), null, false, "Categoria");
		$this->fields['miembroAnterior'] = new Checkbox('Miembro Anterior');
		$this->fields['title'] = new Textbox('Nombre y Apellido');
		$this->fields['imagen'] = new FileUpload();
		$this->fields['anio'] = new Textbox('Año');
		$this->fields['actividad'] = new Textbox('Actividad');
		$this->fields['textoCorto'] = new Textbox('Texto corto', 'Este es el texto corto que aparece en el listado');
		$this->fields['texto'] = new TextEditor('Bio');
		
		
		/*** Extras ***/	
		$this->fields['imagen']->isImage();
		$this->fields['imagen']->maxFilesAllowed = 1;
		$this->fields['imagen']->dimensionesRecomendadas = '340x220';
		$this->fields['idEquipoCategoria']->addDefaultOption = "Principal";
		
		$this->setListableFields(array('title'));
		
		parent::__construct($id);
	}
	
	function validate()
	{
		parent::validate();
		
		$this->ci->form_validation->set_rules('title','','required');
		
		if ($this->ci->form_validation->run() == true)
			return null;
		else
		{
			return $this->ci->form_validation->get_error_array();
		}
	}
}