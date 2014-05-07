<section id="docentes" class="listadoFotos">
	<h1>Docentes</h1>
	<?php if ( count($docentes) ) : ?>
		<ul class="categorias">
			<?php foreach($categorias as $idCategoria => $categoria) : ?>
				<?php if ( count($docentes[$idCategoria]) ) : ?>
					<li>
						<?php if ( $categoria['title'] != 'Permanentes' ) : ?>
                        <h2><?= $categoria['title'] ?></h2>
                        <?php endif; ?>
                        <ul class="principales" rel="<?=$idCategoria?>">
                            <?php foreach($docentes[$idCategoria] as $key => $miembro ) : ?>
                            <li>
                                <a href="<?= $miembro['url'] ?>">
                                    <img src="<?= $miembro['imagen'] ?>" />
                                    <span class="nombre"><?= $miembro['title'] ?></span> <span>[+]</span> <br />
                                    <span class="disciplina"><?= $miembro['disciplina'] ?></span>
                                    <span class="pais"><?= $miembro['pais'] ?></span>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="clear"></div>
						<?php magico_setData($docentes[$idCategoria], 'Docente', "section#docentes ul[rel=\"$idCategoria\"] li") ?>
					</li>
                    <div class="clear"></div>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
	
	<?php //magico_setData($categorias, 'DocenteCategoria', 'section#docentes ul.categorias > li', MAGICO_SORTABLE)  ?>
</section>