<?php
	$toggle_on  = __( 'Show' );
	$toggle_off = __( 'Hide' );
		$class = empty( $errors ) ? 'startclosed' : 'startopen';

	if ( !apply_filters( 'disable_captions', '' ) ) {
		$caption = '
		<tr>
			<th valign="top" scope="row" class="label">
				<span class="alignleft"><label for="caption">' . __('Image Caption') . '</label></span>
			</th>
			<td class="field"><input id="caption" name="caption" value="" type="text" /></td>
		</tr>
';
	} else {
		$caption = '';
	}

?>
<script><!--
jQuery(document).ready(function($){

	var mediaitems = $('.media-items');
	$('a.toggle').click(function(e) {
		$('a.toggle[href='+$(this).attr('href')+']:hidden').toggle();
		$(this).toggle();
		$('table.slidetoggle',$(this).attr('href')).slideToggle();

		addExtImage.getImageData($(this).attr('href'));

		return false;
	});

	$(document).on('click', 'button.url-src', function() {
		var id = $(this).attr('id');
		id = id.replace('url-src-', '');
		$('#url-'+id).val(jQuery('#src-'+id).val());
		return false;
	});

	$(document).on('click', 'button.url-none', function() {
		var id = $(this).attr('id');
		id = id.replace('url-none-', '');
		$('#url-'+id).val('');
		return false;
	});

	$(document).on('click', 'button.url-product', function() {
		var id = $(this).attr('id');
		id = id.replace('url-product-', '');
		<?php
		if(!isset($this->options['seofriendly'])) {
			echo 'var link = "'.$this->storepath.'/ProductDetails.asp?ProductCode="+id;';
		} else {
			echo "var idLower = (id+'').toLowerCase();\n\t\t";
			echo 'var link = "'.$this->storepath.'/product_p/"+idLower+".htm";'."\n";
		}
		?>
		$('#url-'+id).val(link);
		return false;
	});
});

