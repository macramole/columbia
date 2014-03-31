 $(function() {
	$('#txtBuscar').data('texto', 'Buscar...' );
	
	$('#txtBuscar').focus( function() {
		if ( $(this).val() == $(this).data('texto') )
			$(this).val('');
	});
	
	$('#txtBuscar').blur( function() {
		if ( $(this).val().trim() == '' )
			$(this).val( $(this).data('texto') );
	});
	
	$('#frmBuscar').submit( function(e) {
		$txtBuscar = $('#txtBuscar');
		
		if ( $txtBuscar.val().trim() == '' || $txtBuscar.val().trim() == $txtBuscar.data('texto') )
		{
			$txtBuscar.focus();
			e.preventDefault();
		}
	});
 });
 