<?php
/**
 * Customizer settings for the OMD Assessments plugin
 *
 * @link [omd_assessments]
 * @since 1.0.0
 * @package OMD Assessments
 * @subpackage OMD/admin/customizer
 */

/**
 * Customizer settings
 *
 * @link [omd_assessments]
 * @since 1.0.0
 * @package OMD Assessments
 * @subpackage OMD/admin/customizer
 */
class OMD_Assessments_Customizer {
	/**
	 * The name of this Plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	protected $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version The current version of this plugin.
	 */
	protected $version;

	/**
	 * Option Prefix
	 *
	 * @since    1.0.0
	 * @access public
	 * @var string option prefix.
	 */
	public $prefix;


	/**
	 * Panels
	 *
	 * @since    1.0.0
	 * @access public
	 * @var array array of panel names and slugs to create
	 */
	public $panels;

	/**
	 * Sections
	 *
	 * @since    1.0.0
	 * @access public
	 * @var array array of sections (label, slug, panel) to create
	 */
	public $sections;

	/**
	 * Fields
	 * 'capability'
	 * 'choices'
	 * 'default'
	 * 'description'
	 * 'input_args'
	 * 'label'
	 * 'opt_type'
	 * 'priority'
	 * 'render_callback'
	 * 'section'
	 * 'selector'
	 * 'special'
	 * 'transport'
	 * 'type'
	 *
	 * @since    1.0.0
	 * @access public
	 * @var array array of fields (label, type, slug,)
	 */
	public $fields;


	/**
	 * Defaults
	 *
	 * @since    1.0.0
	 * @access public
	 * @var array array of default values.
	 */
	public $defaults;

	/**
	 * Roles
	 *
	 * @since    1.0.0
	 * @access public
	 * @var array array of roles
	 */
	public $roles;

	/**
	 * Custom Controls
	 *
	 * @since    1.0.0
	 * @access public
	 * @var array array of controls to register
	 */
	public $custom_controls;

	/**
	 * @access public
	 * @var [type] [type description]
	 */
	public $tools;

	/**
	 * Initialize the class and set its properties
	 *
	 * @since 1.0.0
	 * @param string $plugin_name  The name of this plugin
	 * @param string $version      The version of this plugin
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->prefix      = 'omda';
		$this->tools       = $this->get_tools();
		$this->defaults    = self::get_defaults();
		$this->panels      = $this->get_panels();
		$this->sections    = $this->get_sections();
		$this->fields      = $this->get_fields();

	}


	/**
	 * Initialize class id, which is used in panels, sections, and fields.
	 * @since 1.0.0
	 * @param [type]  [param description]
	 * @return [type] [return description]
	 */
	public function get_tools() {
		$tools = array(
			'mc'  => 'Managed Care Assessment',
			'vbr' => 'Value Based Reimbursement Readinesss Assessment'
		);

		$tools = apply_filters( 'omda_filter_customizer_available_tools', $tools );
		return $tools;
	}

	/**
	 * Register customizer functions for plugin.
	 *
	 * @method register_customizer
	 * @param  object $wp_customize wp_customize object.
	 */
	public function register_customizer( $wp_customize ) {
		if ( ! empty( $this->panels ) ) {
			$this->add_panels( $wp_customize );
		}

		if ( ! empty( $this->sections ) ) {
			$this->add_sections( $wp_customize );
		}

		if ( ! empty( $this->fields ) ) {
			$this->add_fields( $wp_customize );
		}
	}


	/**
	 * Get customizer Panels
	 * Note: we can edit get_'' functions
	 *
	 * @return  array  $panels to create.
	 */
	public function get_panels() {
		$panels = array(
			__( 'OPEN MINDS Assessment Settings', 'omda' ) => 'omda_panel',
		);
		return $panels;
	}

	/**
	 * Get Customizer Sections
	 *
	 * @return  array  $sections to create.
	 */
	public function get_sections() {
		foreach ( $this->tools as $id => $label ) {
			$sections[] = array(
				'label' => esc_html( $label ),
				'slug'  => esc_attr( $id ) . '_section',
				'panel' => 'omda_panel',
			);
		}
		return $sections;
	}

	/**
	 * Set up Customizer Panels
	 *
	 * @method add_panels
	 * @param  object $wp_customize object.
	 */
	public function add_panels( $wp_customize ) {
		$panels = $this->get_panels();

		foreach ( $panels as $label => $slug ) {
			$wp_customize->add_panel(
				$slug,
				array(
					'title'       => esc_html( $label ),
					'description' => '',
					'priority'    => 1,
				)
			);
		}
	}

