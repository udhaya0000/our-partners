<?php
/**
* Plugin Name:     Our Partners
* Description:     A plugin creates Partners custom post type
* version:         1.0.0
* Author:          Udhayakumar Sadagopan
* Author URI:      http://www.udhayakumars.com
**/


if (! defined('OP_VERSION')) {
    define('OP_VERSION', 1.0);
} // end if

class Partners
{

    /* --------------------------------------------
     * Attributes
     -------------------------------------------- */

    // Represents the nonce value used to save the post media
    private $nonce = 'wp_partners_nonce';
    private $singular_label = "Partner";
    private $plural_label = "Partners";

    /* --------------------------------------------
     * Constructor
     -------------------------------------------- */

    /**
     * Initializes localiztion, sets up JavaScript, and displays the meta box for saving the file
     * information.
     */
    public function __construct()
    {
        // Localization, Styles, and JavaScript
        add_action('admin_enqueue_scripts', array( $this, 'register_admin_scripts' ), 10, 1);

        // Setup the meta box hooks
        add_action('init', array($this, 'create_cpt'));
        add_action('save_post', array( $this, 'save_partners_meta' ));
        add_action('admin_enqueue_scripts', array($this, 'load_wp_media_files'));
        $this->add_meta_box();
    } // end construct

    public function load_wp_media_files()
    {
        wp_enqueue_media();
    }

    public function register_admin_scripts()
    {
        wp_enqueue_style('partners-meta-style', plugins_url('css/style.css', __FILE__), array(), OP_VERSION);

        wp_enqueue_script('partners-meta-js', plugins_url('js/index.js', __FILE__), array('jquery'), 'dfdf');
        wp_enqueue_script('partners-js', plugins_url('js/partners.js', __FILE__), array('jquery'), OP_VERSION);
        wp_localize_script(
         'partners-js',
         'partner_image',
         array(
           'title' => __('Choose or Upload Media'),
           'button' => __('Use this media'),
         )
       );
    } // end register_scripts

    /**
     * Introduces the file meta box for uploading the file to this post.
     */
    public function create_cpt()
    {
        $theme = "estpal";
        $singlur_label = "Partner";
        $plural_label = "Partners";

        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => _x($plural_label, 'Post Type General Name', $theme),
            'singular_name'       => _x($singlur_label, 'Post Type Singular Name', $theme),
            'menu_name'           => __($plural_label, $theme),
            'parent_item_colon'   => __('Parent '.$singlur_label, $theme),
            'all_items'           => __('All '.$plural_label, $theme),
            'view_item'           => __('View '.$singlur_label, $theme),
            'add_new_item'        => __('Add New '.$singlur_label, $theme),
            'add_new'             => __('Add New', $theme),
            'edit_item'           => __('Edit '.$singlur_label, $theme),
            'update_item'         => __('Update '.$singlur_label, $theme),
            'search_items'        => __('Search '.$singlur_label, $theme),
            'not_found'           => __('Not Found', $theme),
            'not_found_in_trash'  => __('Not found in Trash', $theme),
        );

        // Set other options for Custom Post Type

        $args = array(
            'label'               => __('partners', $theme),
            'description'         => __('List of '.$plural_label, $theme),
            'labels'              => $labels,
            // partners this CPT supports in Post Editor
            'supports'            => array('title', 'revisions'),
            /* A hierarchical CPT is like Pages and can have
            * Parent and child items. A non-hierarchical CPT
            * is like Posts.
            */
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 7,
            'menu_icon'           => 'dashicons-slides',
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'page',
        );

        // Registering your Custom Post Type
        register_post_type('partners', $args);
    } // add_file_meta_box

    public function add_meta_box()
    {
        add_action('cmb2_admin_init', array($this, 'register_metabox'));
    }

    /**
     * Hook in and add a metabox that only appears on the 'About' page
     */
    public function register_metabox()
    {
        $prefix = strtolower($this->singular_label).'_';

        $cmb_portfolio_page = new_cmb2_box([
         'id'           => $prefix . 'metabox',
         'title'        => esc_html__($this->singular_label.' Info', 'cmb2'),
         'object_types' => array( strtolower($this->plural_label) ), // Post type
         'context'      => 'normal',
         'priority'     => 'default',
         'show_names'   => true, // Show field names on the left
        ]);


      $cmb_portfolio_page->add_field( array(
      	'name' => 'Partner Logo',
      	'desc' => '',
      	'id'   => $prefix.'logo',
      	'type' => 'file',
      ) );
      $cmb_portfolio_page->add_field( array(
      	'name' => 'Partner URL',
      	'id'   => $prefix.'url',
      	'type' => 'text',
      ) );
    }

} // end class

$GLOBALS['partners'] = new Partners();
