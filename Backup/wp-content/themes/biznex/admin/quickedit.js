(function($){

	$(function(){

		$('#the-list').on('click', 'a.editinline', function(){

			var meta = $.parseJSON($(this).closest('.row-actions').find('.novelty_category_options>span[data-option]').attr('data-option'));

			$('#the-list tr.inline-edit-row>td>fieldset:last').after(novelty_category_options.html);

			$('#the-list .inline-edit-row [name="tt_tax_input[sidebar]"]').prop('checked', 1 === parseInt(meta.sidebar));

			$('#the-list .inline-edit-row [name="tt_tax_input[layout]"]:eq(0)').prop('checked', 0 === parseInt(meta.layout));
			$('#the-list .inline-edit-row [name="tt_tax_input[layout]"]:eq(1)').prop('checked', 1 === parseInt(meta.layout));

			$('#the-list .inline-edit-row [name="tt_tax_input[columns]"]').val(meta.columns);

			$('#the-list .inline-edit-row [name="tt_tax_input[masonry]"]').prop('checked', 1 === parseInt(meta.masonry));

			$('#the-list .inline-edit-row [name="tt_tax_input[twitter_widget]"]').prop('checked', 1 === parseInt(meta.twitter_widget));

			$('#the-list .inline-edit-row [name="tt_tax_input[twitter_user]"]').val(meta.twitter_user);

			$('#the-list .inline-edit-row [name="tt_tax_input[twitter_nr]"]').val(meta.twitter_nr);

			$('#the-list .inline-edit-row [name="tt_tax_input[excerpt_length]"]').val(meta.excerpt_length);

			$('#the-list .inline-edit-row [name="tt_tax_input[news_ticker_title]"]').val(meta.news_ticker_title);

			$('#the-list .inline-edit-row [name="tt_tax_input[news_ticker_nr]"]').val(meta.news_ticker_nr);

			$('#the-list .inline-edit-row [name="tt_tax_input[news_ticker_category]"]').val(meta.news_ticker_category);

			$('#the-list .inline-edit-row [name="tt_tax_input[news_ticker_widget]"]').prop('checked', 1 === parseInt(meta.news_ticker_widget));

			return false;

		});

	});

})(jQuery);