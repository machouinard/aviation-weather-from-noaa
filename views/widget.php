<?php
// Not being used any longer, correct?

	echo '<div class="' . apply_filters( 'adds_widget_wrapper', 'adds-weather-wrapper' ) . '"><p>';
	if ( $show_metar || $show_taf || $show_pireps ) {
		echo esc_html( $title );
	}
	echo '</p>';


if ( ! empty( $pireps[0] ) && $show_pireps ) {
	echo '<p class="adds-heading">pireps <span class="adds-sm">' . absint( $radial_dist ) . 'sm</span></p><ul>';
	foreach ( $pireps[0] as $pirep ) {
		echo '<li>' . esc_html( $pirep ) . '</li>';
	}
	echo '</ul>';
}
echo '</div>'; ?>