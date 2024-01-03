<?php
/**
 * Submission template for assessment tools
 *
 * @package 1.0.0
 */

echo do_shortcode( '[omda-nav subheading="Assessment" linkto="submissions" button_txt="back"]' );
?>

<section class="omda-section">
	<p>This OPEN MINDS assessment is focused on the organizational and technical competencies service provider organizations need to make a successful transition to value-based reimbursement. Our tool was designed to evaluate and identify recommendations in 6 key management domains at each level of the value-based system. The tool is an organizational assessment, and you may only take one assessment at a time. After submitting a completed assessment, you can view archived results or start a new assessment the next time you access the tool. Simultaneous users of this assessment is not recommended as answers may not be recorded properly for accurate findings and recommendations. Once you’ve completed the assessment in its entirety, a series of recommendations and next steps will be available for you to print and save. Come back as often as necessary to retake the assessment before your subscription to the tool has expired.</p>

	<p>Navigate through these 6 domains, using the links to each section below.  You can complete the entire assessment, or the questions in any one section. Make sure that the answers to all questions in the section are completed before submitting the assessment for analysis and recommendations to ensure the most accurate results.</p>
</section>

<section class="omda-section">
	<?php echo do_shortcode( '[omda-form abbrev="vbr"]' ); ?>
</section>