	/**
	 * Add Customizer Sections
	 * Loops over $tools from get_tools().
	 *
	 * @method add_sections
	 * @param  object $wp_customize object.
	 */
	public function add_sections( $wp_customize ) {
		$sections = $this->get_sections();
		foreach ( $sections as $section ) {

			$wp_customize->add_section(
				$section['slug'],
				array(
					'title' => esc_attr( $section['label'] ),
					'panel' => $section['panel'],
				)
			);
		}
	}

	/**
	 * Customizer Fields
	 * $fields['id'] should directly match $defaults['id'] in get_defaults().
	 *
	 * @method get_fields
	 * @return  $fields
	 */
	public function get_fields() {
		$fields = array();

		foreach ( $this->tools as $id => $label ) {
			// Gravity Form ID for tool
			$fields[] = array(
				'id'          => esc_attr( $id ) . '_gform_id',
				'label'       => esc_html( $label ) . ' Gravity Form ID',
				'sanitize_cb' => 'sanitize_html',
				'section'     => esc_attr( $id ) . '_section',
				'type'        => 'text',
			);

			// Gravity View ID for tool
			$fields[] = array(
				'id'          => esc_attr( $id ) . '_gview_id',
				'label'       => esc_html( $label ) . ' Gravity View ID',
				'sanitize_cb' => 'sanitize_html',
				'section'     => esc_attr( $id ) . '_section',
				'type'        => 'text',
			);

			// Logo for Tool
			$fields[] = array(
				'id'          => esc_attr( $id ) . '_logo',
				'label'       => esc_html( $label . ' Tool Logo (for Print)' ),
				'sanitize_cb' => 'sanitize_image',
				'section'     => esc_attr( $id ) . '_section',
				'special'     => 'image',
			);
			// Define Overview Section Title
			$fields[] = array(
				'id'          => esc_attr( $id ) . '_overview_title',
				'label'       => esc_html( $label . ' Overview Title' ),
				'sanitize_cb' => 'sanitize_html',
				'section'     => esc_attr( $id ) . '_section',
				'type'        => 'textarea',
			);

			// Define Overview Section Body
			$fields[] = array(
				'id'          => esc_attr( $id ) . '_overview_body',
				'label'       => esc_html( $label . ' Overview Body' ),
				'sanitize_cb' => 'sanitize_html',
				'section'     => esc_attr( $id ) . '_section',
				'type'        => 'textarea',
			);
		}
		return $fields;
	}

	/**
	 * Get Defaults for Settings.
	 * $defaults['id'] should directly match $fields['id'] from get_fields().
	 *
	 * @method get_defaults
	 */
	public static function get_defaults() {
		// omda_managed_care
		// omda_vbr

		$logo_default = OMD_ASSESSMENTS_URL . 'public/assets/images/logos/Logo_Med_Horizontal.png';

		$strength_overview_body = 'The <i>OPEN MINDS</i> Financial Strength Calculator assesses overall financial strength from a lender\'s perspective and scores results according to bond ratings. The bond rating is based on an analysis of net margin, level of revenue to cover debt, the ratio of assets to liabilities and the days of cash on hand. For details on this tool, please see <a href="https://openminds.com/market-intelligence/editorials/conducting-an-organizational-financial-strength-assessment-why-how-what-to-do-next/" target="_blank">Conducting An Organizational Financial Strength Assessment — Why, How & What To Do Next.</a>';

		$defaults = array(
			'mc_gform_id'       => '',
			'mc_gview_id'       => '',
			'mc_logo'           => $logo_default,
			'mc_overview_title' => '',
			'mc_overview_body'  => '',
			'vbr_gform_id'       => '',
			'vbr_gview_id'       => '',
			'vbr_logo'           => $logo_default,
			'vbr_overview_title' => '',
			'vbr_overview_body'  => ''
		);

		$defaults = apply_filters( 'omda_filter_default_settings', $defaults );

		return $defaults;
	}

	/**
	 * Get default values
	 *
	 * @method get_defaults
	 * @param string $id setting id.
	 * @return mixed default value for setting.
	 */
	public function get_default( $id ) {
		$default = $this->defaults[ $id ] ?? '';

		return $default;
	}

