<?php
/**
 * Setea la data de la página vista actualmente. El lapiz y el tacho aparecen en la barra de mâgico
 * 
 * @param type $id
 * @param type $content_type
 * @param type $language MAGICO_AUTO checkea si la url tiene lang entonces usa ese, Null o un lenguaje forza sin lenguaje o el lenguaje pasado
 * @return string 
 */
function magico_setMainData($id, $content_type, $language = MAGICO_AUTO)
{
	$ci =& get_instance();
	
	if ( !$ci->adminuser->isLogged() || !$ci->adminuser->tienePermiso($content_type) )
		return '';

	if ( $language == MAGICO_AUTO )
	{
		if ( $ci->lang->has_language() )
			$language = $ci->lang->lang();
		else
			$language = null;
	}
	
	if ( $language == null )
		$language = 'null';
	else
		$language = "'$language'";
	
	echo '<script type="text/javascript">';
		echo '$( function() {';
			echo "magico_setMainData('$id','$content_type', $language);";
		echo '});';
	echo '</script>';
}


/**
 * Se le envia un array con elementos de la base de datos (que incluya ID), el tipo de contenido y un selector jQuery con todos los elementos y le
 * setea a los mismos esos datos por JS para que funcione el drag a drop
 * 
 * @param array $arrContent Elementos tipo array( array( 'id' => 20, 'title' => 'hola' ), array( 'id' => 21, 'title' => 'chau' ) ). Super importante que haya id
 * @param string $content_type Tipo de contenido
 * @param string $selector Selector jQuery Si no se establece selector entonces se crea uno '#ContentType_id1, #ContentType_id2, etc' Sirve cuando el contenido es cargado por ajax
 * @param string $listType MAGICO_DRAGGABLE o MAGICO_SORTABLE para que genere automaticamente el script para arrastrar. MAGICO_CUSTOM para hacerlo manualmente.
 * @param string $listParams JSON adicional para el draggable o sortable CON corchetes
 * @param string $addDrag Si le agrega automaticamente el drag
 * @param string $language MAGICO_AUTO checkea si la url tiene lang entonces usa ese, Null o un lenguaje forza sin lenguaje o el lenguaje pasado
 * @return null
 */
function magico_setData($arrContent, $content_type, $selector = null, $listType = MAGICO_DRAGGABLE, $listParams = null, $addDrag = true, $language = MAGICO_AUTO )
{
	$ci =& get_instance();
	
	if ( !$ci->adminuser->isLogged() || !$ci->adminuser->tienePermiso($content_type) )
		return '';
	
	if ( count($arrContent) == 0 )
		return '';
	
	$arrIds = array();
	
	foreach ( $arrContent as $content )
	{
		$arrIds[] = $content['id'];
		$autoSelector .= "#{$content_type}_$content[id],";
	}
	
	if ( !$selector )
		$selector = substr($autoSelector,0, strlen($autoSelector) - 1 );
	
	if ( $language == MAGICO_AUTO )
	{
		if ( $ci->lang->has_language() )
			$language = $ci->lang->lang();
		else
			$language = null;
	}
	
	if ( $language == null )
		$language = 'null';
	else
		$language = "'$language'";
	
	switch ($listType)
	{
		case MAGICO_DRAGGABLE:
			$magicoJs = "$('$selector').parent().magico_draggable($listParams);";
			break;
		case MAGICO_SORTABLE:
			$magicoJs = "$('$selector').parent().magico_sortable($listParams);";
			break;
		default:
			$magicoJs = '';
			break;
	}
	
	if ( $addDrag )
		$magicoJs .= " $('$selector').magico_add_drag();";
	
	echo '<script type="text/javascript">';
		echo '$( function() {';
			echo $magicoJs;
			echo 'magico_setData(' . magico_arrPHP2JS($arrIds) . ", '$content_type', '$selector', $language);";
		echo '});';
	echo '</script>';
}

/**
 * Hacer un field editable por ALOHA
 * 
 * @param type $id
 * @param type $content_type
 * @param type $field
 * @param type $selector
 * @param array $options array( 'pluginName' => 'opcion1, opcion2' )
 * @param type $language MAGICO_AUTO checkea si la url tiene lang entonces usa ese, Null o un lenguaje forza sin lenguaje o el lenguaje pasado
 */
