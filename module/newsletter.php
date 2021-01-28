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

$catList = new Category;

if($url -> op(0) == URL_ADD) {
	$vd = new Validate;
	$vd -> isEmail($_POST['email'], 'Adres e-mail');
	
	if($vd -> pass() == true) {	
		$newsletter = new Newsletter;
		$newsletter -> add($_POST['email'], $_POST['city'], implode(';', $_POST['cat']));
		$main -> alertPrepare(true, 'Twój adres został dodany do naszej bazy danych');
		$url -> redirect();
	}
	
	$main -> alertPrepare($vd -> pass(), $vd -> resultArray());
}

if($url -> op(0) == URL_DELETE) {
	$vd = new Validate;
	$vd -> isEmail($_POST['email'], 'Adres e-mail');
	
	if($vd -> pass() == true) {	
		$newsletter = new Newsletter;
		$newsletter -> delete($_POST['email']);
		$main -> alertPrepare(true, 'Twój adres został usunięty z naszej bazy danych');
		$url -> redirect();
	}
	
	$main -> alertPrepare($vd -> pass(), $vd -> resultArray());
}

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

			<?php $main -> alert(); ?>
			<div id="left" class="left-2">
				<h2 style="margin-top: 0;">Zapisz się do newslettera</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_ADD)); ?>">
					<label>
						<input type="text" name="email" required="required" placeholder="Adres e-mail *">
					</label>
					<label>
						<input type="text" name="city" placeholder="Miejscowość">
					</label>
					<h3>Branże</h3>
					<div class="cols cols-2">

<?php

if($catList -> countCategoryList() > 0) {
	foreach($catList -> getCategoryList(0) as $r) {
		echo('<label class="col-item"><input type="checkbox" name="cat[]" value="'.$r['category_id'].'">'.$r['name'].'</label>');
	}
}
	
?>
					
					</div>
					<div class="buttons">
						<input type="submit" value="Wykonaj">
					</div>
				</form>
			</div>
			<div id="right" class="right-2">
				<h2>Wypisz się z newslettera</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_DELETE)); ?>">
					<label>
						<input type="text" name="email" required="required" placeholder="Adres e-mail *">
					</label>
					<div class="buttons">
						<input type="submit" value="Wykonaj">
					</div>
				</form>
			</div>
			<div class="clear"></div>
			
		
	</div>
	<?php require_once('./template/default/new-box.php'); ?>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>