var addExtImage = {

		width : '',
		height : '',
		align : 'alignnone',

		insert : function(that) {
			var t = this, html, f = jQuery(that).parents('div.media-item'), cls, title = '', alt = '', caption = '';

			if ( '' == jQuery('input[name="src"]', f).val() || '' == t.width )
				return false;

			if ( jQuery('input[name="title"]', f).val() ) {
				title = jQuery('input[name="title"]', f).val().replace(/'/g, '&#039;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
				title = ' title="'+title+'"';
			}

			if ( jQuery('input[name="alt"]', f).val() )
				alt = jQuery('input[name="alt"]', f).val().replace(/'/g, '&#039;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

	<?php if ( ! apply_filters( 'disable_captions', '' ) ) { ?>
			if ( jQuery('input[name="caption"]', f).val() )
				caption = jQuery('input[name="caption"]', f).val().replace(/'/g, '&#039;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
	<?php } ?>

			cls = caption ? '' : ' class="'+t.align+'"';

			html = '<img alt="'+alt+'" src="'+jQuery('input[name="src"]', f).val()+'"'+title+cls+' width="'+t.width+'" height="'+t.height+'" />';

			if ( jQuery('input[name="url"]', f).val() )
				html = '<a href="'+jQuery('input[name="url"]', f).val()+'">'+html+'</a>';

			if ( caption )
				html = '[caption id="" align="'+t.align+'" width="'+t.width+'" caption="'+caption+'"]'+html+'[/caption]';

			var win = window.dialogArguments || opener || parent || top;
			win.send_to_editor(html);
			return false;
		},

		resetImageData : function() {
			var t = addExtImage;

			t.width = t.height = '';
			jQuery('input[id*=go_button]', that).css('color','#bbb');
			if ( jQuery('input[name="src"]', that).val() == '' )
				jQuery('.status_img', t.formEl).html('*');
			else jQuery('.status_img', t.formEl).html('<img src="<?php echo esc_url( admin_url( 'images/no.png' ) ); ?>" alt="" />');
		},

		updateImageData : function() {
			var t = addExtImage;
			t.width = t.preloadImg.width;
			t.height = t.preloadImg.height;
			jQuery('input[id*=go_button]', t.formEl).css('color','#333');
			jQuery('.status_img', t.formEl).html('<img src="<?php echo esc_url( admin_url( 'images/yes.png' ) ); ?>" alt="" />');
		},

		getImageData : function(that) {
			var t = addExtImage, src = jQuery('input[name="src"]', that).val();

			t.formEl = jQuery(that);

			if ( ! src ) {
				t.resetImageData;
				return false;
			}
			jQuery('.status_img', t.formEl).html('<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />');
			t.preloadImg = new Image();
			t.preloadImg.onload = t.updateImageData;
			t.preloadImg.onerror = t.resetImageData;
			t.preloadImg.src = src;
		}
	}
-->
</script>
<div class="tablenav">

<?php
$_GET['paged'] = isset( $_GET['paged'] ) ? intval($_GET['paged']) : 0;
	if ( $_GET['paged'] < 1 )
		$_GET['paged'] = 1;
	$start = ( $_GET['paged'] - 1 ) * 10;
	if ( $start < 1 )
		$start = 0;

$page_links = paginate_links( array(
	'base' => add_query_arg( 'paged', '%#%' ),
	'format' => '',
	'prev_text' => __('&laquo;'),
	'next_text' => __('&raquo;'),
	'total' => ceil(sizeof($Products) / 10),
	'current' => $_GET['paged']
));

	echo "<form id='filter'>";

if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links</div>";

	echo "</form>";

	echo '<div class="alignleft actions">';

	$default_align = get_option('image_default_align');
	if ( empty($default_align) )
		$default_align = 'none';

		echo '<form enctype="multipart/form-data" method="post" action="http://local/dev/devwordpress/wp-admin/media-upload.php?type=image&amp;tab=volusion&amp;post_id=1384" class="media-upload-form validate" id="library-form">
		<div id="media-items">
		';

		$productList = $Products;
		$i = -1;
		foreach ($productList as $key => $product ) {
			$i++;
			if($i < $start || $i > ($start + 9)) { continue; }
			extract((array)$product);

			echo '
			<div id="media-item-'.$ProductCode.'" class="media-item preloaded">
                '."<div class='alignright'><a class='toggle describe-toggle-on' href='#media-item-$ProductCode'>$toggle_on</a>
    <a class='toggle describe-toggle-off' href='#media-item-$ProductCode'>$toggle_off</a></div>".'
				<div style="width:40px; float:left;"><img src="'.$this->storepath.'/v/vspfiles/photos/'.$ProductCode.'-2S.jpg" class="pinkynail toggle" /></div>
				<div class="filename new">
					<span class="title">'.$ProductName.'</span>
				</div>
	<table class="slidetoggle describe '.$class.'">
		<thead class="media-item-info" id="media-head-$post->ID">
		<tbody>
		<tr>
			<th valign="top" scope="row" class="label" style="width:130px;">
				<span class="alignleft"><label for="src-'.$ProductCode.'">' . __('Image URL') . '</label></span>
				<span class="alignright"><abbr title="required" class="status_img required">*</abbr></span>
			</th>
			<td class="field"><input id="src-'.$ProductCode.'" name="src" value="'.$this->storepath.'/v/vspfiles/photos/'.$ProductCode.'-2T.jpg" type="text" aria-required="true" /></td>
		</tr>

		<tr>
			<th valign="top" scope="row" class="label">
				<span class="alignleft"><label for="title-'.$ProductCode.'">' . __('Image Title') . '</label></span>
				<span class="alignright"><abbr title="required" class="required">*</abbr></span>
			</th>
			<td class="field"><input id="title-'.$ProductCode.'" name="title" value="'.$ProductName.'" type="text" aria-required="true" /></td>
		</tr>

		<tr>
			<th valign="top" scope="row" class="label">
				<span class="alignleft"><label for="alt-'.$ProductCode.'">' . __('Alternate Text') . '</label></span>
			</th>
			<td class="field"><input id="alt-'.$ProductCode.'" name="alt" value="'.$ProductName.'" type="text" aria-required="true" />
			<p class="help">' . __('Alt text for the image, e.g. &#8220;The Mona Lisa&#8221;') . '</p></td>
		</tr>
		' . $caption . '
		<tr class="align">
			<th valign="top" scope="row" class="label"><p><label for="align-'.$ProductCode.'">' . __('Alignment') . '</label></p></th>
			<td class="field">
				<input name="align" id="align-none-'.$ProductCode.'" value="none" onclick="addExtImage.align=\'align\'+this.value" type="radio"' . ($default_align == 'none' ? ' checked="checked"' : '').' />
				<label for="align-none-'.$ProductCode.'" class="align image-align-none-label">' . __('None') . '</label>
				<input name="align" id="align-left-'.$ProductCode.'" value="left" onclick="addExtImage.align=\'align\'+this.value" type="radio"' . ($default_align == 'left' ? ' checked="checked"' : '').' />
				<label for="align-left-'.$ProductCode.'" class="align image-align-left-label">' . __('Left') . '</label>
				<input name="align" id="align-center-'.$ProductCode.'" value="center" onclick="addExtImage.align=\'align\'+this.value" type="radio"' . ($default_align == 'center' ? ' checked="checked"' : '').' />
				<label for="align-center-'.$ProductCode.'" class="align image-align-center-label">' . __('Center') . '</label>
				<input name="align" id="align-right-'.$ProductCode.'" value="right" onclick="addExtImage.align=\'align\'+this.value" type="radio"' . ($default_align == 'right' ? ' checked="checked"' : '').' />
				<label for="align-right-'.$ProductCode.'" class="align image-align-right-label">' . __('Right') . '</label>
			</td>
		</tr>

		<tr>
			<th valign="top" scope="row" class="label">
				<span class="alignleft"><label for="url-'.$ProductCode.'">' . __('Link Image To:') . '</label></span>
			</th>
			<td class="field"><input id="url-'.$ProductCode.'" name="url" value="" type="text" /><br />

			<button type="button" id="url-none-'.$ProductCode.'" class="button url-none" value="">' . __('None') . '</button>
			<button type="button" id="url-product-'.$ProductCode.'" class="button url-product" value="">' . __('Link to product') . '</button>
			<button type="button" id="url-src-'.$ProductCode.'" class="button url-src" value="">' . __('Link to image') . '</button>
			<p class="help">' . __('Enter a link URL or click above for presets.') . '</p></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="button" class="button" id="go_button-'.$ProductCode.'" style="color:#bbb;" onclick="addExtImage.insert(this)" value="' . esc_attr__('Insert into Post') . '" />
			</td>
		</tr>
	</tbody></table>
			</div>

			';
		}
		echo '</div></form></div></div>';