function magico_setEditable($id, $content_type, $field, $selector, $language = MAGICO_AUTO)
{
	$ci =& get_instance();
	
	if ( !$ci->adminuser->isLogged() )
		return '';
	
	if ( $language == MAGICO_AUTO )
	{
		if ( $ci->lang->has_language() )
			$language = $ci->lang->lang();
		else
			$language = null;
	}
	
	if ( $language == null )
		$language = 'null';
	else
		$language = "'$language'";
	
	$arrConfig = magico_getEditableConfig($content_type, $field);
	$config = $arrConfig['config'];
	$styles = $arrConfig['styles'];
	
	echo '<script type="text/javascript">';
		
		if ( count($styles) )
			echo "if ( !CKEDITOR.stylesSet.get('$field') ) CKEDITOR.stylesSet.add('$field', $styles); ";
		
		echo "magico_setFieldEditable('$id', '$content_type', '$field', '$selector', $config, $language);";
	echo '</script>';
}

/**
 * Uso interno, devuelve el config de CKEditor para este editable
 * 
 * @param type $content_type
 * @param type $field
 */
function magico_getEditableConfig($content_type, $field)
{
	include_once('application/libraries/admin/lib/ContentType.php');
	include_once("application/libraries/admin/content_types/$content_type.php");
		
	$content = new $content_type();
	$arrReturn['config'] = json_encode($content->fields[$field]->populateConfig());
	$arrReturn['styles'] = json_encode($content->fields[$field]->getStyles());
	return $arrReturn;
}

/**
 * Hacer varios fields editables por ALOHA
 * 
 * @param type $id
 * @param type $content_type
 * @param type $field
 * @param type $selector
 * @param array $options array( 'pluginName' => "'opcion1', 'opcion2'" )
 * @param type $language  MAGICO_AUTO checkea si la url tiene lang entonces usa ese, Null o un lenguaje forza sin lenguaje o el lenguaje pasado
 * 
 * Ejemplo 
 * 
 * array('format' => "'p', 'b', 'i'");
 */
function magico_setEditables($arrContent, $content_type, $field, $selector, $language = MAGICO_AUTO)
{
	$ci =& get_instance();
	
	if ( !$ci->adminuser->isLogged() )
		return '';
	
	$arrIds = array();
	
	foreach ( $arrContent as $content )
	{
		$arrIds[] = $content['id'];
	}
	
	if ( $language == MAGICO_AUTO )
	{
		if ( $ci->lang->has_language() )
			$language = $ci->lang->lang();
		else
			$language = null;
	}
	
	if ( $language == null )
		$language = 'null';
	else
		$language = "'$language'";
	
	$arrConfig = magico_getEditableConfig($content_type, $field);
	$config = $arrConfig['config'];
	$styles = $arrConfig['styles'];
	
	echo '<script type="text/javascript">';
		
		if ( count($styles) )
			echo "if ( !CKEDITOR.stylesSet.get('$field') ) CKEDITOR.stylesSet.add('$field', $styles); ";
		
		echo "magico_setFieldsEditables(" . magico_arrPHP2JS($arrIds) .", '$content_type', '$field', '$selector', $config, $language);";
	echo '</script>';
}

/**
 * Array de PHP a Array de JS
 */
function magico_arrPHP2JS($array, $withQuotes = true)
{
	$arrJs = '[';
	foreach ( $array as $item)
	{
		if ($withQuotes)
			$arrJs .= "'$item',";
		else
			$arrJs .= "$item,";
	}
	
	$arrJs = substr($arrJs, 0, strlen($arrJs) - 1);
	
	$arrJs .= ']';
	
	return $arrJs;
}

/**
 * Devuelve la URL Limpia
 * 
 * @param String $table La tabla donde está el contenido
 * @param String $id El id del contenido
 * @param boolean $full Si devuelve la url completa (con http:// etc) o si sólo la última parte
 * @param String $language MAGICO_AUTO checkea si la url tiene lang entonces usa ese, Null o un lenguaje forza sin lenguaje o el lenguaje pasado
 * @return String 
 */
