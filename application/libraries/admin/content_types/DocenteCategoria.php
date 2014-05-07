<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DocenteCategoria extends ContentType {
	
	function __construct($id = null)
	{
		/*** Datos básicos ***/
		$this->name = "Categoria de docente";
		$this->table = "docentes_categorias";
		$this->hayPaginaIndividual = false;
		$this->returnURL = 'docentes';
		
		/*** Fields ***/
		$this->fields['title'] = new Textbox('Titulo');
		
		
		/*** Extras ***/	
		
		$this->setListableFields(array('title'));
		
		parent::__construct($id);
	}
	
	function validate()
	{
		parent::validate();
		
		//$this->ci->form_validation->set_rules('title','','required');
		
		if ($this->ci->form_validation->run() == true)
			return null;
		else
		{
			return $this->ci->form_validation->get_error_array();
		}
	}
}