<?php
/*
 Mâgico
 http://www.parleboo.com
 Copyright 2012 Leandro Garber <leandrogarber@gmail.com>
 Attribution-NonCommercial-ShareAlike 3.0 Unported (CC BY-NC-SA 3.0)
*/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
?>
<?php if ( AdminUser::isLogged() ) : ?>
<style>	div.pp_details { display: none !important; } </style>
<div id="abm" class="list">
	<h1>
		<?= lang('magico_abm_list') ?><?= $content_type->name; ?>
		
		<?php if ( $content_type->i18n ) : ?>
			<?php $contentLanguage = $this->uri->segment(1); ?>
			<ul class="languages">
				<?php foreach( array_keys($this->lang->languages) as $lang ) : ?>
					<?php $langActive = ( $lang == $contentLanguage ) ? 'active' : '' ?>
					<li rel="<?= $lang?>" class="<?= $langActive ?>">
						<img src="images/backend/languages/<?= $lang?>.png" />
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</h1>
	<input type="text" id="listSearch" />
	<a href="#" class="createNew"><?= lang('magico_abm_create_new') ?></a>
	<div class="fieldsWrapper">
		<table>
			<thead>
				<tr>
					<?php foreach ( $content_type->getListableFields() as $field ) : ?>
					<th><?= $field->label ?></th>
					<?php endforeach; ?>
					<th><?= lang('magico_list_actions') ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ( $content_type->getList() as $row ) : ?>
				<tr rel="<?= $row['id'] ?>">
					<?php foreach ( $row as $fieldName => $value ) : ?>
						<?php if ( $fieldName != 'id') : ?>
							<td><?=$value?></td>
						<?php endif; ?>
					<?php endforeach; ?>
					<td class="actions">
						<?php foreach ( $content_type->getCustomButtons() as $button ) : ?>
						<a href="<?= $button['link'] . $row['id'] ?>"><img src="<?= base_url() ?>images/backend/<?= $button['image'] ?>" title="<?= $button['title'] ?>" /></a>
						<?php endforeach; ?>
						<?php if ( $content_type->hayPaginaIndividual ) : ?>
							<a href="<?= magico_urlclean($content_type->table, $row['id']) ?>" class="go"><img src="<?= base_url() ?>images/backend/ir_32.png" title="<?= lang('magico_abm_go') ?> " /></a>
						<?php endif; ?>
						<a href="#" rel="<?= $row['id'] ?>" class="edit"><img src="<?= base_url() ?>images/backend/edit_32.png" title="<?= lang('magico_abm_edit') ?> " /></a>
						<a href="#" rel="<?= $row['id'] ?>" class="delete" ><img src="<?= base_url() ?>images/backend/delete_32.png" title="<?= lang('magico_abm_delete') ?>" /></a>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<div class="buttons">
		<input class='input-submit' type="button" value="<?= lang('magico_abm_close') ?>" id="btnClose" />
	</div>
	<script type="text/javascript">
		$( function() {
			$('#abm.list ul.languages li').not('.active').click( function() {
				$.prettyPhoto.replaceOpen( $(this).attr('rel') + '/abm/listContent/<?= get_class($content_type) ?>?ajax=true&width=920&height=100%' );
			});

			$('#abm.list .createNew').click( function (e) {
				e.preventDefault();
				$.prettyPhoto.openNew('<?= site_url("abm/create/" . get_class($content_type)) ?>?ajax=true&width=920&height=100%');
			});

			$('#abm.list a.edit').click( function (e) {
				e.preventDefault();
				lang = $('#abm ul.languages li.active').attr('rel');
				lang = lang ? lang + '/' : '';
				
				$.prettyPhoto.openNew( lang + '<?= "abm/edit/" . get_class($content_type)?>/' + $(this).attr('rel') + '?ajax=true&width=920&height=100%');
			});

			$('#abm.list a.delete').click( function (e) {
				e.preventDefault();
				lang = $('#abm ul.languages li.active').attr('rel');
				lang = lang ? lang + '/' : '';
				
				if ( confirm('<?= $content_type->mensajeBorrado ? $content_type->mensajeBorrado : lang('magico_abm_delete_confirmation') ?>') )
				{
					$.ajax({
						url: lang + 'abm/delete/<?= get_class($content_type) ?>/' + $(this).attr('rel'),
						dataType: 'json',
						context: $(this),
						success: function (data) {
							if ( !data.need_confirmation )
							{
								showMessage('<?= lang('magico_abm_delete_successful') ?>');
								$('#abm.list tr[rel="' + $(this).attr('rel') + '"]').fadeOut();
								$('body').data('needReload', true);
							}
							else
							{
								if ( confirm('ATENCIÓN: Este contenido tiene elementos asociados. ¿ Estás seguro que querés eliminar este contenido y todo su contenido asociado ?') )
								{
									$.ajax({
										context: $(this),
										url: lang + 'abm/delete/<?= get_class($content_type)?>/' + $(this).attr('rel') + '/true',
										success: function () {			
											showMessage('<?= lang('magico_abm_delete_successful') ?>');
											$('#abm.list tr[rel="' + $(this).attr('rel') + '"]').fadeOut();
											$('body').data('needReload', true);
										},
										error: function() {
											alert('<?= lang('magico_abm_error'); ?>');
										}
									});
								}
							}
						},
						error: function() {
							alert('<?= lang('magico_abm_error'); ?>');
						}
					});
				}
			});
			
			$('#btnClose').click( function() {
				if ( $('body').data('needReload') )
				{
					$('body').data('needReload', false);
					$(this).val('<?= lang('magico_abm_closing') ?>');
					window.location.reload(true);
				}
				else
					$.prettyPhoto.close();
			});
			//BÃºsqueda
			$('#listSearch').keyup( function() {
				/*$(".fieldsWrapper tbody tr").hide();
				$(".fieldsWrapper tbody tr:contains('" + $('#listSearch').val() + "')").show();*/

				$(".fieldsWrapper tbody tr").hide();

				$(".fieldsWrapper tbody tr").each( function( index, item ) {
					if ( $(item).text().toLowerCase().indexOf( $('#listSearch').val() ) != -1 )
					{
						$(item).show();
					}
				});
			});
		} );
	</script>
</div>
<?php endif; ?>