function magico_urlclean($table, $id, $language = MAGICO_AUTO, $full = true )
{
    $ci =& get_instance();
	
	$arrGet = array('table' => $table, 'node_id' => $id);
	
	if ( $language == MAGICO_AUTO )
	{
		if ( $ci->lang->has_language() )
			$language = $ci->lang->lang();
		else
			$language = null;
	}
	
	
	if ( $language )
		$arrGet['language'] = $language;
	
	$row = $ci->db->get_where('clean_urls', $arrGet)->row_array();
	
	if ($row)
	{
	if ( $full )
		return site_url($row['url']);
	else
		return $row['url'];
	}
	
	return false;
}

/**
 * Devuelve el elemetno de la base de datos pasandole la URL limpia o bien la agarra de la actual
 * 
 * @param type $url Opcional, sino agarra el de la url
 * @param type $language Opcional, sino agarra el de la url
 * @param type $withTable Opcional devuelve la tabla con el objeto ($row[magico_table])
 * @return null 
 */
function magico_getByUrlClean($url = null, $language = null, $withTable = false)
{
	$ci =& get_instance();
	
	if ( $url == null )
	{
		if ( $ci->lang->has_language() )
		{
			if (!$language)
				$language = $ci->lang->lang();
	
			$url = substr(uri_string(),3);
		}
		else
			$url = uri_string();
	}
		
	if ( !$language )
		$row = $ci->db->get_where('clean_urls',array('url' => $url))->row_array();
	else
		$row = $ci->db->get_where('clean_urls',array('url' => $url, 'language' => $language))->row_array();
	
	if ( $row )
	{
		$arrReturn = array();
		
		if ( !$language )
			if ( !$row['language'] )
				$arrReturn = $ci->db->get_where($row['table'], array('id' => $row['node_id']))->row_array();
			else
				$arrReturn = $ci->db->get_where($row['table'], array('id' => $row['node_id'], 'language' => $row['language']))->row_array();
		else
			$arrReturn = $ci->db->get_where($row['table'], array('id' => $row['node_id'], 'language' => $language))->row_array();
		
		if ( !$withTable )
			return $arrReturn;
		else
		{
			$arrReturn['magico_table'] = $row['table'];
			return $arrReturn;
	    }
			
     }

	return null;
}

/**
 * Toma la url y devuelve la misma pero en otro idioma checkeando la clean url
 * 
 * @param type $language 
 */
function magico_switchLanguage($language)
{
	$row = magico_getByUrlClean(null, null, true);
	
	if ( $row )
		return  $language . '/' . magico_urlclean ($row['magico_table'], $row['id'], $language, false);
}

/**
 * Devuelve un archivo de thumb con el width y height especificado. Se puede poner en 0 alguno de los dos y el otro se cambia manteniendo ratio.
 * 
 * Calidad de JPG = 90
 * 
 */
function magico_thumb($file, $width, $height = 0, $method = ZEBRA_IMAGE_CROP_CENTER, $enlarge_smaller_images = true)
{
	$ci =& get_instance();
	
	if ( !$file )
	{
		global $CFG;
		$file =  $CFG->item('default_image');
	}
	
	$filename = substr( $file, 0, strrpos($file, '.') );
	$fileExtension = substr( $file, strrpos($file, '.') + 1 );
	
	$hash = md5( "$filename.$width.$height.$method" );
	$newFile = $hash . '.' . $fileExtension;
	
	if ( file_exists(THUMBS_DIR . $newFile) )
		return THUMBS_DIR . $newFile;
	else
	{
		if ( !file_exists(UPLOAD_DIR . $file) )
			return null;
		
		$ci->load->library('Zebra_Image');
		$ci->zebra_image->source_path = UPLOAD_DIR . $file;
		$ci->zebra_image->target_path = THUMBS_DIR . $newFile;
		$ci->zebra_image->enlarge_smaller_images = $enlarge_smaller_images;
		
		if ( $ci->zebra_image->resize($width, $height, $method) )
		{
			$arrFile = $ci->db->get_where('files', array('filename' => $file))->result_array();
			
			if ( $arrFile[0] )
				$ci->db->insert('files_thumbs', array( 'idFile' => $arrFile[0]['id'], 'filename' => $newFile ));
			
			return THUMBS_DIR . $newFile;
		}
		else 
			return $ci->zebra_image->error;
	}
}

