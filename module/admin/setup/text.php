<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

$user -> onlyAdmin(7);

/**
 *	This file manages SEO settings
 */

if($url -> op(0) == URL_SAVE && is_array($_POST)) {
	foreach($_POST as $k => $v) {
		$setup -> $k = $v;
	}
	$main -> alertPrepare(true);
	$url -> redirect();
}

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
$bc -> add($url -> getLink('admin/index'));
$bc -> add($url -> getLink());
$bc -> output();

?>
			
		<div id="left">
			<?php require_once(INC_ADMIN_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<section class="toggle">
				<h2><span class="ri-file-3-line icon"></span>Jak to działa - dla użytkownika</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<h3>Treść nr 1</h3>
					<label>
						Nagłówek
						<input type="text" name="tips-h-1" value="<?php echo($setup -> tips_h_1); ?>">
					</label>
					<label>
						Treść
						<textarea name="tips-t-1" class="short wysiwyg"><?php echo($setup -> tips_t_1); ?></textarea>
					</label>
					<h3>Treść nr 2</h3>
					<label>
						Nagłówek
						<input type="text" name="tips-h-2" value="<?php echo($setup -> tips_h_2); ?>">
					</label>
					<label>
						Treść
						<textarea name="tips-t-2" class="short wysiwyg"><?php echo($setup -> tips_t_2); ?></textarea>
					</label>
					<h3>Treść nr 3</h3>
					<label>
						Nagłówek
						<input type="text" name="tips-h-3" value="<?php echo($setup -> tips_h_3); ?>">
					</label>
					<label>
						Treść
						<textarea name="tips-t-3" class="short wysiwyg"><?php echo($setup -> tips_t_3); ?></textarea>
					</label>
					<h3>Treść nr 4</h3>
					<label>
						Nagłówek
						<input type="text" name="tips-h-4" value="<?php echo($setup -> tips_h_4); ?>">
					</label>
					<label>
						Treść
						<textarea name="tips-t-4" class="short wysiwyg"><?php echo($setup -> tips_t_4); ?></textarea>
					</label>
					<h3>Treść nr 5</h3>
					<label>
						Nagłówek
						<input type="text" name="tips-h-5" value="<?php echo($setup -> tips_h_5); ?>">
					</label>
					<label>
						Treść
						<textarea name="tips-t-5" class="short wysiwyg"><?php echo($setup -> tips_t_5); ?></textarea>
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
			<section class="toggle">
				<h2><span class="ri-file-3-line icon"></span>Jak to działa - dla firm</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<h3>Treść nr 1</h3>
					<label>
						Nagłówek
						<input type="text" name="tips2-h-1" value="<?php echo($setup -> tips2_h_1); ?>">
					</label>
					<label>
						Treść
						<textarea name="tips2-t-1" class="short wysiwyg"><?php echo($setup -> tips2_t_1); ?></textarea>
					</label>
					<h3>Treść nr 2</h3>
					<label>
						Nagłówek
						<input type="text" name="tips2-h-2" value="<?php echo($setup -> tips2_h_2); ?>">
					</label>
					<label>
						Treść
						<textarea name="tips2-t-2" class="short wysiwyg"><?php echo($setup -> tips2_t_2); ?></textarea>
					</label>
					<h3>Treść nr 3</h3>
					<label>
						Nagłówek
						<input type="text" name="tips2-h-3" value="<?php echo($setup -> tips2_h_3); ?>">
					</label>
					<label>
						Treść
						<textarea name="tips2-t-3" class="short wysiwyg"><?php echo($setup -> tips2_t_3); ?></textarea>
					</label>
					<h3>Treść nr 4</h3>
					<label>
						Nagłówek
						<input type="text" name="tips2-h-4" value="<?php echo($setup -> tips2_h_4); ?>">
					</label>
					<label>
						Treść
						<textarea name="tips2-t-4" class="short wysiwyg"><?php echo($setup -> tips2_t_4); ?></textarea>
					</label>
					<h3>Treść nr 5</h3>
					<label>
						Nagłówek
						<input type="text" name="tips2-h-5" value="<?php echo($setup -> tips2_h_5); ?>">
					</label>
					<label>
						Treść
						<textarea name="tips2-t-5" class="short wysiwyg"><?php echo($setup -> tips2_t_5); ?></textarea>
					</label>
					<h3>Treść nr 6</h3>
					<label>
						Nagłówek
						<input type="text" name="tips2-h-6" value="<?php echo($setup -> tips2_h_6); ?>">
					</label>
					<label>
						Treść
						<textarea name="tips2-t-6" class="short wysiwyg"><?php echo($setup -> tips2_t_6); ?></textarea>
					</label>
					<h3>Treść nr 7</h3>
					<label>
						Nagłówek
						<input type="text" name="tips2-h-7" value="<?php echo($setup -> tips2_h_7); ?>">
					</label>
					<label>
						Treść
						<textarea name="tips2-t-7" class="short wysiwyg"><?php echo($setup -> tips2_t_7); ?></textarea>
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
			<section class="toggle">
				<h2><span class="ri-file-3-line icon"></span>O programie</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<label>
						Nagłówek
						<input type="text" name="about-h" value="<?php echo($setup -> about_h); ?>">
					</label>
					<label>
						Treść
						<textarea name="about-text" class="short wysiwyg"><?php echo($setup -> about_text); ?></textarea>
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
			<section class="toggle">
				<h2><span class="ri-file-3-line icon"></span>Jak to działa</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<h3>Blok nr 1</h3>
					<label>
						Nagłówek
						<input type="text" name="step-h-1" value="<?php echo($setup -> step_h_1); ?>">
					</label>
					<label>
						Treść
						<textarea name="step-1" class="short wysiwyg"><?php echo($setup -> step_1); ?></textarea>
					</label>
					<h3>Blok nr 2</h3>
					<label>
						Nagłówek
						<input type="text" name="step-h-2" value="<?php echo($setup -> step_h_2); ?>">
					</label>
					<label>
						Treść
						<textarea name="step-2" class="short wysiwyg"><?php echo($setup -> step_2); ?></textarea>
					</label>
					<h3>Blok nr 3</h3>
					<label>
						Nagłówek
						<input type="text" name="step-h-3" value="<?php echo($setup -> step_h_3); ?>">
					</label>
					<label>
						Treść
						<textarea name="step-3" class="short wysiwyg"><?php echo($setup -> step_3); ?></textarea>
					</label>
					<h3>Blok nr 4</h3>
					<label>
						Nagłówek
						<input type="text" name="step-h-4" value="<?php echo($setup -> step_h_4); ?>">
					</label>
					<label>
						Treść
						<textarea name="step-4" class="short wysiwyg"><?php echo($setup -> step_4); ?></textarea>
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
			<section class="toggle">
				<h2><span class="ri-file-3-line icon"></span>Gwiazdki</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<h3>Blok nr 1</h3>
					<label>
						Nagłówek
						<input type="text" name="star-h-1" value="<?php echo($setup -> star_h_1); ?>">
					</label>
					<label>
						Treść
						<textarea name="star-1" class="short wysiwyg"><?php echo($setup -> star_1); ?></textarea>
					</label>
					<h3>Blok nr 2</h3>
					<label>
						Nagłówek
						<input type="text" name="star-h-2" value="<?php echo($setup -> star_h_2); ?>">
					</label>
					<label>
						Treść
						<textarea name="star-2" class="short wysiwyg"><?php echo($setup -> star_2); ?></textarea>
					</label>
					<h3>Blok nr 3</h3>
					<label>
						Nagłówek
						<input type="text" name="star-h-3" value="<?php echo($setup -> star_h_3); ?>">
					</label>
					<label>
						Treść
						<textarea name="star-3" class="short wysiwyg"><?php echo($setup -> star_3); ?></textarea>
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
			<section class="toggle">
				<h2><span class="ri-file-3-line icon"></span>Korzyści z dołączenia</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<h3>Blok nr 1</h3>
					<label>
						Nagłówek
						<input type="text" name="benefit-h-1" value="<?php echo($setup -> benefit_h_1); ?>">
					</label>
					<label>
						Treść
						<textarea name="benefit-1" class="short wysiwyg"><?php echo($setup -> benefit_1); ?></textarea>
					</label>
					<h3>Blok nr 2</h3>
					<label>
						Nagłówek
						<input type="text" name="benefit-h-2" value="<?php echo($setup -> benefit_h_2); ?>">
					</label>
					<label>
						Treść
						<textarea name="benefit-2" class="short wysiwyg"><?php echo($setup -> benefit_2); ?></textarea>
					</label>
					<h3>Blok nr 3</h3>
					<label>
						Nagłówek
						<input type="text" name="benefit-h-3" value="<?php echo($setup -> benefit_h_3); ?>">
					</label>
					<label>
						Treść
						<textarea name="benefit-3" class="short wysiwyg"><?php echo($setup -> benefit_3); ?></textarea>
					</label>
					<h3>Blok nr 4</h3>
					<label>
						Nagłówek
						<input type="text" name="benefit-h-4" value="<?php echo($setup -> benefit_h_4); ?>">
					</label>
					<label>
						Treść
						<textarea name="benefit-4" class="short wysiwyg"><?php echo($setup -> benefit_4); ?></textarea>
					</label>
					<h3>Blok nr 5</h3>
					<label>
						Nagłówek
						<input type="text" name="benefit-h-5" value="<?php echo($setup -> benefit_h_5); ?>">
					</label>
					<label>
						Treść
						<textarea name="benefit-5" class="short wysiwyg"><?php echo($setup -> benefit_5); ?></textarea>
					</label>
					<h3>Blok nr 6</h3>
					<label>
						Nagłówek
						<input type="text" name="benefit-h-6" value="<?php echo($setup -> benefit_h_6); ?>">
					</label>
					<label>
						Treść
						<textarea name="benefit-6" class="short wysiwyg"><?php echo($setup -> benefit_6); ?></textarea>
					</label>
					<h3>Blok nr 7</h3>
					<label>
						Nagłówek
						<input type="text" name="benefit-h-7" value="<?php echo($setup -> benefit_h_7); ?>">
					</label>
					<label>
						Treść
						<textarea name="benefit-7" class="short wysiwyg"><?php echo($setup -> benefit_7); ?></textarea>
					</label>
					<h3>Blok nr 8</h3>
					<label>
						Nagłówek
						<input type="text" name="benefit-h-8" value="<?php echo($setup -> benefit_h_8); ?>">
					</label>
					<label>
						Treść
						<textarea name="benefit-8" class="short wysiwyg"><?php echo($setup -> benefit_8); ?></textarea>
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
			<section class="toggle">
				<h2><span class="ri-file-3-line icon"></span>Bądź godny polecenia</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<label>
						Nagłówek
						<input type="text" name="abc-h-1" value="<?php echo($setup -> abc_h_1); ?>">
					</label>
					<label>
						Treść
						<textarea name="abc-1" class="short wysiwyg"><?php echo($setup -> abc_1); ?></textarea>
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
			<section class="toggle">
				<h2><span class="ri-file-3-line icon"></span>Blok z procentami</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<h3>Blok nr 1</h3>
					<label>
						Nagłówek
						<input type="text" name="precent-h-1" value="<?php echo($setup -> precent_h_1); ?>">
					</label>
					<label>
						Treść
						<textarea name="precent-1" class="short wysiwyg"><?php echo($setup -> precent_1); ?></textarea>
					</label>
					<h3>Blok nr 2</h3>
					<label>
						Nagłówek
						<input type="text" name="precent-h-2" value="<?php echo($setup -> precent_h_2); ?>">
					</label>
					<label>
						Treść
						<textarea name="precent-2" class="short wysiwyg"><?php echo($setup -> precent_2); ?></textarea>
					</label>
					<h3>Blok nr 3</h3>
					<label>
						Nagłówek
						<input type="text" name="precent-h-3" value="<?php echo($setup -> precent_h_3); ?>">
					</label>
					<label>
						Treść
						<textarea name="precent-3" class="short wysiwyg"><?php echo($setup -> precent_3); ?></textarea>
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
			<section class="toggle">
				<h2><span class="ri-file-3-line icon"></span>Rekomentacje</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<h3>Blok nr 1</h3>
					<label>
						Nagłówek
						<input type="text" name="rec-h-1" value="<?php echo($setup -> rec_h_1); ?>">
					</label>
					<label>
						Treść
						<textarea name="rec-1" class="short wysiwyg"><?php echo($setup -> rec_1); ?></textarea>
					</label>
					<label>
						Nazwa firmy
						<input type="text" name="rec-n-1" value="<?php echo($setup -> rec_n_1); ?>">
					</label>
					<h3>Blok nr 2</h3>
					<label>
						Nagłówek
						<input type="text" name="rec-h-2" value="<?php echo($setup -> rec_h_2); ?>">
					</label>
					<label>
						Treść
						<textarea name="rec-2" class="short wysiwyg"><?php echo($setup -> rec_2); ?></textarea>
					</label>
					<label>
						Nazwa firmy
						<input type="text" name="rec-n-2" value="<?php echo($setup -> rec_n_2); ?>">
					</label>
					<h3>Blok nr 3</h3>
					<label>
						Nagłówek
						<input type="text" name="rec-h-3" value="<?php echo($setup -> rec_h_3); ?>">
					</label>
					<label>
						Treść
						<textarea name="rec-3" class="short wysiwyg"><?php echo($setup -> rec_3); ?></textarea>
					</label>
					<label>
						Nazwa firmy
						<input type="text" name="rec-n-3" value="<?php echo($setup -> rec_n_3); ?>">
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
			<section class="toggle">
				<h2><span class="ri-file-3-line icon"></span>Info</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<label>
						Nagłówek
						<input type="text" name="abc-h-2" value="<?php echo($setup -> abc_h_2); ?>">
					</label>
					<label>
						Treść
						<textarea name="abc-2" class="short wysiwyg"><?php echo($setup -> abc_2); ?></textarea>
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
			<section class="toggle">
				<h2><span class="ri-file-3-line icon"></span>Slider</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<label>
						Nagłówek
						<input type="text" name="abc-h-3" value="<?php echo($setup -> abc_h_3); ?>">
					</label>
					<label>
						Treść
						<textarea name="abc-3" class="short wysiwyg"><?php echo($setup -> abc_3); ?></textarea>
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
			<section class="toggle">
				<h2><span class="ri-file-3-line icon"></span>Cennik</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<label>
						Kod html
						<textarea name="pricelist"><?php echo($setup -> pricelist); ?></textarea>
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_ADMIN_TPL_FOOTER); ?>