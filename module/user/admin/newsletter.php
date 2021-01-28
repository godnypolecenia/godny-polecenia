<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

$user -> onlyAdmin(5);

/**
 *	This file allows you to send the newsletter
 */

$catList = new Category;
$catArr = [];
if($catList -> countCategoryList() > 0) {
	foreach($catList -> getCategoryList(0) as $r) {
		$catArr[$r['category_id']] = $r['name'];
	}
}

if($url -> op(0) == URL_SEND) {
	$db -> query(
		'SELECT * '.
		'FROM `db_newsletter` '.
		'WHERE 1 '.(($_POST['city'] <> '') ? ' && `city` = "'.$_POST['city'].'"' : '')
	);
	$n = $db -> numRows();
	while($r = $db -> fetchArray()) {
		if($r['category'] <> '') {
			$next = 0;
			$ex = explode(';', $r['category']);
			foreach($ex as $v) {
				if($_POST['cat'][$v] == 1) {
					$next++;
				}
			}
			if($next == 0) {
				continue;
			}
		}
		send_mail($r['email'], $_POST['title'], $_POST['content'], $_POST['sender'], $_POST['email'], $_POST['email']);
	}
	
	$main -> alertPrepare(true, 'Wiadomość została wysłana do '.$n.' '.inflect($n, array('użytkownika', 'użytkowników', 'użytkowników')));
	$url -> redirect();
}

/**
 *
 */
 
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
require_once(INC_ADMIN_TPL_HEADER);

?>

<div id="content">
	<div class="main">

<?php

$bc = new Breadcrumb();
$bc -> add($url -> getLink('admin/index'));
$bc -> add($url -> getLink('user/admin/index'));
$bc -> add($url -> getLink());
$bc -> output();

?>
			
		<div id="left">
			<?php require_once(INC_ADMIN_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<section>
				<h1><span class="ri-mail-line icon"></span><?php echo($meta['title']); ?></h1>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SEND)); ?>">
					<label>
						Adres e-mail nadawcy
						<input type="email" name="email" value="<?php echo($setup -> email); ?>" required="required" maxlength="100">
					</label>
					<label>
						Nadawca wiadomości
						<input type="text" name="sender" value="<?php echo($setup -> sender); ?>" required="required" maxlength="50">
					</label>
					<label>
						Tytuł wiadomości
						<input type="text" name="title" required="required" maxlength="50">
					</label>
					<label>
						Treść wiadomości
						<textarea name="content" class="wysiwyg"></textarea>
					</label>
					<label>
						Miejscowość
						<input type="text" name="city">
					</label>
					<h3>Branże</h3>
					<div class="cols cols-3">

<?php

if($catList -> countCategoryList() > 0) {
	foreach($catList -> getCategoryList(0) as $r) {
		echo('<label class="col-item"><input type="checkbox" name="cat[]" value="'.$r['category_id'].'" checked="checked">'.$r['name'].'</label>');
	}
}
	
?>
					
					</div>
					<div class="buttons">
						<input type="submit" value="Wyślij wiadomość">
						Stopka zostanie dołączona automatycznie
					</div>
				</form>
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_ADMIN_TPL_FOOTER); ?>