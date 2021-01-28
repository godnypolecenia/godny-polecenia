<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This file is the main page of the administration panel
 */

$user -> onlyAdmin();

$db -> query(
	'SELECT COUNT(*) AS `count` '.
	'FROM `db_user` '.
	'WHERE `status` = 0'
);
$r = $db -> fetchArray();
$userDeactiveCount = $r['count'];

$db -> query(
	'SELECT COUNT(*) AS `count` '.
	'FROM `db_newsletter`'
);
$r = $db -> fetchArray();
$emailCount = $r['count'];

$db -> query(
	'SELECT SUM(`counter`) AS `sum` '.
	'FROM `db_item`'
);
$r = $db -> fetchArray();
$sumCounter = $r['sum'];

$db -> query(
	'SELECT COUNT(*) AS `count` '.
	'FROM `db_item` '.
	'WHERE `validity` < "'.time().'" && `premium` < "'.time().'"'
);
$r = $db -> fetchArray();
$pack0 = $r['count'];

$db -> query(
	'SELECT COUNT(*) AS `count` '.
	'FROM `db_item` '.
	'WHERE `validity` > "'.time().'" && `premium` < "'.time().'"'
);
$r = $db -> fetchArray();
$pack1 = $r['count'];

$db -> query(
	'SELECT COUNT(*) AS `count` '.
	'FROM `db_item` '.
	'WHERE `premium` > "'.time().'"'
);
$r = $db -> fetchArray();
$pack2 = $r['count'];

$db -> query(
	'SELECT SUM(`amount`) AS `sum` '.
	'FROM `db_payment`'
);
$r = $db -> fetchArray();
$packPrice = $r['sum'];
	
/**
 *	Add URL to history
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
$bc -> add($url -> getLink());
$bc -> output();

?>
			
		<div id="left">
			<?php require_once(INC_ADMIN_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>

			<?php if($setup -> developer == 1) { ?>
			<section class="alert alert-error">
				<h2><span class="ri-flask-line icon"></span>Tryb developerski</h2>
				<p>Uwaga! Tryb developerski jest włączony. Po skończeniu prac i&#160;udostępnieniu portalu użytkownikom wskazane jest jego <a href="<?php echo($url -> getUrl('admin/setup/other')); ?>" class="underline">wyłączenie</a>.</p>
			</section>
			<?php } ?>
			<h2>Statystyki</h2>
			<div class="cols cols-3">
				<section>
					<h3><span class="ri-line-chart-line icon"></span>Ogólne</h3>
					<ul class="ul">
						<li>Liczba użytkowników: <span class="bold"><?php echo($userCount); ?></span></li>
						<li>Liczba użytkowników nieaktywnych: <span class="bold"><?php echo($userDeactiveCount); ?></span></li>
						<li>Liczba adresów e-mail: <span class="bold"><?php echo($emailCount); ?></span></li>
						<li>Suma wyświetleń wizytówek: <span class="bold"><?php echo($sumCounter); ?></span></li>
					</ul>
				</section>
				<section>
					<h3><span class="ri-line-chart-line icon"></span>Firmy</h3>
					<ul class="ul">
						<li>Liczba firm: <span class="bold"><?php echo($itemCount); ?></span></li>
						<li>Liczba firm do aktywacji: <span class="bold"><?php echo($itemDeactiveCount); ?></span></li>
						<li>Liczba ocen: <span class="bold"><?php echo($voteCount); ?></span></li>
						<li>Liczba ocen do aktywacji: <span class="bold"><?php echo($voteDeactiveCount); ?></span></li>
					</ul>
				</section>
				<section>
					<h3><span class="ri-line-chart-line icon"></span>Pakiety</h3>
					<ul class="ul">
						<li>Liczba pakietów Darmowych: <span class="bold"><?php echo($pack0); ?></span></li>
						<li>Liczba pakietów Standard: <span class="bold"><?php echo($pack1); ?></span></li>
						<li>Liczba pakietów Premium: <span class="bold"><?php echo($pack2); ?></span></li>
						<li>Suma wpływów: <span class="bold"><?php echo(priceFormat($packPrice)); ?></span></li>
					</ul>
				</section>
			</div>
			<h2>Moduły</h2>
			<div class="cols cols-3">
				<section>
					<h3><span class="ri-database-2-line icon"></span><?php echo(ITEM_LIST_TITLE); ?></h3>
					<p>Moduł zawiera wszystkie ogłoszenia dodane przez użytkowników. Możesz nimi zarządzać, aktywować i&#160;usuwać.</p>
					<br>
					<?php echo($url -> getButton('item/admin/index', false, null, ['class' => 'underline'])); ?> -
					<?php echo($url -> getButton('item/admin/index', false, '/'.URL_BOOKMARK.'-2', ['class' => 'underline'], 'Do aktywacji')); ?>
				</section>
				<section>
					<h3><span class="ri-file-3-line icon"></span>Treści</h3>
					<p>Moduł zawiera wszystkie strony z&#160;treścią publikowane w ramach portalu. Możesz dodawać nowe i&#160;usuwać istniejące.</p>
					<br>
					<?php echo($url -> getButton('page/admin/index', false, null, ['class' => 'underline'])); ?>
				</section>
				<section>
					<h3><span class="ri-user-line icon"></span>Użytkownicy</h3>
					<p>Moduł zawiera listę wszystkich kont użytkowników w&#160;serwisie. Możesz nimi zarządzać i je&#160;blokować.</p>
					<br>
					<?php echo($url -> getButton('user/admin/index', false, null, ['class' => 'underline'])); ?> -
					<?php echo($url -> getButton('user/admin/payment-list', false, null, ['class' => 'underline'])); ?> -
					<?php echo($url -> getButton('user/admin/newsletter', false, null, ['class' => 'underline'], 'Newsletter')); ?>
				</section>
				<section>
					<h3><span class="ri-settings-2-line icon"></span>Ustawienia</h3>
					<p>Moduł zawiera szereg opcji pozwalających skonfigurować działanie niniejszego portalu internetowego.</p>
					<br>
					<?php echo($url -> getButton('admin/setup/seo', false, null, ['class' => 'underline'], 'SEO')); ?> -
					<?php echo($url -> getButton('admin/setup/social-media', false, null, ['class' => 'underline'])); ?> -
					<?php echo($url -> getButton('admin/setup/ad-list', false, null, ['class' => 'underline'])); ?> -
					<?php echo($url -> getButton('admin/setup/other', false, null, ['class' => 'underline'], 'Więcej')); ?>
				</section>
				<section>
					<h3><span class="ri-database-2-line icon"></span>Dane</h3>
					<p>Moduł zawiera pliki i&#160;zdjęcia, które są dodwane w&#160;ramach innych modułów. Możesz przejrzeć pliki i&#160;nimi zarządzać.</p>
					<br>
					<?php echo($url -> getButton('admin/data/image-list', false, null, ['class' => 'underline'])); ?> -
					<?php echo($url -> getButton('admin/data/file-list', false, null, ['class' => 'underline'])); ?>
				</section>
				<section>
					<h3><span class="ri-user-line icon"></span>Twoje konto</h3>
					<p>Moduł zawiera opcje zarządzania własnym kontem użytkownika, bezpośrednio z&#160;poziomu panelu administracyjnego.</p>
					<br>
					<?php echo($url -> getButton('admin/manage', false, null, ['class' => 'underline'])); ?> -
					<?php echo($url -> getButton('admin/logout', false, null, ['class' => 'underline'])); ?>
				</section>
			</div>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_ADMIN_TPL_FOOTER); ?>