<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once('application/libraries/admin/content_types/DocenteCategoria.php');

class Docente extends ContentType {
	
	function __construct($id = null)
	{
		/*** Datos básicos ***/
		$this->name = "Docente";
		$this->table = "docentes";
		$this->hayPaginaIndividual = true;
		$this->returnURL = 'docentes/{title}';
		
		/*** Fields ***/
		$this->fields['idDocenteCategoria'] = new DatabaseSelect(new DocenteCategoria(), null, false, "Categoria");
        $this->fields['title'] = new Textbox('Nombre y Apellido');
		$this->fields['imagen'] = new FileUpload();
		$this->fields['texto'] = new TextEditor('Bio');
        $this->fields['disciplina'] = new Textbox('Disciplina','U otro texto para la segunda línea');
        $this->fields['pais'] = new Textbox('País de origen','U otro texto para la tercera línea');
        $this->fields['anio'] = new Textbox('Año','Sólo para los que pasaron por la Fundación');
		
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