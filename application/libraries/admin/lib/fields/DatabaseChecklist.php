<?php
/*
 Mâgico
 http://www.parleboo.com
 Copyright 2012 Leandro Garber <leandrogarber@gmail.com>
 Attribution-NonCommercial-ShareAlike 3.0 Unported (CC BY-NC-SA 3.0)
*/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DatabaseChecklist extends Field {
	
	public $content_type = null; //Content asociado (UNA INSTANCIA, no el nombre)
    public $relationTable = null; //Tabla many to many. Debe tener id, idNombreContentTypeAsociado, idNombreContentTypePadre
	public $addNew = true; //Opcion para agregado rápido.   
    public $arrValues = array();
	public $defaultChecked = false; //En true, checkea todos por default 
	
	function __construct($content_type, $relationTable, $label = null, $helptext = '')
	{
		parent::__construct($label, $helptext);
        $this->autoSave = true;
		$this->content_type = $content_type;
        $this->relationTable = $relationTable;
		$this->getDbValues();
	}
	
	function getDbValues()
	{	
        $this->arrValues = $this->content_type->getList();
	}
    
    function save($table, $id)
    {
        $ci =& get_instance();
        $checks = $_POST[$this->name];
		$idParent = 'id' . get_class($this->getParent());
		$idContentType = 'id' . get_class($this->content_type);
		
		$arrAgregados = $ci->db->get_where($this->relationTable, array($idParent => $id))->result_array();
		
		if ( count($arrAgregados) )
		{
			foreach( $arrAgregados as $agregado )
			{
				if ( ( $pos = array_search($agregado[$idContentType], $checks) ) !== false )
				{
					unset($checks[$pos]);
				}
				else
				{
					$ci->db->delete( $this->relationTable, array( 'id' => $agregado['id'] ) );
				}
			}
		}
		
		if ( count($checks) )
		{
			$data = array();

			foreach( $checks as $value )
			{
				$data[] = array( $idContentType => $value,
									$idParent => $id);
			}

			$ci->db->insert_batch( $this->relationTable, $data );
		}
		
		
		/*
		 * Borra todo y agrega todo... Tuve que cambiarlo porque cambia los ids...
		 * 
		$ci->db->delete( $this->relationTable, array( 'id' . get_class($this->getParent()) => $id ) );
        
        if ( count( $_POST[$this->name] ) )
        {
            foreach( $_POST[$this->name] as $value )
            {
                $data[] = array( 'id' . get_class($this->content_type) => $value,
                                 'id' . get_class($this->getParent()) => $id);
            }
            
            $ci->db->insert_batch( $this->relationTable, $data );
        }*/
    }
    
    function delete($table, $id) 
    {
        $ci =& get_instance();
        
        $ci->db->delete( $this->relationTable, array( 'id' . get_class($this->getParent()) => $id ) );
    }        
    
    function setFieldValue($table, $id, $row)
    {
        $ci =& get_instance();
        
        $idCheck = 'id' . get_class($this->content_type);
        
        $arrSelected = $ci->db->get_where( $this->relationTable, array( 'id' . get_class($this->getParent()) => $id ) )->result_array();
        
        foreach ( $this->arrValues as &$value )
        {
            $selected = false;
            foreach ( $arrSelected as $key => $selValue )
            {
                if ( $selValue[$idCheck] == $value['id'] )
                {
                    $selected = true;
                    unset($arrSelected[$key]);
                    break;
                }
            }
            
            $value['selected'] = $selected;
        }
    }
	
	function ajaxCallBack()
	{
        echo json_encode( $this->arrValues );
	}
    
    function render()
	{
		$ci =& get_instance();
		$data = array();
		
		if ( $this->defaultChecked && $this->getParent()->getOperation() == ContentType::OPERATION_CREATE )
		{
			foreach ( $this->arrValues as &$value )
				$value['selected'] = true;
		}
		
		if ( !$ci->adminuser->tienePermiso(get_class($this->content_type)) )
			$this->addNew = false;
		
		$data['name'] = $this->name;
		$data['arrValues'] = $this->arrValues;
		$data['helptext'] = $this->helptext;
		$data['addNew'] = $this->addNew;
		$data['content_type'] = get_class($this->content_type);
        
        if ( !$this->getParent() instanceof Field )
			$data['ajaxUrl'] = "abm/ajaxFieldCallBack/" . get_class($this->getParent()) . "/" . $this->name;
		else
			$data['ajaxUrl'] = "abm/ajaxFieldCallBack/" . get_class($this->getParent()->getParent()) . "/" . $this->getParent()->name . '/' . $this->name;
		
		$ci->load->view('admin/fields/databasechecklist.php', $data);
	}
    
    public function validate($rules = 'required')
	{	
		if ( count($_POST[$this->name]) == 0 )
		{
			$ci =& get_instance();
			$ci->form_validation->set_error( lang('required'), $this->name );
		}
	}
}

?>
