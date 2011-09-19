<?php

/**
 * CustomPress_Core_Admin 
 * 
 * @uses CustomPress_Core
 * @copyright Incsub 2007-2011 {@link http://incsub.com}
 * @author Ivan Shaovchev (Incsub) {@link http://ivan.sh} 
 * @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
 */
class CustomPress_Core_Admin extends CustomPress_Core {

    /** @var array Avilable Post Types */
    var $post_types;
    /** @var array Avilable Taxonomies */
    var $taxonomies;
    /** @var array Avilable Custom Fields */
    var $custom_fields;
    /** @var boolean Flag whether the users have the ability to declair post type for their own blogs */
    var $enable_subsite_content_types = false;

    function CustomPress_Core_Admin() {
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
        add_action( 'network_admin_menu', array( &$this, 'network_admin_menu' ) );

        add_action( 'admin_print_styles-post.php', array( &$this, 'enqueue_custom_field_styles') );
        add_action( 'admin_print_styles-post-new.php', array( &$this, 'enqueue_custom_field_styles') );

		$this->init_vars();
    }

    /**
     * Initiate variables
     *
     * @return void
     */
    function init_vars() {
        $this->enable_subsite_content_types = apply_filters( 'enable_subsite_content_types', false );

        if ( $this->enable_subsite_content_types ) {
            $this->post_types    = get_option( 'ct_custom_post_types' );
            $this->taxonomies    = get_option( 'ct_custom_taxonomies' );
            $this->custom_fields = get_option( 'ct_custom_fields' );
        } else {
            $this->post_types    = get_site_option( 'ct_custom_post_types' );
            $this->taxonomies    = get_site_option( 'ct_custom_taxonomies' );
            $this->custom_fields = get_site_option( 'ct_custom_fields' );
        }

		if ( is_network_admin() ) {
            $this->post_types    = get_site_option( 'ct_custom_post_types' );
            $this->taxonomies    = get_site_option( 'ct_custom_taxonomies' );
            $this->custom_fields = get_site_option( 'ct_custom_fields' );
		}
    }

    /**
     * Register site admin menues.
	 *
	 * @access public
	 * @return void
     */
    function admin_menu() {
		if ( is_multisite() ) {
			$menu_slug = $this->enable_subsite_content_types ? 'ct_content_types' : 'cp_main';
			$menu_callback = $this->enable_subsite_content_types ? 'handle_content_types_page_requests' : 'handle_settings_page_requests'; 	
		} else {
			$menu_slug = 'ct_content_types';
			$menu_callback = 'handle_content_types_page_requests';
		}

        add_menu_page( __('CustomPress', $this->text_domain), __('CustomPress', $this->text_domain), 'activate_plugins', $menu_slug, array( &$this, $menu_callback ) );

		if ( $this->enable_subsite_content_types || !is_multisite() ) {
			$page_content_types = add_submenu_page( 'ct_content_types' , __( 'Content Types', $this->text_domain ), __( 'Content Types', $this->text_domain ), 'activate_plugins', 'ct_content_types', array( &$this, 'handle_content_types_page_requests' ) );

			add_action( 'admin_print_styles-' .  $page_content_types, array( &$this, 'enqueue_styles' ) );
			add_action( 'admin_print_scripts-' . $page_content_types, array( &$this, 'enqueue_scripts' ) );
		}

        $page_settings = add_submenu_page( $menu_slug, __('Settings', $this->text_domain), __('Settings', $this->text_domain), 'activate_plugins', 'cp_main', array( &$this, 'handle_settings_page_requests' ) );

        add_action( 'admin_print_scripts-' . $page_settings, array( &$this, 'enqueue_settings_scripts' ) );
        add_action( 'admin_head-' . $page_settings, array( &$this, 'ajax_actions' ) );
    }

	/**
	 * Register network admin menus. 
	 * 
	 * @access public
	 * @return void
	 */
	function network_admin_menu() {
        add_menu_page( __('CustomPress', $this->text_domain), __('CustomPress', $this->text_domain), 'manage_network', 'ct_content_types', array( &$this, 'handle_content_types_page_requests' ) );

        $page_content_types = add_submenu_page( 'ct_content_types' , __( 'Content Types', $this->text_domain ), __( 'Content Types', $this->text_domain ), 'manage_network', 'ct_content_types', array( &$this, 'handle_content_types_page_requests' ) );
        $page_settings      = add_submenu_page( 'ct_content_types', __('Settings', $this->text_domain), __('Settings', $this->text_domain), 'manage_network', 'cp_main', array( &$this, 'handle_settings_page_requests' ) );

        add_action( 'admin_print_styles-' .  $page_content_types, array( &$this, 'enqueue_styles' ) );
        add_action( 'admin_print_scripts-' . $page_content_types, array( &$this, 'enqueue_scripts' ) );
        add_action( 'admin_print_scripts-' . $page_settings, array( &$this, 'enqueue_settings_scripts' ) );
        add_action( 'admin_head-' . $page_settings, array( &$this, 'ajax_actions' ) );
	}

