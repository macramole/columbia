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
                        
                        <?php $visible = 'visible' ?>
                        <?php foreach($docentes[$idCategoria] as $año => $miembrosAño ) : ?>
                            <?php if ( $año != 'sinaño' ) : ?>
                                <div class="añoWrapper <?=$visible?> ">
                                    <div class="año"><a href="#"><?= $año ? $año : 'Otros' ?></a> <img src="images/flechas/violeta_<?= $visible ? 'ab' : 'de' ?>.png"/> </div>
                            <?php endif; ?>

                            <ul class="principales" rel="<?=$idCategoria?> <?= $año ?>">
                            <?php foreach( $miembrosAño as $miembro ) : ?>
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
                            
                            <?php $visible = '' ?>
                                    
                            <?php if ( $año != 'sinaño' ) : ?>
                                    <div class="clear"></div>
                                </div>
                            <?php endif; ?>
                        
                            <?php magico_setData($miembrosAño, 'Docente', "section#docentes ul.principales[rel=\"$idCategoria $año\"] li") ?>
                                
                        <?php endforeach; ?>
                        
                        
                        
                        <div class="clear"></div>
                        
						
					</li>
                    <div class="clear"></div>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
	
	<?php //magico_setData($categorias, 'DocenteCategoria', 'section#docentes ul.categorias > li', MAGICO_SORTABLE)  ?>
</section>
<script>
    $( function() {
        $('.año a').click( function(e) {
            e.preventDefault();

            $parent = $(this).parents('.añoWrapper');
            $li = $('li', $parent);
            $img = $('.año img', $parent);

            if ( !$parent.hasClass('visible') ) {
              $parent.addClass('visible');
              $img.attr('src', 'images/flechas/violeta_ab.png');
            }
            else {
              $parent.removeClass('visible');  
              $img.attr('src', 'images/flechas/violeta_de.png');
            }
        });
    });
</script>
    