<?php
/*
 Mâgico
 http://www.parleboo.com
 Copyright 2012 Leandro Garber <leandrogarber@gmail.com>
 Attribution-NonCommercial-ShareAlike 3.0 Unported (CC BY-NC-SA 3.0)
*/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once('application/libraries/admin/fields/SimpleSelect.php');

class DatabaseSelect extends SimpleSelect{
	
	const POST_PLACEHOLDER = '?'; //Si se actualiza dinamicamente (ej: addNew = true) si le puede poner este holder al valor del $where y se llena mandandole _POST[where] al ajaxCallBack
	
	public $content_type = null; //Content asociado (UNA INSTANCIA, no el nombre)
	public $addNew = true; //Opcion para agregado rápido. Se le puede establecer un string para agregar datos GET al new
	public $where = null; //Opcion para mandar un where en vez de toda la tabla 
	public $isDynamic = false; //Opcion para llenar este select mas tarde mediante ajax.
	
	/**
	 * Constructor
	 * 
	 * @param ContentType $content_type Tiene que ser un objeto
	 * @param array $where un array con una where clause si no se quiere mostrar toda la tabla
	 * @param boolean $isDynamic Opcion para llenar este select mas tarde mediante ajax
	 * @param type $label
	 * @param type $helptext
	 * @param type $defaultValue 
	 */
	function __construct($content_type, $where = null, $isDynamic = false, $label = null, $helptext = '', $defaultValue = '')
	{
		parent::__construct($label, $helptext, $defaultValue);
		$this->content_type = $content_type;
		$this->isForeignKey = $content_type->table; //Automaticamente sabe que es foreign key
		$this->where = $where;
		$this->isDynamic = $isDynamic;
		
		if ( !$this->isDynamic )
			$this->getDbValues();
	}
	
	function render()
	{
		$ci =& get_instance();
		$data = array();
		
		if ( !$ci->adminuser->tienePermiso(get_class($this->content_type)) )
			$this->addNew = false;
		
		$data['name'] = $this->name;
		$data['cssId'] = $this->cssId;
		$data['value'] = $this->value;
		$data['helptext'] = $this->helptext;
		$data['arrValues'] = $this->arrValues;
		$data['addNew'] = $this->addNew;
		$data['isDynamic'] = $this->isDynamic;
		$data['addDefaultOption'] = $this->addDefaultOption;
		$data['content_type'] = get_class($this->content_type);
		
		if ( !$this->getParent() instanceof Field )
			$data['ajaxUrl'] = "abm/ajaxFieldCallBack/" . get_class($this->getParent()) . "/" . $this->name;
		else
			$data['ajaxUrl'] = "abm/ajaxFieldCallBack/" . get_class($this->getParent()->getParent()) . "/" . $this->getParent()->name . '/' . $this->name;
		
		
		$ci->load->view('admin/fields/databaseselect.php', $data);
	}
	
	function getDbValues()
	{	
		$this->setValues($this->content_type->getList($this->where));
	}
	
	function ajaxCallBack()
	{
		if ( $this->isDynamic )
		{
			if ( $_POST['where'] )
			{
				foreach( $this->where as &$value )
					$value = str_replace (self::POST_PLACEHOLDER, $_POST['where'], $value);
			}
			
			$this->getDbValues();
		}
		
		echo json_encode( $this->arrValues );
	}
}

?>
