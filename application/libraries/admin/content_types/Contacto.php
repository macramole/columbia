<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once('application/libraries/admin/content_types/Puerta.php');

class Contacto extends ContentType {
	
	function __construct($id = null)
	{
		/*** Datos básicos ***/
		$this->name = "Nuestra casa";
		$this->table = "estaticas";
		$this->hayPaginaIndividual = false;
		$this->returnURL = 'informacion/quienes-somos';
		
		/*** Fields ***/
		$this->fields['imagen'] = new FileUpload();
		$this->fields['textoDestacado'] = new TextEditor('Texto');
		
		/*** Extras ***/	
		$this->setListableFields(array('textoDestacado'));
		$this->fields['textoDestacado']->allowLists = false;
		$this->fields['imagen']->isImage();
		$this->fields['imagen']->dimensionesRecomendadas = '340x220';
		
		parent::__construct($id);
	}
	
	function validate()
	{
		parent::validate();
		
		if ($this->ci->form_validation->run() == true)
			return null;
		else
		{
			return $this->ci->form_validation->get_error_array();
		}
	}
}