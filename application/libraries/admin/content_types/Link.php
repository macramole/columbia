<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Link extends ContentType {
	
	function __construct($id = null)
	{
		/*** Datos básicos ***/
		$this->name = "Sitio amigo";
		$this->table = "links";
		$this->hayPaginaIndividual = false;
		$this->returnURL = '/links';
		
		/*** Fields ***/
		$this->fields['title'] = new Textbox('Nombre');
		$this->fields['link'] = new Textbox(null,'No olvides el http://','http://');
		$this->fields['imagen'] = new FileUpload();
		
		/*** Extras ***/	
		$this->fields['imagen']->isImage();
		$this->fields['imagen']->maxFilesAllowed = 1;
		$this->fields['imagen']->dimensionesRecomendadas = '160 x 100px';
		
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