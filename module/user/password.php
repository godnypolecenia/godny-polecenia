<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

$user -> onlyGuest();

/**
 *	This file allows you to reset the user account password
 */

/**
 *	Verification message
 */
if($url -> op(0) == URL_EXEC) {
	$vd = new Validate;
	$vd -> isEmail($_POST['email'], 'Adres e-mail');
	$vd -> isCaptcha($_POST['g-recaptcha-response']);
	
	if($vd -> pass() == true) {	
		$passUser = new User;
		if($passUser -> getUserByLogin($_POST['email'])) {
			$tmpMail = str_replace(
				['{login}', '{email}', '{link}', '{nazwa}', '{adres}'],
				[$_POST['login'], $passUser -> email, $url -> getUrl('user/password', false, '/'.$passUser -> userId.'-'.password($passUser -> register_time)), $setup -> name, SITE_ADDRESS],
				$setup -> mail_password_1
			);
			send_mail($passUser -> email, $setup -> mail_password_1_title, $tmpMail);
			$main -> alertPrepare(true, 'Na podany adres e-mail została wysłana wiadomość z kluczem potwierdzającym - kliknij w niego by zresetować swoje hasło. Jeżeli wiadomość nie dotrze w ciągu kilku minut <span class="bold">sprawdź zakładkę SPAM</span> w swoim programie pocztowym.');
			$url -> redirect();
		} else {
			$vd -> putError(USER_ERR_NOT_EXISTS);	
		}
	}
	
	$main -> alertPrepare($vd -> pass(), $vd -> resultArray());
}

/**
 *	Message with a new password 
 */
if($url -> op(0) <> '' && $url -> op(0) <> URL_EXEC && !($url -> opd(URL_LOGIN) <> '')) {
	$ex = explode('-', $url -> op(0));
	$passUser = new User;
	if($passUser -> getUserById($ex[0])) {
		if(password($passUser -> register_time) == $ex[1]) {
			$newPassword = randomText(8);
			$passUser -> password = $newPassword;
			
			$tmpMail = str_replace(
			['{login}', '{email}', '{haslo}', '{nazwa}', '{adres}', '{link}'],
			[$_POST['login'], $passUser -> email, $newPassword, $setup -> name, SITE_ADDRESS, $url -> getUrl('user/login')],
			$setup -> mail_password_2
		);
		send_mail($passUser -> email, $setup -> mail_password_2_title, $tmpMail);
		$main -> alertPrepare(true, 'Na podany adres e-mail wysłaliśmy wiadomość z nowym hasłem. Jeżeli wiadomość nie dotrze w ciągu kilku minut <span class="bold">sprawdź zakładkę SPAM</span> w swoim programie pocztowym.');
		$url -> redirect();
		} else {
			$main -> alertPrepare(false);
		}
	} else {
		$main -> alertPrepare(false);
	}
}

$url -> addBackUrl();

/**
 *	Layout
 */

$url -> setBodyId('login');

$meta = [
	'title' => $url -> title,
	'description' => $url -> description,
	'keywords' => $url -> keywords,
	'robots' => (($url -> index == 1) ? 'index' : 'noindex')
];
require_once(INC_DEFAULT_TPL_HEADER);

?>

<div id="top-content">
	<div class="main">
		<?php $main -> alert(); ?>
		<h2 style="margin-top: 0;">Zresetuj hasło</h2>
		<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_EXEC)); ?>" style="max-width: 500px;" class="recaptcha">
			<label>
				<input type="email" name="email" required="required" placeholder="Adres e-mail" value="<?php echo($_POST['email']); ?>">
			</label>
			<div class="buttons">
				<input type="submit" value="Zresetuj hasło">
			</div>
		</form>
	</div>
</div>
<div id="slider" class="slider-mini">
	<div class="main">
		<div class="red-line"></div>
		<h1 class="lora"><?php echo($meta['title']); ?></h1>
		<div class="red-line"></div>
	</div>
</div>
<div id="info" class="info-padding">
	<div class="main">
		<h2 class="center">Korzyści z dołączenia do Godny Polecenia</h2><br>
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
<div id="info-2">
	<div class="main center">
		<div id="red-box">
			<div class="cols cols-3">
				<div class="col-item">
					<h3>54%</h3>
					<p>Zleceń dla małych i średnich<br>firm pochodzi z internetu</p>
				</div>
				<div class="col-item">
					<h3>78%</h3>
					<p>Klientów zanim skorzysta z usług<br>jakiejś firmy szuka informacji<br>na jej temat w internecie</p>
				</div>
				<div class="col-item">
					<h3>52%</h3>
					<p>Zapytań do Wyszukiwarki Google<br>w zakresie firm lokalnych zawiera<br>frazy rekomendacje jak "polecany",<br>"godny polecenia", "opinie"</p>
				</div>
			</div>
		</div>
		<h2>Stwórz profesjonalny profil firmy</h2>
		<h3>i wyróżnij się na tle konkurencji</h3>
		<div class="center"><a href="<?php echo($url -> getUrl('item/add')); ?>" class="button">Dołącz</a></div>
	</div>
</div>
<div id="content">
	<div class="main bottom-space">

<?php

$bc = new Breadcrumb();
$bc -> add($url -> getLink('user/index'));
$bc -> add($url -> getLink());
$bc -> output();

?>

		<h2 class="center">Rekomendacje</h2>
		<div class="cols cols-3 gap-zero">
			<div class="col-item vote-box">
				<h3>Udana współpraca</h3>
				<p>Tekst przykładowy. Jestem zadowolony z korzystania z portalu Godny - polecenia. Usługi zostały wykonanie na wysokim.</p>
				Nazwa firmy
			</div>
			<div class="col-item vote-box">
				<h3>Udana współpraca</h3>
				<p>Tekst przykładowy. Jestem zadowolony z korzystania z portalu Godny - polecenia. Usługi zostały wykonanie na wysokim.</p>
				Nazwa firmy
			</div>
			<div class="col-item vote-box">
				<h3>Udana współpraca</h3>
				<p>Tekst przykładowy. Jestem zadowolony z korzystania z portalu Godny - polecenia. Usługi zostały wykonanie na wysokim.</p>
				Nazwa firmy
			</div>
		</div>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>