<?php
/**
 * Course Description Shortcode Buttons and Modals
 *
 * @package BC Douglas Fir Theme
 */

/**************************************
 * Aggregate functions and WP actions
 **************************************/
// API URL.
define( 'API_MULTICOURSE_URL', 'http://www.bellevuecollege.edu/apis/classes/v1/courses/multiple' );


add_shortcode( 'coursedescription', 'coursedescription_func' );  // declare single course shortcode function
add_shortcode( 'multicoursedescription', 'multi_coursedescription_func' ); // declare multi course shortcode function

/*****************************
 * Single course functions
 *****************************/


/**
 * Render one course from the BC course API.
 *
 * Shortcode: [coursedescription subject="ENGL" courseid="A 101" description="true"]
 * courseid is a letter prefix and number separated by a space.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML for the course, or empty string when attributes or API data are missing.
 */
function coursedescription_func( $atts ) {
	$atts = shortcode_atts(
		array(
			'subject'     => '',
			'courseid'    => '',
			'description' => '',
		),
		$atts,
		'coursedescription'
	);

	$subject     = $atts['subject'];
	$course      = $atts['courseid'];
	$description = $atts['description'];

	if ( empty( $course ) || empty( $subject ) ) {
		return '';
	}

	// courseid must contain both the course letter group and the numeric id.
	$course_split = explode( ' ', trim( $course ) );
	if ( count( $course_split ) < 2 ) {
		return '';
	}

	$course_id = $course_split[1];
	$subject   = trim( html_entity_decode( $subject ) );

	$url  = 'https://www2.bellevuecollege.edu/data/api/v1/course/' . urlencode( $subject ) . '/' . urlencode( trim( $course_id ) );
	$data = wp_remote_get( $url );

	if ( ! empty( $data ) && ! empty( $data['body'] ) ) {
		$json = json_decode( $data['body'] );
		return mayflower_decode_json_class_info( $json, $description );
	}

	return '';
}

// process json returned by API call
function mayflower_decode_json_class_info( $json, $show_description = false ) {
	// return print_r( $course, true );
	$htmlString  = '';
	$htmlString .= '<div class="classDescriptions">';

	$htmlString .= mayflower_course_html( $json, $show_description );

	$htmlString .= '</div>'; // classDescriptions

	return $htmlString;
}


function mayflower_course_html( $data, $show_description = false ) {
	if ( isset( $data->course ) ) { // if there is course data, return course information
		$course = $data->course;
		$title  = $course->subject . ' '
		. $course->courseNumber . ': '
		. $course->title . ' - '
		. ( $course->isVariableCredits ? 'variable' : $course->credits ) . ' credits';

		$url = 'https://www2.bellevuecollege.edu/classes/All/' . $course->subject . '/' .
			$course->courseNumber;

		$description = $course->description;

		$more = 'View details for ' . $course->subject . ' '
			. $course->courseNumber;

		if ( $show_description && 'false' != $show_description ) {
			return "<h3><a href='$url'>$title</a></h3><p>$description</p><p><a href='$url'>$more</a></p>";
		} else {
			return "<h3><a href='$url'>$title</a></h3>";
		}
	} else { // if course not selected, return to inform user there are courses available
		return '<!-- No Course Available -->';
	}
}



/**
 * Render multiple courses from the BC multicourse API.
 *
 * Shortcode: [multicoursedescription courses="ENGL 101,MATH 141" description="true"]
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML for all courses, or empty string when attributes or API data are missing.
 */
function multi_coursedescription_func( $atts ) {
	$atts = shortcode_atts(
		array(
			'courses'     => '',
			'description' => '',
		),
		$atts,
		'multicoursedescription'
	);

	$courses     = $atts['courses'];
	$description = $atts['description'];

	if ( empty( $courses ) ) {
		return '';
	}

	$course_split = explode( ',', $courses );
	$url          = API_MULTICOURSE_URL;

	// build query string for URL
	$qs = '';
	foreach ( $course_split as $course ) {
		if ( ! empty( $qs ) ) {
			$qs .= '&';
		}
		$qs .= 'courses[]=' . urlencode( html_entity_decode( strtoupper( $course ) ) );
	}
	$url .= '?' . $qs;

	$json = wp_remote_get( $url );

	if ( ! empty( $json ) && ! empty( $json['body'] ) ) {
		return process_multicourse_json( $json['body'], $description );
	}

	return '';
}

/**
 * Turn multicourse API JSON into a block of course HTML.
 *
 * @param string      $json_string Raw response body.
 * @param string|null $description When "true", include course descriptions in output.
 * @return string HTML wrapper, or empty string when JSON is invalid.
 */
function process_multicourse_json( $json_string, $description = null ) {
	$json = json_decode( $json_string, true );
	if ( ! is_array( $json ) ) {
		return '';
	}

	$courses = $json['courses'] ?? array();
	$html    = "<div class='classDescriptions'>";

	if ( is_array( $courses ) && ! empty( $courses ) ) {
		foreach ( $courses as $course ) {
			$html .= get_course_html( $course, $description );
		}
	}

	$html .= '</div>';

	return $html;
}

// process individual course for display
function get_course_html( $course, $description = null ) {
	$html  = '';
	$html .= "<div class='class-info'>";
	$html .= "<h5 class='class-heading'>";

	$course_id = $course['subject'] . ' ' . $course['courseNumber'];

	$course_url = CLASSESURL . $course['subject'];
	/*
	if($course["isCommonCourse"])
	{
		$course_url .= "&";
	}*/
	$course_url .= '/' . $course['courseNumber'];

	$html .= "<a href='" . $course_url . "''>";
	$html .= "<span class='course-id'>" . $course_id . '</span>';
	$html .= " <span class='course-title'>" . $course['title'] . '</span>';
	$html .= "<span class='course-credits'> &#8226; ";

	if ( $course['isVariableCredits'] ) {
		$html .= 'V1-' . $course['credits'] . " <abbr title='variable credit'>Cr.</abbr>";
	} else {
		$html .= $course['credits'] . " <abbr title='credit(s)'>Cr.</abbr>";
	}
	$html .= '</span>';
	$html .= '</a>';
	$html .= '</h5>'; // classHeading
	if ( $description == 'true' && ! empty( $course['description'] ) ) {
		// error_log("Not here");
		$html .= "<p class='class-description'>" . $course['description'] . '</p>';
		$html .= "<p class='class-details-link'>";
		$html .= "<a href='" . $course_url . "'>View details for " . $course['subject'] . ' ' . $course['courseNumber'] . '</a>';
		$html .= '</p>';
	}
		$html .= '</div>'; // classInfo
		return $html;
}
