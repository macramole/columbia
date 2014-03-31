<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Puerta extends ContentType {
	
	function __construct($id = null)
	{
		/*** Datos básicos ***/
		$this->name = "Puerta";
		$this->table = "puertas";
		$this->hayPaginaIndividual = true;
		$this->returnURL = 'puertas-de-entrada/{title}';
		
		/*** Fields ***/
		$this->fields['title'] = new Textbox('Nombre');
		$this->fields['descripcion'] = new TextEditor();
		
		/*** Extras ***/	
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