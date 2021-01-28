<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

$user -> onlyAdmin(3);

/**
 *	This file manages user settings
 */

$editUser = new User;
if(!$editUser -> getUserById($url -> op(0))) {
	require_once('./module/tool/404.php');
	exit;
}

$power = explode(';', $editUser -> power);


/**
 *
 */

if($url -> op(1) == URL_EDIT) {
	$vd = new Validate;
	$vd -> isEmail($_POST['email'], 'Adres e-mail');
	//$vd -> isValue($_POST['type'], 'Typ');
	//$vd -> isValue($_POST['status'], 'Status');
	
	if($vd -> pass() == true) {	
		$editUser -> email = $_POST['email'];
		if($user -> power == '') {
			if($editUser -> type <> $_POST['type']) {
				$editUser -> power = '0;0;0;0;0;0;0;0';
			}
			$editUser -> type = $_POST['type'];
		}
		$editUser -> status = $_POST['status'];
		
		$main -> alertPrepare(true);
		$url -> redirect(null, false, '/'.$url -> op(0));
	}
	
	$main -> alertPrepare($vd -> pass(), $vd -> resultArray());
}

if($url -> op(1) == 'power') {
	if($user -> power == '') {
		$tmp = [];
		$tmp[] = (int)$_POST['power-0'];
		$tmp[] = (int)$_POST['power-1'];
		$tmp[] = (int)$_POST['power-2'];
		$tmp[] = (int)$_POST['power-3'];
		$tmp[] = (int)$_POST['power-4'];
		$tmp[] = (int)$_POST['power-5'];
		$tmp[] = (int)$_POST['power-6'];
		$tmp[] = (int)$_POST['power-7'];
		$editUser -> power = implode(';', $tmp);
		$main -> alertPrepare(true);
		$url -> redirect(null, false, '/'.$url -> op(0));
	}
}

if($url -> op(1) == URL_DATA) {
	$vd = new Validate;
	$vd -> isValue($_POST['name'], 'Nazwa');
	
	if($vd -> pass() == true) {	
		$editUser -> name = $_POST['name'];
		$editUser -> region = $_POST['region'];
		$editUser -> city = $_POST['city'];
		$editUser -> postcode = $_POST['postcode'];
		$editUser -> address = $_POST['address'];
		$editUser -> nip = $_POST['nip'];
		
		$main -> alertPrepare(true);
		$url -> redirect(null, false, '/'.$url -> op(0));
	}
	
	$main -> alertPrepare($vd -> pass(), $vd -> resultArray());
}

if($url -> op(1) == URL_ADD) {
	if(is_array($_FILES['file'])) {
		$file = new Upload($_FILES['file']);
		if($file -> allowedType(['image/jpg', 'image/jpeg', 'image/png', 'image/gif'])) {
			if($file -> maxSize(50000000)) {
				$name = $file -> save('./data/upload', $name);
				if($name <> '') {
					$delImage = new Image($editUser -> avatar);
					$delImage -> remove();
	
					$editUser -> avatar = $name;
					//$main -> alertPrepare(true);
					$url -> redirect(null, false, '/'.$url -> op(0));
				} else {
					$main -> alertPrepare(false, FILE_ERR_UPLOAD);
				}
			} else {
				$main -> alertPrepare(false, FILE_ERR_WEIGHT);
			}
		} else {
			$main -> alertPrepare(false, FILE_ERR_FORMAT);
		}
	} else {
		$main -> alertPrepare(false, FILE_ERR_NULL);
	}
}

if($url -> op(1) == URL_CLEAR) {
	$delImage = new Image($editUser -> avatar);
	$delImage -> remove();
	$editUser -> avatar = '';
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$url -> op(0));
}

if($url -> op(1) == URL_PASS) {
	$vd = new Validate;
	$vd -> isString($_POST['password-new'], 'Nowe hasło', 8);
	$vd -> isString($_POST['password-new-confirm'], 'Powtórz hasło', 8);
	
	if($_POST['password-new'] <> $_POST['password-new-confirm']) {
		$vd -> putError(USER_ERR_DIFFERENT_PASS);
	}

	if($vd -> pass() == true) {	
		$editUser -> password = $_POST['password-new'];
		
		$main -> alertPrepare(true);
		$url -> redirect(null, false, '/'.$url -> op(0));
	}
	
	$main -> alertPrepare($vd -> pass(), $vd -> resultArray());
}

