<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once('application/libraries/admin/content_types/Puerta.php');

class Home extends ContentType {
	
	function __construct($id = null)
	{
		/*** Datos básicos ***/
		$this->name = "Home";
		$this->table = "home";
		$this->hayPaginaIndividual = false;
		$this->returnURL = '/';
		
		/*** Fields ***/
		$this->fields['imagenes'] = new FileUpload(0, 'Link');
		$this->fields['texto'] = new TextEditor('Texto de bienvenida');
		$this->fields['apoyo'] = new TextEditor('Texto de apoyo');
		
		
		/*** Extras ***/	
		$this->setListableFields(array('texto'));
		$this->fields['texto']->extraTags = array('h2');
		$this->fields['imagenes']->isImage();
		$this->fields['imagenes']->dimensionesRecomendadas = '750x290';
		
		
		parent::__construct($id);
	}
	
	function delete()
	{
		return;
	}
	
	function validate()
	{
		parent::validate();
		
		$this->ci->form_validation->set_rules('texto','','required');
		
		if ($this->ci->form_validation->run() == true)
			return null;
		else
		{
			return $this->ci->form_validation->get_error_array();
		}
	}
}