/**
 * Devuelve todos los archivos asociados a ese elemento
 * 
 * @param String $table
 * @param int $id
 * @param int $flag
 * @return multiarray 
 */
function magico_getFiles($table, $id, $flag = 0)
{
	$ci =& get_instance();
	$whereFields = array('table' => $table, 'node_id' => $id);
	
	if ( $flag !== null )
		$whereFields['flag'] = $flag;
	
	$ci->db->order_by('weight ASC, id ASC'); 
	
	return $ci->db->get_where('files',$whereFields)->result_array();
}

/**
 * Agrega el campo "imagenes" a cada item del array con sus imagenes con el width y height establecido
 * Es un método rápido para setear las imágenes, usa los otros helpers.
 * 
 * Si son muchos rows es recomendado hacer un query con inner join, este método llama a la base tantas veces como rows haya
 * 
 * @param type $array
 * @param type $table
 * @param type $width
 * @param type $height
 * @param type $flag 
 */
function magico_getImagesToArray(&$array, $table, $width, $height = null, $flag = 0)
{
	foreach( $array as &$item )
	{
		magico_getImagesToRow( $item, $table, $width, $height, $flag );
	}
}

/**
 * Agrega el campo "imagen" a cada item del array con una imagen con el width y height establecido
 * Es un método rápido para setear la imágen principal, usa los otros helpers.
 * 
 * Si son muchos rows es recomendado hacer un query con inner join, este método llama a la base tantas veces como rows haya
 * 
 * @param type $array
 * @param type $table
 * @param type $width
 * @param type $height
 * @param type $flag 
 */
function magico_getImageToArray(&$array, $table, $width, $height = null, $flag = 0)
{
	foreach( $array as &$item )
	{
		magico_getImageToRow( $item, $table, $width, $height, $flag );
	}
}

/**
 * Agrega el campo "imagenes" al row con sus imagenes con el width y height establecido
 * Es un método rápido para setear las imágenes, usa los otros helpers
 * 
 * @param type $row
 * @param type $table
 * @param type $width
 * @param type $height
 * @param type $flag 
 */
function magico_getImagesToRow(&$row, $table, $width, $height = null, $flag = 0)
{
	$files = magico_getFiles($table, $row['id'], $flag);
	$imagenes = array();

	if ( count($files) )
	{
		foreach( $files as $imagen )
		{
			$imagenes[] = magico_thumb($imagen['filename'], $width, $height);
		}
	}
	else
	{
		$row['imagenes'][] = magico_thumb('', $width, $height); //default image
		return false;
	}
	
	$row['imagenes'] = $imagenes;
	return true;
}

/**
 * Agrega el campo "imagen" al row con su imagen con el width y height establecido
 * Es un método rápido para setear la imágen, usa los otros helpers
 * 
 * @param type $row
 * @param type $table
 * @param type $width
 * @param type $height
 * @param type $flag 
 * @param type $addFull
 */
function magico_getImageToRow(&$row, $table, $width, $height = null, $flag = 0, $addFull = false)
{
	$file = magico_getFile($table, $row['id'], $flag);
	$imagen = null;

	if ( $file )
	{
		$imagen = magico_thumb($file['filename'], $width, $height);
		
		if ( $addFull )
			$row['imagenFull'] = UPLOAD_DIR . $file['filename'];
	}
	else
	{
		$imagen = magico_thumb('', $width, $height); //default image
	}
	
	$row['imagen'] = $imagen;
}

/**
 * Devuelve el archivo principal (menor weight, menor id) de ese elemento
 * 
 * @param String $table
 * @param int $id
 * @param int $flag
 * @return array 
 */
function magico_getFile($table, $id, $flag = 0)
{
	$ci =& get_instance();
	$whereFields = array('table' => $table, 'node_id' => $id);
	
	if ( $flag !== null )
		$whereFields['flag'] = $flag;
	
	$ci->db->order_by('weight ASC, id ASC'); 
	$ci->db->limit(1);
	
	return $ci->db->get_where('files',$whereFields)->row_array();
}

