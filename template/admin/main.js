$(document).ready(function() {
	
	/**
	 *	RWD
	 */
	var mobile = 0;
	$('body').each(function() {
		mobile = ((window.matchMedia('(min-width: 1025px)').matches) ? 0 : 1);
	});
	
	if(mobile == 1) {
		$('#left').each(function() {
			$(this).hide();
		});
		$('#menu-toggle').click(function() {
			$('#left').toggle();
			$('#right').toggle();
			return(false);
		});
	}
	
	/**
	 *	Dropzone
	 */
	$('#dropzone').each(function() {
		$('#dropzone').dropzone({url: $(this).attr('action'), dictDefaultMessage: '<span style="text-decoration: underline;">Kliknij tutaj</span> lub przeciągnij pliki'});
		$('#dropzone label, #dropzone .buttons').hide();
	});
	
	/**
	 *	Required input
	 */
	$('label *:required[required]').each(function() {
		if($(this).attr('type') == 'checkbox' || $(this).attr('type') == 'radio') {
			$(this).parent('label').append('<span class="red">*</span>');
		} else {
			if($.trim($(this).parent('label').clone().children().remove().end().text()).length > 0) {
				$(this).before('<span class="red">*</span>');
			}
		}
	});
	
	/**
	 *	Section toggle
	 */
	$('section.toggle').each(function() {
		$(this).append('<div class="toggle-button ri-arrow-down-circle-line icon"></div>');
	});
	$('section.toggle').on('click', function() {
		$(this).removeClass('toggle');
		$(this).children('.toggle-button').removeClass('ri-arrow-down-circle-line').addClass('ri-arrow-up-circle-line');
	});
	$(document).on('click', 'section:not(.toggle) .toggle-button', function() {
		$(this).parent('section').addClass('toggle');
		$(this).removeClass('ri-arrow-up-circle-line').addClass('ri-arrow-down-circle-line');
	});
	$('.hide-container').each(function() {
		$(this).hide();
	});
	$('.show-container').click(function() {
		$('#'+$(this).attr('id')+'-container').show();
		$(this).remove();
		return(false);
	});
	
	/**
	 *	Search section
	 */
	$('.open-section-container:not(.open-section-container-show)').each(function() {
		$(this).hide();
	});
	$('.open-section').click(function() {
		$('#'+$(this).attr('id').replace('-open', '-container')).toggle();
		return(false);
	});
	
	/**
	 *	Wysiwyg editor
	 */
	$('.wysiwyg').each(function() {
		$(this).parent('label').after('<div class="label">'+$(this).parent('label').html()+'</div>');
		$(this).parent('label').remove();
	});

	$('.wysiwyg').trumbowyg({
		lang: 'pl',
		btns: [
			['viewHTML'],
			['undo', 'redo'],
			['formatting'],
			['strong', 'em', 'del'],
			['superscript', 'subscript'],
			['link'],
			['insertImage'],
			['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
			['unorderedList', 'orderedList'],
			['horizontalRule'],
			['removeformat'],
			['fullscreen']
		],
		autogrow: true
	});
	
	/**
	 *	Datepicker
	 */
	$('.datepicker').datepicker({dateFormat: 'dd.mm.yy'});

	$.datepicker.regional['pl'] = {clearText: 'Effacer', clearStatus: '',
		closeText: 'sluiten', closeStatus: 'Onveranderd sluiten ',
		prevText: '<poprzedni', prevStatus: 'Zobacz poprzedni miesiąc',
		nextText: 'następny>', nextStatus: 'Zobacz w następny miesiąc',
		currentText: 'Miesiąc', currentStatus: 'Zobacz kolejny miesiąc',
		monthNames: ['styczeń','luty','marzec','kwiecień','maj','czerwiec',
		'lipiec','sierpień','wrzesień','październik','listopad','grudzień'],
		monthNamesShort: ['sty','lut','mar','kwi','maj','cze',
		'lip','sie','wrz','paź','lis','gru'],
		monthStatus: 'Zobacz kolejny miesiąc', yearStatus: 'Zobacz kolejny rok',
		weekHeader: 'Sm', weekStatus: '',
		dayNames: ['niedziela','poniedziałek','wtorek','środa','czwartek','piątek','sobota'],
		dayNamesShort: ['nd', 'pn','wt','śr','cz','pt','sb'],
		dayNamesMin: ['nd', 'pn','wt','śr','cz','pt','sb'],
		dayStatus: 'Użyj DD jako pierwszy dzień tygodnia', dateStatus: 'Wybierz DD, MM d',
		dateFormat: 'dd.mm.yy', firstDay: 1, 
		initStatus: 'Wybierz datę', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['pl']);
	
	/**
	 *	Item add - features
	 */
	$('#edit-item').each(function() {
		if(parseInt($('#category-select').val()) > 0)  {
			$.ajax({
				type: 'POST',
				url: $('#feature-container').attr('data-url')+'/'+$('#category-select').val(),
				data: $('#edit-item').serialize(),
				success: function(html) {
					$('#feature-container').html(html);
				}
			});
		}
	});
	$('#category-select').change(function() {
		$('#feature-container').html('').load($('#feature-container').attr('data-url')+'/'+$(this).val());
	});
	
	/**
	 *	Item search - features
	 */
	$('#feature-search').change(function() {
		$('#feature-container').load($('#feature-container').attr('data-url')+'/'+$(this).val());
	});
	$('#search-item').each(function() {
		if($('#feature-container').attr('data-url') != '') {
			if(parseInt($('#feature-search').val()) > 0)  {
				$.ajax({
					type: 'POST',
					url: $('#feature-container').attr('data-url')+'/'+$('#feature-search').val(),
					data: $('#search-item').serialize(),
					success: function(html) {
						$('#feature-container').html(html);
					}
				});
			}
		}
	});
	
	/**
	 *	Recaptcha
	 */
	grecaptcha.ready(function() {
		$('form.recaptcha').each(function() {
			var form = $(this);
			grecaptcha.execute($('#recaptcha-script').attr('data-value'), {action: 'homepage'}).then(function(token) {
				$(form).prepend('<input type="hidden" name="g-recaptcha-response" value="'+token+'">');
			});
		});
	});
	
	/**
	 *
	 */
	$('.cat-hide').each(function() {
		$(this).hide();
	});
	
	$('.cat-head').change(function() {
		if($(this).is(':checked')) {
			$('.'+$(this).attr('id')).show();
		} else {
			$('.'+$(this).attr('id')).hide();
			$('.'+$(this).attr('id')).children('input').prop('checked', false);
		}
	});
	
	$('.cat-head').each(function() {
		if($(this).is(':checked')) {
			$('.'+$(this).attr('id')).show();
		} else {
			$('.'+$(this).attr('id')).hide();
			$('.'+$(this).attr('id')).children('input').prop('checked', false);
		}
	});
	
	/**
	 *
	 */
	$('.service-edit').each(function() {
		$(this).hide()
	});
	
	$('.edit-service-toggle').click(function() {
		$('#edit-service-'+$(this).attr('data-value')).toggle();
		return(false);
	});
});