<?php

/**
 * CustomPress_Core 
 * 
 * @copyright Incsub 2007-2011 {@link http://incsub.com}
 * @author Ivan Shaovchev (Incsub) {@link http://ivan.sh} 
 * @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
 */
class CustomPress_Core {

    /** @var string $plugin_version Plugin version */
    var $plugin_version = CP_VERSION;
    /** @var string $plugin_url Plugin URL */
    var $plugin_url = CP_PLUGIN_URL;
    /** @var string $plugin_dir Path to plugin directory */
    var $plugin_dir = CP_PLUGIN_DIR;
    /** @var string $text_domain The text domain for strings localization */
    var $text_domain = 'custompress';
    /** @var string $text_domain The text domain for strings localization */
    var $options_name = 'cp_options';

    function CustomPress_Core() {
        add_action( 'init', array( &$this, 'load_plugin_textdomain' ), 0 );
        add_filter( 'pre_get_posts', array( &$this, 'display_custom_post_types' ) );
        add_action( 'wp_ajax_cp_get_post_types', array( &$this, 'ajax_action_callback' ) );

        register_activation_hook( $this->plugin_dir . 'loader.php', array( &$this, 'plugin_activate' ) );
        register_deactivation_hook( $this->plugin_dir . 'loader.php', array( &$this, 'plugin_deactivate' ) );
        $plugin = plugin_basename(__FILE__);

        add_filter( "plugin_action_links_$plugin", array( &$this, 'plugin_settings_link' ) );
        add_filter( 'enable_subsite_content_types', array( &$this, 'enable_subsite_content_types' ) );
    }

    /**
     * Loads "custompress-[xx_XX].mo" language file from the "languages" directory
     *
     * @return void
     */
    function load_plugin_textdomain() {
        load_plugin_textdomain( $this->text_domain, null, 'custompress/launguages' );
    }

    /**
     * Plugin activation. 
     *
     * @return void
     */
    function plugin_activate() {}

    /**
     * Deactivate plugin. If $this->flush_plugin_data is set to "true"
     * all plugin data will be deleted
     *
     * @return void
     */
    function plugin_deactivate() {
        /* if true all plugin data will be deleted */
        if ( false ) {
            delete_option( $this->options_name );
            delete_option( 'ct_custom_post_types' );
            delete_option( 'ct_custom_taxonomies' );
            delete_option( 'ct_custom_fields' );
            delete_option( 'ct_flush_rewrite_rules' );
            delete_site_option( $this->options_name );
            delete_site_option( 'ct_custom_post_types' );
            delete_site_option( 'ct_custom_taxonomies' );
            delete_site_option( 'ct_custom_fields' );
            delete_site_option( 'ct_flush_rewrite_rules' );
        }
    }

