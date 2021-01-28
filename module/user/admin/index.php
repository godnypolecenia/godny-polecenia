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
 *	This file manages users settings
 */

if($url -> op(0) == URL_ADD) {
	$newUser = new User;
	$lid = $newUser -> add($_POST['email']);
	if($lid > 0) {
		if(!($_POST['password'] <> '')) $_POST['password'] = randomText(8);
		
		$newUser -> password = $_POST['password'];
		$newUser -> type = $_POST['type'];
		$newUser -> status = $_POST['status'];
		$newUser -> name = $_POST['name'];
		$newUser -> address = $_POST['address'];
		$newUser -> city = $_POST['city'];
		$newUser -> postcode = $_POST['postcode'];
		$newUser -> nip = $_POST['nip'];
		
		if($_POST['send'] == 1) {
			$tmpEmail = str_replace(
				['{nazwa}', '{email}', '{haslo}', '{link}'],
				[$setup -> name, $_POST['email'], $_POST['password'], $url -> getUrl('user/login')],
				$setup -> mail_register_admin
			);
			send_mail($_POST['email'], $setup -> mail_register_admin_title, $tmpEmail);
		}
		$main -> alertPrepare(true, '<p>Konto zostało utworzone.<br>Login: '.$_POST['email'].'<br>Hasło: '.$_POST['password'].'</p>');
		$url -> redirect('user/admin/user', false, '/'.$lid);
	} else {
		$main -> alertPrepare(false, 'Wybrany adres e-mail już jest zajęty');
	}
}

/**
 *	Search
 */

$sqlSearch = '';
if($url -> opd(URL_BOOKMARK) == 1) $sqlSearch .= ' && `type` = 0 ';
if($url -> opd(URL_BOOKMARK) == 2) $sqlSearch .= ' && `type` = 9 ';

$search = [];
$searchCount = 0;

if($url -> opd(URL_SEARCH) == URL_SEND) {
	$tmpUrl = '/'.URL_SEARCH;
	if($_POST['word'] <> '') $tmpUrl .=  '/'.URL_QUERY.'-'.urlencode($_POST['word']);
	if($url -> opd(URL_BOOKMARK) <> '') $tmpUrl .=  '/'.URL_BOOKMARK.'-'.$url -> opd(URL_BOOKMARK);
	$url -> redirect(null, false, $tmpUrl);
} elseif($url -> issetOpd(URL_SEARCH)) {
	if($url -> opd(URL_QUERY) <> '') {
		$search['word'] = urldecode($url -> opd(URL_QUERY));
		$sqlWordTmp = str_replace(' ', '%', $search['word']);
		$sqlSearch .= ' && (`email` LIKE "%'.$sqlWordTmp.'%" || `name` LIKE "%'.$sqlWordTmp.'%") ';
		$searchCount++;
	}
}

/**
 *
 */

$userList = new User;
$countUser = $userList -> countUserList($sqlSearch);
 
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
$bc -> add($url -> getLink());
$bc -> output();

?>
			
		<div id="left">
			<?php require_once(INC_ADMIN_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<section id="search-container" class="open-section-container<?php if($searchCount > 0) echo(' open-section-container-show'); ?>">
				<h2><span class="ri-search-line icon"></span>Szukaj</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, (($url -> opd(URL_BOOKMARK) <> '') ? '/'.URL_BOOKMARK.'-'.$url -> opd(URL_BOOKMARK) : '').'/'.URL_SEARCH.'-'.URL_SEND)); ?>">
					<label>
						Szukana fraza
						<input type="text" name="word" required="required" value="<?php echo($search['word']); ?>">
					</label>
					<div class="buttons">
						<input type="submit" value="Szukaj">
						<?php if($searchCount > 0) echo('<a href="'.$url -> getUrl().'" class="underline">Anuluj wyszukiwanie</a>'); ?>
					</div>
				</form>
			</section>
			<ul class="bookmark">
				<?php if($mobile == 1) echo('<li class="bookmark-slide"><span class="ri-arrow-left-right-line"></span></li>'); ?>
				<li><?php echo($url -> getBookmark(0, 'Wszyscy')); ?></li>
				<li><?php echo($url -> getBookmark(1, 'Użytkownicy')); ?></li>
				<li><?php echo($url -> getBookmark(2, 'Administratorzy')); ?></li>
			</ul>
			<section>
				<h1><span class="ri-user-line icon"></span><?php echo($meta['title']); ?></h1>
				<a href="#" id="search-open" class="ri-search-line open-section section-top-right" title="Szukaj"></a>

