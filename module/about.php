<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	
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

<div id="slider" class="slider-mini slider-about">
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
			
		<h2 class="center"><?php echo($setup -> about_h); ?></h2>
		<p class="center"><?php echo($setup -> about_text); ?></p>
		<hr>
		<div id="certificate">
			<div id="certificate-name">Nazwa Twojej firmy</div>
			<div id="certificate-date"><?php echo(dateFormat(time())); ?></div>
		</div>
		<hr>
	</div>
	<div id="info" class="info-padding">
		<div class="main">
			<h2 class="center">Korzyści z dołączenia do Godny Polecenia</h2><br>
			<div class="cols cols-4">
				<div class="col-item">
					<img src="./template/default/image/star-red.png" alt="<?php echo($setup -> benefit_h_1); ?>">
					<h3><?php echo($setup -> benefit_h_1); ?></h3>
					<p><?php echo($setup -> benefit_1); ?></p>
				</div>
				<div class="col-item">
					<img src="./template/default/image/star-yellow.png" alt="<?php echo($setup -> benefit_h_2); ?>">
					<h3><?php echo($setup -> benefit_h_2); ?></h3>
					<p><?php echo($setup -> benefit_2); ?></p>
				</div>
				<div class="col-item">
					<img src="./template/default/image/star-blue.png" alt="<?php echo($setup -> benefit_h_3); ?>">
					<h3><?php echo($setup -> benefit_h_3); ?></h3>
					<p><?php echo($setup -> benefit_3); ?></p>
				</div>
				<div class="col-item">
					<img src="./template/default/image/star-black.png" alt="<?php echo($setup -> benefit_h_4); ?>">
					<h3><?php echo($setup -> benefit_h_4); ?></h3>
					<p><?php echo($setup -> benefit_4); ?></p>
				</div>
			</div>
			<div class="cols cols-4">
				<div class="col-item">
					<img src="./template/default/image/star-black.png" alt="<?php echo($setup -> benefit_h_5); ?>">
					<h3><?php echo($setup -> benefit_h_5); ?></h3>
					<p><?php echo($setup -> benefit_5); ?></p>
				</div>
				<div class="col-item">
					<img src="./template/default/image/star-blue.png" alt="<?php echo($setup -> benefit_h_6); ?>">
					<h3><?php echo($setup -> benefit_h_6); ?></h3>
					<p><?php echo($setup -> benefit_6); ?></p>
				</div>
				<div class="col-item">
					<img src="./template/default/image/star-red.png" alt="<?php echo($setup -> benefit_h_7); ?>">
					<h3><?php echo($setup -> benefit_h_7); ?></h3>
					<p><?php echo($setup -> benefit_7); ?></p>
				</div>
				<div class="col-item">
					<img src="./template/default/image/star-yellow.png" alt="<?php echo($setup -> benefit_h_8); ?>">
					<h3><?php echo($setup -> benefit_h_8); ?></h3>
					<p><?php echo($setup -> benefit_8); ?></p>
				</div>
			</div>
		</div>
	</div>
	<hr>
	<div class="main bottom-space center">
		<h2><?php echo($setup -> abc_h_1); ?></h2>
		<p><?php echo($setup -> abc_1); ?></p>
		<br>
		<?php echo($url -> getButton('item/add', false, null, ['class' => 'button'.(($user -> userId == 0) ? ' login-window' : '')], 'Dla firm')); ?>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>