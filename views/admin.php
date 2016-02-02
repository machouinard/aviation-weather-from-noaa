<label
	for="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"><?php _e( 'Title',
		'machouinard_adds' ); ?></label>
<input class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
       value="<?php echo esc_html( $title ); ?>"/>

<label
	for="<?php echo esc_attr( $this->get_field_name( 'icao' ) ); ?>"><?php _e( 'ICAO',
		'machouinard_adds' ); ?></label>
<input class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'icao' ) ); ?>" type="text"
       value="<?php echo esc_attr( $icao ); ?>" placeholder="Please Enter a Valid ICAO"/>
<label for="<?php echo esc_attr( $this->get_field_name( 'hours' ) ); ?>">Hours before now</label>
<select name="<?php echo esc_attr( $this->get_field_name( 'hours' ) ); ?>"
        id="<?php echo esc_attr( $this->get_field_id( 'hours' ) ); ?>" class="widefat">

	<?php
	foreach ( range( 1, 6, 1 ) as $i ) {
		echo '<option value="' . absint( $i ) . '" id="' . absint( $i ) . '"', $hours == $i ? ' selected="selected"' : '', '>', $i, '</option>';
	}
	?>
</select>
<table>
	<thead>Display:</thead>
	<tr>
		<td><label
				for="<?php echo esc_attr( $this->get_field_id( 'show_metar' ) ); ?>"><?php _e( 'METAR',
					'machouinard_adds' ); ?></label></td>
		<td><input id="<?php echo esc_attr( $this->get_field_id( 'show_metar' ) ); ?>"
		           name="<?php echo esc_attr( $this->get_field_name( 'show_metar' ) ); ?>" type="checkbox"
		           value="1" <?php checked( true, $show_metar ); ?> class="checkbox"/></td>
	</tr>
	<tr>
		<td><label
				for="<?php echo esc_attr( $this->get_field_id( 'show_taf' ) ); ?>"><?php _e( 'TAF',
					'machouinard_adds' ); ?></label></td>
		<td><input id="<?php echo esc_attr( $this->get_field_id( 'show_taf' ) ); ?>"
		           name="<?php echo esc_attr( $this->get_field_name( 'show_taf' ) ); ?>" type="checkbox"
		           value="1" <?php checked( true, $show_taf ); ?> class="checkbox"/></td>
	</tr>
	<tr>
		<td><label
				for="<?php echo esc_attr( $this->get_field_id( 'show_pireps' ) ); ?>"><?php _e( 'PIREPS',
					'machouinard_adds' ); ?></label></td>
		<td><input id="<?php echo esc_attr( $this->get_field_id( 'show_pireps' ) ); ?>"
		           name="<?php echo esc_attr( $this->get_field_name( 'show_pireps' ) ); ?>" type="checkbox"
		           value="1" <?php checked( true, $show_pireps ); ?> class="checkbox"/></td>
	</tr>
	<tr>
		<td><label
				for="<?php echo esc_attr( $this->get_field_id( 'show_station_info' ) ); ?>"><?php _e( 'Airport Location',
					'machouinard_adds' ); ?></label></td>
		<td><input id="<?php echo esc_attr( $this->get_field_id( 'show_station_info' ) ); ?>"
		           name="<?php echo esc_attr( $this->get_field_name( 'show_station_info' ) ); ?>" type="checkbox"
		           value="1" <?php checked( true, $show_station_info ); ?> class="checkbox"/></td>
	</tr>
	<tr>
		<td><label
				for="<?php echo esc_attr( $this->get_field_name( 'radial_dist' ) ); ?>"><?php _e( 'Radial Distance',
					'machouinard_adds' ); ?></label></td>
		<td><select name="<?php echo esc_attr( $this->get_field_name( 'radial_dist' ) ); ?>"
		            id="<?php echo esc_attr( $this->get_field_id( 'radial_dist' ) ); ?>" class="widefat">
				<?php
				foreach ( range( 10, 300, 10 ) as $i ) {
					echo '<option value="' . absint( $i ) . '" id="' . absint( $i ) . '"', $radial_dist == $i ? ' selected="selected"' : '', '>', $i, '</option>';
				}
				?>
			</select></td>
	</tr>
</table>