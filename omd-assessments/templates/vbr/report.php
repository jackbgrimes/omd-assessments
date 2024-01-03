<?php
/**
 * Report template for VBR Assessment tool
 *
 * @package 1.0.0
 */

echo do_shortcode( '[omda-nav subheading="VBR Assessment Results" linkto="submissions" button_txt="back"]' );

/**
 * Logic
 * get individual user ID
 * get individual entry ID (from submissions template)
 */
$user_id    = get_current_user_id();
$entry_id   = '';

if ( isset( $_GET['entry_id'] ) ) {
	$entry_id = filter_input( INPUT_GET, 'entry_id', FILTER_SANITIZE_STRING );
}

/**
 * Logic & Markup
 * if the entry ID doesn't exist, return
 * else,
 *   1. lookup and convert entry data from wp_usermeta into $entry_data
 *   2. initialize ChartJS canvas element
 *   3. iterate through metadata object
 *      a. get section name and score
 *      b. populate section_scores and _benchmark arrays for chartjs
 *      b. return sprintf (section accordian header)
 *      c. iterate through section for individual questions
 *         i.   get score from $entry_data
 *         ii.  get feedback language if question score not 100%
 *         iii. return sprintf (individual accordian row or subheader row)
 */
if ( ! $entry_id ) {
	return false;

} else {
	// 1. data convert
	$entry_data         = array();
	$usermeta_import    = get_user_meta( $user_id, $entry_id );
	$decoded_import     = json_decode( $usermeta_import[0], true );
	$entry_data         = $decoded_import;
	$overall_score      = number_format( (float) ( $entry_data['overall_score'] ), 2, '.', '' );
	$section_scores     = array();
	$section_benchmarks = array();
	

	// 2. ChartJS canvas element
	// 3ab. assessment section headers
	echo '
	<section class="omda-section">
		<div class="content-container">
			<h4 class="section-header">Domain Scores</h4>
			<p class="section-content">Overall Score: ' . esc_html( $overall_score ) . '%</p>
		</div>

		<div class="chart-container">
			<canvas id="vbrChart"></canvas>
		</div>
	</section>

	<section class="omda-section">
		<h4>Domains</h4>	
		<ul class="omda-accordian">';

	// 2ab. assessment section headers
	foreach ( $this->vbr_metadata['sections'] as $section_key => $section ) {
		// section_scores and _benchmarks are for JS
		$section_id           = $section['section_id'] ?? '';
		$section_score        = $entry_data['section_scores'][$section_id] ?? 0;
		$section_score        = number_format( (float) ( $section_score ), 2, '.', '' );
		$section_scores[]     = $section_score ?? 0;
		$section_benchmarks[] = $section['benchmark'] ?? 0;
		$accordian_header     = sprintf(
			'<li class="accordian-list-item">
				<button class="accordian-head">
					<p>%1$s</p>
					<p>Score: %2$s</p>
					<p class="accordian-button-toggle">+</p>
				</button>
				<div class="accordian-row hidden-row">
					<div class="accordian-row-head">
						<p>Competency</p>
						<p>Status</p>
						<p>Recommendation</p>
					</div>',
			$section_key,        // 1 [section name]
			$section_score . '%' // 2 [section score]
		);
		echo wp_kses_post( $accordian_header );


		// 3c. individual section questions
		foreach ( $section['questions'] as $question_index => $question ) {
			$question_id             = $question['answer_id'];
			$question_score          = $entry_data['entry_answers'][$section_id][$question_id] ?? 0;
			$question_recommendation = '';

			/**
			 * Conditional checks
			 * 1. if question_score !== 100%, feedback will be populated
			 * 2. if question_id === null, return subheader; otherwise return question row
			 */
			if ( $question_score < 4 ) {
				$question_recommendation = $question['recommendations'] ?? '';
			} else {
				$question_recommendation = 'No recommendations.';
			}

			if ( ! is_null( $question_id ) ) {
				$accordian_row = sprintf(
					'<div class="accordian-row-body">
						<p class="accordian-content">%1$s</p>
						<p class="accordian-content">%2$s / 4</p>
						<p class="accordian-content">%3$s</p>
					</div>',
					$question['name'] ?? '', // 1 [question name]
					$question_score,         // 2 [question score]
					$question_recommendation // 3 [question recommendation]
				);
				echo wp_kses_post( $accordian_row );

			} else {
				echo '<div class="accordian-row-subhead">' . esc_html( $question['name'] ?? '' ) . '</div>';
			}
		}

		/**
		 *    accordian-row
		 * accordian-list-item
		 */
		echo '
			</div>
		</li>
		';
	}

	/**
	 *    omda-accordian 
	 * section
	 */
	echo '
		</ul>
	</section>
	';
}
?>

