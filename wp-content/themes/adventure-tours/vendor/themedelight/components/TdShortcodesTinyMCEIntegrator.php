<?php
/**
 * Class for integration shortcodes into TinyMCE editor.
 *
 * @author    Themedelight
 * @package   Themedelight/Components
 * @version   1.1.0
 */

class TdShortcodesTinyMCEIntegrator extends TdComponent
{
	/**
	 * @var TdShortcodesRegister
	 */
	public $registerService;

	/**
	 * @var string
	 */
	public $baseUrl;

	/**
	 * @var string
	 */
	public $assetsUrl;

	public function init() {
		if ( parent::init() ) {
			add_action( 'admin_init', array( $this, 'action_admin_init' ) );
			return true;
		}
		return false;
	}

	public function action_admin_init() {
		if ( ( current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ) ) && get_user_option( 'rich_editing' ) == 'true' ) {
			//Only use wp_ajax if user is logged in
			//add_action( 'wp_ajax_check_url_action', array( $this, 'ajax_action_check_url' ) );

			add_action( 'wp_ajax_themedelight_render_shortcode', array( $this, 'ajax_render_shortcode' ) );

			add_action( 'wp_ajax_themedelight_render_shortcode_dialog_template', array( $this, 'ajax_render_dialog_template' ) );

			$this->baseUrl = $plugin_url = get_template_directory_uri().'/includes/shortcodes/tinymce/';
			$this->assetsUrl = $this->baseUrl . 'assets/';

			//TinyMCE plugin stuff
			add_filter( 'mce_buttons', array( $this, 'filter_mce_buttons' ) );
			add_filter( 'mce_external_plugins', array( $this, 'filter_mce_external_plugins' ) );

			//TinyMCE shortcode plugin CSS
			wp_enqueue_style( 'tinymce-shortcodes', $this->assetsUrl .'admin.css' );
			add_action( 'admin_print_scripts', array( $this, 'print_config' ) );
		}
	}

	public function print_config() {
		$config = array(
			'menu' => $this->registerService->getMenuConfig(),
			'attributes' => $this->registerService->getDialogsConfig(),
			'dialog_template_url' => admin_url('admin-ajax.php?action=themedelight_render_shortcode_dialog_template'),
		);

		echo '<script>var ThemeShortcodesConfig='.json_encode( $config ).'</script>';
	}

	// Filter mce buttons
	public function filter_mce_buttons($buttons) {
		array_push( $buttons, 'shortcodes_button', 'shortcodes_render_mode_switcher' );
		return $buttons;
	}

	// Actually add tinyMCE plugin attachment
	public function filter_mce_external_plugins($plugins) {
		$plugins['ThemeShortcodesPlugin'] = $this->assetsUrl.'ThemeShortcodesPlugin.js';
		// $plugins['ThemeShortcodeRender'] = $this->assetsUrl.'ThemeShortcodeRender.js';
		return $plugins;
	}

	// Ajax actions - renders shortcode for the visual editor
	public function ajax_render_shortcode() {
		if ( isset( $_GET['shortcode'] ) ) {
			$shortcode = urldecode( $_GET['shortcode'] );
			echo '<html><head>';
			wp_head();
			echo '</head><body><div class="wrapper">';
			echo do_shortcode( $shortcode );
			echo '</div></body>';
			#wp_footer();
			echo '</html>';
		}
		exit();
	}

	// Ajax actions - renders template for shortcode managment dialog.
	public function ajax_render_dialog_template() {
		echo 
<<<HTML
<div>
	<div id="themeShortcodesDialod">
		<div id="themeShortcodesDialogBody">
			<table id="attributesContainer" width="100%">
				<tr>
					<th width="120">&nbsp;</th>
					<th>&nbsp;</th>
				</tr>
			</table>
			<div class="buttons-toolbar">
				<input type="button" id="cancelBtn" class="button alignleft" name="cancel" value="Cancel" accesskey="C" />
				<input type="button" id="insertBtn" class="button-primary alignright" name="insert" value="Insert Shortcode" accesskey="I" />
				<div class="clear"></div>
			</div>
		</div>
	</div>
</div>
HTML;
		exit();
	}

	/*public function ajax_action_check_url()
	{
		$hadError = true;

		$url = isset( $_REQUEST['url'] ) ? $_REQUEST['url'] : '';
		if ( strlen( $url ) > 0  && function_exists( 'get_headers' ) ) {
			$file_headers = @get_headers( $url );
			$exists       = $file_headers && $file_headers[0] != 'HTTP/1.1 404 Not Found';
			$hadError     = false;
		}

		echo '{ "exists": '. ($exists ? '1' : '0') . ($hadError ? ', "error" : 1 ' : '') . ' }';
		die();
	}*/

} // end TinyMCE_Shortcodes class
