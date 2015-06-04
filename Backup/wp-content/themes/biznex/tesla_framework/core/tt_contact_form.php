<?php
/*
	Class to render the contact form
 */
class TT_Contact_Form_Builder {

	public static $args;
	private static $TTL ;

	public static function init_builder(){
		self::$TTL = new TT_LOAD;
		self::$args['form_elements'] = file_exists(TT_STYLE_DIR . '/theme_config/contact-form-config.php')?  include TT_STYLE_DIR . '/theme_config/contact-form-config.php' : include TT_THEME_DIR . '/theme_config/contact-form-config.php';

		add_action( "admin_menu", array( 'TT_Contact_Form_Builder', "setup_contact_admin_submenu"),11 );
		add_action('admin_init',array('TT_Contact_Form_Builder','register_settings'));

		add_action('wp_ajax_contact_builder_save_forms', array('TT_Contact_Form_Builder','save_forms_ajax'));
		add_action('wp_ajax_contact_form_send_message', array('TT_Contact_Form_Builder','send_message_ajax'));
		add_action('wp_ajax_nopriv_contact_form_send_message', array('TT_Contact_Form_Builder','send_message_ajax'));

		add_shortcode('tesla_form', array('TT_Contact_Form_Builder','form_shortcode'));

	}

	public static function register_settings(){
		register_setting( THEME_OPTIONS, THEME_OPTIONS . '_forms' );
	}

  	public static function setup_contact_admin_submenu() {	    
	    $admin_contact_builder_page = add_submenu_page(THEME_NAME . '_options', 'Contact Forms Builder', 'Forms', 'manage_options', 'tt_contact_builder', array('TT_Contact_Form_Builder', 'contact_builder_page'));
	    add_action( 'load-' . $admin_contact_builder_page, array('TT_Contact_Form_Builder','load_scripts' ) );
	}

	public static function contact_builder_page() {
		self::$args['forms'] = tt_get_forms();
	    self::$TTL->view( 'admin-contact-builder', self::$args );  //Loading Contact Form Builder View
	}

	public static function load_scripts(){
		//Load JS
		wp_enqueue_script( 'admin-bootstrap-js', TT_FW . '/static/js/bootstrap.js', array( 'jquery' ) ,true);
		wp_enqueue_script( 'admin-contact-builder-js', TT_FW . '/static/js/admin-contact-builder.js', array( 'jquery' ,'jquery-ui-droppable','jquery-ui-draggable','jquery-ui-sortable') ,true);
		//Load CSS
		wp_enqueue_style( 'admin-ui-css','http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/themes/smoothness/jquery-ui.css' );
		wp_enqueue_style( 'admin-bootstrap-css', TT_FW . '/static/css/bootstrap.min.css' );
		wp_enqueue_style( 'admin-contact-builder-css', TT_FW . '/static/css/admin-contact-builder.css' );
	}

	public static function save_forms_ajax(){
		if(!empty($_POST['form_builder']['forms'])){
			$forms = $_POST['form_builder']['forms'];
			if(update_option( THEME_OPTIONS . '_forms', $forms ))
				die('1');
			else
				die('2');
		}
		if(update_option( THEME_OPTIONS . '_forms', '' ) )
				die('1');
			else
				die('2');
		die('0');
	}

	public static function send_message_ajax(){
		if(!empty($_POST)){
			$form = tt_get_form($_POST['id']);
			$form_data = array_diff_key($_POST,array('action'=>'','id'=>''));	//deleting data from init POST array
			$subject = 'From: ' . get_bloginfo('name') . "\r\n";

			$headers[] = ( ! empty( $form_data[ 'url' ] ) ) ? 'From:' . $form_data[ 'url' ] : !empty($form_data[ 'name' ] )? 'From:' . $form_data[ 'name' ] :'From: "'. get_bloginfo('name') . '" <'.get_bloginfo("admin_email").'>';
			$headers[] = ( ! empty( $form_data[ 'email' ] ) ) ? 'Reply-To: ' . $form_data[ 'email' ] : '';
			$headers[] = 'MIME-Version: 1.0';
	    	$headers[] = 'Content-type: text/html';

			$receiver_email = !empty($form['receiver_email']) ? $form['receiver_email'] : get_bloginfo('admin_email' );
			
			ob_start(); ?>
			<table style="margin: 0 auto; border: 1px solid #dddddd;">
			    <tbody>
			        <?php 
			        $i = 0;
			        foreach ($form_data as $name => $field_value) : ?>
			            <tr>
			                <td style="padding: 10px;<?php echo $i < count($form_data) - 1 ? 'border-bottom: 1px solid #ddd;' : ''?>"><?php echo $name ?></td>
			                <td style="padding: 10px;<?php echo $i < count($form_data) - 1 ? 'border-bottom: 1px solid #ddd;' : ''?>border-left: 1px solid #ddd;"><?php echo $field_value ?></td>
			            </tr>
			        <?php 
			        $i++;
			        endforeach; ?>
			    </tbody>
			</table>
			<?php $message = ob_get_clean();

			if(wp_mail( $receiver_email, $subject, $message,$headers ))
				die('1');
			else
				die('2');
		}
		die('0');
	}

	public static function render_form($id,$form = NULL){
		if (empty($form)){
			$the_form = tt_get_form($id);
			if(!$the_form)
				return FALSE;
		}
		self::enqueue_frontend_scripts();
	    self::$TTL->view( 'views/contact_form/contact-form', $form , false, true);  //Showing Contact form
	}

	private static function enqueue_frontend_scripts(){
		wp_enqueue_script( 'tt-parsley-js', TT_FW . '/static/js/parsley.min.js', array( 'jquery' ) ,true); //	adding validation parsley
		wp_enqueue_script( 'tt-contact-form-js', TT_FW . '/static/js/contact-form-front.js', array( 'tt-parsley-js' ) ,true); //	adding contact form javascript (ajax and other)
	}

	public static function add_form_locations($locations){
		foreach ($locations as $id => $name)
			self::$args['locations'][$id] = $name;
	}

	public static function form_shortcode($atts, $content = null) {
		extract(shortcode_atts(array(
                'id' => '',
                    ), $atts));
		ob_start();
		tt_form($id);
		return ob_get_clean();
	}
}