<style>
	/* omda-accordian
	 * > accordian-list-item
	 * > > accordian-head
	 * > > accordian-row
	 * > > hidden row
	 */
	.omda-accordian {
		width: 100%;
		margin: 0;
		padding: 0;
	}
	.accordian-list-item {
		width: 100%;
		display: flex;
		flex-direction: column;
		list-style: none;
		margin-bottom: 12px;
	}
	.accordian-head {
		width: 100%;
		display: grid;
		grid-template-columns: 20fr 5fr 1fr;
		border: none;
		outline: none;
		margin: 0;
		padding: 0;
		/* temp */
		background-color: #00b6a9;
		color: #334152;
		font-size: 16px;

	}
	.accordian-head:hover,
	.accordian-head:active {
		color: #fff;	
	}
	.accordian-head p {
		margin: 0;
		padding: 16px 10px;
	}
	.accordian-head p:first-child {
		text-align: left;
	}
	.hidden-row {
		display: none;
	}

	/*
	 * accordian-row
	 * > accordian-row-head
	 * > accordian-row-subhead
	 * > accordian-row-body
	 */
	.accordian-row-head,
	.accordian-row-subhead,
	.accordian-row-body {
		padding: 14px 10px 14px 24px;
	}
	.accordian-row-head {
		display: grid;
		grid-template-columns: 4fr 1fr 5fr;
		background-color: #334152;
	}
	.accordian-row-head p {
		margin: 0;
		/* temp */
		color: #fff;
		font-size: 14px;
	}
	.accordian-row-subhead {
		margin: 0;
		border-bottom: 1px solid #33415250;
		/* temp */
		color: #334152;
		font-weight: bold;
		font-size: 14px;
	}
	.accordian-row-body {
		display: grid;
		grid-template-columns: 4fr 1fr 5fr;
		border-bottom: 1px solid #33415250;
	}
	.accordian-row-body {
		margin: 0;
		/* temp */
		color: #334152;
		font-size: 14px;
	}
	.accordian-content {
		margin: 0;
	}
	.accordian-row-body .accordian-content:first-child {
		margin: 0 12px 0 0;
	}

	.accordian-row:nth-child(even) {
		background-color: #ececec;
	}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-annotation/2.0.1/chartjs-plugin-annotation.min.js"></script>
<script type="text/javascript">
	// chartjs
	const section_scores = JSON.parse('<?php echo wp_json_encode( $section_scores ); ?>');
	const section_benchmarks = JSON.parse('<?php echo wp_json_encode( $section_benchmarks ); ?>');
	const ctx = 'vbrChart';
	const vbrChart = new Chart(ctx, {
	type: 'bar',
	data: {
		labels: [
			['Provider', 'Network', 'Management'], 
			['Clinical', 'Management', '& Clinical', 'Performance', 'Optimization'], 
			['Consumer', 'Access, Service', '& Engagement'], 
			['Financial', 'Management'], 
			['Technology', '& Reporting', 'Infrastructure', 'Functionality'],
			['Leadership', '& Governance']
		],
		datasets: [
			{
				label: 'You',
				type: 'bar',
				data: section_scores,
				backgroundColor: 'rgba(0, 182, 170, 0.3)',
				borderColor: 'rgba(0, 182, 170, 1)',
				borderWidth: 1.5
			},
			{
				label: `Industry Avg`,
				type: 'bar',
				data: section_benchmarks,
				backgroundColor: 'rgba(51, 65, 82, 0.3)',
				borderColor: 'rgba(51, 65, 82, 1)',
				borderWidth: 1.5
			}
		]
	},
	options: {
		plugins: {
			tooltip: {
				enabled: true,
				callbacks: {
					// hides tooltip title
					title: function() {}
				}
			},
			// plugin CDN no longer works
			annotation: {
				annotations: {
					average: {
						type: 'line',
						yMin: 2,
						yMax: 2,
						borderColor: '#F7934F',
						borderWidth: 2,
					},
					industryAverage: {
						type: 'line',
						yMin: 34,
						yMax: 34,
						borderColor: 'rgba(247, 147, 79, 0.5)',
						borderWidth: 2,
					}
				}
			}
		},
		scales: {
			y: {
				beginAtZero: true,
				max: 100
			}
		}
	}	
});
</script>
