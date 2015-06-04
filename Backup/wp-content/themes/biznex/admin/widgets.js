(function($){

	"use strict";

	$(function(){

		// Zoomy Widget Banner

		$('body').on('click', '.novelty_widget_banner input[type="button"]:even', function(){

			var button = $(this);

			var image_input = button.parent('label').siblings('input[type="hidden"]');

			var media = button.data('media');

			if(!media){

				media = wp.media();

				media.on('select', function(){

					var url = media.state().get('selection').first().toJSON().url;

					image_input.val(url);

					if(button.siblings('img').length)

						button.siblings('img').attr('src', url);

					else

						button.parent('label').prepend($('<img>').attr('src', url));

					button.val('Change image');

					button.parent('label').siblings('input[type="button"]').removeAttr('style');

				});

				button.data('media', media);

			}

			media.open();

		});

		$('body').on('click', '.novelty_widget_banner input[type="button"]:odd', function(){

			var button = $(this);

			button.css({display: 'none'});

			button.prev('label').children('img').remove();

			button.prev('label').children('input[type="button"]').val('Set image');

		});



		// Zoomy Widget Tabs

		$('body').on('click', '.novelty_widget_tabs>p>input[type="button"]:even', function(){

			var base = $(this).closest('.novelty_widget_tabs');

			var current = $(this).closest('p');

			var clone = base.children('p:last').clone();

			clone.find('select').prop('disabled', false);

			current.after(clone);

		});

		$('body').on('click', '.novelty_widget_tabs>p>input[type="button"]:odd', function(){

			var base = $(this).closest('.novelty_widget_tabs');

			if(base.children('p').length>2)

				$(this).closest('p').remove();

			else

				$(this).closest('p').find('select').val('');

		});

	});

})(jQuery);