<?php 
class ET_Builder_Module_Breadcrumb extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Wc Breadcrumb', 'et_builder' );
		$this->slug       = 'et_pb_breadcrumb';
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'background_layout',
			'admin_label',
			'module_id',
			'module_class',
			'max_width',
			'max_width_tablet',
			'max_width_phone',
			'max_width_last_edited',
			'linkbread',
		);

		$this->fields_defaults = array(
			'background_layout' => array( 'light' ),
		);

		$this->main_css_element = '%%order_class%%';
		$this->advanced_options = array(
			'fonts' => array(
				'text'   => array(
					'label'    => esc_html__( 'Breadcrumb', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} nav.woocommerce-breadcrumb",
						'important' => 'all',
						
					),
					'font_size' => array('default' => '14px'),
                     'line_height' => array('default' => '1.5em'),
					 'color' => "{$this->main_css_element}.et_pb_text",
				),
			),
			'background' => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'border' => array(),
			'custom_margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
		);
	}

	function get_fields() {
		$fields = array(
		
		 'linkbread' => array(
				            'label'    => esc_html__( 'Breadcrumb Links Color', 'et_builder' ),
				            'type'     => 'color-alpha',
				            'custom_color'      => true,
				            'tab_slug' => 'advanced',
                    ),
			'background_layout' => array(
				'label'             => esc_html__( 'Text Color', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'light' => esc_html__( 'Dark', 'et_builder' ),
					'dark'  => esc_html__( 'Light', 'et_builder' ),
				),
				'description'       => esc_html__( 'Here you can choose the value of your text. If you are working with a dark background, then your text should be set to light. If you are working with a light background, then your text should be dark.', 'et_builder' ),
			),

			'max_width' => array(
				'label'           => esc_html__( 'Max Width', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'mobile_options'  => true,
				'tab_slug'        => 'advanced',
				'validate_unit'   => true,
			),
			'max_width_tablet' => array(
				'type'      => 'skip',
				'tab_slug'  => 'advanced',
			),
			'max_width_phone' => array(
				'type'      => 'skip',
				'tab_slug'  => 'advanced',
			),
			'max_width_last_edited' => array(
				'type'     => 'skip',
				'tab_slug' => 'advanced',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id            = $this->shortcode_atts['module_id'];
		$module_class         = $this->shortcode_atts['module_class'];
		$background_layout    = $this->shortcode_atts['background_layout'];
		$text_orientation     = $this->shortcode_atts['text_orientation'];
		$max_width            = $this->shortcode_atts['max_width'];
		$max_width_tablet     = $this->shortcode_atts['max_width_tablet'];
		$max_width_phone      = $this->shortcode_atts['max_width_phone'];
		$max_width_last_edited = $this->shortcode_atts['max_width_last_edited'];
		$linkbread				= $this->shortcode_atts['linkbread'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$this->shortcode_content = et_builder_replace_code_content_entities( $this->shortcode_content );

		if ( '' !== $max_width_tablet || '' !== $max_width_phone || '' !== $max_width ) {
			$max_width_responsive_active = et_pb_get_responsive_status( $max_width_last_edited );

			$max_width_values = array(
				'desktop' => $max_width,
				'tablet'  => $max_width_responsive_active ? $max_width_tablet : '',
				'phone'   => $max_width_responsive_active ? $max_width_phone : '',
			);

			et_pb_generate_responsive_css( $max_width_values, '%%order_class%%', 'max-width', $function_name );
		}

		if ( is_rtl() && 'left' === $text_orientation ) {
			$text_orientation = 'right';
		}
		
		if ( '' !== $linkbread ) {
			                    ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '.et_pb_module%%order_class%% .woocommerce-breadcrumb a',
				                'declaration' => sprintf(
					            'color: %1$s%2$s!important;',
					            esc_html( $linkbread ),
					            et_is_builder_plugin_active() ? ' !important' : ''
				                 ),
			                     ) );
		                        }

		$class = " et_pb_module et_pb_bg_layout_{$background_layout} et_pb_text_align_{$text_orientation}";
		                                /////////////////////TEMPLATE WOOCOMERCE PRICE/////////////////////////
                                  
                                ob_start();
                                woocommerce_breadcrumb();
                                $content = ob_get_clean();
                                  
                              /////////////////////TEMPLATE WOOCOMERCE PRICE/////////////////////////
                                  

		$output = sprintf(
  '<div%5$s class="%1$s%3$s%6$s">
                                        %2$s
                                    %4$s',
                                    'clearfix ',
                                    $content,
                                    esc_attr( $class ),
                                    '</div>',
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
		);

		return $output;
	}
}
new ET_Builder_Module_Breadcrumb;
?>