/**
 *
 * 
 * @param type $table
 * @param type $image_width
 * @param type $image_height
 * @param type $where
 * @param type $order_by
 * @param type $language MAGICO_AUTO checkea si la url tiene lang entonces usa ese, Null o un lenguaje forza sin lenguaje o el lenguaje pasado
 * @return type 
 */
function magico_getList($table, $image_width, $image_height = null, $where = null, $order_by = null, $language = MAGICO_AUTO)
{
	$ci =& get_instance();
	
	if ( $language == MAGICO_AUTO )
	{
		if ( $ci->lang->has_language() )
			$language = $ci->lang->lang();
		else
			$language = null;
	}
	
	$sql = "
		SELECT
			t.*,
			( SELECT filename FROM files f WHERE f.node_id = t.id AND f.table = '$table' ORDER BY f.weight ASC, f.id DESC LIMIT 1 ) AS imagen,
			cu.url
		FROM
			$table t
		LEFT JOIN
			clean_urls cu ON
			cu.node_id = t.id AND cu.table = '$table'
	";
	
	if ( $language )
		if ( $where )
			$where .= " AND t.language='$language' AND cu.language='$language' ";
		else
			$where = "t.language='$language' AND cu.language='$language'";
	
	if ( $where )
		$sql .= "WHERE $where ";
	
	if ( $order_by )
		$sql .= "ORDER BY $order_by";
	
	$arrReturn = $ci->db->query($sql)->result_array();

	foreach ( $arrReturn as &$item )
	{
		$item['imagen'] = magico_thumb ($item['imagen'], $image_width, $image_height);

		if ( $item['url'] )
			$item['url'] = site_url($item['url']);
	}
	
	return $arrReturn;
}

/**
 * Pone la imagen de drag
 */
function magico_drag($white = true)
{
	$ci =& get_instance();
	$white = $white ? '_white' : '';
	
	if ( !$ci->adminuser->isLogged() )
		echo '';
	else
		echo "<img src='images/backend/move_icon$white.gif' class='drag' />";
}

/**
 * Establece el locale. Si es español (es) se fija si el server es windows o linux porque cambia ...
 * 
 * @param type $locale 
 */
function magico_setLocale($locale)
{
	$arrLangs = array('es' => 'esp', 'en' => 'eng'); //deberia agregar mas en el caso de que haya otros lenguajes
	
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
		setlocale(LC_ALL, $arrLangs[$locale]);
	else
		setlocale(LC_ALL, $locale . '_' . strtoupper($locale));
}

function magico_getGlobalConfig()
{
	$ci =& get_instance();
	return $ci->db->get('configuracion')->row_array();
}

function magico_sendmail($to, $subject, $body, $from)
{
	$ci =& get_instance();
	
	$ci->load->library('phpmailer');
	$sitename = $ci->config->item('magico_sitename');
	
	if ( $_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'macramole.no-ip.org' )
	{
		$ci->phpmailer->IsSMTP(); 
		$ci->phpmailer->Host = "ssl://mail.parleboo.com"; 
		$ci->phpmailer->Port = 465; 
		$ci->phpmailer->SMTPAuth = true;
		$ci->phpmailer->Username = "localhost@parleboo.com"; 
		$ci->phpmailer->Password = "111asd222";
		$from = "localhost@parleboo.com";
	}
	
	$ci->phpmailer->ClearAddresses();
	$ci->phpmailer->ClearReplyTos();
	
	if ( !is_array($to) )
		$to = array($to);
	
	$ci->phpmailer->From = $from;
	$ci->phpmailer->FromName = $sitename;
	
	foreach ( $to as $address )
		$ci->phpmailer->AddAddress($address);
	
	$ci->phpmailer->AddReplyTo($from,$sitename);

	$ci->phpmailer->IsHTML(true); // send as HTML
	$ci->phpmailer->CharSet = "UTF-8";
	$ci->phpmailer->Subject = $subject;
	$ci->phpmailer->Body = $body; //HTML Body
	//$ci->phpmailer->AltBody = "This is the body when user views in plain text format"; //Text Body

	if(!$ci->phpmailer->Send())
	{
		echo "Mailer Error: " . $ci->phpmailer->ErrorInfo;
		return false;
	}
	else
	{
		return true;
	}
}