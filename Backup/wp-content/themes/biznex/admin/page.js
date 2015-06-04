(function($){

	$(function(){

		var show_template_section = function(e){

			var t = $(e);

			var section = t.parent('label').parent('p').next('div.tesla_template_section');

			if(t.prop('checked'))
				section.addClass('tesla_template_section_disabled');
			else
				section.removeClass('tesla_template_section_disabled');

		}

		var show_template_options = function(){

			$('#biznex_metabox_template_options [data-template]').hide();

			$('#biznex_metabox_template_options [data-template] [name]').prop('disabled', true);

			$('#biznex_metabox_template_options [data-template="'+$('#page_template').val()+'"]').show();

			$('#biznex_metabox_template_options [data-template="'+$('#page_template').val()+'"] [name]').prop('disabled', false);

			$('.tesla_template_disable_section').each(function(i, e){

				show_template_section(e);

			});

		};

		$('body').on('change','#page_template',show_template_options);

		$('body').on('click','.tesla_template_meta_image label',function(ev){

			if(ev.preventDefault)
				ev.preventDefault();
			
			return false;

		});

		var open_media_frame = function(button, image_input){

			var media = button.data('media');

			if(!media){

				media = wp.media();

				media.on('select', function(){

					var url = media.state().get('selection').first().toJSON().url;

					image_input.val(url);

					if(button.siblings('img').length)

						button.siblings('img').attr('src', url);

					else

						button.before($('<img>').attr('src', url));

				});

				button.data('media', media);

			}

			media.open();

		};

		$('body').on('click','.tesla_template_meta_image img',function(ev){

			var t_image = $(this);

			var t = t_image.siblings('button');

			var t_parent = t.closest('.tesla_template_meta_image');

			var t_input = t_parent.find('input[type="hidden"]');

			open_media_frame(t, t_input);

			if(ev.preventDefault)
				ev.preventDefault();
			
			return false;

		});

		$('body').on('click','.tesla_template_meta_image button',function(ev){

			var t = $(this);

			var t_parent = t.closest('.tesla_template_meta_image');

			var t_input = t_parent.find('input[type="hidden"]');

			if(''===t_input.val()){

				t.text('Remove image');
				open_media_frame(t, t_input);

			}else{

				t.text('Set image');
				t.siblings('img').remove();
				t_input.val('');

			}

			if(ev.preventDefault)
				ev.preventDefault();
			
			return false;

		});

		$('body').on('change','.tesla_template_disable_section',function(ev){

			show_template_section(this);

		})

		show_template_options();

	});

})(jQuery);