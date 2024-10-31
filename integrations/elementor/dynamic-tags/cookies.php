<?php
Class Elementor_Cookies_Tag extends \Elementor\Core\DynamicTags\Tag {

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
		return 'cookie';
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
		return __( 'Browser Cookie', 'pardotmarketing' );
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
				'label' => __( 'Cookie Name', 'pardotmarketing' ),
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

    if ( empty( $_COOKIE[ $param_name ] ) ) {
      return;
    }

		echo wp_kses_post( $_COOKIE[ $param_name ] );
	}
}
