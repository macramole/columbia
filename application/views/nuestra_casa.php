<section id="nuestra_casa">
	<h1>Nuestra casa</h1>
	<div class="left">
		<?= $nuestraCasa['textoDestacado'] ?>
	</div>
	<div class="right">
		<?php if ( count($nuestraCasa['imagenes']) ) : ?>
			<div id="coin-slider-casa" class="slide">
				<?php foreach( $nuestraCasa['imagenes'] as $imagen ) : ?>
				<a href="#">
					<img src="<?= $imagen ?>" />
				</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<iframe width="340" height="270" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q=Jorge+Borges+2020++buenos+aires&amp;ie=UTF8&amp;hq=&amp;hnear=Jorge+Luis+Borges+2020,+Palermo,+Buenos+Aires,+Argentina&amp;t=m&amp;ll=-34.585984,-58.426001&amp;spn=0.006713,0.007274&amp;z=16&amp;iwloc=near&amp;output=embed"></iframe>
	</div>
	<?php magico_setEditable(3, 'Estatica', 'textoDestacado', 'section#nuestra_casa .left') ?>
	<?php magico_setMainData(3, 'Contacto') ?>
</section>