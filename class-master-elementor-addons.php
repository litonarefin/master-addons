<?php

namespace MasterAddons;

use MasterAddons\Admin\Dashboard\Master_Addons_Admin_Settings;
use MasterAddons\Admin\Dashboard\Addons\Extensions\JLTMA_Addon_Extensions;
use MasterAddons\Admin\Dashboard\Addons\Elements\JLTMA_Addon_Elements;
use MasterAddons\Admin\Dashboard\Addons\Extensions\JLTMA_Third_Party_Extensions;
use MasterAddons\Inc\Helper\Master_Addons_Helper;

if (!defined('ABSPATH')) {
	exit;
} // No, Direct access Sir !!!

if (!class_exists('Master_Elementor_Addons')) {
	final class Master_Elementor_Addons
	{

		static public $class_namespace = '\\MasterAddons\\Inc\\Classes\\';
		public $controls_manager;

		const VERSION = JLTMA_PLUGIN_VERSION;
		const JLTMA_STABLE_VERSION = JLTMA_STABLE_VER;
		const MINIMUM_PHP_VERSION = '5.4';
		const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

		private $_localize_settings = [];
		private $reflection;
		private static $plugin_path;
		private static $plugin_url;
		private static $plugin_slug;
		public static $plugin_dir_url;
		public static $plugin_name = 'Master Addons';
		public $gsap_version = '1.20.2';

		private static $instance = null;

		public static $maad_el_default_widgets;
		public static $jltma_new_widgets_settings;
		public static $maad_el_pro_widgets;
		public static $ma_el_extensions;
		public static $jltma_third_party_plugins;


		public static function get_instance()
		{
			if (!self::$instance) {
				self::$instance = new self;
				self::$instance->ma_el_init();
			}
			return self::$instance;
		}


		public function __construct()
		{
			$this->reflection = new \ReflectionClass($this);

			$this->constants();
			$this->maad_el_include_files();
			$this->jltma_load_extensions();

			self::$plugin_slug = 'master-addons';
			self::$plugin_path = untrailingslashit(plugin_dir_path('/', __FILE__));
			self::$plugin_url  = untrailingslashit(plugins_url('/', __FILE__));

			// Initialize Plugin
			add_action('plugins_loaded', [$this, 'ma_el_plugins_loaded']);

			add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'plugin_actions_links']);

			add_action('elementor/init', [$this, 'mela_category']);

			add_action('elementor/init', [$this, 'jltma_add_actions_to_elementor'], 0);

			// Enqueue Styles and Scripts
			add_action('wp_enqueue_scripts', [$this, 'jltma_enqueue_scripts'], 100);

			// Placeholder image replacement
			add_filter('elementor/utils/get_placeholder_image_src', [$this, 'jltma_replace_placeholder_image']);


			// Elementor Scripts Dependencies
			add_action('elementor/frontend/after_register_styles', [$this, 'jltma_register_frontend_styles']);
			add_action('elementor/frontend/after_register_scripts', [$this, 'jltma_register_frontend_scripts']);
			// add_action( 'elementor/frontend/after_enqueue_scripts', [$this, 'jltma_enqueue_scripts'] );

			//		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'ma_el_enqueue_frontend_styles' ] );
			//		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'ma_el_enqueue_frontend_scripts' ] );




			// add_action( 'elementor/editor/before_enqueue_scripts'  , array( $this, 'jltma_editor_scripts_enqueue_js' ) );

			add_action('elementor/editor/after_enqueue_scripts', [$this, 'jltma_editor_scripts_js'], 100);
			add_action('elementor/editor/after_enqueue_styles', [$this, 'jltma_enqueue_preview_scripts'], 100);

			add_action('elementor/preview/enqueue_styles', [$this, 'jltma_enqueue_preview_scripts'], 100);
			add_action('elementor/preview/enqueue_scripts', [$this, 'jltma_enqueue_preview_scripts'], 100);


			// Add Elementor Widgets
			add_action('elementor/widgets/widgets_registered', [$this, 'jltma_init_widgets']);

			//Register Controls
			add_action('elementor/controls/controls_registered', array($this, 'jltma_register_controls'));

			add_action('admin_post_master_addons_rollback', 'jltma_post_addons_rollback');

			//Body Class
			add_action('body_class', [$this, 'jltma_body_class']);

			// Override Freemius Filters
			ma_el_fs()->add_filter('support_forum_submenu', array($this, 'override_support_menu_text'));
		}


		public function ma_el_init()
		{

			$this->mela_load_textdomain();
			$this->ma_el_image_size();

			//Redirect Hook
			add_action('admin_init', [$this, 'mael_ad_redirect_hook']);
		}

		public function override_support_menu_text()
		{
			return __('Support', MELA_TD);
		}


		public static function jltma_elementor()
		{
			return \Elementor\Plugin::$instance;
		}

		// Deactivation Hook
		public static function jltma_plugin_deactivation_hook()
		{
			delete_option('jltma_activation_time');
		}

		// Activation Hook
		public static function jltma_plugin_activation_hook()
		{

			if (get_option('jltma_activation_time') === false)
				update_option('jltma_activation_time', strtotime("now"));

			self::activated_widgets();
			self::activated_extensions();
			self::activated_third_party_plugins();

			ma_el_fs()->add_action('after_premium_version_activation', array('\\MasterAddons\\Master_Elementor_Addons', 'jltma_network_activate'));
		}

		// Multisite Activation
		public static function jltma_network_activate($network_wide)
		{

			if (function_exists('is_multisite') && is_multisite()) {
				//do nothing for multisite!
			} else {
				//Make sure we redirect to the welcome page
				set_transient(JLTMA_ACTIVATION_REDIRECT_TRANSIENT_KEY, true, 30);
			}
		}


		// Initialize
		public function ma_el_plugins_loaded()
		{

			// Check if Elementor installed and activated
			if (!did_action('elementor/loaded')) {
				add_action('admin_notices', array($this, 'mela_admin_notice_missing_main_plugin'));
				return;
			}

			// Check for required Elementor version
			if (!version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
				add_action('admin_notices', array($this, 'mela_admin_notice_minimum_elementor_version'));
				return;
			}

			// Check for required PHP version
			if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
				add_action('admin_notices', array($this, 'mela_admin_notice_minimum_php_version'));
				return;
			}

			self::jltma_plugin_activation_hook();
		}


		public function constants()
		{

			if (!defined('MELA')) {
				define('MELA', self::$plugin_name);
			}

			//Defined Constants
			if (!defined('MA_EL_BADGE')) {
				define('MA_EL_BADGE', '<span class="ma-el-badge"></span>');
			}

			if (!defined('MELA_VERSION')) {
				define('MELA_VERSION', self::version());
			}

			if (!defined('JLTMA_STABLE_VERSION')) {
				define('JLTMA_STABLE_VERSION', self::JLTMA_STABLE_VERSION);
			}

			if (!defined('MA_EL_SCRIPT_SUFFIX')) {
				define('MA_EL_SCRIPT_SUFFIX', defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min');
			}

			if (!defined('MELA_BASE')) {
				define('MELA_BASE', plugin_basename(__FILE__));
			}

			if (!defined('MELA_PLUGIN_URL')) {
				define('MELA_PLUGIN_URL', self::mela_plugin_url());
			}

			if (!defined('MELA_PLUGIN_PATH')) {
				define('MELA_PLUGIN_PATH', self::mela_plugin_path());
			}

			if (!defined('MELA_PLUGIN_PATH_URL')) {
				define('MELA_PLUGIN_PATH_URL', self::mela_plugin_dir_url());
			}

			if (!defined('MELA_IMAGE_DIR')) {
				define('MELA_IMAGE_DIR', self::mela_plugin_dir_url() . '/assets/images/');
			}

			if (!defined('MELA_ADMIN_ASSETS')) {
				define('MELA_ADMIN_ASSETS', self::mela_plugin_dir_url() . '/inc/admin/assets/');
			}

			if (!defined('MAAD_EL_ADDONS')) {
				define('MAAD_EL_ADDONS', plugin_dir_path(__FILE__) . 'addons/');
			}

			if (!defined('MELA_TEMPLATES')) {
				define('MELA_TEMPLATES', plugin_dir_path(__FILE__) . 'inc/template-parts/');
			}

			// Master Addons Text Domain
			if (!defined('MELA_TD')) {
				define('MELA_TD', $this->mela_load_textdomain());
			}

			if (!defined('MELA_FILE')) {
				define('MELA_FILE', __FILE__);
			}

			if (!defined('MELA_DIR')) {
				define('MELA_DIR', dirname(__FILE__));
			}

			if (ma_el_fs()->can_use_premium_code()) {
				if (!defined('MASTER_ADDONS_PRO_ADDONS_VERSION')) {
					define('MASTER_ADDONS_PRO_ADDONS_VERSION', ma_el_fs()->can_use_premium_code());
				}
			}

			define('JLTMA_ACTIVATION_REDIRECT_TRANSIENT_KEY', '_master_addons_activation_redirect');
		}



		function mela_category()
		{

			\Elementor\Plugin::instance()->elements_manager->add_category(
				'master-addons',
				[
					'title' => esc_html__('Master Addons', MELA_TD),
					'icon'  => 'font',
				],
				1
			);
		}

		public function ma_el_image_size()
		{
			add_image_size('master_addons_team_thumb', 250, 330, true);
		}

		// Widget Elements
		public static function activated_widgets()
		{
			$jltma_default_element_settings 	= array_fill_keys(Master_Addons_Admin_Settings::jltma_addons_array(), true);
			$jltma_get_element_settings     	= get_option('maad_el_save_settings', $jltma_default_element_settings);
			$jltma_new_element_settings     	= array_diff_key($jltma_default_element_settings, $jltma_get_element_settings);
			$jltma_updated_element_settings 	= array_merge($jltma_get_element_settings, $jltma_new_element_settings);

			if ($jltma_get_element_settings === false) {
				$jltma_get_element_settings = array();
			}
			update_option('maad_el_save_settings', $jltma_updated_element_settings);

			return $jltma_get_element_settings;
		}

		// Extensions
		public static function activated_extensions()
		{
			$jltma_default_extensions_settings 	= array_fill_keys(Master_Addons_Admin_Settings::jltma_addons_extensions_array(), true);
			$jltma_get_extension_settings     	= get_option('ma_el_extensions_save_settings', $jltma_default_extensions_settings);
			$jltma_new_extension_settings     	= array_diff_key($jltma_default_extensions_settings, $jltma_get_extension_settings);
			$jltma_updated_extension_settings 	= array_merge($jltma_get_extension_settings, $jltma_new_extension_settings);

			if ($jltma_get_extension_settings === false) {
				$jltma_get_extension_settings = array();
			}

			update_option('ma_el_extensions_save_settings', $jltma_updated_extension_settings);

			return $jltma_get_extension_settings;
		}


		// Third Party Plugins
		public static function activated_third_party_plugins()
		{
			$jltma_third_party_plugins_settings 		= array_fill_keys(Master_Addons_Admin_Settings::jltma_addons_third_party_plugins_array(), true);
			$jltma_get_third_party_plugins_settings     = get_option('ma_el_third_party_plugins_save_settings', $jltma_third_party_plugins_settings);
			$jltma_new_third_party_plugins_settings     = array_diff_key($jltma_third_party_plugins_settings, $jltma_get_third_party_plugins_settings);
			$maad_el_updated_extension_settings 		= array_merge($jltma_get_third_party_plugins_settings, $jltma_new_third_party_plugins_settings);

			if ($jltma_get_third_party_plugins_settings === false) {
				$jltma_get_third_party_plugins_settings = array();
			}
			update_option('ma_el_third_party_plugins_save_settings', $maad_el_updated_extension_settings);

			return $jltma_get_third_party_plugins_settings;
		}


		public function jltma_add_actions_to_elementor()
		{

			$classes = glob(MELA_PLUGIN_PATH . '/inc/classes/JLTMA_*.php');

			// include all classes
			foreach ($classes as $key => $value) {
				require_once $value;
			}

			// instance all classes
			foreach ($classes as $key => $value) {
				$name = pathinfo($value, PATHINFO_FILENAME);
				$class = self::$class_namespace . $name;
				$this->jltma_classes[strtolower($name)] = new $class();
			}
		}

		public function jltma_register_controls($controls_manager)
		{

			$controls_manager = \Elementor\Plugin::$instance->controls_manager;

			$controls = array(
				'jltma-visual-select' => array(
					'file'  => MELA_PLUGIN_PATH . '/inc/controls/visual-select.php',
					'class' => 'MasterAddons\Inc\Controls\MA_Control_Visual_Select',
					'type'  => 'single'
				),
				'jltma-transitions' => array(
					'file'  => MELA_PLUGIN_PATH . '/inc/controls/group/transitions.php',
					'class' => 'MasterAddons\Inc\Controls\MA_Group_Control_Transition',
					'type'  => 'group'
				),
				'jltma_query' => array(
					'file'  => MELA_PLUGIN_PATH . '/inc/controls/jltma-query.php',
					'class' => 'MasterAddons\Inc\Controls\JLTMA_Control_Query',
					'type'  => 'single'
				)
			);

			foreach ($controls as $control_type => $control_info) {
				if (!empty($control_info['file']) && !empty($control_info['class'])) {

					include_once($control_info['file']);

					if (class_exists($control_info['class'])) {
						$class_name = $control_info['class'];
					} elseif (class_exists(__NAMESPACE__ . '\\' . $control_info['class'])) {
						$class_name = __NAMESPACE__ . '\\' . $control_info['class'];
					}

					if ($control_info['type'] === 'group') {
						$controls_manager->add_group_control($control_type, new $class_name());
					} else {
						$controls_manager->register_control($control_type, new $class_name());
					}
				}
			}
		}

		public function get_widgets()
		{
			return [];
		}

		public function jltma_init_widgets()
		{

			$activated_widgets = self::activated_widgets();

			// Network Check
			if (defined('JLTMA_NETWORK_ACTIVATED') && JLTMA_NETWORK_ACTIVATED) {
				global $wpdb;
				$blogs = $wpdb->get_results("
				    SELECT blog_id
				    FROM {$wpdb->blogs}
				    WHERE site_id = '{$wpdb->siteid}'
				    AND spam = '0'
				    AND deleted = '0'
				    AND archived = '0'
				");
				$original_blog_id = get_current_blog_id();


				foreach ($blogs as $blog_id) {
					switch_to_blog($blog_id->blog_id);

					foreach (self::$maad_el_default_widgets as $widget) {
						$is_pro = "";
						if (isset($widget)) {
							if (is_array($widget)) {
								$is_pro = $widget[1];
								$widget = $widget[0];

								if (ma_el_fs()->can_use_premium_code()) {
									if ($activated_widgets[$widget] == true && $is_pro == "pro") {
										require_once MAAD_EL_ADDONS . $widget . '/' . $widget . '.php';
									}
								}
							}
						}

						if ($activated_widgets[$widget] == true && $is_pro != "pro") {
							require_once MAAD_EL_ADDONS . $widget . '/' . $widget . '.php';
						}
					}
				}

				switch_to_blog($original_blog_id);
			} else {

				$widget_manager = Master_Addons_Helper::jltma_elementor()->widgets_manager;

				ksort(JLTMA_Addon_Elements::$jltma_elements['jltma-addons']['elements']);
				foreach (JLTMA_Addon_Elements::$jltma_elements['jltma-addons']['elements'] as $key =>  $widget) {

					$widget_file = MAAD_EL_ADDONS . $widget['key'] . '/' . $widget['key'] . '.php';

					if (!ma_el_fs()->can_use_premium_code() && (isset($widget['is_pro']) && $widget['is_pro'])) {
						continue;
					}

					if (file_exists($widget_file)) {
						require_once $widget_file;
					}

					$widget_class_name = preg_replace('/\s+/', '_', $widget['title']);

					$class_name = $this->reflection->getNamespaceName() . '\Addons\\' . $widget_class_name;
					$widget_manager->register_widget_type(new $class_name);
				}
			}
		}




		public function jltma_load_extensions()
		{

			// Extension
			$activated_extensions = self::activated_extensions();


			// Network Check
			if (defined('JLTMA_NETWORK_ACTIVATED') && JLTMA_NETWORK_ACTIVATED) {
				global $wpdb;
				$blogs = $wpdb->get_results("
				    SELECT blog_id
				    FROM {$wpdb->blogs}
				    WHERE site_id = '{$wpdb->siteid}'
				    AND spam = '0'
				    AND deleted = '0'
				    AND archived = '0'
				");
				$original_blog_id = get_current_blog_id();


				foreach ($blogs as $blog_id) {
					switch_to_blog($blog_id->blog_id);


					foreach (JLTMA_Addon_Extensions::$jltma_extensions['jltma-extensions']['extension'] as $extensions) {

						$is_pro = "";

						if (isset($extensions)) {
							if (is_array($extensions)) {
								$is_pro = $extensions[1];
								$extensions = $extensions[0];

								if (ma_el_fs()->can_use_premium_code()) {
									if ($activated_extensions[$extensions] == true && $is_pro == "pro") {
										include_once MELA_PLUGIN_PATH . '/inc/modules/' . $extensions . '/' . $extensions . '.php';
									}
								}
							}
						}

						if ($activated_extensions[$extensions] == true && $is_pro != "pro") {
							include_once MELA_PLUGIN_PATH . '/inc/modules/' . $extensions . '/' .  $extensions . '.php';
						}
					}
				}

				switch_to_blog($original_blog_id);
			} else {

				// OLD Style
				foreach (JLTMA_Addon_Extensions::$jltma_extensions['jltma-extensions']['extension'] as $extensions) {
					// print_r($extensions);

					// if (isset($extensions)) {
					// 	if (is_array($extensions)) {
					// 		$is_pro = $extensions[1];
					// 		$extensions = $extensions[0];

					// 		if (ma_el_fs()->can_use_premium_code()) {
					// 			if ($activated_extensions[$extensions] == true && $is_pro == "pro") {
					// 				include_once MELA_PLUGIN_PATH . '/inc/modules/' . $extensions . '/' . $extensions . '.php';
					// 			}
					// 		}
					// 	}
					// }

					// if (!ma_el_fs()->can_use_premium_code() && isset($extensions['is_pro']) && $extensions['is_pro']) {
					// 	include_once MELA_PLUGIN_PATH . '/inc/modules/' . $extensions['key'] . '/' .  $extensions['key'] . '.php';
					// 	// echo "Pro <br>";
					// } else {
					// 	// echo "Not Pro <br>";
					if (
						$extensions['key'] == 'particles'
						|| $extensions['key'] == 'animated-gradient'
						|| $extensions['key'] == 'reading-progress-bar'
						|| $extensions['key'] == 'bg-slider'
						|| $extensions['key'] == 'custom-css'
						|| $extensions['key'] == 'custom-js'
						|| $extensions['key'] == 'positioning'
						|| $extensions['key'] == 'extras'
						|| $extensions['key'] == 'mega-menu'
						|| $extensions['key'] == 'transition'
						|| $extensions['key'] == 'transforms'
						|| $extensions['key'] == 'rellax'
						|| $extensions['key'] == 'reveal'
						|| $extensions['key'] == 'header-footer-comment'
						|| $extensions['key'] == 'display-conditions'
						|| $extensions['key'] == 'dynamic-tags'
						|| $extensions['key'] == 'floating-effects'
						|| $extensions['key'] == 'wrapper-link'
					) {

						require_once MELA_PLUGIN_PATH . '/inc/modules/' . $extensions['key'] . '/' .  $extensions['key'] . '.php';
					}

					// }

					// 	// if ($activated_extensions[$extensions] == true && $is_pro != "pro") {
					// include_once MELA_PLUGIN_PATH . '/inc/modules/' . $extensions . '/' .  $extensions . '.php';
					// 	// }
				}

				// New Style with Namespace
				// ksort(JLTMA_Addon_Extensions::$jltma_extensions['jltma-extensions']['extension']);
				// foreach (JLTMA_Addon_Extensions::$jltma_extensions['jltma-extensions']['extension'] as $key =>  $extensions) {

				// 	$extensions_file = MELA_PLUGIN_PATH . '/inc/modules/' . $extensions['key'] . '/' .  $extensions['key'] . '.php';

				// 	if (file_exists($extensions_file)) {
				// 		require_once $extensions_file;
				// 	}
				// }
			}
		}

		public function jltma_replace_placeholder_image()
		{
			return MELA_IMAGE_DIR . 'placeholder.png';
		}


		/**
		 *
		 * Enqueue Elementor Editor Styles
		 *
		 */

		public function jltma_editor_scripts_js()
		{
			wp_enqueue_script('master-addons-editor', MELA_ADMIN_ASSETS . 'js/editor.js', array('jquery'), MELA_VERSION, true);
		}

		public function jltma_editor_scripts_enqueue_js()
		{

			wp_enqueue_script('ma-el-rellaxjs-lib', MELA_PLUGIN_URL . '/assets/vendor/rellax/rellax.min.js', array('jquery'), self::VERSION, true);
		}

		public function jltma_editor_scripts_css()
		{
			wp_enqueue_style('master-addons-editor', MELA_PLUGIN_URL . '/assets/css/master-addons-editor.css');
		}


		/**
		 * Enqueue Plugin Styles and Scripts
		 *
		 */
		public function jltma_enqueue_scripts()
		{

			$is_activated_widget = self::activated_widgets();
			$is_activated_extensions = self::activated_extensions();
			$jltma_api_settings = get_option('jltma_api_save_settings');

			// Register Styles
			wp_register_style('jltma-bootstrap', MELA_PLUGIN_URL . '/assets/css/bootstrap.min.css');

			//Reveal
			wp_register_script('ma-el-reveal-lib', MELA_PLUGIN_URL . '/assets/vendor/reveal/revealFx.js', array('jquery'), self::VERSION, true);
			wp_register_script('ma-el-anime-lib', MELA_PLUGIN_URL . '/assets/vendor/anime/anime.min.js', array('jquery'), self::VERSION, true);

			//Rellax
			wp_register_script('ma-el-rellaxjs-lib', MELA_PLUGIN_URL . '/assets/vendor/rellax/rellax.min.js', array('jquery'), self::VERSION, true);

			// Register Scripts
			wp_register_script('jltma-bootstrap', MELA_PLUGIN_URL . '/assets/js/bootstrap.min.js', array('jquery'), MELA_VERSION, true);


			// Enqueue Styles
			wp_enqueue_style('jltma-bootstrap');
			wp_enqueue_style('master-addons-main-style', MELA_PLUGIN_URL . '/assets/css/master-addons-styles.css');


			// Enqueue Scripts
			// wp_enqueue_script( 'jltma-bootstrap' );
			wp_enqueue_script('master-addons-plugins', MELA_PLUGIN_URL . '/assets/js/plugins.js', ['jquery'], self::VERSION, true);
			wp_enqueue_script('master-addons-scripts', MELA_PLUGIN_URL . '/assets/js/master-addons-scripts.js', ['jquery'], self::VERSION, true);


			// Add essential inline scripts to header
			$jltma_header_inline_scripts = 'function jltmaNS(n){for(var e=n.split("."),a=window,i="",r=e.length,t=0;r>t;t++)"window"!=e[t]&&(i=e[t],a[i]=a[i]||{},a=a[i]);return a;}';
			if ($jltma_header_inline_scripts = apply_filters('jltma_header_inline_scripts', $jltma_header_inline_scripts)) {
				wp_add_inline_script(
					'jquery-core',
					"/* < ![CDATA[ */\n" . $jltma_header_inline_scripts . "\n/* ]]> */",
					'before'
				);
			}


			$localize_data = array(
				'plugin_url'    => MELA_PLUGIN_URL,
				'ajaxurl'       => admin_url('admin-ajax.php'),
				'nonce'       	=> 'master-addons-elementor',
			);
			wp_localize_script('master-addons-scripts', 'jltma_scripts', $localize_data);



			// Addons specific Script/Styles Dependencies

			// Need to Check Extensions
			// if ( $is_activated_extensions['floating-effects'] ) {
			// 	wp_enqueue_script( 'jltma-floating-effects' );
			// }

			//Mega Menu
			// if ( $is_activated_extensions['mega-menu'] ) {
			// 	wp_enqueue_style('jltma-bootstrap');
			// 	wp_enqueue_script('jltma-bootstrap');
			// }


			//Progressbar
			// if ( $is_activated_widget['ma-progressbar'] ) {
			// 	wp_enqueue_script('master-addons-progressbar');
			// 	wp_enqueue_script( 'master-addons-waypoints');
			// }

			//Team Members
			// if ( $is_activated_widget['ma-team-members'] ) {
			// 	wp_enqueue_style( 'gridder' );
			// 	wp_enqueue_script( 'gridder' );
			// 	wp_enqueue_script( 'jltma-owl-carousel' );
			// }


			//Restrict Content
			// if ( $is_activated_widget['ma-restrict-content'] ) {
			// 	wp_enqueue_style( 'fancybox' );
			// 	wp_enqueue_script( 'fancybox' );
			// }

			//Creative Buttons
			// if ( $is_activated_widget['ma-creative-buttons'] ) {
			// 	// echo Master_Addons_Helper::jltma_elementor()->frontend->get_builder_content_for_display(
			// 	// \Elementor\Plugin::$instance->editor->is_edit_mode()
			// 	// \Elementor\Plugin::$instance->editor->is_edit_mode()
			// 	// \Elementor\Plugin::$instance->preview->is_preview_mode()
			// 	if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			// 		wp_enqueue_style( 'ma-creative-buttons' );
			// 	}
			// }

			//Image Hover Effects
			// if ( $is_activated_widget['ma-image-hover-effects'] ) {
			// 	wp_enqueue_style( 'ma-image-hover-effects', MELA_PLUGIN_URL . '/assets/vendor/image-hover-effects/image-hover-effects.css' );
			// }

			//Table of Contents
			// if ( $is_activated_widget['ma-table-of-contents'] ) {
			// 	wp_enqueue_script( 'tocbot' );
			// }


			//News Ticker
			// if ( $is_activated_widget['ma-news-ticker'] ) {
			// 	wp_enqueue_script( 'ma-news-ticker' );
			// }


			//Counter Up
			// if ( $is_activated_widget['ma-counter-up'] ) {
			// 	wp_enqueue_script( 'ma-counter-up' );
			// }

			//MA Blog
			// if ( $is_activated_widget['ma-blog'] ) {
			// 	wp_enqueue_script( 'isotope' );
			// }

			//MA Filterable Gallery
			// if ( $is_activated_widget['ma-image-filter-gallery'] ) {
			// 	wp_enqueue_script( 'isotope' );

			// 	wp_enqueue_style( 'fancybox' );
			// 	wp_enqueue_script( 'fancybox' );
			// }

			// //MA Instagram Feed
			// if ( $is_activated_widget['ma-instagram-feed'] ) {

			// 	wp_enqueue_style( 'fancybox' );

			// 	wp_enqueue_script( 'isotope' );
			// 	wp_enqueue_script( 'fancybox' );
			// 	wp_enqueue_script( 'imagesloaded' );
			// }

			// //MA Image Comparison
			// if ( $is_activated_widget['ma-image-comparison'] ) {
			// 	wp_enqueue_style( 'twentytwenty' );
			// 	wp_enqueue_script( 'jquery-event-move' );
			// 	wp_enqueue_script( 'twentytwenty' );
			// 	wp_enqueue_script( 'master-addons-scripts' );
			// }

			// //MA Toggle Content
			// if ( $is_activated_widget['ma-toggle-content'] ) {
			// 	wp_enqueue_script( 'jltma-toggle-content' );
			// 	wp_enqueue_script( 'gsap-js' );
			// }

			// //MA Gallery Slider
			// if ( $is_activated_widget['ma-gallery-slider'] ) {
			// 	wp_enqueue_script( 'swiper' );
			// 	wp_enqueue_script( 'master-addons-scripts' );
			// }


			//Google Maps
			//		if ( $is_activated_widget['google-maps'] ) {
			//			wp_enqueue_script( 'master-addons-google-map-api', 'https://maps.googleapis.com/maps/api/js?key='
			//.get_option
			//('exad_google_map_api_option'), array('jquery'),'1.8', false );
			//			// Gmap 3 Js
			//			wp_enqueue_script( 'master-addons-gmap3', MELA_PLUGIN_URL . 'assets/js/vendor/gmap3.min.js', array(
			// 'jquery' )
			//, self::VERSION, true );
			//		}


		}


		// Register Frontend Styles
		public function jltma_register_frontend_styles()
		{
			wp_register_style('gridder', MELA_PLUGIN_URL . '/assets/vendor/gridder/css/jquery.gridder.min.css');
			wp_register_style('fancybox', MELA_PLUGIN_URL . '/assets/vendor/fancybox/jquery.fancybox.min.css');
			wp_register_style('twentytwenty', MELA_PLUGIN_URL . '/assets/vendor/image-comparison/css/twentytwenty.css');

			wp_register_style('ma-creative-buttons', MELA_PLUGIN_URL . '/assets/vendor/creative-btn/buttons.css');
			wp_register_style('ma-image-hover-effects', MELA_PLUGIN_URL . '/assets/vendor/image-hover-effects/image-hover-effects.css');
		}



		// Enqueue Preview Scripts
		public function jltma_register_frontend_scripts()
		{

			wp_register_script('ma-animated-headlines', MELA_PLUGIN_URL . '/assets/js/animated-main.js', array('jquery'),	'1.0', true);

			wp_register_script('master-addons-progressbar', MELA_PLUGIN_URL . '/assets/js/loading-bar.js', ['jquery'], self::VERSION, true);

			wp_register_script('jquery-stats', MELA_PLUGIN_URL . '/assets/js/jquery.stats.js', ['jquery'], MELA_VERSION, true);

			wp_register_script('master-addons-waypoints', MELA_PLUGIN_URL . '/assets/vendor/jquery.waypoints.min.js', ['jquery'], self::VERSION, true);

			wp_register_script('jltma-owl-carousel', MELA_PLUGIN_URL . '/assets/vendor/owlcarousel/owl.carousel.min.js', ['jquery'], MELA_VERSION, true);

			wp_register_script('gridder', MELA_PLUGIN_URL . '/assets/vendor/gridder/js/jquery.gridder.min.js', ['jquery'], MELA_VERSION, true);

			wp_register_script('isotope', MELA_PLUGIN_URL . '/assets/js/isotope.js', array('jquery'), MELA_VERSION, true);

			wp_register_script('ma-news-ticker', MELA_PLUGIN_URL . '/assets/vendor/newsticker/js/newsticker.js', array('jquery'), MELA_VERSION, true);

			wp_register_script(
				'jquery-rss',
				MELA_PLUGIN_URL . '/assets/vendor/newsticker/js/jquery.rss.min.js',
				array('jquery'),
				MELA_VERSION,
				true
			);

			wp_register_script('ma-counter-up', MELA_PLUGIN_URL . '/assets/js/counterup.min.js', array('jquery'), MELA_VERSION, true);

			wp_register_script('ma-countdown', MELA_PLUGIN_URL . '/assets/vendor/countdown/jquery.countdown.js', array('jquery'), self::VERSION, true);

			wp_register_script('tocbot', MELA_PLUGIN_URL . '/assets/vendor/tocbot/tocbot.min.js', array('jquery'), self::VERSION, true);

			wp_register_script('fancybox', MELA_PLUGIN_URL . '/assets/vendor/fancybox/jquery.fancybox.min.js', array('jquery'), self::VERSION, true);


			// Image Comparison
			wp_register_script('jquery-event-move', MELA_PLUGIN_URL . '/assets/vendor/image-comparison/js/jquery.event.move.js', array('jquery'), self::VERSION, true);
			wp_register_script('twentytwenty', MELA_PLUGIN_URL . '/assets/vendor/image-comparison/js/jquery.twentytwenty.js', array('jquery'), self::VERSION, true);

			// Toggle Content
			wp_register_script('jltma-toggle-content', MELA_PLUGIN_URL . '/assets/vendor/toggle-content/toggle-content.js', array('jquery'), self::VERSION, true);

			// GSAP TweenMax
			wp_register_script('gsap-js', '//cdnjs.cloudflare.com/ajax/libs/gsap/' . $this->gsap_version . '/TweenMax.min.js', array(), null, true);


			// Advanced Animations
			wp_register_script('jltma-floating-effects', MELA_PLUGIN_URL . '/assets/vendor/floating-effects/floating-effects.js', array('ma-el-anime-lib', 'jquery'), self::VERSION);
		}


		// Enqueue Preview Scripts
		public function jltma_enqueue_preview_scripts()
		{
			wp_enqueue_style('ma-creative-buttons');
		}



		public function is_elementor_activated($plugin_path = 'elementor/elementor.php')
		{
			$installed_plugins_list = get_plugins();

			return isset($installed_plugins_list[$plugin_path]);
		}


		/*
		 * Activation Plugin redirect hook
		 */
		public function mael_ad_redirect_hook()
		{
			if (is_plugin_active('elementor/elementor.php')) {
				if (get_option('ma_el_update_redirect', false)) {
					delete_option('ma_el_update_redirect');
					delete_transient('ma_el_update_redirect');
					if (!isset($_GET['activate-multi']) && $this->is_elementor_activated()) {
						wp_redirect('admin.php?page=master-addons-settings');
						exit;
					}
				}
			}
		}


		public static function version()
		{
			return self::VERSION;
		}


		// Text Domains
		public function mela_load_textdomain()
		{
			load_plugin_textdomain('mela');
		}


		// Plugin URL
		public static function mela_plugin_url()
		{

			if (self::$plugin_url) {
				return self::$plugin_url;
			}

			return self::$plugin_url = untrailingslashit(plugins_url('/', __FILE__));
		}

		// Plugin Path
		public static function mela_plugin_path()
		{
			if (self::$plugin_path) {
				return self::$plugin_path;
			}

			return self::$plugin_path = untrailingslashit(plugin_dir_path(__FILE__));
		}

		// Plugin Dir Path
		public static function mela_plugin_dir_url()
		{

			if (self::$plugin_dir_url) {
				return self::$plugin_dir_url;
			}

			return self::$plugin_dir_url = untrailingslashit(plugin_dir_url(__FILE__));
		}


		public function plugin_actions_links($links)
		{
			if (is_admin()) {
				$links[] = sprintf('<a href="admin.php?page=master-addons-settings">' . esc_html__('Settings', MELA_TD) . '</a>');
				$links[] = '<a href="https://master-addons.com/contact-us" target="_blank">' . esc_html__('Support', MELA_TD) . '</a>';
				$links[] = '<a href="https://master-addons.com/docs/" target="_blank">' . esc_html__('Documentation', MELA_TD) . '</a>';
			}

			// go pro
			if (!ma_el_fs()->can_use_premium_code()) {
				$links[] = sprintf('<a href="https://master-addons.com/" target="_blank" style="color: #39b54a; font-weight: bold;">' . esc_html__('Go Pro', MELA_TD) . '</a>');
			}

			return $links;
		}


		// Include Files
		public function maad_el_include_files()
		{

			// Helper Class
			include_once MELA_PLUGIN_PATH . '/inc/classes/helper-class.php';

			// Templates Control Class
			include_once MELA_PLUGIN_PATH . '/inc/classes/template-controls.php';

			//Reset Theme Styles
			include_once MELA_PLUGIN_PATH . '/inc/classes/class-reset-themes.php';

			// Dashboard Settings
			include_once MELA_PLUGIN_PATH . '/inc/admin/dashboard-settings.php';

			//Utils
			include_once MELA_PLUGIN_PATH . '/inc/classes/utils.php';

			//Rollback
			include_once MELA_PLUGIN_PATH . '/inc/classes/rollback.php';

			// Templates
			require_once MELA_PLUGIN_PATH . '/inc/templates/templates.php';

			// Extensions
			require_once MELA_PLUGIN_PATH . '/inc/classes/JLTMA_Extension_Prototype.php';
		}


		public function jltma_body_class($classes)
		{
			global $pagenow;

			if (in_array($pagenow, ['post.php', 'post-new.php'], true) && \Elementor\Utils::is_post_support()) {
				$post = get_post();

				$mode_class = \Elementor\Plugin::$instance->db->is_built_with_elementor($post->ID) ? 'elementor-editor-active' : 'elementor-editor-inactive master-addons';

				$classes .= ' ' . $mode_class;
			}

			return $classes;
		}


		public function get_localize_settings()
		{
			return $this->_localize_settings;
		}

		public function add_localize_settings($setting_key, $setting_value = null)
		{
			if (is_array($setting_key)) {
				$this->_localize_settings = array_replace_recursive($this->_localize_settings, $setting_key);

				return;
			}

			if (!is_array($setting_value) || !isset($this->_localize_settings[$setting_key]) || !is_array($this->_localize_settings[$setting_key])) {
				$this->_localize_settings[$setting_key] = $setting_value;

				return;
			}

			$this->_localize_settings[$setting_key] = array_replace_recursive($this->_localize_settings[$setting_key], $setting_value);
		}


		public function mela_admin_notice_missing_main_plugin()
		{
			$plugin = 'elementor/elementor.php';

			if ($this->is_elementor_activated()) {
				if (!current_user_can('activate_plugins')) {
					return;
				}
				$activation_url = wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin);
				$message = __('<b>Master Addons</b> requires <strong>Elementor</strong> plugin to be active. Please activate Elementor to continue.', MELA_TD);
				$button_text = __('Activate Elementor', MELA_TD);
			} else {
				if (!current_user_can('install_plugins')) {
					return;
				}

				$activation_url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=elementor'), 'install-plugin_elementor');
				$message = sprintf(__('<b>Master Addons</b> requires %1$s"Elementor"%2$s plugin to be installed and activated. Please install Elementor to continue.', MELA_TD), '<strong>', '</strong>');
				$button_text = __('Install Elementor', MELA_TD);
			}




			$button = '<p><a href="' . $activation_url . '" class="button-primary">' . $button_text . '</a></p>';

			printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p>%2$s</div>', $message, $button);
		}

		public function mela_admin_notice_minimum_elementor_version()
		{
			if (isset($_GET['activate'])) {
				unset($_GET['activate']);
			}

			$message = sprintf(
				/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
				esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', MELA_TD),
				'<strong>' . esc_html__('Master Addons for Elementor', MELA_TD) . '</strong>',
				'<strong>' . esc_html__('Elementor', MELA_TD) . '</strong>',
				self::MINIMUM_ELEMENTOR_VERSION
			);

			printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
		}

		public function mela_admin_notice_minimum_php_version()
		{
			if (isset($_GET['activate'])) {
				unset($_GET['activate']);
			}

			$message = sprintf(
				/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
				esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', MELA_TD),
				'<strong>' . esc_html__('Master Addons for Elementor', MELA_TD) . '</strong>',
				'<strong>' . esc_html__('PHP', MELA_TD) . '</strong>',
				self::MINIMUM_PHP_VERSION
			);

			printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
		}
	}


	Master_Elementor_Addons::get_instance();
}