    /**
     * Set Settings link for plugin.
     *
     * @param array $links
     * @return array
     */
    function plugin_settings_link( $links ) {
        $settings_link = '<a href="admin.php?page=cp_main">Settings</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    /**
     * Allow users to be able to register content types for their sites or
     * disallow it ( only super admin can add content types )
     *
     * @param <type> $bool
     * @return bool
     */
    function enable_subsite_content_types( $bool ) {
        $option = get_site_option('allow_per_site_content_types');

        if ( !empty( $option ) )
            return true;
        else
            return $bool;
    }

    /**
     * Display custom post types on home page.
     *
     * @todo Display custom post types on any page set by the user.
     *
     * @param object $query
     * @return object $query
     */
    function display_custom_post_types( $query ) {
        //if ( is_main_site() || get_site_option('allow_per_site_content_types') )
        $options = $this->get_options();

        if ( isset( $options['display_post_types']['home']['post_type'] )
          && is_array( $options['display_post_types']['home']['post_type'] ) ) {
            if ( is_home() && !in_array( 'default', $options['display_post_types']['home']['post_type'] ) )
                $query->set( 'post_type', $options['display_post_types']['home']['post_type'] );
        }

        return $query;

        /** @todo Display custom post types on any page set by the user.
        global $post;
        wp_reset_query();
        if ( is_home() && !in_array( 'default', $settings['home']['post_type'] ))
            query_posts( array( 'post_type' => $settings['home']['post_type'] ));
        elseif ( $settings[$post->post_name]['page'] == $post->post_name )
            query_posts( array( 'post_type' => $settings[$post->post_name]['post_type'] ));
         */
    }

    /**
     * Make AJAX POST request for getting the post type info associated with
     * a particular page.
     */
    function ajax_actions() { ?>
        <script type="text/javascript" >
            jQuery(document).ready(function($) {
                // bind event to function
                $(window).bind('load', handle_ajax_requests);
                //$('#cp-select-page').bind('change', cp_ajax_post_process_request)

                function handle_ajax_requests() {
                    // clear attributes
                    //$('.cp-main input[name="post_type[]"]').attr( 'checked', false );
                    // assign variables
                    var data = {
                        action: 'cp_get_post_types',
                        cp_ajax_page_name: 'home'
                        //@todo
                        //cp_ajax_page_name: $('#cp-select-page option:selected').val()
                    };
                    // make the post request and process the response
                    $.post(ajaxurl, data, function(response) {
                        $.each(response, function(i,item) {
                            if ( item != null ) {
                                $('.cp-main input[name="post_type[]"][value="' + item + '"]').attr( 'checked', true );
                            }
                        });
                    });
                }
            });
        </script> <?php
    }

    /**
     * Ajax callback which gets the post types associated with each page.
     *
     * @return JSON Encoded data
     */
    function ajax_action_callback() {
        $page_name = $_POST['cp_ajax_page_name'];
        $options = $this->get_options();
        if ( isset( $options['display_post_types'][$page_name]['post_type'] ) ) {
            /* json encode the response */
            $response = json_encode( $options['display_post_types'][$page_name]['post_type'] );
            /* response output */
            header( "Content-Type: application/json" );
            echo $response;
            die();
        } else {
            die();
        }
    }

    /**
     * Create a copy of the single.php file with the post type name added
     *
     * @param string $post_type
     */
    function create_post_type_files( $post_type ) {
        $file = TEMPLATEPATH . '/single.php';
        if ( !empty( $post_type ) ) {
            foreach ( $post_type as $post_type ) {
                $newfile = TEMPLATEPATH . '/single-' .  strtolower( $post_type ) . '.php';
                if ( !file_exists( $newfile )) {
                    if ( @copy( $file, $newfile ) ) {
                        chmod( $newfile, 0777 );
                    } else {
                        echo '<div class="error">Failed to copy ' .  $file . '. Please set your active theme folder permissions to 777.</div>';
                    }
                }
            }
        }
    }

    /**
     * Save plugin options.
     *
     * @param  array $params The $_POST array
     * @return die() if _wpnonce is not verified
     */
    function save_options( $params ) {
        if ( wp_verify_nonce( $params['_wpnonce'], 'verify' ) ) {
            /* Remove unwanted parameters */
            unset( $params['_wpnonce'], $params['_wp_http_referer'], $params['save'] );
            /* Update options by merging the old ones */
            $options = $this->get_options();
            $options = array_merge( $options, array( $params['key'] => $params ) );
            update_option( $this->options_name, $options );
        } else {
            die( __( 'Security check failed!', $this->text_domain ) );
        }
    }

    /**
     * Get plugin options.
     *
     * @param  string|NULL $key The key for that plugin option.
     * @return array $options Plugin options or empty array if no options are found
     */
    function get_options( $key = null ) {
        $options = get_option( $this->options_name );
        $options = is_array( $options ) ? $options : array();
        /* Check if specific plugin option is requested and return it */
        if ( isset( $key ) && array_key_exists( $key, $options ) )
            return $options[$key];
        else
            return $options;
    }

    /**
	 * Renders an admin section of display code.
	 *
	 * @param  string $name Name of the admin file(without extension)
	 * @param  string $vars Array of variable name=>value that is available to the display code(optional)
	 * @return void
	 */
    function render_admin( $name, $vars = array() ) {
		foreach ( $vars as $key => $val )
			$$key = $val;
		if ( file_exists( "{$this->plugin_dir}ui-admin/{$name}.php" ) )
			include "{$this->plugin_dir}ui-admin/{$name}.php";
		else
			echo "<p>Rendering of admin template {$this->plugin_dir}ui-admin/{$name}.php failed</p>";
	}
}

new CustomPress_Core();

?>
