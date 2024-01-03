<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://openminds.com
 * @since      1.0.0
 *
 * @package    Omd_Assessments
 * @subpackage Omd_Assessments/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Omd_Assessments
 * @subpackage Omd_Assessments/public
 * @author     Billy Fischabch
 */
class Omd_Assessments_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name  = $plugin_name;
		$this->version      = $version;
		$this->options      = get_option( 'omda', array() );

		$this->register_shortcodes();

		/**
		 * Assessment metadata
		 * 1. Managed Care metadata
		 * 2. VBR metadata
		 */
		$this->mc_metadata   = $this->get_assessment_meta();
	}


	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Omd_Assessments_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Omd_Assessments_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/omd-assessments-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Omd_Assessments_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Omd_Assessments_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/src/js/omd-assessments-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script('chart-js', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js');
	}


	/**
	 * TODO: fold into single function
	 * This function registers the shortcodes for Managed Care and VBR tools.
	 *
	 * @return [string] [return description]
	 * @since 1.0.0
	 */
	public function register_shortcodes() {
		add_shortcode( 'omda-mc-tool', array( $this, 'get_managed_care_tool' ) );
		add_shortcode( 'omda-vbr-tool', array( $this, 'get_vbr_tool' ) );

		// Output as part of a larger shortcode
		add_shortcode( 'omda-submissions', array( $this, 'get_tool_submissions' ) );
		add_shortcode( 'omda-nav', array( $this, 'get_tool_navigation' ) );
		add_shortcode( 'omda-form', array( $this, 'get_tool_form' ) );

	}

	public function get_logged_out_user_info( $tool = '' ) {

		$output = 'HOLLA';
		$output = apply_filters( 'omd_assessments_filter_logged_out_content', $output, $tool );

		echo $output;
	}

	/**
	 * This function grabs the templates for the Managed Care assessment.
	 * @return [string] template partial
	 * @since 1.0.0
	 */
	public function get_managed_care_tool() {

		if( ! is_user_logged_in() ) {
			$this->get_logged_out_user_info( 'managed_care' );
			return;
		}
		$page_name = '';

		if ( isset( $_GET['page_type'] ) ) {
			$page_name = filter_input( INPUT_GET, 'page_type', FILTER_SANITIZE_STRING );
		}

		switch( $page_name ) {
			case 'form':
				$partial = OMD_ASSESSMENTS_DIR . 'templates/managed-care/form.php';
				break;

			case 'report':
				$partial = OMD_ASSESSMENTS_DIR . 'templates/managed-care/report.php';
				break;

			case 'submissions':
			default:
				$partial = OMD_ASSESSMENTS_DIR . 'templates/managed-care/submissions.php';
				break;
		}

		ob_start();
		include $partial;
		return ob_get_clean();
	}

	/**
	 * This function grabs the templates for the VBR Readiness assessment.
	 * @return [string] template partial
	 * @since 1.0.0
	 */
	public function get_vbr_tool() {

		if( ! is_user_logged_in() ) {
			$this->get_logged_out_user_info();
			return;
		}

		$page_name = '';

		if ( isset( $_GET['page_type'] ) ) {
			$page_name = filter_input( INPUT_GET, 'page_type', FILTER_SANITIZE_STRING );
		}

		switch( $page_name ) {
			case 'form':
				$partial = OMD_ASSESSMENTS_DIR . 'templates/vbr/form.php';
				break;

			case 'report':
				$partial = OMD_ASSESSMENTS_DIR . 'templates/vbr/report.php';
				break;

			case 'submissions':
			default:
				$partial = OMD_ASSESSMENTS_DIR . 'templates/vbr/submissions.php';
				break;
		}

		ob_start();
		include $partial;
		return ob_get_clean();
	}

	/**
	 * Assessment Navigation Shortcode
	 *
	 * @param   [type] $atts     [$atts description]
	 * @param   array  $content  [$content description]
	 * @return  [type]           [return description]
	 */
	public function get_tool_navigation( $atts = array(), $content = null ) {
		$page_name = '';

		if ( isset( $_GET['page_type'] ) ) {
			$page_name = filter_input( INPUT_GET, 'page_type', FILTER_SANITIZE_STRING );
		}

		$defaults = array(
			'subheading'     => '',
			'page_name'      => $page_name,
			'linkto'         => 'submissions',
			'button_txt'     => 'Back',
			'year'           => '',
			'show_print_btn' => ''
		);

		/**
		 * 1. parse defaults with passed attributes
		 * 2. assemble module navigation
		 */
		$atts           = wp_parse_args( $atts, $defaults );
		$nav_subheading = $atts['subheading'] ?? '';
		$show_print     = $atts['show_print_btn'] ?? false;
		$company        = $atts['company'] ?? false;
		$year           = $atts['year'] ?? false;
		$linkto         = $atts['linkto'] ?? '';
		$button_txt     = $atts['button_txt'] ?? '';

		if ( $company && $year ) {
			$subheading = $company . ' ' . $year;
		} else {
			$subheading = $nav_subheading;
		}

		$nav_markup = sprintf(
			'<section class="ft-header">
				<nav class="ft-nav">
					<h3 class="header-subtitle">%1$s</h3>
					<form class="nav-form" method="get">
						<button name="page_type" value="%2$s" class="btn btn-primary ft-nav-button">%3$s</button>
					</form>
				</nav>
			</section>',
			$subheading,             // 1
			esc_attr( $linkto ),     // 2
			esc_html( $button_txt ), // 3
		);

		$allowed = array(
			'nav'    => array(
				'class' => array(),
				'id'    => array(),
			),
			'h2'     => array(
				'class' => array(),
			),
			'form'   => array(
				'class'  => array(),
				'id'     => array(),
				'method' => array(),
			),
			'button' => array(
				'name'  => array(),
				'value' => array(),
				'class' => array(),
				'id'    => array(),
			),
			'div'    => array(
				'class' => array(),
				'id'    => array(),
			),
		);

		echo wp_kses( $nav_markup, $allowed );
	}


	/**
	 * Assessment Gravity Form Shortcode
	 *
	 * Logic
	 * 1. grab attributes passed into shortcode
	 * 2. save $atts abbreviation as lookup value for form_id
	 * 3. generate Gravity Form shortcode
	 * 4. echo Gravity Form
	 *
	 * Notes
	 * abbrev is passed via shortcode attributes
	 * Gravity Form ID's are stored in the customizer
	 *
	 * @param   array  $atts  [attributes passed into shortcode]
	 * @return  [type]        [return description]
	 */
	public function get_tool_form( $atts = array() ) {
		$defaults       = OMD_Assessments_Customizer::get_defaults();
		$settings       = get_option( 'omda', array() );
		$atts           = wp_parse_args( $atts, $defaults );
		$abbrev         = $atts['abbrev'] ?? '';
		$form_id        = $settings[$abbrev . '_gform_id'] ?? $defaults[$abbrev . '_gform_id'];
		$form_shortcode = sprintf(
			'[gravityform id="%1$s" title="false" description="false" ajax="true"]',
			$form_id
		);

		if ( shortcode_exists( 'gravityform' ) ) {
			$section_markup = sprintf(
				'
				<section>
					%1$s
				</section>
				',
				do_shortcode( $form_shortcode ) // 1
			);
			echo $section_markup;
		}
	}


	/**
	 * Assessment Submissions List Shortcode
	 *
	 * Logic
	 * 1. grab attributes passed into shortcode
	 * 2. save $atts abbreviation as lookup value for form_id
	 * 3. get all gform entries for assessment via form_id
	 * 4. get Gravity View ID
	 * 5. filter entries by current user ID
	 * 6. store all current user entries in submissions array
	 * 7. echo submissions list
	 *
	 * Notes
	 * abbrev is passed via shortcode attributes
	 * Gravity Form ID's are stored in the customizer
	 * Gravity View ID's are stored in the customizer
	 *
	 * @param   array  $atts  [attributes passed into shortcode]
	 * @return  [type]        [return description]
	 */
	public function get_tool_submissions( $atts = array() ) {
		$defaults       = OMD_Assessments_Customizer::get_defaults();
		$settings       = get_option( 'omda', array() );
		$atts           = wp_parse_args( $atts, $defaults );
		$abbrev         = $atts['abbrev'] ?? '';

		$form_id           = $settings[$abbrev . '_gform_id'] ?? $defaults[$abbrev . '_gform_id'];
		$gview_id          = $settings[$abbrev . '_gview_id'] ?? $defaults[$abbrev . '_gview_id'];
		$results           = GFAPI::get_entries( $form_id );
		$user_id           = get_current_user_id();
		$submissions_array = array();

		/**
		 * for each entry,
		 * if the user's ID matches the user ID in the form entry,
		 * save that entry into $submissions_array via year & entry_id
		 */
		foreach ( $results as $key => $entry ) {
			$form_entry_user_id = $entry['created_by'];
			$form_entry_user_id = (int)$form_entry_user_id;
			$entry_id           = $entry['id'];

			if ( $user_id === $form_entry_user_id ) {
				$submissions_array[] = array(
					'year'     => date( 'M j, Y', strtotime( $entry['date_created'] ) ),
					'entry_id' => $entry_id,
					'user'     => $user_id,
					'form'     => $form_id,
				);
			}
		}

		/**
		 * Markup generation
		 *
		 * Logic
		 * if the user has no submissions, echo placeholder
		 * else,
		 *   1. create unordered list markup
		 *   2. iterate through submissions array
		 *   3. if Gravity View ID is populated, generate shortcode
		 *   4. print out individual submission row
		 *
		 */
		if ( empty ( $submissions_array ) ) {
			echo '<p>No assessments have been completed.</p>';

		} else {
			echo '
			<section>
				<h4>Submissions</h4>
				<ul class="omda-submissions-list">
					<li class="submission-list-item submission-list-header">
						<p class="submission-content">Completed Submission Date</p>
					</li>
			';

			foreach ( $submissions_array as $key => $value ) {

				$gview_link = '';

				if ( '' !== $gview_id ) {
					$gview_shortcode = sprintf(
						'[gv_entry_link action="edit" return="url" entry_id="%1$s" view_id="%2$s"]Edit[/gv_entry_link]',
						$entry_id, // 1
						$gview_id, // 2
					);

					$gview_link = sprintf(
						'<a href="%1$s" class="submission-link">edit</a>',
						do_shortcode( $gview_shortcode ),
					);
				}

				$row = sprintf(
					'<li class="submission-list-item">
						<p class="submission-content">%1$s</p>

						<form method="GET" class="submission-form">
							<input type="hidden" name="entry_id" value="%2$s" />
							<button name="page_type" value="report" class="submission-button">view</button>
						</form>
					</li>',
					$value['year'],     // 1
					$value['entry_id'], // 2
					$gview_link         // 3 disabled until edit submission hook is completed
				);
				echo $row;
			}

			echo '
				</ul>
			</section>
			';
		}
	}


	/**
	 * Manged Care Gravity Form Submission hook
	 *
	 * Logic
	 * 1. Grab Gravity Form entry ID
	 * 2. Store entry answers into associative array
	 * 3. Calculate scoring, both by section and overall
	 * 4. Aggregate answer and scoring data, encode into JSON
	 * 4. add to wp_usermeta, using submission ID as row name
	 *
	 * Notes
	 * $answers is organized by section with each question numbered, leading
	 * to the following:
	 *   1. this negates the issue of GForm fields having fixed ID's
	 *   2. when iterating through metadata object, answer value lookup
	 *      in $entry_answers has a time complexity of O(n)
	 *
	 * @param   array  $entry [gravity form submission array]
	 */
	public function mc_submission( $entry ) {
		/**
		 * $submission_id is the Gravity Form entry ID
		 * $section_scores is used to sum all answer scores in an entry, for each section
		 * $score_total is used to sum all answer scores in an entry
		 * $score_denominator is the total number of questions in the assessment, multipled by 4
		 */
		$submission_id     = $entry['id'];
		$score_denominator = 0;
		$score_total       = 0;
		$section_scores    = array();
		$entry_answers     = array(
			'a' => array(
				'a1'  => floatval( $entry['1'] ),
				'a2'  => floatval( $entry['2'] ),
				'a3'  => floatval( $entry['3'] ),
				'a4'  => floatval( $entry['4'] ),
				'a5'  => floatval( $entry['46'] ),
				'a6'  => floatval( $entry['47'] ),
				'a7'  => floatval( $entry['48'] ),
				'a8'  => floatval( $entry['49'] ),
				'a9'  => floatval( $entry['50'] ),
				'a10' => floatval( $entry['51'] ),
				'a11' => floatval( $entry['52'] ),
				'a12' => floatval( $entry['53'] ),
				'a13' => floatval( $entry['54'] ),
				'a14' => floatval( $entry['55'] ),
			),
			'b' => array(
				'b1' => floatval( $entry['5'] ),
				'b2' => floatval( $entry['6'] ),
				'b3' => floatval( $entry['7'] ),
				'b4' => floatval( $entry['8'] ),
			),
			'c' => array(
				'c1' => floatval( $entry['9'] ),
				'c2' => floatval( $entry['10'] ),
				'c3' => floatval( $entry['11'] ),
			),
			'd' => array(
				'd1' => floatval( $entry['38'] ),
				'd2' => floatval( $entry['39'] ),
				'd3' => floatval( $entry['40'] ),
				'd4' => floatval( $entry['41'] ),
				'd5' => floatval( $entry['42'] ),
				'd6' => floatval( $entry['43'] ),
				'd7' => floatval( $entry['44'] ),
				'd8' => floatval( $entry['45'] ),
			),
			'e' => array(
				'e1'  => floatval( $entry['12'] ),
				'e2'  => floatval( $entry['13'] ),
				'e3'  => floatval( $entry['14'] ),
				'e4'  => floatval( $entry['15'] ),
				'e5'  => floatval( $entry['16'] ),
				'e6'  => floatval( $entry['17'] ),
				'e7'  => floatval( $entry['19'] ),
				'e8'  => floatval( $entry['18'] ),
				'e9'  => floatval( $entry['20'] ),
				'e10' => floatval( $entry['21'] ),
				'e11' => floatval( $entry['22'] ),
				'e12' => floatval( $entry['23'] ),
				'e13' => floatval( $entry['24'] ),
				'e14' => floatval( $entry['25'] ),
				'e15' => floatval( $entry['26'] ),
				'e16' => floatval( $entry['27'] ),
				'e17' => floatval( $entry['28'] ),
				'e18' => floatval( $entry['29'] ),
				'e19' => floatval( $entry['30'] ),
				'e20' => floatval( $entry['31'] ),
				'e21' => floatval( $entry['32'] ),
				'e22' => floatval( $entry['33'] ),
				'e23' => floatval( $entry['34'] ),
				'e24' => floatval( $entry['56'] ),
				'e25' => floatval( $entry['57'] ),
				'e26' => floatval( $entry['58'] ),
				'e27' => floatval( $entry['59'] ),
				'e28' => floatval( $entry['60'] ),
			),
			'f' => array(
				'f1' => floatval( $entry['35'] ),
				'f2' => floatval( $entry['36'] ),
				'f3' => floatval( $entry['37'] ),
			)
		);

		/**
		 * For each section in $entry_answers,
		 *   1. Calculate section total, mapping key value pairs exactly like $entry_answers
		 *   2. Sum total answer scores
		 *   3. Sum total number of questions
		 * Once answers and scores are aggregated in $entry_data, we'll calculate the overall score
		 *
		 * Note: we multiply individual questions and answers by 4 due to how scoring is determined
		 * by Casey and Ken.
		 */
		foreach ( $entry_answers as $key => $section ) {
			$section_scores[$key] = floatval( ( array_sum( $entry_answers[$key] ) / ( count( $entry_answers[$key] ) * 4 )  ) * 100 );
			$score_total         += floatval( array_sum( $entry_answers[$key] ) );
			$score_denominator   += floatval( count( $section ) * 4 );
		};

		$entry_data = array(
			'entry_answers'  => $entry_answers,
			'section_scores' => $section_scores,
			'overall_score'  => floatval( ( $score_total / $score_denominator ) * 100 ),
		);
		$entry_data = wp_json_encode( $entry_data );

		add_user_meta( get_current_user_id(), $submission_id, $entry_data );
	}

	/**
	 * VBR Gravity Form Submission hook
	 *
	 * Logic
	 * 1. Grab Gravity Form entry ID
	 * 2. Store entry answers into associative array
	 * 3. add to wp_usermeta, using submission ID as row name
	 *
	 * Notes
	 * this logic exactly mirrors managed_care_submission.
	 *
	 * @param   array  $entry [gravity form submission array]
	 */
	public function vbr_submission( $entry ) {
		$submission_id = $entry['id'];
		$score_denominator = 0;
		$score_total       = 0;
		$section_scores    = array();
		$entry_answers     = array(
			'a' => array(
				'a1'  => floatval( $entry['21'] ),
				'a2'  => floatval( $entry['28'] ),
				'a3'  => floatval( $entry['30'] ),
				'a4'  => floatval( $entry['32'] ),
				'a5'  => floatval( $entry['359'] ),
				'a6'  => floatval( $entry['36'] ),
				'a7'  => floatval( $entry['39'] ),
				'a8'  => floatval( $entry['45'] ),
				'a9'  => floatval( $entry['390'] ),
				'a10' => floatval( $entry['391'] )
			),
			'b' => array(
				'b1'  => floatval( $entry['68'] ),
				'b2'  => floatval( $entry['72'] ),
				'b3'  => floatval( $entry['74'] ),
				'b4'  => floatval( $entry['76'] ),
				'b5'  => floatval( $entry['78'] ),
				'b6'  => floatval( $entry['85'] ),
				'b7'  => floatval( $entry['87'] ),
				'b8'  => floatval( $entry['89'] ),
				'b9'  => floatval( $entry['91'] ),
				'b10' => floatval( $entry['60'] )
			),
			'c' => array(
				'c1'  => floatval( $entry['387'] ),
				'c2'  => floatval( $entry['101'] ),
				'c3'  => floatval( $entry['105'] ),
				'c4'  => floatval( $entry['110'] ),
				'c5'  => floatval( $entry['121'] ),
				'c6'  => floatval( $entry['125'] ),
				'c7'  => floatval( $entry['132'] ),
				'c8'  => floatval( $entry['134'] ),
				'c9'  => floatval( $entry['154'] ),
				'c10' => floatval( $entry['177'] )
			),
			'd' => array(
				'd1'  => floatval( $entry['196'] ),
				'd2'  => floatval( $entry['198'] ),
				'd3'  => floatval( $entry['203'] ),
				'd4'  => floatval( $entry['205'] ),
				'd5'  => floatval( $entry['360'] ),
				'd6'  => floatval( $entry['209'] ),
				'd7'  => floatval( $entry['211'] ),
				'd8'  => floatval( $entry['213'] ),
				'd9'  => floatval( $entry['257'] ),
				'd10' => floatval( $entry['222'] )
			),
			'e' => array(
				'e1'  => floatval( $entry['228'] ),
				'e2'  => floatval( $entry['240'] ),
				'e3'  => floatval( $entry['259'] ),
				'e4'  => floatval( $entry['264'] ),
				'e5'  => floatval( $entry['266'] ),
				'e6'  => floatval( $entry['268'] ),
				'e7'  => floatval( $entry['272'] ),
				'e8'  => floatval( $entry['275'] ),
				'e9'  => floatval( $entry['279'] ),
				'e10' => floatval( $entry['290'] )
			),
			'f' => array(
				'f1'  => floatval( $entry['311'] ),
				'f2'  => floatval( $entry['313'] ),
				'f3'  => floatval( $entry['315'] ),
				'f4'  => floatval( $entry['319'] ),
				'f5'  => floatval( $entry['323'] ),
				'f6'  => floatval( $entry['330'] ),
				'f7'  => floatval( $entry['339'] ),
				'f8'  => floatval( $entry['343'] ),
				'f9'  => floatval( $entry['345'] ),
				'f10' => floatval( $entry['349'] )
			),
		);

		foreach ( $entry_answers as $key => $section ) {
			$section_scores[$key] = floatval( ( array_sum( $entry_answers[$key] ) / ( count( $entry_answers[$key] ) * 4 )  ) * 100 );
			$score_total         += floatval( array_sum( $entry_answers[$key] ) );
			$score_denominator   += floatval( count( $section ) * 4 );
		};

		$entry_data = array(
			'entry_answers'  => $entry_answers,
			'section_scores' => $section_scores,
			'overall_score'  => floatval( ( $score_total / $score_denominator ) * 100 ),
		);
		$entry_data = wp_json_encode( $entry_data );

		add_user_meta( get_current_user_id(), $submission_id, $entry_data );
	}


	public function get_assessment_meta() {

		$metadata       = array(
			'sections' => array(
				'Clinical Operations'                  => array(
					'section_id' => 'a',
					'count'      => 14,
					'benchmark'  => 44.9,
					'questions'  => array(
						array(
							'name'      => 'Managed Care Clinical Operations',
							'answer_id' => null,
						),
						array(
							'name'            => '1. You have a dedicated care management role established in your organization',
							'answer_id'       => 'a1',
							'recommendations' => 'A dedicated program care manager should be identified and trained to provide routine customer focused interface with health plan care managers providing a single point of contact with the program for client inquiries, updates and document requests.',
						),
						array(
							'name'            => '2. You have a program protocol for participating in continued stay reviews with health plan care managers',
							'answer_id'       => 'a2',
							'recommendations' => 'A protocol for continued stay reviews should be developed that outlines the transfer of clinical information to validate client continued stay (medical necessity) in treatment including: client treatment update, client improvement, client decompensation, barriers to meeting treatment goals, treatment non-adherence, relapse prevention plan, status of the discharge plan and target discharge date.',
						),
						array(
							'name'            => '3. You have an active relapse prevention and client/family education program for clients with a history of frequent hospital readmissions and/or poor treatment outcomes in outpatient and community-based settings',
							'answer_id'       => 'a3',
							'recommendations' => 'Health plan care managers expect treatment providers to have a relapse prevention program that provides client intervention and education while in treatment to reduce the incidence of premature termination of treatment and to improve outpatient treatment outcomes.',
						),
						array(
							'name'            => '4. You have an active relationship in a network of mental health and related health care community-based social supports for client linkage and access to social determinants',
							'answer_id'       => 'a4',
							'recommendations' => 'To reduce the incidence of relapse and to improve client recovery and resiliency in the community, discharge plans should include active linkage, not just referral, to essential social supports to ensure timely client access and connection to supportive services, i.e. support groups, caregiver respite, child care, transportation, employment, housing, etc.',
						),
						array(
							'name'      => 'Quality Management',
							'answer_id' => null,
						),
						array(
							'name'            => '5. You have a quality management (QM) program',
							'answer_id'       => 'a5',
							'recommendations' => 'To succeed in a managed care environment, providers will need to track a number of critical outcome and process measures, and then quickly address any variance using quality management and improvement tools such as root cause analysis, process improvement, etc.',
						),
						array(
							'name'            => '6. Your QM program includes a monthly review of clinical documentation, including:',
							'answer_id'       => 'a6',
							'recommendations' => 'In a managed care environment, it is critical that clinical documentation is timely, complete and accurate. Errors in any of these areas can result in delays in payment, or in the worst case scenario, no payment for services that have been provided, but that do not have the correct documentation.',
						),
						array(
							'name'            => '7. Clinical managers review the results of documentation QM reviews with staff',
							'answer_id'       => 'a7',
							'recommendations' => 'Timely, accurate and complete documentation should be an expectation for all clinical staff. Clinical managers need to review staff documentation on a regular basis, and implement corrective action plans when documentation does not meet standards.',
						),
						array(
							'name'            => '8. MCO quality expectations are incorporated into your QM program',
							'answer_id'       => 'a8',
							'recommendations' => 'All MCO quality expectations must be incorporated into your QM program. Results should be reviewed by Senior Management on a regular basis, and any negative variance must be addressed immediately.',
						),
						array(
							'name'            => '9. Staff have been trained to understand HEDIS measures and their value to MCOs',
							'answer_id'       => 'a9',
							'recommendations' => 'All MCO quality expectations, including HEDIS measures, must be incorporated into your QM program. A critical component of your QM program should include training on HEDIS measures for both clinical leadership and clinicians.',
						),
						array(
							'name'            => '10. Your program maintains records of MCO appeals and suggests strategies to clinicians and utiliation review staff for improving relationships and/or modify service delivery to reduce denials',
							'answer_id'       => 'a9',
							'recommendations' => 'It is critical to maintain records for MCO appeals that identify the root cause for the issue and corrective action taken, and strategies to work effectively with the MCOs.',
						),
						array(
							'name'      => 'Compliance Management',
							'answer_id' => null,
						),
						array(
							'name'            => '11. You know the key laws and regulations that impact your organization',
							'answer_id'       => 'a11',
							'recommendations' => 'Every organization needs to have Compliance Plan that includes a designated compliance officer, policies and procedures that are reviewed and updated at least annually, a training program for new staff and ongoing training for all staff. The Board is updated on the Compliance Plan and the status of compliance activities at least annually.',
						),
						array(
							'name'            => '12. You have a formal compliance program, including a compliance plan',
							'answer_id'       => 'a12',
							'recommendations' => 'Every organization needs to have Compliance Plan that includes a designated compliance officer, policies and procedures that are reviewed and updated at least annually, a training program for new staff and ongoing training for all staff. The Board is updated on the Compliance Plan and the status of compliance activities at least annually.',
						),
						array(
							'name'            => '13. You have a designated compliance officer',
							'answer_id'       => 'a13',
							'recommendations' => 'Every organization needs to have Compliance Plan that includes a designated compliance officer, policies and procedures that are reviewed and updated at least annually, a training program for new staff and ongoing training for all staff. The Board is updated on the Compliance Plan and the status of compliance activities at least annually.',
						),
						array(
							'name'            => '14. You continually train your staff, leadership, and board on compliance and risk management issues',
							'answer_id'       => 'a14',
							'recommendations' => 'Every organization needs to have Compliance Plan that includes a designated compliance officer, policies and procedures that are reviewed and updated at least annually, a training program for new staff and ongoing training for all staff. The Board is updated on the Compliance Plan and the status of compliance activities at least annually.',
						),
					)
				),
				'Customer Focus'                       => array(
					'section_id' => 'b',
					'count'      => 4,
					'benchmark'  => 46.0,
					'questions'  => array(
						array(
							'name'      => 'Customer Centric Intake & Admissions',
							'answer_id' => null,
						),
						array(
							'name'            => '1. You have dedicated and trained intake and admissions staff to provide a high quality telephonic or face-to-face engagement experience for referral sources, clients and families',
							'answer_id'       => 'b1',
							'recommendations' => 'Trained intake and admission staff who are customer focused and motivated to "screen in" new clients by overcoming barriers to accessing treatment at the time of the initial referral encounter will maximize the opportunity for high admission rates and low no show rates to support financially viable patient volume and caseloads.',
						),
						array(
							'name'            => '2. You have tracking and measurement systems in place for intake and admission data',
							'answer_id'       => 'b2',
							'recommendations' => 'As consumer choice is becoming increasingly relevant in the selection of a treatment provider, organizations should track and measure admission and no show rates to identify intake staff that have higher conversion rates due to good customer service and effective client engagement and intervene with lower performing staff providing further training, supervision and/or change in department protocols.',
						),
						array(
							'name'            => '3. You have tracking and reporting systems in place to track and analyze intake calls and admission rates by referral source',
							'answer_id'       => 'b3',
							'recommendations' => 'Marketing and business development staff will need to know this information to make sure that marketing plans and targeted referral development outreach is effective in generating new referrals.',
						),
						array(
							'name'            => '4. You have a centralized intake and admission department to manage inquiry calls from new referrals',
							'answer_id'       => 'b4',
							'recommendations' => 'Centralized intake and admission functions are necessary to ensure that properly trained staff are engaging new clients and that the department is staffed appropriately to handle high call volumes and reduce long wait times on hold and lost calls.',
						)
					)
				),
				'Network Management & Marketing'       => array(
					'section_id' => 'c',
					'count'      => 3,
					'benchmark'  => 19.6,
					'questions'  => array(
						array(
							'name'      => 'Managed Care Marketing',
							'answer_id' => null,
						),
						array(
							'name'            => '1. You have a dedicated marketing and business development team',
							'answer_id'       => 'c1',
							'recommendations' => 'Health plan and managed care business requires a variety of analysis and outreach functions to ensure targeted contract and referral development. Functions should include: strategic planning, market positioning, competitive analysis, and focused outreach in the community done by trained and dedicated marketing staff that create and drive new referrals to the provider organization.',
						),
						array(
							'name'            => '2. You conduct referral development planning that includes cultivation of defined referral networks that refer appropriate clients with the proper treatment and funding profile to your organization',
							'answer_id'       => 'c2',
							'recommendations' => 'Intake and admission departments can be overwhelmed and handicapped by managing referral calls that are not appropriate clinically or lack the appropriate funding source i.e. insurance coverage, for admission into your program, resulting in lower rates of admission.',
						),
						array(
							'name'            => '3. You have an account manager in the marketing department to manage and ensure payer and referral source customer satisfaction',
							'answer_id'       => 'c3',
							'recommendations' => 'Routine contact with key health plan and referral source contacts is vital to maintaining strong business relationships and ensuring customer satisfaction with provider services. Contacts can include: health plan representatives, i.e. provider relations, contract manager, network manager, crisis or call center supervisor; as well as key referral sources, i.e. hospital discharge planners, hospital ED social workers, payer crisis or call center supervisor, school counselors, local medical groups / PCPs, etc.',
						)
					)
				),
				'Technology & Data Management'         => array(
					'section_id' => 'd',
					'count'      => 8,
					'benchmark'  => 43.5,
					'questions'  => array(
						array(
							'name'      => 'Information Technology Systems',
							'answer_id' => null,
						),
						array(
							'name'            => '1. You have a commercial billing software product',
							'answer_id'       => 'd1',
							'recommendations' => 'If you do not have a commercial billing product, you will be doing all billing functions manually, which will increase chances of errors, slow down the billing process, and negatively impact your cash flow.',
						),
						array(
							'name'            => '2. You have an electronic health record (EHR)',
							'answer_id'       => 'd2',
							'recommendations' => 'The EHR is a critical source of information that you will need for effective billing.',
						),
						array(
							'name'            => '3. Your billing software interfaces with your EHR and general ledger',
							'answer_id'       => 'd3',
							'recommendations' => 'If your billing software does not interface with your EHR and general ledger, you will need to collect some information manually, which will increase chances of errors, slow down the billing process, and negatively impact your cash flow.',
						),
						array(
							'name'      => 'Metrics Management',
							'answer_id' => null,
						),
						array(
							'name'            => '4. You use strategic key performance indicators (KPIs) to track organizational performance',
							'answer_id'       => 'd4',
							'recommendations' => 'Key performance indicators link strategy to operating results. The educate staff on what is important and what they need to do to impact service quality and organizational sustainability.',
						),
						array(
							'name'            => '5. You have at least one individual responsible for metrics analysis and reporting',
							'answer_id'       => 'd5',
							'recommendations' => 'Diffuse accountability can result in multiple measures for the same item, no measures for some key items, and a lack of ability to quickly implement new needed measures.',
						),
						array(
							'name'            => '6. You are utilizing specialized software applications to support your metrics management',
							'answer_id'       => 'd6',
							'recommendations' => 'Relying on manual systems for metrics management is costly, inconsistent, and much more error-prone.',
						),
						array(
							'name'            => '7. You are documenting the clinical impact of your services',
							'answer_id'       => 'd7',
							'recommendations' => 'In today\'s environment, providers must be able to demonstrate the clinical impact of services delivered, rather than relying on vague claims about longevity, commitment to quality, etc.',
						),
						array(
							'name'            => '8. You are able to deliver to MCOs the outcomes and related measures that they use to define success',
							'answer_id'       => 'd8',
							'recommendations' => 'In a managed care environment it is critical that are you can demonstrate proof of performance, i.e., that you are tracking measures that are important to the payer: access, length of stay, outcomes, etc.',
						),
					)
				),
				'Financial & Revenue Cycle Management' => array(
					'section_id' => 'e',
					'count'      => 28,
					'benchmark'  => 43.6,
					'questions'  => array(
						array(
							'name'      => 'Revenue Cycle Management Admissions',
							'answer_id' => null,
						),
						array(
							'name'            => '1. Client registration procedure and checklist to capture all relevant demographic information',
							'answer_id'       => 'e1',
							'recommendations' => 'Client demographic information can be important for clinical care, accurate billing, compliance, proof of performance, and quality assurance reporting.',
						),
						array(
							'name'            => '2. Insurance verification policy, including verification of ongoing coverage (minimum monthly)',
							'answer_id'       => 'e2',
							'recommendations' => 'If you do not verify that the client\'s insurance is active, you run the risk of not being paid for services provided.',
						),
						array(
							'name'            => '3. Eligibility verification',
							'answer_id'       => 'e3',
							'recommendations' => 'If eligibility is not verified, your organization runs the risk of providing services to a client who is not eligible for those services. If this occurs, you will have to contact the payer after the fact to determine eligibility. In the best case scenario, it will be determined that the client is indeed eligible for services, and you should be able to bill for those services. In the worst case scenario, it will be determined that the client is not eligible for services, and you will not be able to bill for the services provided.',
						),
						array(
							'name'            => '4. Benefits verification',
							'answer_id'       => 'e4',
							'recommendations' => 'If benefits do not include your services, your organization runs the risk of providing services to a client that are not paid for by the plan.',
						),
						array(
							'name'            => '5. Authorization',
							'answer_id'       => 'e5',
							'recommendations' => 'It is critical for you to know if there any types of authorization processes for services provided, the criteria for authorizations, and the appeal process. If you do not follow the authorization process, you run the risk of not being paid for services delivered.',
						),
						array(
							'name'            => '6. Required clinician credentials',
							'answer_id'       => 'e6',
							'recommendations' => 'Services must be provided by clinicians with the credentials as specified by the health plan, or they will not be paid for.',
						),
						array(
							'name'            => '7. Coordination of benefits',
							'answer_id'       => 'e7',
							'recommendations' => 'Many insurance policies have strict guidelines about how benefits must be coordinated if a client has more than one insurer.',
						),
						array(
							'name'            => '8. Self-pay fee assessment and collection policy',
							'answer_id'       => 'e8',
							'recommendations' => 'Many insurance policies have strict guidelines about collection of payment owed by the client (co-pays, deductibles). It is also important that you have policies to ensure consistent practices for payment and collection of the full amount for self-pay clients.',
						),
						array(
							'name'      => 'Revenue Cycle Management Billing',
							'answer_id' => null,
						),
						array(
							'name'            => '9. You submit billings electronically',
							'answer_id'       => 'e9',
							'recommendations' => 'Many insurers expect bills to be submitted electronically, usually in 837P format.',
						),
						array(
							'name'            => '10. You have the following policies and procedures in place',
							'answer_id'       => 'e10',
							'recommendations' => 'Comprehensive policies and procedures ensure that all critical tasks are identified and assigned to staff members. For billing policies and procedures to be effective, staff must trained, documented must be accessible, compliance must be monitored, there should be a continuous process for review and updates.',
						),
						array(
							'name'            => '11. Your billing system has all required data fields for client registration',
							'answer_id'       => 'e11',
							'recommendations' => 'If your system does not have all required data fields for client registration, you will have to enter this information manually which is costly, with a greater chance for errors.',
						),
						array(
							'name'            => '12. Your billing system can easily add new fields (e.g. names of new payers)',
							'answer_id'       => 'e12',
							'recommendations' => 'Configurability and scalability are two key features to consider with an EHR. Configurability will create efficiency to adapt the technology to the most efficient workflows and facilitate payer changes. Scalability will enable your organization to add and bill more new services with a minimum of additional workforce.',
						),
						array(
							'name'            => '13. Quality assurance (QA) is performed on data entry for 100% of client registrations',
							'answer_id'       => 'e13',
							'recommendations' => 'It is critical to ensure that all information is correct at the beginning of the process.',
						),
						array(
							'name'            => '14. All services are entered into the billing system within 24 hours of service',
							'answer_id'       => 'e14',
							'recommendations' => 'The longer the lag time between service provision and billing, the greater the negative impact on cash flow and greater potential impact on compliance',
						),
						array(
							'name'            => '15. QA checks are completed for correct coding',
							'answer_id'       => 'e15',
							'recommendations' => 'The correct code is critical for quality of documentation, and to ensure that your organization is billing for the correct amount for each service.',
						),
						array(
							'name'            => '16. Payers are billed on a weekly basis',
							'answer_id'       => 'e16',
							'recommendations' =>'Timely billing improves your cash flow.',
						),
						array(
							'name'            => '17. Billings are edited prior to submission to find and fix any errors',
							'answer_id'       => 'e17',
							'recommendations' => 'Any errors in billings could results in the claim being denied.',
						),
						array(
							'name'            => '18. You have reports for common billing problems, including',
							'answer_id'       => 'e18',
							'recommendations' => 'Reports for common billing errors are essential for targeting needed quality improvement initiatives.',
						),
						array(
							'name'            => '19. Reports on key billing performance metrics',
							'answer_id'       => 'e19',
							'recommendations' => 'This is a key element of reports on billing performance.',
						),
						array(
							'name'      => 'Revenue Cycle Management Collections',
							'answer_id' => null,
						),
						array(
							'name'            => '20. You have implemented the following billing practices to improve collections',
							'answer_id'       => 'e20',
							'recommendations' => 'This is a best practice for effective collection.',
						),
						array(
							'name'            => '21. You have the following policies and procedures in place',
							'answer_id'       => 'e21',
							'recommendations' => 'This is a best practice for effective collection.',
						),
						array(
							'name'            => '22. You track the following metrics',
							'answer_id'       => 'e22',
							'recommendations' => 'This is a metric for effective collection.',
						),
						array(
							'name'            => '23. You track all reasons for claims denial, including',
							'answer_id'       => 'e23',
							'recommendations' => 'It is critical to track any reasons for claims denial, and then initiate a quality improvement process to address the root cause for the issue, and take corrective action.',
						),
						array(
							'name'      => 'Margin Management',
							'answer_id' => null,
						),
						array(
							'name'            => '24. You know the unit cost for each of your programs/services',
							'answer_id'       => 'e24',
							'recommendations' => 'Knowing your unit cost is critical for success in a managed care environment.',
						),
						array(
							'name'            => '25. Unit cost and target margin reports are sent to program/service managers on a frequent basis',
							'answer_id'       => 'e25',
							'recommendations' => 'Managers need frequent access to this information so that they can manage unit costs, and ensure that costs are not exceeding established targets.',
						),
						array(
							'name'            => '26. Clinical staff know productivity requirements and receive performance data regularly',
							'answer_id'       => 'e26',
							'recommendations' => 'It is essential that staff and program managers know the productivity targets that are required for each program to achieve either its break even target or target margin, as appropriate.',
						),
						array(
							'name'            => '27. Program/service manager\'s ability to achieve target margins is reflected in yearly performance appraisals',
							'answer_id'       => 'e27',
							'recommendations' => 'It is critical that accountable managers’ performance is monitored on a regular basis.',
						),
						array(
							'name'            => '28. Program/service managers believe that managing costs and ensuring quality are both critical components of their jobs',
							'answer_id'       => 'e28',
							'recommendations' => 'Managers need training to give them the necessary skills to both manage costs and ensure quality.',
						)
					)
				),
				'Leadership & Strategy'                => array(
					'section_id' => 'f',
					'count'      => 3,
					'benchmark'  => 33.8,
					'questions'  => array(
						array(
							'name'      => 'Leadership & Human Resources',
							'answer_id' => null,
						),
						array(
							'name'            => '1. Your Senior Management understands managed care, supports what is necessary to succeed in managed care, is willing to ask the hard questions, and supports accountability for performance',
							'answer_id'       => 'f1',
							'recommendations' => 'If your Senior Management does not understand managed care, support what is necessary to succeed in managed care, is willing to ask the hard questions, and supports accountability for performance it will be very difficult for your organization to succeed in a managed care environment.',
						),
						array(
							'name'            => '2. Your care managers understand the basic principles of managed behavioral health care as well the care coordination priorities and expectations of health plan care managers',
							'answer_id'       => 'f2',
							'recommendations' => 'If program care managers have not participated in the coordination of client care with health plan care managers in the past, it is important that they understand the routine communication, treatment and discharge planning goals of those health plan care managers.',
						),
						array(
							'name'            => '3. There is a clear linkage of performance with compensation in your organizations',
							'answer_id'       => 'f3',
							'recommendations' => 'Implementing the processes and systems needed to succeed in a managed care payment system often involves major changes for an organization. Some organizations have implemented have linked improved performance with rewards such as bonuses tied to productivity and contractual outcomes when possible.',
						)
					)
				)
			)
		);
		$this->vbr_metadata         = array(
			'sections' => array(
				'Provider Network Management'                             => array(
					'section_id' => 'a',
					'count'      => 10,
					'benchmark'  => 39,
					'questions'  => array(
						array(
							'name'      => 'Provider Network Management & Credentialing',
							'answer_id' => null,
						),
						array(
							'name'            => '1. Qualified accreditation in care coordination, health home or serving medically complex consumers (i.e. CARF, COA, TJC)?',
							'answer_id'       => 'a1',
							'recommendations' => 'As provider organizations enter into risk-based contracts, obtaining industry recognized accreditation establishes credibility to carry out these functions. Payers will use such accreditations as key criteria to screen potential candidates.',
						),
						array(
							'name'            => '2. System to research, document and implement credentialing requirements of all payers?',
							'answer_id'       => 'a2',
							'recommendations' => 'Each payer may have different rules for provider credentialing.  For example, a payer may allow a provider organization to be responsible to credential their own providers (subdelegatged credentialing) while another payer may require the same provider organization to have each provider get credentialing directly through the payer.',
						),
						array(
							'name'            => '3. Efficient workflow to obtain, review, and manage credentialing information for all clinicians, care managers and care coordinators?',
							'answer_id'       => 'a3',
							'recommendations' => 'Establishing and maintaining a single provider database to maintain all provider credentialing, contracting, rate information, and network participation status will be critical to streamline workflows, gain efficiencies and allow the staff to see real-time updates in data changes. Maintaining various types of spreadsheets that contain separate pieces of this information will contribute towards staff confusion and errors.',
						),
						array(
							'name'            => '4. Automated process to monitor expiration dates of staff credentials and ensure renewal prior to expiration?',
							'answer_id'       => 'a4',
							'recommendations' => 'Weekly reports about providers who are due for renewal will enable staff to proactively avoid gaps in service delivery where the provider may be considered non-contracted.  Such a proactive process will help avoid problems with claims payment or referrals.',
						),
						array(
							'name'            => '5. Contracting process in place to ensure provider network is contracted and considered in-network for all payer lines-of-business with separate rate schedules for each?',
							'answer_id'       => 'a5',
							'recommendations' => 'It is common for VBR contracts to be very selective where a payer may only use VBR within certain lines of business and not across all of them.  Also, payers may use narrow network contracts and use only a sub-set of the overall contracted network as a way to ensure quality and minimize induced demand for services. Therefore, it is important to ensure the provider network is considered to be "in-network" across all product lines.',
						),
						array(
							'name'            => '6. Process to recruit and assign clinicians and clinical teams based on capacity for effectiveness at individual client needs?',
							'answer_id'       => 'a6',
							'recommendations' => 'The process of recruiting clinicians is typically driven by staff turnover or an increased consumer demand for services. Staff recruitment should be an ongoing process where prescreened resumes are on hand. Creating a detailed list of demonstrated clinical specialties will be required in order to make the right fit between consumers and providers.',
						),
						array(
							'name'      => 'Care Coordination & Care Management',
							'answer_id' => null,
						),
						array(
							'name'            => '7. Process in place to for network providers to receive care management referrals from payers and other providers, and identify and track referral sources?',
							'answer_id'       => 'a7',
							'recommendations' => 'In risk-based contracting the provider organization will need to have well integrated clinical workflows and processes between care management and clinical delivery through the provider network.  Tracking the activity will provide insight into how well it is working and identify areas of opportunity.',
						),
						array(
							'name'            => '8. Communication tools in place to facilitate the integration of care teams within the organizations\' affiliated care delivery sites, including clear hand-offs of responsibility?',
							'answer_id'       => 'a8',
							'recommendations' => 'An EHR that can provide a single platform for clinical documentation of referrals, and highlight issues of importance, will improve communication and address any potential gaps in care while improving consistent adherence to clinical protocols.',
						),
						array(
							'name'            => '9. Arranges for periodic collaborative opportunities with providers, payers, and consumers to identify performance improvement initiatives?',
							'answer_id'       => 'a9',
							'recommendations' => 'Taking a proactive approach to work collaboratively across other medical, behavioural, and social-service stakeholders will improve quality of care within your community.',
						),
						array(
							'name'            => '10. Care management process to ensure that each consumer has at least one primary care wellness appointment annually?',
							'answer_id'       => 'a10',
							'recommendations' => 'The linkage between physical and behavioral health is necessary under VBR arrangements. Providing the assurance that such measures are being tracked and acted upon will raise your organization\'s credibility to payers and the medical community.',
						)
					)
				),
				'Clinical Management & Clinical Performance Optimization' => array(
					'section_id' => 'b',
					'count'      => 10,
					'benchmark'  => 33,
					'questions'  => array(
						array(
							'name'      => 'Decision Support & Care Standardization',
							'answer_id' => null,
						),
						array(
							'name'            => '1. System to determine early intervention and risk-adjusted care planning of consumers to ensure the most appropirate level of care?',
							'answer_id'       => 'b1',
							'recommendations' => 'Enabling data analytics derived from your population health management process enables your care team to prioritize their outreach efforts and make the most efficient use of time to improve quality of care.',
						),
						array(
							'name'            => '2. Inclusion of the consumer and family in service and care planning activities and care plan documentation?',
							'answer_id'       => 'b2',
							'recommendations' => 'Although most organizations may already follow this guideline of including the consumer and family in care planning, it is the ability to capture this consistently in your documentation system that makes this competency valuable.',
						),
						array(
							'name'            => '3. Standardized protocols that guide care management (i.e. clinical guidelines, medical necessity criteria, evidence-based practices, etc.) and include established timeframes for services, re-evaluation and continuity of care between care settings?',
							'answer_id'       => 'b3',
							'recommendations' => 'Establishing protocols and standardized timeframes for re-evaluation of the treatment plan improves the effectiveness and consistency across your entire team of care managers.',
						),
						array(
							'name'            => '4. Access to patient outcomes data to inform care management planning, access to the appropriate level of service, and referrals?',
							'answer_id'       => 'b4',
							'recommendations' => 'Using the output of data analytics helps improve decision making, minimizes the risk of adverse events, and improves the consistency of care management.',
						),
						array(
							'name'            => '5. Performance metrics to assess provider specific results (i.e. provider profiling that may include average length of treatment, average cost per episode, number of re-hospitalizations, ER visits, adherence to medication, etc.)?',
							'answer_id'       => 'b5',
							'recommendations' => 'VBR contracts will make organizations more accountable for their outcomes when their portion of treatment is completed. Knowing the outcomes of the downstream providers being referred to, will help ensure better long-term outcomes.',
						),
						array(
							'name'      => 'Clinical Performance Tracking, Assessment, & Optimization',
							'answer_id' => null,
						),
						array(
							'name'            => '6. Established process to assess outcomes and update practices to achieve greater fidelity to service quality indicators?',
							'answer_id'       => 'b6',
							'recommendations' => 'Using outcome data that is unbiased and objective will gain buy-in from the stakeholders in your organization to drive change.',
						),
						array(
							'name'            => '7. Established process to track and measure against key indicators related to cancellations and no-shows (by service, site, clinician, day of week, and time)?',
							'answer_id'       => 'b7',
							'recommendations' => 'A process that uses trend data will serve to uncover possible staff misperceptions and assumptions that can be changed for organizational improvement.  A formally established process will allow your staff to share the same expectations about service delivery.',
						),
						array(
							'name'            => '8. Clinician and support staff access to dashboards and population health management analytics to assess outcomes and improve performance?',
							'answer_id'       => 'b8',
							'recommendations' => 'Sharing organizational performance data and reviewing dashboards with staff not only improves accountability by key service area but also sets performance expectations to attain future results.',
						),
						array(
							'name'            => '9. Reporting process to assess case-level data regarding readiness for change, intervention processes, extent of symptom resolution, and level of goal attainment, and to refine and revise care plans on an ongoing basis?',
							'answer_id'       => 'b9',
							'recommendations' => 'Client-centered reporting supported by a robust data information set will provide a complete picture to adjust and customize the consumers treatment plan.',
						),
						array(
							'name'      => 'Integration Of Physical Health, Behavioral Health, & Social Services',
							'answer_id' => null,
						),
						array(
							'name'            => '10. Established referral and data sharing relationships with primary care and other physical health specialty providers in the community?',
							'answer_id'       => 'b10',
							'recommendations' => 'Integration with other providers within the overall system of care is extremely important not only to improve care collaboration and patient outcomes but also to demonstrate your organization\'s ability to take on this responsibility. A by-product of doing so will also be improving the reputation of your organization and will help grow your referrals over time.',
						)
					)
				),
				'Consumer Access, Service, & Engagement'                  => array(
					'section_id' => 'c',
					'count'      => 10,
					'benchmark'  => 31,
					'questions'  => array(
						array(
							'name'      => 'Consumer Input & Access To Services',
							'answer_id' => null,
						),
						array(
							'name'            => '1. Removal of technical, staffing, and procedural barriers of the comsumer\'s experience in accessing health information?',
							'answer_id'       => 'c1',
							'recommendations' => 'Eliciting consumer feedback will identify barriers to accessing services and information.',
						),
						array(
							'name'            => '2. Clearly identified and convenient days and hours and locations of service availability (i.e., outpatient,  telehealth, walk-in, etc.)?',
							'answer_id'       => 'c2',
							'recommendations' => 'Consumer preferences may be different from standard operating rules.  For example, convenient hours for some consumers may be weekends and evenings.',
						),
						array(
							'name'            => '3. Individualized care planning based on measurable goals to determine care outcomes and the need for ongoing treatment?',
							'answer_id'       => 'c3',
							'recommendations' => 'Developing individualized treatment plans based on consumer goals is an industry-wide best practice.',
						),
						array(
							'name'      => 'Automated Consumer Service Functionality',
							'answer_id' => null,
						),
						array(
							'name'            => '4. Access to services when needed (flexible, open, same day, consumer-focused scheduling)?',
							'answer_id'       => 'c4',
							'recommendations' => 'Building a process such as same day appointments to accommodate unexpected events (i.e. personal crisis) offers a valuable resource to prevent avoidable emergency room use and other more expensive service options.',
						),
						array(
							'name'      => 'Mobile Health Applications',
							'answer_id' => null,
						),
						array(
							'name'            => '5. Use of technology with standardized clinical assessments (i.e. PHQ, SBRT, CAGE) to assist with diagnostics, clinical decision support, treatment, and cognitive function restoration?',
							'answer_id'       => 'c5',
							'recommendations' => 'Administrative efficiencies can be gained within the clinical process by using technology to conduct standardized assessments, which can be easily uploaded to an EHR and be used as a benchmark to assess future progress.',
						),
						array(
							'name'            => '6. Use of technology applications for early detection of relapse, and relapse prevention?',
							'answer_id'       => 'c6',
							'recommendations' => 'Technology applications for substance use disorder treatment can provide objective data that the consumer can use to determine if they need to seek treatment immediately to prevent an relapse.  This technology can truly be lifesaving.',
						),
						array(
							'name'      => 'Consumer Wellness Support',
							'answer_id' => null,
						),
						array(
							'name'            => '7. System to engage consumers in ongoing wellness support, including offering or facilitating access to disease management programs and classes?',
							'answer_id'       => 'c7',
							'recommendations' => 'These services can help stabilize members with chronic medical and/or behavioral health issues. In order to make these types of linkages, the organization will need to know the types of services covered under a consumer\'s health benefit plan.',
						),
						array(
							'name'            => '8. Use of biometric screening to monitor basic health indicators (height, weight, BMI, blood pressure, waist circumference, fasting glucose/blood sugar, total cholesterol, HDL, LDL), and engage consumers in wellness strategies?',
							'answer_id'       => 'c8',
							'recommendations' => 'The ability to provide and track biometric screening will play a valuable role in physical and behavioral health integration.',
						),
						array(
							'name'      => 'Consumer Satisfaction Feedback Availability',
							'answer_id' => null,
						),
						array(
							'name'            => '9. Survey tool used to gain consumer feedback about services? For example, obtaining an appointment, hours of operation, helpfulness of administrative staff, effectiveness of clinician, addressed to consumers satisfaction, etc?',
							'answer_id'       => 'c9',
							'recommendations' => 'Behavioral health is a consumer-centric industry. As such, organizations must keep a pulse on how consumers view their brand by understanding their level of satisfaction among some key areas such as scheduling, administrative support, clinical effectiveness, and overall satisfaction in getting issues addressed.',
						),
						array(
							'name'      => 'Consumer Performance Metrics',
							'answer_id' => null,
						),
						array(
							'name'            => '10. Claims-based outcome measures in place that track reductions in costly behavioral health care (re-hospitalizations within 30 days of discharge from inpatient psychiatric care, re-hospitalizations for medical conditions, follow-up after hospitalization for substance use disorder)?',
							'answer_id'       => 'c10',
							'recommendations' => 'These measures require claims data to ensure a comprehensive view of consumer services across the service system, so there must be a strong partnership with the health plan and a plan for sharing data.',
						)
					)
				),
				'Financial Management'                                    => array(
					'section_id' => 'd',
					'count'      => 10,
					'benchmark'  => 35,
					'questions'  => array(
						array(
							'name'      => 'Encounter Reporting',
							'answer_id' => null,
						),
						array(
							'name'            => '1. Ability to electronically capture and report encounter data in the format and within the timeframe required by payers?',
							'answer_id'       => 'd1',
							'recommendations' => 'Encounter reporting is a fundamental requirement for capitation arrangements where there must be documentation of a service being delivered, however, the actual claim submission must be suppressed. Reporting will compare and measure any changes in service utilization over different time periods.',
						),
						array(
							'name'            => '2. Quality assurance system in place to verify encounter data and ensure accuracy prior to submission?',
							'answer_id'       => 'd2',
							'recommendations' => 'Submitting encounter data is necessary to demonstrate proof of service under value-based reimbursement such as capitation.  Being proactive to ensure encounter data is accurate prior to being sent to the payer will ensure clean and accurate record keeping that will show volume of utilization, frequent diagnostic categories, etc.',
						),
						array(
							'name'      => 'Value-Based Payment Capabilities',
							'answer_id' => null,
						),
						array(
							'name'            => '3. Bill for services not included in value-based reimbursement agreements such as services that fall outside of bundled payment arrangements?',
							'answer_id'       => 'd3',
							'recommendations' => 'Most value-based and capitated contracts do not cover every conceivable service that is necessary for the provider to render.  In such cases, two things must occur: 1) the provider must be able to bill for services not covered by the VBR contract and 2) the payer must be able to recognize such claims and reimburse them accurately.',
						),
						array(
							'name'            => '4. Established reporting system to reconcile capitation payments against enrollment data files?',
							'answer_id'       => 'd4',
							'recommendations' => 'Sharing enrollment files between the payer and your organization is necessary to understand the day-to-day financial performance.  In the contract negotiation process it will be necessary to describe the process and how inconsistencies will be resolved.',
						),
						array(
							'name'            => '5. Capability to process claims and pay providers FFS who are not covered under the VBP agreement?',
							'answer_id'       => 'd5',
							'recommendations' => 'Although VBP are to help contain costs and improve quality, the claims systems capability should allow Non-VBP to be paid separately.  This will help avoid getting members caught in the middle of claim disputes and also identify providers who can be brought into VBP in future contracting efforts.',
						),
						array(
							'name'            => '6. System can effectively identify consumers and providers who were part of a value-based reimbursement arrangement?',
							'answer_id'       => 'd6',
							'recommendations' => 'This reporting will provide the data to determine the success of VBP.  By identifying consumers who received services under VBP they can be compared to FFS consumers who received the same services to understand impact of costs.',

						),
						array(
							'name'            => '7. Established system to track capitated contract consumers receiving care from other providers outside of contract (leakage)?',
							'answer_id'       => 'd7',
							'recommendations' => 'Understanding the spend that occurs outside of the capitation arrangement is a key indicator for the contract\'s financial success. Payers may identify providers who can be pursued for in-network contracting to reduce the costs of leakage.',

						),
						array(
							'name'            => '8. Systems in place to understand and manage the cost of care provided under FFS, Bundled Payment, Shared Savings, Shared Risk and Capitation, and role of the total cost of care (including medical, mental health, substance abuse, eating disorder, intellectual and developmental disabilities) to consumers in the service system?',
							'answer_id'       => 'd8',
							'recommendations' => 'A single reporting system will need to capture all of the variations of FFS and Value-Based Reimbursement in order to position the organization in the best way possible to assess financial performance, and assist with financial forecasting of various VBR methods with payers.',
						),
						array(
							'name'      => 'Financial Performance Monitoring',
							'answer_id' => null,
						),
						array(
							'name'            => '9. Cost accounting system to calculate unit costs, target costs and identify the total cost of care?',
							'answer_id'       => 'd9',
							'recommendations' => 'These calculations provide valuable information about your organization\'s costs during contract negotiations with payers. As contract agreements are finalized, the rates must be loaded into your accounting system to calculate your ongoing revenue per service for each contract.',
						),
						array(
							'name'            => '10. Comprehensive set of key performance indicators that project short-term (3 - 6 months) and long-term (6 - 18 months) financial health?',
							'answer_id'       => 'd10',
							'recommendations' => 'Short-term financial metrics enable the organization to avoid unexpected crises related to cash, credit and contractual responsibilities. Long-term performance metrics will align with strategy and position the organization for financial sustainability, innovation and long-term growth.',
						)
					)
				),
				'Technology & Reporting Infrastructure Functionality'     => array(
					'section_id' => 'e',
					'count'      => 10,
					'benchmark'  => 26,
					'questions'  => array(
						array(
							'name'      => 'Capacity To Collect Data',
							'answer_id' => null,
						),
						array(
							'name'            => '1. EHR includes additional functionality areas of population health data analysis, business intelligence, and care management in addition to service documentation and revenue cycle management?',
							'answer_id'       => 'e1',
							'recommendations' => 'The capabilities of scheduling, documentation, billing and reporting are seen as core features on an EHR. Additional EHR features can maximize data that is already captured and can perform data analysis to provide insight through business intelligence reports about financial and clinical trends to improve decision making.',
						),
						array(
							'name'      => 'Capacity To Analyze Data For Population Health Management',
							'answer_id' => null,
						),
						array(
							'name'            => '2. Data summarized in health registries for stratification consumer populations by diagnosis for risk-adjusted care planning (using diagnoses to identify high utilizer interventions)?',
							'answer_id'       => 'e2',
							'recommendations' => 'VBR readiness starts with Population Health Management in order to understand the challenges and opportunities within your consumer base.  Summarized patient registries is a great place to begin developing clinical strategies and interventions for your care managers and clinical team.',
						),
						array(
							'name'      => 'Ability to Manage Value-Based Contracts',
							'answer_id' => null,
						),
						array(
							'name'            => '3. Service utilization prediction model to assess resource needs and impact on financial resources?',
							'answer_id'       => 'e3',
							'recommendations' => 'Service utilization projections will offer insight about the staffing required to meet future demand as well as conduct workforce development as demand increases.  As staffing is one of the biggest budget items for an organization, getting this right will influence your future success.',
						),
						array(
							'name'      => 'Ability To Exchange Healthcare Information',
							'answer_id' => null,
						),
						array(
							'name'            => '4. Health information exchange agreements in place for key providers in the community (hospitals, ERs, physicians, specialty providers)?',
							'answer_id'       => 'e4',
							'recommendations' => 'Value-based contracting will continue to require organizations to improve their level of care integration activities.  HIEs are an excellent way to share information and coordinate services.',
						),
						array(
							'name'            => '5. Automated technology to notify staff of inpatient or crisis services provided to consumers (ER visit, hospital admission, hospital discharge)?',
							'answer_id'       => 'e5',
							'recommendations' => 'Automated technology will improve staff efficiency to respond to a crisis much more quickly and it will also provide the opportunity to have a greater impact on care outcomes through such a timely response.',
						),
						array(
							'name'            => '6. Secure infrastructure in place and protocols in place that meets federal and state requirements, including HIPAA and HITECH?',
							'answer_id'       => 'e6',
							'recommendations' => 'Using technology that meets all state and federal requirements is a "must have" for any organization. Obtaining independent proof from an outside source that can actually test compliance with federal and state policy will provide an added level of assurance and peace-of-mind.',
						),
						array(
							'name'            => '7. IT staff have experience with systems integration, data conversion and managing expert resources to fill gaps in internal skills?',
							'answer_id'       => 'e7',
							'recommendations' => 'There are typically two ways to ensure IT staff have updated skill sets to manage data: keep your IT in-house and invest in on-going training and certification or consider using an outside vendor who can provide this to your organization for typically a monthly fee along with service guarantees.',
						),
						array(
							'name'      => 'Care Management Functionality',
							'answer_id' => null,
						),
						array(
							'name'            => '8. Risk assessment tools to identify those consumers needing care management intervention plans?',
							'answer_id'       => 'e8',
							'recommendations' => 'Compiling information from various data sources such as claims, on-line self-evaluations, health risk assessments, etc. can provide the ability to not only identify these consumers but stratify them by level risk level to prioritize care management interventions.',
						),
						array(
							'name'            => '9. Healthcare provider and social services referral database to facilitate care management referrals efficiently?',
							'answer_id'       => 'e9',
							'recommendations' => 'Gathering proactive information from other providers or community-based organizations can prevent a crisis situation. It will be important for your organization to develop this collaborative approach across primary care, social services and other provider organizations.',
						),
						array(
							'name'      => 'Consumer Portal Functionality',
							'answer_id' => null,
						),
						array(
							'name'            => '10. You have implemented a comprehensive consumer portal that includes helpful functionality, for instance:
								',
							'answer_id'       => 'e10',
							'recommendations' => 'Providing consumers convenient access to their health information empowers them to become more engaged and knowledgeable about their health care decisions related to  costs, treatment options, medical history, etc.',
						),
					)
				),
				'Leadership & Governance'                                 => array(
					'section_id' => 'f',
					'count'      => 10,
					'benchmark'  => 40,
					'questions'  => array(
						array(
							'name'      => 'Strategic Alignment Around Value',
							'answer_id' => null,
						),
						array(
							'name'            => '1. Planning process that prioritizes resources based on services that bring value to the community?',
							'answer_id'       => 'f1',
							'recommendations' => 'A top priority of a human services organization is to serve the needs of its community - prioritizing resources to meet the most important needs of its consumers will improve the consumer populations health and improve the organizations impact and reputation in the community.',
						),
						array(
							'name'            => '2. Adequate cash reserves to implement new payment methods and withstand changes in cash flow related to risk-based contracts?',
							'answer_id'       => 'f2',
							'recommendations' => 'Cost over-runs can be common when implementing new technology to account for unanticipated complications and customization. Transitioning from FFS to VBR payment may affect cash flow. VBR payment may not occur until after an episode of care is completed or at specific times throughout the year when quality results are measured and reported.  This is a much different process compared to FFS, when the service is rendered and bill is submitted.',
						),
						array(
							'name'            => '3. Sufficient access to capital for infrastructure investment, or plan for accessing capital?',
							'answer_id'       => 'f3',
							'recommendations' => 'Preparing to be successful at VBR is a long-term investment. Upfront costs for technology, implementation, customization, licensing fees, upgrades, etc. will draw down cash reserves. It is rare for organizations to be fully ready and successful in VBR by maintaining the exact systems and IT structure used for FFS.',
						),
						array(
							'name'            => '4. Established strong payer relationships and marketing plan to facilitate negotiation of new care models, payment innovations and data sharing agreements?',
							'answer_id'       => 'f4',
							'recommendations' => 'Strong relationships are built on mutual trust which can be greatly enhanced through a process of data transparency on both sides. Discuss how new VBR strategy will help resolve the pain points of the payer.  Strive for win-win negotiations focused on quality improvement supported by on-going reporting to make course adjustments to operating processes or clinical workflows.',
						),
						array(
							'name'            => '5. Board engaged in governance activities and knowledgeable about healthcare trends and strategic objectives?',
							'answer_id'       => 'f5',
							'recommendations' => 'Embarking on value-based reimbursement is a dramatic shift to an organization\'s culture, clinical philosophy and fiscal responsibility that requires the Board to understand the historical reasons why such a change is necessary and the hope VBR brings to the future viability of their organization.',
						),
						array(
							'name'      => 'Culture Of Innovation',
							'answer_id' => null,
						),
						array(
							'name'            => '6. Track record of successful implementation of new innovative programs, collaborative service arrangements, and value-based purchasing contracts?',
							'answer_id'       => 'f6',
							'recommendations' => 'This rating builds on the concept that past results are a good indicator of future success. VBR readiness is new across the industry, so no organization has a corner on it right now. Organizations that have shown the ability to adapt and apply the lessons learned from past challenges to VBR readiness will be at an advantage.',
						),
						array(
							'name'      => 'Workforce Adequacy',
							'answer_id' => null,
						),
						array(
							'name'            => '7. Workforce culture, experience and capacity to innovate and adapt to the changing value-based payment business model, market and regulatory demands?',
							'answer_id'       => 'f7',
							'recommendations' => 'Diversity in skill sets and experience are essential ingredients to prepare and be successful in executing VBR strategies. Organizations that allow their teams to "think outside the box" and consider failure to be a learning experience from which to improve will thrive.',
						),
						array(
							'name'            => '8. Compensation aligned with performance outcomes and strategic priorities?',
							'answer_id'       => 'f8',
							'recommendations' => 'As the industry is preparing to financially reward providers to achieve improved care outcomes, the same approach should be taken with organizations and their staff. Tying compensation to attaining service quality and corporate goals can incentivize achievement of quality outcomes.',
						),
						array(
							'name'            => '9. Staff development function that identifies top talent, provides development opportunities and growth into highest level of functioning?',
							'answer_id'       => 'f9',
							'recommendations' => 'A key motivator for high achievers is providing mentorship opportunities to expand their knowledge base and giving them additional opportunities to grow their skill set, which in turn will build tremendous bench strength and strong future leaders. Motivated and enthusiastic staff are a model to the rest of the workforce.',
						),
						array(
							'name'            => '10. Process to assess staff competency on at least an annual basis and provide identified training focused on achieving actual improvements in performance?',
							'answer_id'       => 'f10',
							'recommendations' => 'When used as a best practice - the performance review process is very effective to reinforce the organization\'s cultural beliefs and mission to serve its stakeholders in the best possible way. Feedback during this process must be specific and provide actionable recommendations an employee can take to improve such as specific types of trainings.',
						),
					)
				)
			)
		);

		return $metadata;
	}

}
