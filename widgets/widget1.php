<?php
/**
 * Elementor oEmbed Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */

// Includes Custom Functions
require_once 'custom_functions.php';


class Elementor_CPT_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve oEmbed widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cpt_magic_tool';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve oEmbed widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'CPT Magic Tool', 'cpt-magic-tool' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve oEmbed widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'fa fa-code';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the oEmbed widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'general' ];
	}

	/**
	 * Register oEmbed widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'cpt-magic-tool' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
        );

        
		$acf_field_group = get_acf_groups();
		$acf = array();

		foreach($acf_field_group as $k){
			$acf[$k['ID']] = $k['post_title'];
		}

        $pods_cpt = get_pods_cpts();
		$pods = array();
		foreach($pods_cpt as $p){
			$pods[$p['ID']] = $p['post_title'];
		}

        $this->add_control(
			'field_type',
			[
				'label' => __( 'Field Type', 'cpt-magic-tool' ),
				'type' =>  \Elementor\Controls_Manager::SELECT2,
				'options' => [
					'acf' => __( 'ACF', 'cpt-magic-tool' ),
					'pods' => __( 'Pods', 'cpt-magic-tool' )	
                ],
                'multiple' => false,
                'default' => ''
			]
        );
        
        $this->add_control(
			'acf_field_group',
			[
				'label' => __( 'ACF Field Group', 'cpt-magic-tool' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => false,
                'options' => $acf,
                'default' => '',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_type',
                            'value' => 'acf'
                        ]
                    ]
                ]
			]
        );

		$this->add_control(
			'show_label',
			[
				'label' => __( 'Show Label', 'cpt-magic-tool' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'cpt-magic-tool' ),
				'label_off' => __( 'Hide', 'cpt-magic-tool' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
      
        $this->add_control(
			'pods_fields',
			[
				'label' => __( 'Pods Fields', 'cpt-magic-tool' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => false,
                'options' => $pods,
                'default' => '',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_type',
                            'value' => 'pods'
                        ]
                    ]
                ]
			]
		);

		$this->add_control(
			'field_name',
			[
				'label' => __( 'Field Name', 'cpt-magic-tool' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Type field name', 'cpt-magic-tool' ),
			]
		);

		$this->add_control(
			'post_id',
			[
				'label' => __( 'Post Id', 'cpt-magic-tool' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Type Id', 'cpt-magic-tool' ),
				'default' => get_the_ID(),
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render oEmbed widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();
		$label = $settings['show_label'] == 'yes' ? true : false;
		$field_name = $settings['field_name'];
		$field_type = $settings['field_type'];
		$post_id = $settings['post_id'];

		if($field_type == 'acf'){
			$group = $settings['acf_field_group'];
			$acf_field = get_acf_field_by_slug($group, $field_name);

			if($acf_field){
				$field = get_field_object($field_name, $post_id );
				$type = $field['type'];
				if($label){
					display($field_name,$type,$field, $post_id, 'acf', $field['label']);
				}else{
					display($field_name,$type, $field, $post_id, 'acf');
				}
			}else{
				echo 'Field doesn\'t exist!';
			}

		}else{
			$cpt = $settings['pods_fields'];
			$pods_field = get_pods_field_by_slug($cpt, $field_name);
			
			if($pods_field){
				$type = $pods_field['type'];
				$name = $pods_field['name'];
				if($label){
					display($field_name,$type,$pods_field, $post_id, 'pods', $pods_field['label']);
				}else{
					display($field_name,$type, $pods_field,  $post_id, 'pods');
				}
			}else{
				echo "Field Doesn't Exist!";
			}
			
		}
	}   

}