if($url -> op(1) == URL_SEND && $_POST['title'] <> '') {
	$status = send_mail($editUser -> email, $_POST['title'], $_POST['content']);
	if($status === true) {
		$main -> alertPrepare(true);
		$url -> redirect(null, false, '/'.$url -> op(0));
	} else {
		$main -> alertPrepare(false, 'Nie udało się wysłać wiadomości, gdyż natrafiono na błąd: '.$status);
	}
}

if($url -> op(1) == URL_DEL && $_POST['delete'] == 1) {
	if($editUser -> delete($url -> op(0))) {
		$main -> alertPrepare(true);
		$url -> redirect('user/admin/index');
	}
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
$bc -> add($url -> getLink(null, true));
$bc -> output();

?>
			
		<div id="left">
			<?php require_once(INC_ADMIN_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<section>
				<h1><span class="ri-user-line icon"></span><?php echo($meta['title']); ?></h1>
				<ul class="ul">
					<li>Nazwa: <span class="bold"><?php echo($editUser -> name); ?></span></li>
					<li>Uprawnienia: <span class="bold"><?php echo(($editUser -> type == 9) ? 'Administrator' : 'Użytkownik'); ?></span></li>
					<li>Status: <span class="bold"><?php echo(($editUser -> status == 1) ? 'Aktywny' : 'Niekatywny'); ?></span></li>
					<li>Adres-email (login): <span class="bold"><?php echo($editUser -> email); ?></span></li>
					<li>Dołączył: <span class="bold"><?php echo(dateTimeFormat($editUser -> register_time)); ?></span> z IP: <span class="bold"><?php echo($editUser -> register_ip); ?></span> - <a href="<?php echo($url -> getUrl('user/admin/bans', null, '/'.URL_EXEC.'-'.str_replace('.', '-', $editUser -> register_ip))); ?>" class="underline">zablokuj</a></li>
					<li>Ostatnio online: <span class="bold"><?php echo(dateTimeFormat($editUser -> session_time)); ?></span> z IP: <span class="bold"><?php echo($editUser -> session_ip); ?></span> - <a href="<?php echo($url -> getUrl('user/admin/bans', null, '/'.URL_EXEC.'-'.str_replace('.', '-', $editUser -> session_ip))); ?>" class="underline">zablokuj</a></li>
				</ul>
			</section>
			<section class="toggle">
				<h2><span class="ri-edit-box-line icon"></span>Edytuj użytkownika</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_EDIT)); ?>">
					<label>
						Adres e-mail (login)
						<input type="email" name="email" placeholder="Uwaga! Adres e-mail musi być unikalny" value="<?php echo($editUser -> email); ?>" required="required">
					</label>
					<label>
						Uprawnienia konta
						<select name="type"<?php if($user -> power <> '') echo(' disabled="disabled"'); ?>>
							<option value="0"<?php if($editUser -> type == 0) echo(' selected="selected"'); ?>>Użytkownik</option>
							<option value="9"<?php if($editUser -> type == 9) echo(' selected="selected"'); ?>>Administrator</option>
						</select>
					</label>
					<label>
						Status użytkownika
						<select name="status">
							<option value="0"<?php if($editUser -> status == 0) echo(' selected="selected"'); ?>>Nieaktywny</option>
							<option value="1"<?php if($editUser -> status == 1) echo(' selected="selected"'); ?>>Aktywny</option>
						</select>
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
			<?php if($editUser -> type == 9 && $user -> power == '') { ?>
			<section class="toggle">
				<h2><span class="ri-edit-box-line icon"></span>Uprawnienia administratora</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/power')); ?>">
					<label>
						<input type="checkbox" name="power-0" value="1"<?php if($power[0] == 1) echo(' checked=checked"'); ?>>
						Firmy
					</label>
					<label>
						<input type="checkbox" name="power-1" value="1"<?php if($power[1] == 1) echo(' checked=checked"'); ?>>
						Oceny
					</label>
					<label>
						<input type="checkbox" name="power-2" value="1"<?php if($power[2] == 1) echo(' checked=checked"'); ?>>
						Ustawienia
					</label>
					<label>
						<input type="checkbox" name="power-3" value="1"<?php if($power[3] == 1) echo(' checked=checked"'); ?>>
						Użytkownicy
					</label>
					<label>
						<input type="checkbox" name="power-4" value="1"<?php if($power[4] == 1) echo(' checked=checked"'); ?>>
						Płatności
					</label>
					<label>
						<input type="checkbox" name="power-5" value="1"<?php if($power[5] == 1) echo(' checked=checked"'); ?>>
						Newsletter
					</label>
					<label>
						<input type="checkbox" name="power-6" value="1"<?php if($power[6] == 1) echo(' checked=checked"'); ?>>
						Treści
					</label>
					<label>
						<input type="checkbox" name="power-7" value="1"<?php if($power[7] == 1) echo(' checked=checked"'); ?>>
						Ustawienia strony
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
			<?php } ?>
			<section class="toggle">
				<h2><span class="ri-edit-box-line icon"></span>Dane użytkownika</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_DATA)); ?>">
					<label>
						Nazwa
						<input type="text" name="name" placeholder="Nick lub imię i nazwisko" required="required" value="<?php echo($editUser -> name); ?>">
					</label>
					<div class="cols cols-4">
						<label>
							Województwo
							<select name="region">
								<option value="0">Wybierz</option>
								<?php foreach($regionName as $k => $v) echo('<option value="'.$k.'"'.(($editUser -> region == $k) ? ' selected="selected"' : '').'>'.$v.'</option>'); ?>
							</select>
						</label>
						<label>
							Miejscowość
							<input type="text" name="city" value="<?php echo($editUser -> city); ?>">
						</label>
						<label>
							Kod pocztowy
							<input type="text" name="postcode" placeholder="00-000" value="<?php echo($editUser -> postcode); ?>">
						</label>
						<label>
							Adres
							<input type="text" name="address" value="<?php echo($editUser -> address); ?>">
						</label>
					</div>
					<label>
						Numer NIP
						<input type="text" name="nip" placeholder="Tylko firmy" value="<?php echo($editUser -> nip); ?>">
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
			<section class="toggle">
				<h2><span class="ri-image-line icon"></span>Zmień avatar</h2>
				<?php if($editUser -> avatar <> '') { $ex = explode('.', $editUser -> avatar); ?>
				<img src="<?php echo($url -> getUrl('tool/image', false, '/'.$ex[0].'.150x150.'.end($ex))); ?>" alt="<?php echo($user -> name); ?>" class="avatar" style="margin-right: 10px;">
				<a href="<?php echo($url -> getUrl(null, false, '/'.$url -> op(0).'/'.URL_CLEAR)); ?>" class="underline">Usuń avatar</a>
				<hr>
				<?php } ?>
				<section>
					Dozwolone formaty: JPG, PNG, GIF
				</section>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.$url -> op(0).'/'.URL_ADD)); ?>" enctype="multipart/form-data" class="dropzone" id="dropzone">
					<label class="fallback">
						<input type="file" name="file" required="required">
					</label>
					<div class="buttons">
						<input type="submit" value="Dodaj">
					</div>
				</form>
				<div class="buttons">
					<a href="<?php echo($url -> getUrl(null, true)); ?>" class="button">Zapisz</a>
				</div>
			</section>
			<section class="toggle">
				<h2><span class="ri-lock-password-line icon"></span>Zmień hasło</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_PASS)); ?>">
					<label>
						Nowe hasło
						<input type="password" name="password-new" placeholder="Minimum 8 znaków" required="required">
					</label>
					<label>
						Powtórz nowe hasło
						<input type="password" name="password-new-confirm" required="required">
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
			<section class="toggle">
				<h2><span class="ri-mail-line icon"></span>Wyślij e-mail do użytkownika</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.$url -> op(0).'/'.URL_SEND)); ?>">
					<label>
						Temat
						<input type="text" name="title" required="required" value="<?php echo($_POST['title']); ?>">
					</label>
					<label>
						Treść wiadomości
						<textarea name="content" class="wysiwyg"><?php echo($_POST['content']); ?></textarea>
					</label>
					<div class="buttons">
						<input type="submit" value="Wyślij">
						Stopka zostanie załączona automatycznie
					</div>
				</form>
			</section>
			<section class="toggle">
				<h2><span class="ri-delete-bin-line icon"></span>Usuń użytkownika</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_DEL)); ?>">
					<label>
						<input type="checkbox" name="delete" value="1" required="required">
						Potwierdzam chęć usunięcia tego użytkownika
					</label>
					<div class="buttons">
						<input type="submit" value="Usuń">
					</div>
				</form>
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_ADMIN_TPL_FOOTER); ?>