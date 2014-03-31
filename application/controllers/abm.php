<?php 
/*
 Mâgico
 http://www.parleboo.com
 Copyright 2012 Leandro Garber <leandrogarber@gmail.com>
 Attribution-NonCommercial-ShareAlike 3.0 Unported (CC BY-NC-SA 3.0)
*/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Abm extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
		
		if ( !$this->adminuser->isLogged() )
			exit;
		
		include_once('application/libraries/admin/lib/fields/Field.php');
		spl_autoload_register(array($this,'_autoIncludeFields'));
	}
	
	private function _autoIncludeFields($name)
	{
		@include_once("application/libraries/admin/lib/fields/$name.php");
	}
	
	private function _returnContentType($type, $id = null)
	{
		include_once('application/libraries/admin/lib/ContentType.php');
		include_once("application/libraries/admin/content_types/$type.php");
		
		return ( new $type($id) );
	}
	
	/*
	 * Para actualizar el orden por ajax
	 */
	public function updateOrder($type, $ids)
	{
		$content_type = $this->_returnContentType($type);
		
		$arrIds = explode('_', $ids);
		unset($arrIds[count($arrIds) - 1]);
		
		foreach ($arrIds as $key => $id)
		{
			$this->db->where('id', $id);
			$this->db->update($content_type->table, array('weight' => $key) );
		}
	}
	
	//
	/**
	 * Los fields pueden hacer uso de ajax también (por POST los datos)
	 * 
	 * @param String $type Nombre del content type
	 * @param String $field Nombre del field
	 * @param String $childField Si ese field es hijo de un field hay que poner como $field el padre y este como hijo //Esto capas debería ser un array porque asi estoy soportando hasta un nivel
	 */
	public function ajaxFieldCallBack($type, $field, $childField = null)
	{		
		$content_type = $this->_returnContentType($type);
		
		if ( !$childField )
			$content_type->fields[$field]->ajaxCallBack();
		else
			$content_type->fields[$field]->fields[str_replace ("{$field}_", '', $childField)]->ajaxCallBack();
		
	}
	
	//Llamar por ajax a una función de un content type (por POST los datos)
	public function contentTypeFunction($type, $functionName)
	{
		$content_type = $this->_returnContentType($type);
		$content_type->$functionName();
	}
	
	
	public function listContent($type, $page = null)
	{
		$content_type = $this->_returnContentType($type);
		
		if ($content_type->i18n && !$this->lang->has_language())
			redirect( $this->lang->default_lang() . '/' . uri_string () );
		else
			$this->load->view( 'admin/abm_list', array('content_type' => $content_type, 'page' => $page) );
	}
	
	public function listToXLS($type)
	{
		$content_type = $this->_returnContentType($type);
		$this->load->library('csvwriter');
		
		$data = array();
		
		$arrList = $content_type->getList();
		
		foreach ( $arrList as $key => $row )
		{
			unset($row['id']);
			
			if ( $key == 0 )
			{
				$columnNames = array_keys($row);
				$header = array();
				
				foreach( $columnNames as $column )
				{
					if ( isset( $content_type->fields[$column] ) )
						$header[] = $content_type->fields[$column]->label;
					else
						$header[] = $column;
				}
				
				$data[] = $header;
			}
			
			$data[] = $row;
		}

		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename="listado_' . $type . '.csv"');
		header('Cache-Control: max-age=0');
		
		foreach ($data as $line) {
			$this->csvwriter->addLine($line);
		}
	}
	
	//Muestra el diálogo de creación de contenido
	public function create($type, $id = null)
	{	
		if ( $this->adminuser->tienePermiso($type) || ($type == 'Admin' && $this->adminuser->getId() == $id  ) )
		{
			$content_type = $this->_returnContentType($type, $id);
		
			if ($content_type->i18n && !$this->lang->has_language())
				redirect( $this->lang->default_lang() . '/' . uri_string () );
			else
				$this->load->view( 'admin/abm_view', array('content_type' => $content_type) );
		}
		else
		{
			$this->load->view( 'admin/abm_view_denied');
		}
	}
	
	// Para que quede el edit en la url
	public function edit($type, $id)
	{
		$this->create($type, $id);
	}
	
	// Guarda el contenido (la función save probablamente deba ser overrideada)
	public function update($type, $id = null)
	{
		if ( $this->adminuser->tienePermiso($type) || ($type == 'Admin' && $this->adminuser->getId() == $id  ) )
		{
			$content_type = $this->_returnContentType($type, $id);
		
			$arrValidate = $content_type->validate();

			if ( $arrValidate )
			{
				echo json_encode(array('errors' => $arrValidate));
			}
			else
			{
				$content_type->save();
				echo json_encode(array('returnUrl' => $content_type->getReturnURL(), 'id' => $content_type->id ));
			}
		}
		else
		{
			echo 'Acceso Denegado';
		}
		
	}
	
	public function cloneContent($type, $id = null)
	{
		$content_type = $this->_returnContentType($type);
		$content_type->save($id);
		
		$lang = $this->lang->has_language() ? $this->lang->lang() . '/' : '';
		
		echo json_encode( array('returnUrl' => $lang . "abm/edit/$type/" . $content_type->id ) );
	}
	
	/**
	 * Guarda un field en particular. Se usa para el CKEditor INLINE. $_POST['data'] tiene la data a modificar.
	 * 
	 * @param type $id
	 * @param type $type
	 * @param type $field 
	 */
	public function updateField($id, $type, $field)
	{
		$content_type = $this->_returnContentType($type, $id);
		$content_type->saveField($field);
	}
	
	/**
	 * Elimina un contenido
	 * 
	 * @param String $type Nombre del tipo de contenido
	 * @param String $id Id del contenido
	 * @param boolean $force Si este contenido tiene contenidos asociados elimina todos
	 */
	public function delete($type, $id, $force = false)
	{
		$content_type = $this->_returnContentType($type, $id);
		
		if ( !$content_type->isForeignKey )
		{
			$content_type->delete();
			echo json_encode( array('need_confirmation' => false ) );
		}
		else
		{
			if ( !$force && $content_type->foreignKeyType == ContentType::FOREIGNKEY_TYPE_ONE_TO_MANY )
				echo json_encode( array('need_confirmation' => true ) );
			else
			{
				if ( $content_type->foreignKeyType == ContentType::FOREIGNKEY_TYPE_ONE_TO_MANY )
                {
					foreach( $content_type->isForeignKey as $strContentType )
					{
						$oForeignContentType = $this->_returnContentType($strContentType);
						$arrContenido = $this->db->get_where($oForeignContentType->table, array( 'id' . get_class($content_type) => $id ))->result_array();

						foreach( $arrContenido as $rowContenido )
						{
							$oContenido = $this->_returnContentType( $strContentType, $rowContenido['id'] );
							$oContenido->delete();
						}
					}
                }
                else //many to many
                {
                    foreach( $content_type->isForeignKey as $table )
                    {
                        $this->db->delete($table, array( 'id' . get_class($content_type) => $id ));
                    }
                }
				
				$content_type->delete();
                echo json_encode( array('need_confirmation' => false ) );
			}
		}
		
	}
	
	public function customList($listKey)
	{
		$customList = $this->config->item('magico_customList');
		$listadoConfig = $customList[$listKey];
		
		$listado = $this->db->query($listadoConfig['sqlList'])->result_array();
		
		foreach ( $listado[0] as $field => $value )
		{
			$fields[] = $field;
		}
		
		$this->load->view('admin/abm_custom_list', array('listado' => $listado, 'listadoConfig' => $listadoConfig, 'fields' => $fields, 'listKey' => $listKey));
	}
	
	public function customListAction($listKey, $param)
	{
		$this->load->library('csvwriter');
		$customList = $this->config->item('magico_customList');
		$listadoConfig = $customList[$listKey];
		
		$data = array();
		$sql = str_replace('{1}', $param, $listadoConfig['sqlAction']);
		
		$arrList = $this->db->query( $sql )->result_array();
		
		foreach ( $arrList as $key => $row )
		{
			if ( $key == 0 )
			{
				$columnNames = array_keys($row);
				$header = array();
				
				foreach( $columnNames as $column )
				{
					if ( isset( $content_type->fields[$column] ) )
						$header[] = $content_type->fields[$column]->label;
					else
						$header[] = $column;
				}
				
				$data[] = $header;
			}
			
			$data[] = $row;
		}

		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename="' . $listadoConfig['title'] . '.csv"');
		header('Cache-Control: max-age=0');
		
		foreach ($data as $line) {
			$this->csvwriter->addLine($line);
		}
		
	}
	
	public function addMessage($message)
	{
		$_SESSION['messages'][] = urldecode($message) ; 
	}
	
}