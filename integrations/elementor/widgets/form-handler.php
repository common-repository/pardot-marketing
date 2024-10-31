<?php
namespace PardotMarketing\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * @since 1.1.0
 */
class FormHandler extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'pardotmarketing-form-handler';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Pardot Form Handler', 'pardotmarketing' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'general' ];
	}

	public function get_style_depends() {
		$styles = apply_filters( 'pardotmarketing_form_handler_styles_filter', [ 'pardotmarketing-form-handler' ] );

		return $styles;
	}

	public function get_script_depends() {
		$scripts = apply_filters( 'pardotmarketing_form_handler_scripts_filter', [ 'pardotmarketing-jquery-validation' ] );

		return $scripts;
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.1.0
	 *
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'settings_section',
			[
				'label' => __( 'Settings', 'pardotmarketing' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'pardot_form_handler_settings',
			[
				'label' => __( 'Padot Form Handler Settings', 'pardotmarketing' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'error_location',
			[
				'label' => __( 'Form Handler "Error Location"', 'pardotmarketing' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( '<p>Be sure to set the "<strong>Error Location</strong>" for the Form Handler in Pardot to "<strong>Referring URL</strong>" or the URL where this form is located.</p>', 'pardotmarketing' ),
				'content_classes' => 'elementor-control-field-description'
			]
		);

		$this->add_control(
			'success_location',
			[
				'label' => __( 'Form Handler "Success Location"', 'pardotmarketing' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( '<p>To display the success message, set the <strong>Success Location</strong> to the URL where this form is located with a success parameter (e.g. ' . site_url() . '/form-location?success=1).</p>', 'pardotmarketing' ),
				'content_classes' => 'elementor-control-field-description'
			]
		);

		$this->add_control(
			'hr',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'endpoint',
			[
				'label' => __( 'Endpoint URL', 'pardotmarketing' ),
				'type' => \Elementor\Controls_Manager::URL,
				'show_external' => false,
				'description' => __( 'Copy & paste the Pardot Form Handler endpoint URL.', 'pardotmarketing' ),
			]
		);

		$this->add_control(
			'success_hide_form',
			[
				'label' => __( 'Hide Form on Success', 'pardotmarketing' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Hide', 'pardotmarketing' ),
				'label_off' => __( 'Show', 'pardotmarketing' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'description' => __( 'Hide the form when the user has successfully submitted & been redirected back to the form page.', 'pardotmarketing' )
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'fields_section',
			[
				'label' => __( 'Form Fields', 'pardotmarketing' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'key',
			array(
				'label'       => __( 'External Field Name', 'pardotmarketing' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'i.e. first_name', 'pardotmarketing' ),
				'description' => __( "Found of the form handler summary page under 'Form Field Mappings'", 'pardotmarketing' ),
				'conditions'  => array(
					'terms' => array(
						array(
							'name'     => 'type',
							'operator' => '!in',
							'value'    => array(
								'recaptcha',
							),
						),
					),
				),
			)
		);

		$repeater->add_control(
			'type',
			array(
				'label'   => __( 'Type', 'pardotmarketing' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'text',
				'options' => array(
					'text'      => __( 'Text', 'pardotmarketing' ),
					'textarea'  => __( 'Textarea', 'pardotmarketing' ),
					'email'     => __( 'Email', 'pardotmarketing' ),
					'url'       => __( 'URL', 'pardotmarketing' ),
					'tel'       => __( 'Telephone', 'pardotmarketing' ),
					'checkbox'  => __( 'Checkbox', 'pardotmarketing' ),
					'radio'     => __( 'Radio', 'pardotmarketing' ),
					'select'    => __( 'Select', 'pardotmarketing' ),
					'country'   => __( 'Country', 'pardotmarketing' ),
					'hidden'    => __( 'Hidden', 'pardotmarketing' ),
					'recaptcha' => __( 'reCAPTCHA v3', 'pardotmarketing' ),
				),
			)
		);

		$repeater->add_control(
			'label',
			array(
				'label'       => __( 'Label', 'pardotmarketing' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'i.e. First Name', 'pardotmarketing' ),
				'conditions'  => array(
					'terms' => array(
						array(
							'name'     => 'type',
							'operator' => '!in',
							'value'    => array(
								'hidden',
								'recaptcha',
							),
						),
					),
				),
			)
		);

		$repeater->add_control(
			'options',
			[
				'label' => __( 'Options', 'pardotmarketing' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'rows' => 5,
				'description' => __( 'Enter each option on a separate line. To differentiate between value and label, separate them with a pipe character ("|"). For example: f_name|First Name', 'pardotmarketing' ),
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => 'in',
							'value' => [
								'select', 'checkbox', 'radio'
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'placeholder',
			array(
				'label'       => __( 'Placeholder', 'pardotmarketing' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'i.e. Enter your first name', 'pardotmarketing' ),
				'conditions'  => array(
					'terms' => array(
						array(
							'name'     => 'type',
							'operator' => '!in',
							'value'    => array(
								'hidden',
								'checkbox',
								'select',
								'radio',
								'recaptcha',
							),
						),
					),
				),
			)
		);

		$repeater->add_control(
			'submitted_value',
			array(
				'label' => __( 'Submitted Value', 'pardotmarketing' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'name' => __( 'Full Name', 'pardotmarketing' ),
					'abbv' => __( '2-letter Abbr.', 'pardotmarketing' ),
				),
				'default' => 'abbv',
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'type',
							'operator' => 'in',
							'value'    => array( 'country' ),
						),
						array(
							'name'     => 'type',
							'operator' => '!in',
							'value'    => array(
								'recaptcha',
							),
						),
					),
				),
			)
		);

		$repeater->add_control(
			'value',
			array(
				'label'       => __( 'Default Value', 'pardotmarketing' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'i.e. John Doe', 'pardotmarketing' ),
				'dynamic'     => array(
					'active' => true,
				),
				'conditions'  => array(
					'terms' => array(
						array(
							'name'     => 'type',
							'operator' => '!in',
							'value'    => array(
								'recaptcha',
							),
						),
					),
				),
			)
		);

		$repeater->add_control(
			'inline',
			[
				'label' => __( 'Display Horizontally', 'pardotmarketing' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pardotmarketing' ),
				'label_off' => __( 'No', 'pardotmarketing' ),
				'return_value' => 'yes',
				'default' => 'no',
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => 'in',
							'value' => [
								'checkbox', 'radio'
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'rows',
			[
				'label' => __( 'Rows', 'pardotmarketing' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 100,
				'step' => 1,
				'default' => 4,
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => 'in',
							'value' => [
								'textarea',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'required',
			array(
				'label'        => __( 'Required', 'pardotmarketing' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'pardotmarketing' ),
				'label_off'    => __( 'No', 'pardotmarketing' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'conditions'   => array(
					'terms' => array(
						array(
							'name'     => 'type',
							'operator' => '!in',
							'value'    => array(
								'hidden',
								'recaptcha',
							),
						),
					),
				),
			)
		);

		$repeater->add_control(
			'minlength',
			[
				'label' => __( 'Min. Character Length', 'pardotmarketing' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'step' => 1,
				'default' => '',
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => 'in',
							'value' => [
								'text', 'tel', 'textarea'
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'maxlength',
			[
				'label' => __( 'Max. Character Length', 'pardotmarketing' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'step' => 1,
				'default' => '',
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => 'in',
							'value' => [
								'text', 'tel', 'textarea'
							],
						],
					],
				],
			]
		);

		$repeater->add_responsive_control(
			'width',
			array(
				'label'           => __( 'Column Width', 'pardotmarketing' ),
				'type'            => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'auto' => __( 'Default', 'pardotmarketing' ),
					'100%' => __( '100%', 'pardotmarketing' ),
					'80%'  => __( '80%', 'pardotmarketing' ),
					'75%'  => __( '75%', 'pardotmarketing' ),
					'66%'  => __( '66%', 'pardotmarketing' ),
					'60%'  => __( '60%', 'pardotmarketing' ),
					'50%'  => __( '50%', 'pardotmarketing' ),
					'40%'  => __( '40%', 'pardotmarketing' ),
					'33%'  => __( '33%', 'pardotmarketing' ),
					'25%'  => __( '25%', 'pardotmarketing' ),
					'20%'  => __( '20%', 'pardotmarketing' ),
				),
				'desktop_default' => 'auto',
				'tablet_default'  => 'auto',
				'mobile_default'  => 'auto',
				'devices'         => array(
					'desktop',
					'tablet',
					'mobile',
				),
				'selectors'       => array(
					'(desktop) {{WRAPPER}} {{CURRENT_ITEM}}' => 'width: {{VALUE}};',
					//'(tablet) {{WRAPPER}} {{CURRENT_ITEM}} .pardotmarketing-form-handler-field' => 'width: {{width_tablet.VALUE}};',
					//'(mobile) {{WRAPPER}} {{CURRENT_ITEM}} .pardotmarketing-form-handler-field' => 'width: {{width_mobile.VALUE}};',
				),
				'conditions'      => array(
					'terms' => array(
						array(
							'name'     => 'type',
							'operator' => '!in',
							'value'    => array(
								'hidden',
								'recaptcha',
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'fields',
			[
				'label' => __( 'Pardot Form Handler Fields', 'pardotmarketing' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ label }}}',
			]
		);

		$this->add_control(
			'show_label',
			array(
				'label'        => __( 'Label', 'pardotmarketing' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'pardotmarketing' ),
				'label_off'    => __( 'Hide', 'pardotmarketing' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'submit_section',
			[
				'label' => __( 'Submit Button', 'pardotmarketing' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'submit_text',
			[
				'label' => __( 'Text', 'pardotmarketing' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Submit', 'pardotmarketing' ),
			]
		);

		$this->add_control(
			'submit_text_processing',
			[
				'label' => __( 'Processing Text', 'pardotmarketing' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Processing...', 'pardotmarketing' ),
			]
		);

		$this->add_responsive_control(
			'submit_width',
			[
				'label' => __( 'Column Width', 'pardotmarketing' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'auto'  => __( 'Default', 'pardotmarketing' ),
					'100%' => __( '100%', 'pardotmarketing' ),
					'80%' => __( '80%', 'pardotmarketing' ),
					'75%' => __( '75%', 'pardotmarketing' ),
					'66%' => __( '66%', 'pardotmarketing' ),
					'60%' => __( '60%', 'pardotmarketing' ),
					'50%' => __( '50%', 'pardotmarketing' ),
					'40%' => __( '40%', 'pardotmarketing' ),
					'33%' => __( '33%', 'pardotmarketing' ),
					'25%' => __( '25%', 'pardotmarketing' ),
					'20%' => __( '20%', 'pardotmarketing' ),
				],
				'desktop_default' => 'auto',
				'tablet_default' => 'auto',
				'mobile_default' => 'auto',
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'selectors' => [
					'(desktop) {{WRAPPER}} .pardotmarketing-form-handler-field-submit' => 'width: {{VALUE}};',
					'(tablet) {{WRAPPER}} .pardotmarketing-form-handler-field-submit' => 'width: {{submit_width_tablet.VALUE}};',
					'(mobile) {{WRAPPER}} .pardotmarketing-form-handler-field-submit' => 'width: {{submit_width_mobile.VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'options_sections',
			[
				'label' => __( 'Additional Options', 'pardotmarketing' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'form_id',
			[
				'label' => __( 'Form ID', 'pardotmarketing' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'new_form_id', 'pardotmarketing' ),
				'description' => __( "Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows A-z 0-9 & underscore chars without spaces.", 'pardotmarketing' )
			]
		);

		$this->add_control(
			'custom_messages',
			[
				'label' => __( 'Custom Messages', 'pardotmarketing' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pardotmarketing' ),
				'label_off' => __( 'No', 'pardotmarketing' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_control(
			'success_message',
			[
				'label' => __( 'Success Message', 'pardotmarketing' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'default' => __( 'The form has been successfully submitted.', 'pardotmarketing' ),
				'conditions' => [
					'terms' => [
						[
							'name' => 'custom_messages',
							'operator' => 'in',
							'value' => [
								'yes',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'error_message',
			[
				'label' => __( 'Error Message', 'pardotmarketing' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'default' => __( 'The was a problem submitting the form.', 'pardotmarketing' ),
				'conditions' => [
					'terms' => [
						[
							'name' => 'custom_messages',
							'operator' => 'in',
							'value' => [
								'yes',
							],
						],
					],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'form_section',
			[
				'label' => __( 'Form', 'pardotmarketing' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'column_gap',
			[
				'label' => __( 'Column Gap', 'pardotmarketing' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .pardotmarketing-form-handler-field' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pardotmarketing-form-handler-fields' => 'margin-left: -{{SIZE}}{{UNIT}}; margin-right: -{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'rows_gap',
			[
				'label' => __( 'Rows Gap', 'pardotmarketing' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 10,
						'step' => 0.5,
					]
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .pardotmarketing-form-handler-field' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pardotmarketing-form-handler-fields' => 'margin-bottom: -{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'button_section',
			[
				'label' => __( 'Button', 'pardotmarketing' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'border',
				'label'    => __( 'Border', 'pardotmarketing' ),
				'selector' => '{{WRAPPER}} .pardotmarketing-form-handler-button',
			)
		);

		$this->add_control(
			'button_border_radius',
			[
				'label' => __( 'Border Radius', 'pardotmarketing' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors' => [
					'{{WRAPPER}} .pardotmarketing-form-handler-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_text_padding',
			[
				'label' => __( 'Text Padding', 'pardotmarketing' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors' => [
					'{{WRAPPER}} .pardotmarketing-form-handler-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs(
			'button_style_tabs'
		);

		$this->start_controls_tab(
			'style_normal_tab',
			[
				'label' => __( 'Normal', 'pardotmarketing' ),
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label' => __( 'Background Color', 'pardotmarketing' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .pardotmarketing-form-handler-button' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_txt_color',
			[
				'label' => __( 'Text Color', 'pardotmarketing' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .pardotmarketing-form-handler-button' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_hover_tab',
			[
				'label' => __( 'Hover', 'pardotmarketing' ),
			]
		);

		$this->add_control(
			'button_bg_color_hover',
			[
				'label' => __( 'Background Color', 'pardotmarketing' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .pardotmarketing-form-handler-button:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_txt_color_hover',
			[
				'label' => __( 'Text Color', 'pardotmarketing' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .pardotmarketing-form-handler-button:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.1.0
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( ! $settings['fields'] ) {
			return;
		}

		$settings['form_id'] = ! empty( $settings['form_id'] ) ? $settings['form_id'] : $this->get_id();
		echo pardotmarket_form_handler_form( $settings ); // @codingStandardsIgnoreLine
	}

	/**
	 * Render the widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.1.0
	 *
	 * @access protected
	 */
	protected function _content_template() {
		?>
		<div class="pardotmarketing-form-handler">


			<div class="pardotmarketing-form-handler-message pardotmarketing-form-handler-success">
				<# if ( settings.custom_messages == 'yes' && settings.success_message ) { #>
					<p>{{{ settings.success_message }}}</p>
				<# } else { #>
					<p>Your submission has been successfully sent.</p>
				<# } #>
			</div>

			<form
				class="pardotmarketing-form-handler-form"
				method="post"
				data-url="{{ settings.endpoint.url }}"
				id="{{ settings.form_id }}"
			>
				<div class="pardotmarketing-form-handler-message pardotmarketing-form-handler-error">
					<# if ( settings.custom_messages == 'yes' && settings.error_message ) { #>
						<p>{{{ settings.error_message }}}</p>
					<# } else { #>
						<p>There was a problem submitting the form. Please try again.</p>
					<# } #>
				</div>

				<div class="pardotmarketing-form-handler-fields">
					<# if ( settings.fields.length ) { #>
						<# _.each( settings.fields, function( field ) { #>
							<# if( field.type != 'hidden' ) { #>
								<div class="pardotmarketing-form-handler-field elementor-repeater-item-{{ field._id }}">
								<label class="pardotmarketing-form-handler-label" for="pardotmarketing-form-handler-{{ field._id }}">
									{{{ field.label }}}
								</label>
							<# } #>

							<# if( field.type == 'country' ) { #>
								<select
									name="{{ field.key }}"
									class="pardotmarketing-form-handler-select"
								>
									<# if( field.placeholder ) { #>
										<option value="">{{{ field.placeholder }}}</option>
									<# } #>
								</select>
							<# } #>

							<# if( field.type == 'text' || field.type == 'tel' || field.type == 'email' || field.type == 'hidden' ) { #>
								<input
									class="pardotmarketing-form-handler-input"
									type="{{ field.type }}"
									name="{{ field.key }}"
									id="pardotmarketing-form-handler-{{ field.key }}"
									<# if( field.minlength ) { #>
										minlength="{{ field.minlength }}"
									<# } #>
									<# if( field.maxlength ) { #>
										maxlength="{{ field.maxlength }}"
									<# } #>
									<# if( field.placeholder ) { #>
										placeholder="{{ field.placeholder }}"
									<# } #>
									<# if( field.value ) { #>
										value="{{ field.value }}"
									<# } #>
									<# if( field.required ) { #>
										required
									<# } #>
								/>
							<# } #>

							<# if( field.type == 'textarea' ) { #>
								<textarea
									rows="{{ field.rows }}"
									class="pardotmarketing-form-handler-input"
									name="{{ field.key }}"
									id="pardotmarketing-form-handler-{{ field.key }}"
									<# if( field.minlength ) { #>
										minlength="{{ field.minlength }}"
									<# } #>
									<# if( field.maxlength ) { #>
										maxlength="{{ field.maxlength }}"
									<# } #>
									<# if( field.placeholder ) { #>
										placeholder="{{ field.placeholder }}"
									<# } #>
									<# if( field.required ) { #>
										required
									<# } #>
								>{{{ field.value }}}</textarea>
							<# } #>

							<# if( field.type == 'select' ) { #>
								<select
									name="{{ field.key }}"
									class="pardotmarketing-form-handler-select"
									<# if( field.required ) { #>
										required
									<# } #>
								>
									<#
									var select_option = field.options.split("\n");
									#>
									<# _.each( select_option, function( opt ) { #>
										<#
										var option = opt.split("|");
										#>
										<option value="{{ option }}">{{{ option }}}</option>
									<# }); #>
								</select>
							<# } #>

							<# if( field.type == 'checkbox' ) { #>
								<#
								var options_classes = [ "pardotmarketing-form-handler-options" ];
								if ( field.inline == 'yes') {
									options_classes.push("pardotmarketing-form-handler-options-inline");
								}
								#>
								<div class="{{ options_classes.join() }}">
									<#
									var options = field.options.split("\n");
									var name = field.key;

									if ( options.length > 1 ) {
										name = name + "[]";
									}

									var option_count = 0;
									#>
									<# _.each( options, function( opt ) { #>
										<#
										option_count++;
										var option = opt.split("|");

										if ( option[1] ) {
											var value = option[0];
											var keyValue = option[1];
										} else {
											var value = option[0];
											var keyValue = option[0];
										}

										var option_id = 'pardotmarketing-form-handler-' + keyValue + '-' + option_count;
										#>
										<div class="pardotmarketing-form-handler-option">
											<input
												type="{{ field.type }}"
												value="{{ keyValue }}"
												id="{{ option_id }}"
												<# if( field.value == keyValue ) { #>checked="checked"<# } #>
												name="{{ name }}"
												class="pardotmarketing-form-handler-option-input"
											>
											<label for="{{ option_id }}">
												{{ keyValue }}
											</label>
										</div>
									<# }); #>
								</div>
							<# } #>

							<# if( field.type != 'hidden' ) { #>
								</div>
							<# } #>
						<# }); #>
						<div class="pardotmarketing-form-handler-field pardotmarketing-form-handler-field-submit">
							<button
								type="submit"
								class="pardotmarketing-form-handler-button"
							>{{{ settings.submit_text }}}</button>
						</div>
					<# } #>
				</div>
			</form>
		</div>
		<?php
	}
}
