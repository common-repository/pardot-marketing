<?php
/**
 * Registers Elementor dynamic tags
 *
 * @package PardotMarketing
 * @since 1.0.0
 */

Class Elementor_Request_Var_Tag extends \Elementor\Core\DynamicTags\Tag {

	/**
	* Get Name
	*
	* Returns the Name of the tag
	*
	* @since 2.0.0
	* @access public
	*
	* @return string
	*/
	public function get_name() {
		return 'request-variable';
	}

	/**
	* Get Title
	*
	* Returns the title of the Tag
	*
	* @since 2.0.0
	* @access public
	*
	* @return string
	*/
	public function get_title() {
		return __( 'URL Parameter', 'pardotmarketing' );
	}

	/**
	* Get Group
	*
	* Returns the Group of the tag
	*
	* @since 2.0.0
	* @access public
	*
	* @return string
	*/
	public function get_group() {
		return 'request-variables';
	}

	/**
	* Get Categories
	*
	* Returns an array of tag categories
	*
	* @since 2.0.0
	* @access public
	*
	* @return array
	*/
	public function get_categories() {
		return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ];
	}

	/**
	* Register Controls
	*
	* Registers the Dynamic tag controls
	*
	* @since 2.0.0
	* @access protected
	*
	* @return void
	*/
	protected function _register_controls() {
		$this->add_control(
			'param_name',
			[
				'label' => __( 'Param Name', 'pardotmarketing' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);
	}

	/**
	* Render
	*
	* Prints out the value of the Dynamic tag
	*
	* @since 2.0.0
	* @access public
	*
	* @return void
	*/
	public function render() {
		$param_name = $this->get_settings( 'param_name' );

        	if ( ! $param_name ) {
			return;
    }

    if ( empty( $_REQUEST[ $param_name ] ) ) {
      return;
    }

		$value = $_REQUEST[ $param_name ];
		echo wp_kses_post( $value );
	}
}
