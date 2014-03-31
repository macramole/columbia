<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<select class="input-select" name="<?=$name?>">
	<?php if ( $addDefaultOption ) : ?>
	<option value="0"><?=$addDefaultOption?></option>
	<?php endif; ?>
	
	<?php foreach ($arrValues as $option) : ?>
	<option value="<?=$option['id']?>" <?= $option['id'] == $value ? 'selected' : ''?>><?=$option['value'] ? $option['value'] : $option['title']?></option>
	<?php endforeach; ?>
</select>
<?php if ($helptext) : ?>
<div class="helptext"><?= $helptext ?></div>
<?php endif; ?>