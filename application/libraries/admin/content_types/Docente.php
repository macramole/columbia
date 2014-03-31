<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Docente extends ContentType {
	
	function __construct($id = null)
	{
		/*** Datos básicos ***/
		$this->name = "Docente";
		$this->table = "docentes";
		$this->hayPaginaIndividual = true;
		$this->returnURL = 'docentes/{title}';
		
		/*** Fields ***/
		$this->fields['title'] = new Textbox('Nombre y Apellido');
		$this->fields['imagen'] = new FileUpload();
		$this->fields['texto'] = new TextEditor('Bio');
		
		/*** Extras ***/	
		$this->fields['imagen']->isImage();
		$this->fields['imagen']->maxFilesAllowed = 1;
		$this->fields['imagen']->dimensionesRecomendadas = '340x220';
		
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