	/**
	 * Add Fields to Customizer Sections
	 * DO NOT EDIT
	 *
	 * @method add_fields
	 * @param object $wp_customize object.
	 */
	public function add_fields( $wp_customize ) {
		$i = 0;
		$fields = $this->get_fields();

		foreach ( $fields as $field ) {
			$id = $this->prefix . '[' . $field['id'] . ']';
			$default = $this->get_default( $field['id'] ) ?? '';

			$capability      = $field['capability'] ?? 'edit_theme_options';
			$choices         = $field['choices'] ?? array();
			$description     = $field['description'] ?? '';
			$input_args      = $field['input_args'] ?? array();
			$label           = $field['label'] ?? '';
			$opt_type        = $field['opt_type'] ?? 'option';
			$priority        = $field['priority'] ?? '';
			$render_callback = $field['render_callback'] ?? false;
			$sanitize_cb     = $field['sanitize_cb'] ?? 'sanitize_html';
			$section         = $field['section'] ?? '';
			$selector        = $field['selector'] ?? array();
			$special         = $field['special'] ?? false; /* valid: image, color */
			$transport       = $field['transport'] ?? 'postMessage';
			$type            = $field['type'] ?? '';

			// Support custom setting controls
			$custom = $field['custom'] ?? false;

			$setting_args = array(
				'capability'        => $capability,
				'default'           => $default,
				'sanitize_callback' => array( $this, $sanitize_cb ),
				'transport'         => $transport,
				'type'              => $opt_type,
			);

			$control_args = array(
				'description' => $description,
				'label'       => $label,
				'priority'    => $priority,
				'section'     => $section,
				'settings'    => $id,
				'type'        => $type,
			);

			switch ( $type ) {
				case 'date':
				case 'email':
				case 'hidden':
				case 'number':
				case 'text':
				case 'url':
					$control_args['input_args'] = $input_args;
					break;

				case 'radio':
				case 'select':
					// LANG: maybe can fix here?
					$control_args['choices'] = $choices;
					break;

				case 'checkbox':
				case 'dropdown-pages':
				case 'textarea':
				default:
					$control_args = $control_args;
					break;
			}

			if ( $special ) :
				unset( $control_args[ 'type' ] );
				switch ( $special ) {
					case 'image':
						$wp_customize->add_setting( $id, $setting_args );
						$control_args[ 'sanitize_cb' ] = 'sanitize_image';

						$wp_customize->add_control(
							new WP_Customize_Image_Control(
								$wp_customize,
								$id,
								$control_args
							)
						);
						break;

					case 'color':
						$wp_customize->add_setting( $id, $setting_args );
						$control_args[ 'sanitize_cb' ] = 'sanitize_hex_color';

						$wp_customize->add_control(
							new WP_Customize_Color_Control(
								$wp_customize,
								$id,
								$control_args
							)
						);
						break;

					case 'heading':
						$wp_customize->add_setting( $id, $setting_args );
						$control_args[ 'sanitize_cb' ] = 'sanitize_html';

						$wp_customize->add_control(
							new IEX_Control_Heading(
								$wp_customize,
								$id,
								$control_args
							)
						);
				}
			else :
				$wp_customize->add_setting( $id, $setting_args );
				$wp_customize->add_control( $id, $control_args );
		endif;
			if ( isset( $wp_customize->selective_refresh ) && $render_callback ) {
				$wp_customize->selective_refresh->add_partial(
					$id,
					array(
						'selector'        => $selector,
						'render_callback' => $render_callback,
					)
				);
			}

			$i++;
		}
	}

	/**
	 * No-HTML sanitization callback
	 *
	 * - Sanitization: nohtml
	 * - Control: text, textarea, password
	 *
	 * Sanitization callback for 'nohtml' type text inputs. This callback sanitizes `$nohtml`
	 * to remove all HTML.
	 *
	 * @see wp_filter_nohtml_kses() https://developer.wordpress.org/reference/functions/wp_filter_nohtml_kses/
	 *
	 * @param string $nohtml The no-HTML content to sanitize.
	 * @return string Sanitized no-HTML content.
	 */
	public function sanitize_nohtml( $nohtml ) {
		return wp_filter_nohtml_kses( $nohtml );
	}

	/**
	 * Sanitize HTML
	 *
	 * @method sanitize_html
	 * @param  string $html input.
	 * @return string sanitized $html
	 */
	public function sanitize_html( $html ) {
		return stripslashes( wp_filter_post_kses( $html ) );
	}

