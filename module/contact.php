<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This file displays the contact page
 */
 

/**
 *	Send message
 */
if($url -> op(0) == URL_SEND) {
	$vd = new Validate;
	$vd -> isEmail($_POST['email'], 'Twój adres e-mail');
	$vd -> isValue($_POST['sender'], 'Twoje imię i nazwisko');
	$vd -> isValue($_POST['content'], 'Treść wiadomości');
	$vd -> isCaptcha($_POST['g-recaptcha-response']);
	
	if($vd -> pass() == true) {	
	
		if($_POST['item'] > 0) {
			$item = new Item($_POST['item']);
		}
	
		send_mail($setup -> email_reply, (($_POST['item'] > 0) ? 'Zgłoszenie firmy' : 'Wiadomość'), 'Zgłoszenie firmy: '.$item -> title.'(ID: '.$item -> itemId.')<br><br>'.$_POST['content'], $_POST['email']);
		$main -> alertPrepare(true, 'Wiadomość została wysłana. Postaramy się odpowiedzieć w ciągu najbliższych godzin');
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
		<h2><?php echo((($url -> opd('zglos') > 0) ? 'Zgłoś firmę' : 'Kontakt')); ?></h2>
		<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SEND)); ?>">

<?php

if($url -> opd('zglos') > 0) {
	$item = new Item($url -> opd('zglos'));
	if($item -> itemId > 0) {
		echo('<input type="hidden" name="item" value="'.$item -> itemId.'">');
		echo('<section class="alert alert-error">');
		echo('<p>Zgłaszasz firmę: <span class="bold">'.$item -> title.'</span></p>');
		echo('</section>');
	}
}

?>

			<label>
				<textarea name="content" placeholder="<?php echo(($url -> opd('zglos') > 0) ? 'Powód zgłoszenia' : 'Treść wiadomości'); ?>" required="required"><?php echo($_POST['content']); ?></textarea>
			</label>
			<div class="cols cols-2">
				<label>
					<input type="text" name="sender" placeholder="Twoje imię i nazwisko" required="required" value="<?php echo($_POST['sender']); ?>">
				</label>
				<label>
					<input type="text" name="email" placeholder="Twój adres e-mail" required="required" value="<?php echo($_POST['email']); ?>">
				</label>
			</div>
			<div class="buttons">
				<input type="submit" value="Wyślij">
			</div>
		</form>
	</div>
	
	<div id="info">
		<div class="main">
			<div class="cols cols-4">
				<div class="col-item">
					<img src="./template/default/image/star-red.png" alt="Budujemy">
					<h3>Budujemy</h3>
					<p>Pozytywny wizerunek firmy<br>w sieci poprzez e-wizytówkę</p>
				</div>
				<div class="col-item">
					<img src="./template/default/image/star-yellow.png" alt="Ułatwiamy">
					<h3>Ułatwiamy</h3>
					<p>Dotrzeć do klientów poprzez domenę,<br>która jest najczęściej wyszukiwaną<br>frazą w google</p>
				</div>
				<div class="col-item">
					<img src="./template/default/image/star-blue.png" alt="Proponujemy">
					<h3>Proponujemy</h3>
					<p>Firmę na Facebooku<br>w okolicy przedsiębiorstwa</p>
				</div>
				<div class="col-item">
					<img src="./template/default/image/star-black.png" alt="Łączymy">
					<h3>Łączymy</h3>
					<p>Najlepsze firmy z całego kraju</p>
				</div>
			</div>
			<div class="cols cols-4">
				<div class="col-item">
					<img src="./template/default/image/star-black.png" alt="Kreujemy">
					<h3>Kreujemy</h3>
					<p>Nowy wizerunek firmy,<br>dzięki galerii zdjęć<br>oraz opinii klientów</p>
				</div>
				<div class="col-item">
					<img src="./template/default/image/star-blue.png" alt="Pomagamy">
					<h3>Pomagamy</h3>
					<p>Dotrzeć do nowych klientów<br>szukających firm w internecie<br>oraz podniesienia obrotów firmie</p>
				</div>
				<div class="col-item">
					<img src="./template/default/image/star-red.png" alt="Rozwijamy">
					<h3>Rozwijamy</h3>
					<p>Możliwości dotarcia<br>do tysięcy klientów w swojej okolicy</p>
				</div>
				<div class="col-item">
					<img src="./template/default/image/star-yellow.png" alt="Wyróżniamy">
					<h3>Wyróżniamy</h3>
					<p>Przedsiębiorstwa na tle konkurencji</p>
				</div>
			</div>
		</div>
	</div>
	<hr>
	<div class="main bottom-space center">
		<h2>Bądź godny polecenia</h2>
		<p>
			Dołącz do naszego programu.  Zaoszczędź na kilku abonamentach i reklamie.<br>
			To wszystko poprowadzimy za Ciebie. Skierujemy do Ciebie klientów, <br>
			chcących skorzystać z Twoich usług.<br>
		</p>
		<br>
		<?php echo($url -> getButton('item/add', false, null, ['class' => 'button'.(($user -> userId == 0) ? ' login-window' : '')], 'Dla firm')); ?>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>