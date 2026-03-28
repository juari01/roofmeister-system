<?php

	class calendar {
	/*
	 * Calendar
	 * Functions to format calendar elements.
	 */

		public static function appointment( $appointment_id, $dt_start, $dt_end, $left, $width, $top, $height,$customer_name,$project_name, $description,$color) {
		/*
		 * Appointment
		 * Given the appointment details, returns the HTML to display the
		 * appointment.
		 */
		// Create objects for date
			$dt_start = new DateTime( $dt_start );
			$dt_end   = new DateTime( $dt_end );

		// Create color for border based on background
			return
	
				  "<div class='appointment' data-appointmentid='$appointment_id' style='background:$color; color: black; left: $left; width: calc( $width ); top: $top; height: $height;'>"
				  . "<div class='desc'>*$customer_name $project_name  <br>  *$description</div>"
				. "</div>";
		}

		public static function corner_date( $day ) {
		/*
		 * Corner Date
		 * Given the day, returns the HTML to place the date appropriately
		 * in the calendar.
		 */
			return "<div class='corner-date'>$day</div>";
		}

		public static function day_cell( $day, $appointments ) {
		/*
		 * Day Cell
		 * Creates the HTML for each day's appointments as well as the day's
		 * label.
		 */
			if( is_array( $appointments )) {
				$appointments_markup = "";

				foreach( $appointments as $i => $appointment ) {
				// Calculate top/bottom position based on time of day
					$dt_start = new DateTime( $appointment['start'] );
					$dt_end   = new DateTime( $appointment['end'] );
					$interval = $dt_start->diff( $dt_end, TRUE );
					$diff     = $interval->format( "%h" ) * 60 + $interval->format( "%i" );

				// Calculate left/width based on number of overlapping appointments
					$indent = 0;
					for( $j = 0; $j < $i; ++$j ) {
						if(( $appointments[ $j ]['start'] < $appointment['end'] ) && ( $appointment['start'] < $appointments[ $j ]['end'] )) {
							$indent = $appointments[ $j ]['indent'] + 1;
						}
					}

					$appointments[ $i ]['indent'] = $indent;

					$appointments_markup .= calendar::appointment(
						$appointment['appointment_id'],
						$appointment['start'],
						$appointment['end'],
						(( $indent / (( $indent + 5 ) * 1 )) * 100 ) . "%",                                   // left
						( 100 - (( $indent / (( $indent + 5 ) * 1 )) * 100 )) . "%",                          // width
						(( $dt_start->format( "G" ) * 50 ) + ( $dt_start->format( "i" ) / 60 * 50 ) . "px" ), // top
						(( $diff / 60 * 50 ) . "px" ), 														  // height
						$appointment['customer_name'],
						$appointment['project_name'],
						$appointment['description'],
						$appointment['color']                                             
					);
				}

				return
					  "<div class='day'>"
					. calendar::time_marker()
					. calendar::corner_date( $day )
					. $appointments_markup
					. "</div>\n";
			} else {
				return "no_appointments_array";
			}
		}

		public static function time_marker( $include_time = FALSE ) {
		/*
		 * Time Marker
		 * Creates the incremental markers showing time within the day.
		 */
			$markers = '';

			for ( $i = 0; $i < 24; ++$i ) {
				$markers .= "<div class='time-marker' style='top: " . ( $i * 50 ) . "px;'>" . ( $include_time ? date( "ga", strtotime( "$i:00" )) : '' ) . "</div>\n";
			}

			return $markers;
		}
	}

?>
