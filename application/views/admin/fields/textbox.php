<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<input class="input-text" type="<?= $isPassword ? 'password' : 'text' ?>" name="<?=$name?>" value="<?=$value?>" maxlength="<?= $maxLength ?>" />
<?php if ($helptext) : ?>
<div class="helptext"><?= $helptext ?></div>
<?php endif; ?>