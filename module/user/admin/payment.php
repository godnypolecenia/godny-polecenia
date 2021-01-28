<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

$user -> onlyAdmin(4);

/**
 *	This file manages payment settings
 */

$editPay = new Payment;
if(!$editPay -> getPaymentById($url -> op(0))) {
	require_once('./module/tool/404.php');
	exit;
}

$payUser = new User($editPay -> user_id);

if($editPay -> item_id > 0) {
	$payItem = new Item($editPay -> item_id);
}

/**
 *
 */

if($url -> op(1) == URL_SAVE && $_POST['status'] == 1) {
	$payItem -> status = -1;
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$url -> op(0));
}

if($url -> op(1) == URL_SAVE && $_POST['status'] == 2) {
	file_get_contents($url -> getUrl('payment-exec', false, '/'.$url -> op(0).'/'.password(date('Y-m-d').TOKEN)));
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$url -> op(0));
}

if($url -> op(1) == URL_SAVE && $_POST['status'] == 3) {
	$payItem -> status = 1;
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$url -> op(0));
}

if($url -> op(1) == URL_DEL && $_POST['delete'] == 1) {
	if($editPay -> delete($url -> op(0))) {
		$main -> alertPrepare(true);
		$url -> redirect('user/admin/payment-list');
	}
}

if($url -> op(1) == URL_UPLOAD && is_array($_POST)) {
	if(is_array($_FILES['file'])) {
		$file = new upload($_FILES['file']);
		if($file -> save('./data/bill', $user -> user_id.'-'.password(time().rand(0, 999))) <> false) {
			$editPay -> bill = $file -> getFullName();
			$main -> alertPrepare(true);
			$url -> redirect(null, false, '/'.$url -> op(0));
		} else {
			$main -> alertPrepare(false, FILE_ERR_UPLOAD);
		}
	} else {
		$main -> alertPrepare(false, FILE_ERR_NULL);
	}
}

if($url -> op(1) == URL_BILL) {
	@unlink('./data/bill/'.$payItem -> bill);
	$editPay -> bill = '';
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$url -> op(0));
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
$bc -> add($url -> getLink('user/admin/payment-list'));
$bc -> add($url -> getLink(null, true));
$bc -> output();

?>
			
		<div id="left">
			<?php require_once(INC_ADMIN_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<section>
				<h1><span class="ri-money-dollar-circle-line icon"></span><?php echo($meta['title']); ?></h1>
				<ul class="ul">
					<li>Typ: <span class="bold"><?php echo($paymentType[$editPay -> type]); ?></span></li>
					<li>Data rozpoczęcia: <span class="bold"><?php echo(dateTimeFormat($editPay -> date)); ?></span></li>
					<li>Data płatności: <span class="bold"><?php echo(($editPay -> date_pay > 0) ? dateTimeFormat($editPay -> date_pay) : 'Nieopłacone'); ?></span></li>
					<li>Kwota: <span class="bold"><?php echo(priceFormat($editPay -> amount)); ?></span></li>
					<li>Faktura: <span class="bold"><?php echo(($editPay -> bill <> '') ? '<a href="'.$url -> getUrl('bill', false, $editPay -> paymentId).'">Pobierz</a> lub <a href="'.$url -> getUrl(null, true, '/'.URL_BILL).'">Usuń</a>' : 'Nie wystawiono'); ?></span></li>
					<li>Status: <span class="bold"><?php echo($paymentStatus[$editPay -> status]); ?></span></li>
					<li>Użytkownik: <a href="<?php echo($url -> getUrl('user/admin/user', false, '/'.$payUser -> userId)); ?>" class="bold"><?php echo($payUser -> name); ?></a></li>
					<?php if($editPay -> item_id) echo('<li>'.ITEM_TITLE.': <a href="'.$url -> getUrl('admin/item/item', false, '/'.$payItem -> itemId).'" class="bold">'.$payItem -> title.'</a></li>'); ?>
				</ul>
			</section>
			<?php if($editPay -> status == 0) { ?>
			<section class="toggle">
				<h2><span class="ri-settings-2-line icon"></span>Zarządzaj płatnością</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_SAVE)); ?>">
					<label>
						Status płatności
						<select name="status" required="required">
							<option value="">Wybierz</option>
							<option value="1">Anuluj płatność</option>
							<option value="2">Ustaw jako opłacona (przetwórz operację)</option>
							<option value="3">Ustaw jako opłacona (nie przetwarzaj operacji - zmień tylko status)</option>
						</select>
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
			<?php } ?>
			<?php if($editPay -> status == 1 && $editPay -> bill == '') { ?>
			<section class="toggle">
				<h2><span class="ri-bill-line icon"></span>Dodaj fakturę</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_UPLOAD)); ?>" enctype="multipart/form-data" class="dropzone" id="dropzone">
					<label class="fallback">
						<input type="file" name="file" required="required">
					</label>
					<div class="buttons">
						<input type="submit" value="Dodaj">
					</div>
				</form>
				<br>
				<a href="<?php echo($url -> getUrl(null, true)); ?>" class="button">Dodaj</a>
			</section>
			<?php } ?>
			<section class="toggle">
				<h2><span class="ri-delete-bin-line icon"></span>Usuń płatność</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_DEL)); ?>">
					<label>
						<input type="checkbox" name="delete" value="1" required="required">
						Potwierdzam chęć usunięcia tego wpisu o płatności
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