	/**
	 * Checkbox sanitization callback example.
	 *
	 * Sanitization callback for 'checkbox' type controls. This callback sanitizes `$checked`
	 * as a boolean value, either TRUE or FALSE.
	 *
	 * @param bool $checked Whether the checkbox is checked.
	 * @return bool Whether the checkbox is checked.
	 */
	public function sanitize_checkbox( $checked ) {
		/* Boolean check. */
		return ( ( isset( $checked ) && true === $checked ) ? true : false );
	}

	/**
 * Sanitize the Multiple checkbox values.
 *
 * @param string $values Values.
 * @return array Checked values.
 */
	public function sanitize_multicheckbox( $values ) {
		$multi_values = ! is_array( $values ) ? explode( ',', $values ) : $values;

		return ! empty( $multi_values ) ? array_map( 'sanitize_text_field', $multi_values ) : array();
	}


	/**
	 * CSS sanitization callback
	 *
	 * - Sanitization: css
	 * - Control: text, textarea
	 *
	 * Sanitization callback for 'css' type textarea inputs. This callback sanitizes
	 * `$css` for valid CSS.
	 *
	 * @see wp_strip_all_tags() https://developer.wordpress.org/reference/functions/wp_strip_all_tags/
	 *
	 * @param string $css CSS to sanitize.
	 * @return string Sanitized CSS.
	 */
	public function sanitize_css( $css ) {
		return wp_strip_all_tags( $css );
	}


	/**
	 * Select sanitization callback
	 *
	 * - Sanitization: select
	 * - Control: select, radio
	 *
	 * Sanitization callback for 'select' and 'radio' type controls. This callback sanitizes `$input`
	 * as a slug, and then validates `$input` against the choices defined for the control.
	 *
	 * @see sanitize_key()               https://developer.wordpress.org/reference/functions/sanitize_key/
	 * @see $wp_customize->get_control() https://developer.wordpress.org/reference/classes/wp_customize_manager/get_control/
	 *
	 * @param string               $input   Slug to sanitize.
	 * @param WP_Customize_Setting $setting Setting instance.
	 * @return string Sanitized slug if it is a valid choice; otherwise, the setting default.
	 */
	public function sanitize_select( $input, $setting ) {
		/* Ensure input is a slug. */
		$input = sanitize_key( $input );
		/* Get list of choices from the control associated with the setting.*/
		$choices = $setting->manager->get_control( $setting->id )->choices;

		/* If the input is a valid key, return it; otherwise, return the default.*/
		return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
	}


	/**
	 * Image sanitization callback.
	 *
	 * Checks the image's file extension and mime type against a whitelist. If they're allowed,
	 * send back the filename, otherwise, return the setting default.
	 *
	 * - Sanitization: image file extension
	 * - Control: text, WP_Customize_Image_Control
	 *
	 * @see wp_check_filetype() https://developer.wordpress.org/reference/functions/wp_check_filetype/
	 *
	 * @param string               $image   Image filename.
	 * @param WP_Customize_Setting $setting Setting instance.
	 * @return string The image filename if the extension is allowed; otherwise, the setting default.
	 */
	public function sanitize_image( $image, $setting ) {
		/*
		 * Array of valid image file types.
		 *
		 * The array includes image mime types that are included in wp_get_mime_types()
		 */
		$mimes = array(
			'jpg|jpeg|jpe' => 'image/jpeg',
			'gif'          => 'image/gif',
			'png'          => 'image/png',
			'bmp'          => 'image/bmp',
			'tif|tiff'     => 'image/tiff',
			'ico'          => 'image/x-icon',
		);
		/* Return an array with file extension and mime_type. */
		$file = wp_check_filetype( $image, $mimes );

		/* If $image has a valid mime_type, return it; otherwise, return the default. */
		return ( $file['ext'] ? $image : $setting->default );
	}


	/**
	 * [sanitize_url description]
	 *
	 * @method sanitize_url
	 * @param  string $url unescaped url.
	 * @return string $url escaped url.
	 */
	public function sanitize_url( $url ) {
		return esc_url_raw( $url );
	}


	/**
	 * Sanitize Numbers
	 *
	 * @method sanitize_number_absint
	 * @param  number $number unsanitized number.
	 * @param  string $setting setting id.
	 * @return number sanitized $number.
	 */
	public function sanitize_number( $number, $setting ) {
		/* Ensure $number is an absolute integer (whole number, zero or greater).*/
		$number = absint( $number );

		/* If the input is an absolute integer, return it; otherwise, return the default */
		return ( $number ? $number : $setting->default );
	}
}
