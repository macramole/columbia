/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

/*CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For the complete reference:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'forms' },
		{ name: 'tools' },
		{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'others' },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'about' }
	];

	// Remove some buttons, provided by the standard plugins, which we don't
	// need to have in the Standard(s) toolbar.
	config.removeButtons = 'Underline,Subscript,Superscript';
};*/





/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	
	//config.extraPlugins = 'whitelist';
}

CKEDITOR.on('dialogDefinition', function(e) {
    var
        dd = e.data.definition, // NOTE: this is an instance of CKEDITOR.dialog.definitionObject, not CKEDITOR.dialog.definition
        tabInfo;
 
    if (e.data.name === 'link')
    {
        dd.removeContents('advanced');
        //dd.removeContents('target');
 
		
		tabInfo = dd.getContents('info');
        //tabInfo.remove('url');
        tabInfo.remove('linkType');
        //tabInfo.remove('browse');
        //tabInfo.remove('protocol');
 
        /*tabInfo.add({
            type : 'text',
            id : 'urlNew',
            label : 'URL',
            setup : function(data)
            {
                if (typeof(data.url) !== 'undefined')
                {
                    this.setValue(data.url.url);
                }
            },
            commit : function(data)
            {
				data.url = { url: this.getValue() };
				
				if ( data.url.url.match("^(.*http).*" ) )
				{
					data.target = { name: '_blank', type: 'magico' };
				}
				
				data.url.protocol = '';
            }
        });
		
		tabInfo.add({
			type : 'html',
			id: 'protocol',
			html: 'Para <strong>links externos</strong> escribir la url completa (ej: <strong>http://</strong>www.google.com.)<br /> Estos links se abriran en una ventana nueva<br /><br />\n\
				   Para <strong>links internos</strong> escribir <strong>s&oacute;lo</strong> la ruta (ej: novedades/titulo-de-la-novedad)<br />Estos links se abriran en la misma ventana.'
			
		});*/
		
		/*
        tabInfo.add({
            type : 'checkbox',
            id : 'newPage',
            label : 'Abrir en una nueva ventana',
            commit : function(data)
            {
                if (this.getValue())
                {
                    data.target = '_blank';
                }
                return data;
            }
        });*/
    }
});

