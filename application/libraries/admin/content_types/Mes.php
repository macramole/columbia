<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mes extends ContentType {
	
	function __construct($id = null)
	{
		/*** Datos básicos ***/
		$this->name = "Mes";
		$this->table = "meses";
		$this->hayPaginaIndividual = false;
		
		/*** Fields ***/
		$this->fields['title'] = new Textbox('Nombre');
		
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