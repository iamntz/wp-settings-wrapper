<?php 

namespace Ntz\Utils;

abstract class Settings {
    function __construct( $options = array() )
    {
        $this->options = array_merge( array(
            "settings_title" => "Sample Options",
            "access_level"   => "manage_options",
            "namespace"      => "sample-options"
        ), $options );

        add_action( 'admin_init', array( $this, 'settings_init' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );

        add_action( 'admin_enqueue_scripts', array( $this, 'register_assets' ) );
        add_action( "admin_print_scripts-settings_page_{$this->options['namespace']}", array( $this, 'load_assets' ) );
    }


    abstract public function register_sections();
    abstract public function register_fields();

    public function register_assets(){}
    public function load_assets(){}


    public function noop(){}


    public function admin_menu()
    {
        add_options_page(
            $this->options['settings_title'],
            $this->options['settings_title'],
            $this->options['access_level'],
            $this->options['namespace'],
            array( $this, 'options_page' )
        );
    }


    public function settings_init()
    {
        register_setting( $this->options['namespace'], $this->options['namespace'] );
        $this->register_sections();
        $this->register_fields();
    }


    public function register_section( $title, $section_name, $section_callback = null )
    {
        $section_callback = !$section_callback ? array( $this, 'noop' ) : $section_callback;
        add_settings_section( $section_name, $title, $section_callback, $this->options['namespace'] );
    }


    protected function add_field( $options )
    {
        $options = array_merge( array(
            "type"  => "text",
            "help"  => "",
            "label" => "",
            "title" => "",
            "attrs" => ""
         ), $options );

        if( !isset( $options['name'] ) ){
            $options['name'] = sanitize_title_with_dashes( !empty( $options['label'] ) ? $options['label'] : $options['title'] );
        }

        extract( $options );

        if( !method_exists( $this, $type ) ){
            $type = 'text';
        }
        add_settings_field( $name, $title,
            array( $this, $type ),
            $this->options['namespace'],
            $section,
            $options
        );
    }


    public function checkbox( $options )
    {
        extract( $options );
        printf( '<label><input type="checkbox" name="%1$s[%2$s]" value="1" %3$s %4$s /> %5$s</label>',
            $this->options['namespace'],
            $name,
            checked( $this->get_option( $name ), 1, false ),
            $attrs,
            $label
        );

        $this->show_help( $help );
    }


    public function text( $options )
    {
        extract( $options );
        if( !empty( $label ) ) {
            printf( '<label>%s</label><br>', $label );
        }
        printf( '<input type="text" name="%1$s[%2$s]" value="%3$s" class="regular-text" %4$s />',
            $this->options['namespace'],
            $name,
            isset( $value ) ? $value : esc_attr( $this->get_option( $name ) ),
            $attrs
        );

        $this->show_help( $help );
    }


    protected function show_help( $help )
    {
        if( !empty( $help ) ){
            printf( '<p class="description">%s</p>', $help );
        }
    }


    public function options_page()
    {
        ?>
        <div class="wrap">
            <h2><?php echo $this->options['settings_title'] ?></h2>
            <form action="options.php" method="post">
               <?php settings_fields( $this->options['namespace'] ); ?>
               <?php do_settings_sections( $this->options['namespace'] ); ?>
               <?php submit_button(); ?>
            </form>
        </div>

        <?php
    }


    public function get_option( $option_name )
    {
        $options = get_option( $this->options['namespace'] );
        return isset( $options[ $option_name ] ) ? $options[ $option_name ] : null;
    }
}