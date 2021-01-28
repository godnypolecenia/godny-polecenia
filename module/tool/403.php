<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This displays 403 errors
 */

file_put_contents('./data/log/error-403.txt', date('Y-m-d H:i:s').' '.$_SERVER['SCRIPT_URI']."\n", FILE_APPEND);

/**
 *	Layout
 */
$meta = [
	'title' => 'Błąd 403',
	'description' => '',
	'keywords' => '',
	'robots' => 'noindex'
];
require_once(INC_DEFAULT_TPL_HEADER);

?>


<div id="content">
	<div class="main">

<?php

$bc = new Breadcrumb();
$bc -> add($url -> getUrl(), $meta['title']);
$bc -> output();

?>
				
		<section>
			<h1><?php echo($meta['title']); ?></h1>
			<p>Odmowa dostępu</p>
		</section>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>