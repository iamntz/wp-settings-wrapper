<?php

class Sample extends \Ntz\Utils\Settings {

    public function register_sections()
    {
        $this->register_section( 'Sample Section 1', "sample-1" );
        $this->register_section( 'Sample Section 2', "sample-2", array( $this, 'section_2_callback') );
    }

    public function section_2_callback( $section ){
      echo "ohai!";
    }


    public function register_fields(){
        $this->add_field(array(
            "section" => 'sample-1',
            "title"   => "Sample Field",
        ));


        $this->add_field(array(
            "section" => 'sample-2',
            "type"    => 'checkbox',
            "title"   => "Optional title",
            "name"    => 'sample-field-2',
            "label"   => 'consectetur adipisicing',
            "help"    => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quas, asperiores."
        ));


        $this->add_field(array(
            "section" => 'sample-2',
            "type"    => 'checkbox',
            "label"   => 'Quas, asperiores'
        ));
    }

}

new Sample(array(
    "settings_title" => "Awesome Options Wrapper",
    "namespace"      => "my-options",
));