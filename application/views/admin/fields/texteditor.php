<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script type="text/javascript">
	$( function() {
		<?php if ( count($styles) ) : ?>
		if ( !CKEDITOR.stylesSet.get('<?= $name ?>') )
			CKEDITOR.stylesSet.add('<?= $name ?>', <?= $styles ?>);		
		<?php endif;?>
		
		$('#<?=$name?>').ckeditor(<?=$config?>);
		
		//Bug FIX
		$('body').bind('onAbmClose', function (event, now, force) { 
			
			//console.log('deleting <?=$name?>');
			
			/*if ( $('#<?=$name?>').length == 0 && !force )
				return;
			
			if ( typeof now !== 'undefined' && now == true )
			{
				CKEDITOR.instances['<?=$name?>'].destroy(); 
				//console.log('deleted <?=$name?>');
			}
			else
			{
				var interval = setInterval( function() {
					CKEDITOR.instances['<?=$name?>'].destroy(); 
					//console.log('deleted <?=$name?>');
					clearInterval(interval);
				}, 500 );
			}*/
			
			$('body').unbind('onAbmClose'); 
		} );
	});
</script>
<textarea name="<?=$name?>" id="<?=$name?>"><?=$value?></textarea>
<?php if ($helptext) : ?>
<div class="helptext"><?= $helptext ?></div>
<?php endif; ?><br />
