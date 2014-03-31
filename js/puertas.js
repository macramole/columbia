function puertasEntrada_setHeight()
{
	$('section#puertas_de_entrada .puerta').each( function() {
		$this = $(this);
		targetHeight = $('ul', $this).height();
		$('.textoWrapper', $this).css('height', targetHeight );
		$('.textoWrapper .texto', $this).css('height', targetHeight - 40 );
	});
}

$(function() {
	 $puertasEntrada = $('section#puertas_de_entrada');
	 if ( $puertasEntrada.length > 0 )
	 {
		 puertasEntrada_setHeight();
		 
		 $('body').load(puertasEntrada_setHeight);
		 
		 $('.puerta ul li a', $puertasEntrada).click( function(e) {
			 e.preventDefault();
			 $this = $(this);
			 $actividades = $('#actividades', $puertasEntrada);
			 id = $this.parent().attr('rel');
			 
			 targetTop = $this.offset().top; //- 138;
			 
			 
			 $('li, div', $puertasEntrada).removeClass('selected');
			 $('li[rel="' + id + '"], div[rel="' + id + '"]').addClass('selected');
			 
			 $('ul li', $actividades).not('.cargando').remove();
			 $('ul li.cargando', $actividades).show();
			 
			 actividadesHeight = $actividades.height();
			 
			 $actividades.addClass('active').css('top', targetTop )
			 $('.textoWrapper', $this.parents('.puerta')).stop().scrollTo('.texto[rel="' + $this.parent().attr('rel') + '"]', 1000);
			 
			 $.ajax({
				 url: 'puertasdeentrada/getActividades',
				 data: {'idDisciplina' : id},
				 dataType: 'json',
				 type: 'POST',
				 success: function(data) {
					 $('ul li.cargando', $actividades).hide();
					 
					 $('ul.regulares', $actividades).append(data.regulares);
					 
					 if ( data.especiales )
					 {
						$('ul.especiales, h3.especiales', $actividades).show();
						$('ul.especiales', $actividades).append(data.especiales);
					 } 
					 else
					 {
						 $('ul.especiales, h3.especiales', $actividades).hide();
					 }
					 
					 sectionHeight = $puertasEntrada.height();
					 actividadesHeight =  $('#actividades', $puertasEntrada).height() - 200;
					 targetTop =  $this.offset().top;
					 
					 if ( targetTop + actividadesHeight > sectionHeight )
						targetTop = sectionHeight - actividadesHeight;
					 
					 $('#actividades', $puertasEntrada).addClass('active').css('top', targetTop )
				}
			 })
			 
		 });
		 
		 $('#mainContent > .left').css('padding-top', $puertasEntrada.outerHeight() );
	 }
	 
	 $('section#disciplina .contenido .right .inscribirse.precios').click( function() {
		 
		 $('section#disciplina .contenido .right .preciosInfo, section#disciplina .contenido .right .inscribirse.precios').toggleClass('abierto');
		 
	 });
 });
 