<?php

if($countUser > 0) {
	echo('<table>');
	echo('<tr>');
	echo('<th>Adres e-mail</th>');
	echo('<th>'.URL_BOOKMARK.'</th>');
	echo('<th>Status</th>');
	echo('<th>Opcje</th>');
	echo('</tr>');
	foreach($userList -> getUserList($sqlSearch) as $r) {
		echo('<tr>');
		echo('<td>'.$r['email'].'</td>');
		echo('<td>'.(($r['type'] == 9) ? 'Administrator' : 'Użytkownik').'</td>');
		echo('<td>'.(($r['status'] == 1) ? 'Aktywny' : 'Nieaktywny').'</td>');
		echo('<td><a href="'.$url -> getUrl('user/admin/edit', false, '/'.$r['user_id']).'">Zarządzaj</a></td>');
		echo('</tr>');
	}
	echo('</table>');
	paging($n);
} else {
	echo('<div>Niczego nie znaleziono</div>');
}

?>

			</section>
			<section class="toggle">
				<h2><span class="ri-user-add-line icon"></span>Dodaj użytkownika</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_ADD)); ?>">
					<div class="cols cols-2">
						<label>
							Adres e-mail (login)
							<input type="email" name="email" required="required" value="<?php echo($_POST['email']); ?>">
						</label>
						<label>
							Hasło
							<input type="password" name="password" placeholder="Pozostaw puste by wygenerować losowe hasło">
						</label>
					</div>
					<a href="#" class="show-container underline" id="s1">Pokaż więcej opcji</a>
					<div class="hide-container" id="s1-container">
						<div class="cols cols-2">
							<label>
								Typ konta
								<select name="type">
									<option value="0"<?php if($_POST['type'] === 0) echo(' selected="selected"'); ?>>Użytkownik</option>
									<option value="9"<?php if($_POST['type'] === 9) echo(' selected="selected"'); ?>>Administrator</option>
								</select>
							</label>
							<label>
								Status użytkownika
								<select name="status">
									<option value="1"<?php if($_POST['status'] === 1) echo(' selected="selected"'); ?>>Aktywny</option>
									<option value="0"<?php if($_POST['status'] === 0) echo(' selected="selected"'); ?>>Nieaktywny</option>
								</select>
							</label>
						</div>
						<label>
							Nazwa
							<input type="text" name="name" placeholder="Nick lub imię i nazwisko" value="<?php echo($_POST['name']); ?>">
						</label>
						<div class="cols cols-3">
							<label>
								Adres
								<input type="text" name="address" value="<?php echo($_POST['address']); ?>">
							</label>
							<label>
								Miejscowość
								<input type="text" name="city" value="<?php echo($_POST['city']); ?>">
							</label>
							<label>
								Kod pocztowy
								<input type="text" name="postcode" value="<?php echo($_POST['postcode']); ?>" pattern="^[0-9]{2}-[0-9]{3}$" placeholder="00-000">
							</label>
						</div>
						<label>
							Numer NIP
							<input type="text" name="nip" placeholder="Tylko firmy" value="<?php echo($_POST['nip']); ?>">
						</label>
						<label>
							<input type="checkbox" name="send" value="1"<?php if($_POST['send'] == 1) echo(' checked="checked"'); ?>>
							Wyślij wiadomość e-mail z hasłem do użytkownika
						</label>
					</div>
					<div class="buttons">
						<input type="submit" value="Dodaj">
					</div>
				</form>
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_ADMIN_TPL_FOOTER); ?>