<section id="institucional">
	<h1><?= $estatica['title'] ?></h1>
	<div class="top">
		<div class="imageWrapper">
			<img src="<?= $estatica['imagen'] ?>" />
		</div>
		<div class="text">
			<?= $estatica['textoDestacado'] ?>
		</div>
		<div class="clear"></div>
	</div>
	<div class="contenido">
		<div class="left">
			<div class="textoIzWrapper">
				<?= $estatica['textoIz'] ?>
			</div>
		</div>
		<div class="right">
			<div class="cuerpo" contenteditable="true">
				<?= $estatica['textoDe'] ?>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="links">
		<a href="pdf/Fundacion_Columbia_Filosofia_Institucional.pdf" target="_blank" class="filosofia">
			<img src="images/pdf_download.jpg" />
			<span><strong>El nuevo paradigma</strong>, <br /> nuestra Filosofía Institucional.</span>
		</a>
		<a href="informacion/equipo" class="equipo">
			Nuestro Equipo
		</a>
	</div>
	<?php magico_setEditable($estatica['id'], 'Estatica', 'textoDestacado', 'section#institucional .top .text');  ?>
	<?php magico_setEditable($estatica['id'], 'Estatica', 'textoIz', 'section#institucional .contenido .left .textoIzWrapper');  ?>
	<?php magico_setEditable($estatica['id'], 'Estatica', 'textoDe', 'section#institucional .contenido .right .cuerpo');  ?>
	<?php magico_setMainData($estatica['id'], 'Estatica')  ?>
</section>