    /**
     * Load styles on plugin admin pages only.
     *
     * @return void
     */
    function enqueue_styles() {
        wp_enqueue_style( 'ct-admin-styles',
                           $this->plugin_url . 'ui-admin/css/styles.css');
    }

    /**
     * Load scripts on plugin specific admin pages only.
     *
     * @return void
     */
    function enqueue_scripts() {
        wp_enqueue_script( 'ct-admin-scripts',
                            $this->plugin_url . 'ui-admin/js/ct-scripts.js',
                            array( 'jquery' ) );
    }

    /**
     * Load scripts on plugin specific admin pages only.
     *
     * @return void
     */
    function enqueue_settings_scripts() {
        wp_enqueue_script( 'settings-admin-scripts',
                            $this->plugin_url . 'ui-admin/js/settings-scripts.js',
                            array( 'jquery' ) );
    }

    /**
     * Load styles for "Custom Fields" on add/edit post type pages only.
     *
     * @return void
     */
    function enqueue_custom_field_styles() {
        wp_enqueue_style( 'ct-admin-custom-field-styles',
                           $this->plugin_url . 'ui-admin/css/custom-fields-styles.css' );
    }
    /**
     * Handle $_GET and $_POST requests for Settings admin page.
	 *
	 * @return void
     */
    function handle_settings_page_requests() {
		// Save settings 
		if ( isset( $_POST['save'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'verify' ) ) {

			// Set network-wide content types 
			if ( is_multisite() && is_super_admin() && is_network_admin() ) {

				if ( !empty( $_POST['enable_subsite_content_types'] ) ) {
					update_site_option( 'allow_per_site_content_types', true );
					update_site_option( 'keep_network_content_types', (bool) $_POST['keep_network_content_types'] );
				}
				else {
					update_site_option( 'allow_per_site_content_types', false );
					update_site_option( 'keep_network_content_types', false );
				}

				// Create template file 
				if ( !empty( $_POST['post_type_file'] ) ) {
					$this->create_post_type_files( $_POST['post_type_file'] );
				}
			}

			// Process post types display 
			$args = array( 'page' => 'home', 'post_type' => ( isset( $_POST['post_type'] ) ) ? $_POST['post_type'] : null );
			$options = $this->get_options();
			$options = array_merge( $options , array( 'display_post_types' => array( $args['page'] => $args ) ) );
			update_option( $this->options_name, $options );
		}

		$this->render_admin('settings');
    }

    /**
     * Handle $_GET and $_POST requests for Content Types admin page.
     *
     * @return void
     */
    function handle_content_types_page_requests() {
            $this->render_admin('navigation');
            
            if ( empty( $_GET['ct_content_type'] ) || $_GET['ct_content_type'] == 'post_type' ) {
                if ( isset( $_GET['ct_add_post_type'] ) )
                    $this->render_admin('add-post-type');
                elseif ( isset( $_GET['ct_edit_post_type'] ) )
                    $this->render_admin('edit-post-type');
                else 
                    $this->render_admin('post-types');
            }
            elseif ( $_GET['ct_content_type'] == 'taxonomy' ) {
				if ( isset( $_GET['ct_add_taxonomy'] ) )
                    $this->render_admin('add-taxonomy');
				elseif ( isset( $_GET['ct_edit_taxonomy'] ) )
                    $this->render_admin('edit-taxonomy');
                else
                    $this->render_admin('taxonomies');
            }
            elseif ( $_GET['ct_content_type'] == 'custom_field' ) {
                if ( isset( $_GET['ct_add_custom_field'] ) )
                    $this->render_admin('add-custom-field');
                elseif ( isset( $_GET['ct_edit_custom_field'] ) )
                    $this->render_admin('edit-custom-field');
                else
                    $this->render_admin('custom-fields');
            }
    }

}

/* Initiate Admin Class */
new CustomPress_Core_Admin();

?>
