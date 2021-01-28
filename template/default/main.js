$(document).ready(function() {
	
	/**
	 *	RWD
	 */
	var mobile = 0;
	
	$('body').each(function() {
		mobile = ((window.matchMedia('(min-width: 1025px)').matches) ? 0 : 1);
	});
	
	$('body').resize(function() {
		mobile = ((window.matchMedia('(min-width: 1025px)').matches) ? 0 : 1);
	});
	
	$('#mobile').each(function() {
		$('#menu').hide();
		$('#mobile-menu').click(function() {
			$('#menu').toggle();
			return(false);
		});
	});
	
	$(window).each(function() {
		if(mobile == 0) {
			var top = parseInt($(window).scrollTop());
			if(top > 100) {
				$('body#index #header').css('background', '#243244');
			} else {
				$('body#index #header').css('background', 'none');
			}
		}
	});
	
	$(window).scroll(function() {
		if(mobile == 0) {
			var top = parseInt($(window).scrollTop());
			if(top > 100) {
				$('body#index #header').css('background', '#243244');
			} else {
				$('body#index #header').css('background', 'none');
			}
		}
		
		if($('#newsletter').length) {
			if(parseInt($(window).scrollTop()) >= (parseInt($('#newsletter').position().top)-parseInt($('#big-map').height()))-130) {
				$('#big-map').hide();
			} else {
				$('#big-map').show();
			}
		}
	});	
	
	$('#menu-toggle').click(function() {
		if(mobile == 1) {
			$('#menu').toggle();
			$('#content').toggleClass('dark');
		} else {
			$(this).hide();
			$('#header').removeClass('index');
			state = 1;
			$('#menu').show();
		}
		return(false);
	});
	
	$('#left-toggle').click(function() {
		$('#left-container').show();
		$(this).remove();
		return(false);
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
	 *	Cookie window
	 */
	$('#cookie-window-container').each(function() {
		$(this).load('cookie-window');
	});
	$('#cookie-window-container').on('click', '.window-close', function() {
		$('#cookie-window-container').html('');
		return(false);
	});
	
	/**
	 *	Dropzone
	 */
	$('#dropzone').each(function() {
		$('#dropzone').dropzone({url: $(this).attr('action'), dictDefaultMessage: '<span style="text-decoration: underline;">Kliknij tutaj</span> lub przeciągnij pliki'});
		$('#dropzone label, #dropzone .buttons').hide();
	});
	
	/**
	 *	Item
	 */
	$('.item-click').click(function() {
		window.location.href = $(this).attr('data-url');
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
	
	/**
	 *	Pay form
	 */
	$('#pay-form').each(function() {
		$(this).children('form').submit();
	});
	
	/**
	 *	Message box
	 */
	$('#message-container').each(function() {
		$(this).hide();
	});
	$('#message-toggle').click(function() {
		$('#message-container').toggle();
		return(false);
	});
	
	/**
	 *	Message list
	 */
	$('#message-list').each(function() {
		$('#message-list').scrollTop($('#message-list')[0].scrollHeight);
		
		setInterval(function() {
			$.ajax({
				type: 'POST',
				url: $('#message-list').attr('data-url'),
				success: function(html) {
					$('#message-list').html(html);
					$('#message-list').scrollTop($('#message-list')[0].scrollHeight);
				}
			});
		}, 5000);
	});
	
	/**
	 *	Slider item
	 */
	$('.item-slider').each(function() {
		var move = parseInt($(this).attr('data-move'));
		var mobileMove = parseInt($(this).attr('data-mobile'));
		if(!(mobileMove > 0)) mobileMove = 1;

		$(this).lightSlider({
			item: ((mobile == 1) ? mobileMove : move),
			loop: false,
			slideMove: ((mobile == 1) ? mobileMove : move),
			slideMargin: 20,
			controls: ((mobile == 1) ? false : true),
			enableDrag: true,
			pager: ((mobile == 1) ? true : false)
		});
	});
	
	/**
	 *	Phone show
	 */
	$('#phone-toggle').click(function() {
		if(mobile == 1) {
			$(this).attr('href', 'tel:'+$(this).attr('data-p1')+$(this).attr('data-p2')+$(this).attr('data-p3')+$(this).attr('data-p4'));
		} else {
			$('#phone-number').text($(this).attr('data-p1')+' '+$(this).attr('data-p2')+'-'+$(this).attr('data-p3')+'-'+$(this).attr('data-p4'));
			return(false);
		}
	});
	
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
	//$('#feature-search').change(function() {
	//	$('#feature-container').load($('#feature-container').attr('data-url')+'/'+$(this).val());
	//});
	
	$('#feature-search').change(function() {
		$('#sub-category-container').html('').load($('#sub-category-container').attr('data-url')+'/'+$(this).val());
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
	 *
	 */
	$('#search-more').each(function() {
		$(this).hide();
	});
	$('#search-more-toggle').click(function() {
		$('#search-more').show();
		$(this).remove();
		return(false);
	});
	
	/**
	 *
	 */
	$('.vote-click').click(function() {
		$('#'+$(this).attr('data-id')).val($(this).attr('data-value'));
		$('.vote-click[data-id="'+$(this).attr('data-id')+'"]').children('span').attr('class', 'ri-star-line');
		
		var number = parseInt($(this).attr('data-value'));
		if(number > 0) $('.vote-click[data-id="'+$(this).attr('data-id')+'"][data-value="1"]').children('span').attr('class', 'ri-star-fill');
		if(number > 1) $('.vote-click[data-id="'+$(this).attr('data-id')+'"][data-value="2"]').children('span').attr('class', 'ri-star-fill');
		if(number > 2) $('.vote-click[data-id="'+$(this).attr('data-id')+'"][data-value="3"]').children('span').attr('class', 'ri-star-fill');
		if(number > 3) $('.vote-click[data-id="'+$(this).attr('data-id')+'"][data-value="4"]').children('span').attr('class', 'ri-star-fill');
		if(number > 4) $('.vote-click[data-id="'+$(this).attr('data-id')+'"][data-value="5"]').children('span').attr('class', 'ri-star-fill');
		
		return(false);
	});
	
	/**
	 *
	 */
	$('#lat-lng').each(function() {
		if($(this).attr('data-lat') == '' || $(this).attr('data-lng') == '') {
			if(navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(function(position) {
					
					var lat = position.coords.latitude.toString();
					$(this).attr('data-lat', lat);
					lat = lat.replace('.', '-');
					
					var lng = position.coords.longitude.toString();
					$(this).attr('data-lng', lng);
					lng = lng.replace('.', '-');
					//alert('https://godny-polecenia.pl/index/geo/'+lat+'/'+lng);
					$.get('https://godny-polecenia.pl/index/geo/'+lat+'/'+lng);
				});
			}
		}
	});
	
	/**
	 *
	 */
	$('.vote-reply-form').each(function() {
		$(this).hide();
	});
	
	$('.vote-reply-toggle').click(function() {
		$(this).parent('div').children('form').show();
		$(this).remove();
		return(false);
	});
	
	/**
	 *
	 */
	$('.li-phone a.hide-phone').click(function() {
		$(this).text($(this).attr('href').replace('+48', '').replace('tel:', '')).removeClass('hide-phone');
		$.get($(this).attr('data-counter'));
		return(false);
	});
	
	/**
	 *
	 */
	$('#li-hide-toggle').click(function() {
		$('.li-hide').removeClass('li-hide');
		$(this).remove();
		return(false);
	});
	
	/**
	 *
	 */
	$('#vote-hide-toggle').click(function() {
		$('.vote-hide').removeClass('vote-hide');
		$(this).remove();
		return(false);
	});
	
	/**
	 *
	 */
	$('#service-hide-toggle').click(function() {
		$('.service-hide').removeClass('service-hide');
		$(this).remove();
		return(false);
	});
	
	/**
	 *
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