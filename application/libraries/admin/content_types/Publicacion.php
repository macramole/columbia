<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Publicacion extends ContentType {
	
	function __construct($id = null)
	{
		/*** Datos básicos ***/
		$this->name = "Publicación";
		$this->table = "publicaciones";
		$this->hayPaginaIndividual = false;
		$this->returnURL = 'publicaciones/';
		
		/*** Fields ***/
		$this->fields['medio'] = new Textbox();
		$this->fields['title'] = new Textbox('Título');
		$this->fields['imagen'] = new FileUpload(0);
		$this->fields['pdf'] = new FileUpload(1, false, 'Archivo relacionado','Se utilizará este archivo en vez del link');
		$this->fields['fecha'] = new DatePicker();
		$this->fields['link'] = new Textbox('Link', 'No olvidar el http:// . Si cargaste un PDF no es necesario poner un link', 'http://');
		
		/*** Extras ***/	
		$this->fields['imagen']->isImage();
		$this->fields['imagen']->maxFilesAllowed = 1;
		$this->fields['imagen']->dimensionesRecomendadas = '200x130';
		
		$this->fields['pdf']->allowedExtensions = array('pdf');
		$this->fields['pdf']->maxFilesAllowed = 1;
		
		$this->setListableFields(array('medio', 'title'));
		
		parent::__construct($id);
	}
	
	function validate()
	{
		parent::validate();
		
		$this->ci->form_validation->set_rules('title','','required');
		$this->ci->form_validation->set_rules('medio','','required');

		if ($this->ci->form_validation->run() == true)
			return null;
		else
		{
			return $this->ci->form_validation->get_error_array();
		}
	}
}