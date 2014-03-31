<?php
/*
 Mâgico
 http://www.parleboo.com
 Copyright 2012 Leandro Garber <leandrogarber@gmail.com>
 Attribution-NonCommercial-ShareAlike 3.0 Unported (CC BY-NC-SA 3.0)
*/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SimpleSelect extends Field {
	
	public $arrValues = array(); // array( array('id' => 1, 'value' => hola) );
	public $addDefaultOption = false; //cambiar al nombre del default para que aparezca
	public $safeHtml = false;
	
	function render()
	{
		$ci =& get_instance();
		$data = array();
		
		
		$data['name'] = $this->name;
		$data['value'] = $this->value;
		$data['helptext'] = $this->helptext;
		$data['arrValues'] = $this->arrValues;
		$data['addDefaultOption'] = $this->addDefaultOption;
		
		$ci->load->view('admin/fields/simpleselect.php', $data);
		
		parent::render();
	}
	
	/**
	 * Se le puede pasar como parámentro un array de arrays ej: array( array('id' => 1, 'value' => 'hola'), array('id' => 2, 'value' => 'chau') );
	 * El 'value' también puede ser 'title' de este 
	 * 
	 * o bien un array tipo array( 'id' => 'key', 'id' => 'key' );
	 */
	function setValues($arrValues)
	{
		if ( $arrValues && count($arrValues) > 0 )
		{
			if ( is_array(current($arrValues)) )
			{
				$this->arrValues = $arrValues; // supongo array( array('id' => 1, 'value' => 'hola'), array('id' => 2, 'value' => 'chau') );
			}
			else
			{
				// supongo array( 'id' => 'key', 'id' => 'key' );
				
				$this->arrValues = array();
				
				foreach ( $arrValues as $key => $value )
				{
					$this->arrValues[] = array( 'id' => $key, 'value' => $value );
				}
			}	
		}
		else
			$this->arrValues = array();
	}
	
	public function validate($rules = 'required')
	{	
		if ( $_POST[$this->name] <= 0 )
		{
			$ci =& get_instance();
			$ci->form_validation->set_error( lang('required'), $this->name );
		}
	}
}

?>
