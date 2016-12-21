<?php
/**
 * Available variables passed from the caller script
 * - $aTweets    : the fetched tweet arrays.
 * - $aArguments : the passed arguments such as item count etc.
 * - $aOptions   : the plugin options saved in the database.
 */
 ?>
<h4>Arguments</h4>
<div class='var_dump'>
	<?php var_dump( $aArguments ); ?>
</div>
<h4>Tweets</h4>
<div class='var_dump'>
	<?php var_dump( $aTweets ); ?>
</div>
 