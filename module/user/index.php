<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */
 
if(!defined('SOPICMS')) exit('Forbidden 403');

$user -> onlyUser();

/**
 *	This file contains the user account's home page
 */
 
$it = new Item;
$countIt = $it -> countItemListOfUser($user -> userId);
if($countIt > 0) {
	$url -> redirect('item/add-list');
} else {
	$url -> redirect('item/add');
}

$url -> addBackUrl();

/**
 *	Layout
 */
$meta = [
	'title' => $url -> title,
	'description' => $url -> description,
	'keywords' => $url -> keywords,
	'robots' => (($url -> index == 1) ? 'index' : 'noindex')
];
require_once(INC_DEFAULT_TPL_HEADER);

?>

<div id="content">
	<div class="main">

<?php

$bc = new Breadcrumb();
$bc -> add($url -> getLink());
$bc -> output();

?>
				
		<div id="left">
			<?php require_once(INC_DEFAULT_TPL_MENU); ?>
		</div>
		<div id="right">		
			<section>
				<h1><span class="ri-user-line icon"></span><?php echo($meta['title']); ?></h1>

			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>