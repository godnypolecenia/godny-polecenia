<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This file displays the features when adding
 */

$featureList = new Feature;


if($url -> op(0) == 'sub' && $url -> op(1) > 0) {
	$catList = new Category;
	if($catList -> countCategoryList($url -> op(1)) > 0) {
		foreach($catList -> getCategoryList($url -> op(1)) as $r) {
			echo('<label>');
			echo('<input type="checkbox" name="sub-cat[]" value="'.$r['category_id'].'" checked="checked">');
			echo($r['name']);
			echo('</label>');
		}
	}	
}

/**
 *	The form is visible when adding or editing
 */
if($url -> op(0) == URL_ADD || $url -> op(0) == URL_EDIT) {
	
	$countFeatureList = $featureList -> countFeatureListOfCategory($url -> op(1));
	if($countFeatureList > 0) {
		
		$colsType = 1;
		if($countFeatureList >= 2) $colsType = 2;
		if($countFeatureList >= 3) $colsType = 3;
		
		if($countFeatureList > 1 && $countFeatureList % 2 == 0) $colsType = 2;
		if($countFeatureList > 1 && $countFeatureList % 3 == 0) $colsType = 3;
		
		echo('<div></div>'); /* not first css */
		echo('<h2>Cechy</h2>');
		echo('<div class="cols cols-'.$colsType.'">');
		foreach($featureList -> getFeatureListOfCategory($url -> op(1)) as $r) {
			if($r['type'] == 1) {
				echo('<label>');
				echo($r['label']);
				echo('<input type="text" name="feature['.$r['feature_id'].']" value="'.$_POST['feature'][$r['feature_id']].'"'.(($r['required'] == 1) ? ' required="required"' : '').(($r['placeholder'] <> '') ? ' placeholder="'.$r['placeholder'].'"' : '').'>');
				echo('</label>');
			}
			if($r['type'] == 2) {
				echo('<label>');
				echo($r['label']);
				echo('<input type="number" name="feature['.$r['feature_id'].']" value="'.$_POST['feature'][$r['feature_id']].'"'.(($r['required'] == 1) ? ' required="required"' : '').(($r['placeholder'] <> '') ? ' placeholder="'.$r['placeholder'].'"' : '').'>');
				echo('</label>');
			}
			if($r['type'] == 3) {
				echo('<label>');
				echo($r['label']);
				$ex = explode(';', $r['value']);
				echo('<select name="feature['.$r['feature_id'].']"'.(($r['required'] == 1) ? ' required="required"' : '').'>');
				echo('<option value="">Wybierz</option>');
				foreach($ex as $v) {
					echo('<option'.(($_POST['feature'][$r['feature_id']] == $v) ? ' selected="selected"' : '').'>'.trim($v).'</option>');
				}
				echo('</select>');
				echo('</label>');
			}
			if($r['type'] == 4) {
				echo('<input type="hidden" name="feature['.$r['feature_id'].']" value="0">');
				echo('<label>');
				echo('<input type="checkbox" name="feature['.$r['feature_id'].']" value="1"'.(($r['required'] == 1) ? ' required="required"' : '').(($_POST['feature'][$r['feature_id']] == 1) ? ' checked="checked"' : '').'>');
				echo($r['label']);
				echo('</label>');
			}
			echo('</label>');
			echo("\n");
		}
		echo('</div>');
	}
}

/**
 *	Form visible during search
 */
if($url -> op(0) == URL_SEARCH) {
	if($featureList -> countFeatureListOfCategory($url -> op(1)) > 0) {
		echo('<section>');
		echo('<h2>Cechy</h2>');
		foreach($featureList -> getFeatureListOfCategory($url -> op(1)) as $r) {
			if($r['type'] == 1) {
				echo('<label>');
				echo($r['label']);
				echo('<input type="text" name="feature['.$r['feature_id'].']" value="'.$_POST['feature'][$r['feature_id']].'"'.(($r['placeholder'] <> '') ? ' placeholder="'.$r['placeholder'].'"' : '').'>');
				echo('</label>');
			}
			if($r['type'] == 2) {
				$exPost = explode(',', $_POST['feature'][$r['feature_id']]);
				$_POST['feature'][$r['feature_id']] = ['from' => $exPost[0], 'to' => $exPost[1]];

				echo('<div class="cols cols-2">');
				echo('<label>');
				echo($r['label'].' od');
				echo('<input type="number" name="feature['.$r['feature_id'].'][from]" value="'.$_POST['feature'][$r['feature_id']]['from'].'"'.(($r['placeholder'] <> '') ? ' placeholder="'.$r['placeholder'].'"' : '').'>');
				echo('</label>');
				echo('<label>');
				echo('do');
				echo('<input type="number" name="feature['.$r['feature_id'].'][to]" value="'.$_POST['feature'][$r['feature_id']]['to'].'"'.(($r['placeholder'] <> '') ? ' placeholder="'.$r['placeholder'].'"' : '').'>');
				echo('</label>');
				echo('</div>');
			}
			if($r['type'] == 3) {
				$exPost = explode(',', $_POST['feature'][$r['feature_id']]);
				
				echo('<div class="select-checkbox">');
				echo($r['label']);
				$ex = explode(';', $r['value']);
				echo('<div class="select-checkbox-list">');
				foreach($ex as $k => $v) {
					echo('<label>');
					echo('<input type="checkbox" name="feature['.$r['feature_id'].']['.$k.']" value="'.$v.'"'.((in_array($v, $exPost)) ? ' checked="checked"' : '').'>');
					echo($v);
					echo('</label>');
				}
				echo('</div>');
				echo('</div>');
			}
			if($r['type'] == 4) {
				echo('<label>');
				echo('<input type="checkbox" name="feature['.$r['feature_id'].']" value="1"'.(($_POST['feature'][$r['feature_id']] == 1) ? ' checked="checked"' : '').'>');
				echo($r['label']);
				echo('</label>');
			}
			echo("\n");
		}
		echo('<div class="buttons">');
		echo('<input type="submit" value="Szukaj">');
		echo('</div>');
		echo('</section>');
	}
}

?>