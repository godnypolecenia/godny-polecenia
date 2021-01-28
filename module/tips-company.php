<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This file displays the tips for company
 */


/**
 *
 */

$url -> addBackUrl();

/**
 *	Layout
 */

$url -> setBodyId('index');

$meta = [
	'title' => $url -> title,
	'description' => $url -> description,
	'keywords' => $url -> keywords,
	'robots' => (($url -> index == 1) ? 'index' : 'noindex')
];
require_once(INC_DEFAULT_TPL_HEADER);

?>

<div id="slider" class="slider-mini">
	<div class="main">
		<div class="red-line"></div>
		<h1 class="lora"><?php echo($meta['title']); ?></h1>
		<div class="red-line"></div>
	</div>
</div>
<div id="content">
	<div class="main">

<?php

$bc = new Breadcrumb();
$bc -> add($url -> getLink());
$bc -> output();

?>
			
		<div id="tips-company">
			<div class="tip-item">
				<h2><?php echo($setup -> tips2_h_1); ?></h2>
				<p><?php echo($setup -> tips2_t_1); ?></p>
			</div>
			<div class="tip-item">
				<h2><?php echo($setup -> tips2_h_2); ?></h2>
				<p><?php echo($setup -> tips2_t_2); ?></p>
			</div>
			<div class="tip-item">
				<h2><?php echo($setup -> tips2_h_3); ?></h2>
				<p><?php echo($setup -> tips2_t_3); ?></p>
			</div>
			<div class="tip-item">
				<h2><?php echo($setup -> tips2_h_4); ?></h2>
				<p><?php echo($setup -> tips2_t_4); ?></p>
			</div>
			<div class="tip-item">
				<h2><?php echo($setup -> tips2_h_5); ?></h2>
				<p><?php echo($setup -> tips2_t_5); ?></p>
			</div>
			<div class="tip-item">
				<h2><?php echo($setup -> tips2_h_6); ?></h2>
				<p><?php echo($setup -> tips2_t_6); ?></p>
			</div>
			<div class="tip-item">
				<h2><?php echo($setup -> tips2_h_7); ?></h2>
				<p><?php echo($setup -> tips2_t_7); ?></p>
			</div>
		</div>
	</div>
	<?php require_once('./template/default/newsletter-box.php'); ?>
	<?php require_once('./template/default/new-box.php'); ?>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>