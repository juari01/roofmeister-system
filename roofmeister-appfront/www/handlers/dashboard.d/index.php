<?php

session_write_close();

include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/dashboard/comcalendar.php" );


		$datetime = new DateTime();

		if( isset( $_POST['year'] ) && isset( $_POST['week'] )) {
			$datetime->setISODate( $_POST['year'], $_POST['week'], 0 );
		} else {
			$datetime->setISODate( date( "o" ), date( "W" ), 0 );
		}

		$week = $datetime->format( 'W' );
		$year = $datetime->format( 'o' );

		$dt_search = $datetime;

		$date_start = $dt_search->format( 'Y-m-d' );
		$date_end   = $dt_search->add( new DateInterval( 'P6D' ))->format( 'y-m-d' );
		$user_id 	= $_SESSION['user']['user_id'];

		$get_appointments = new jsonrpc\method( "appointments.get_appoinntment_calendar" );
		$get_appointments->param( 'api_token',  $jsonrpc_api_token );
		$get_appointments->param( 'hash',       $_SESSION['user']['hash'] );
		$get_appointments->param( 'user_id',    $user_id  );
		$get_appointments->param( 'date_start', $date_start );
		$get_appointments->param( 'date_end',   $date_end );
		$get_appointments->id = $jsonrpc_client->generate_unique_id();
		$jsonrpc_client->method( $get_appointments );

		$get_calendar = new jsonrpc\method( "admin.user.get_user_access_calendar" );
		$get_calendar->param( 'api_token', $jsonrpc_api_token );
		$get_calendar->param( 'hash',      $_SESSION['user']['hash'] );
		$get_calendar->param( 'user_id', $user_id );
		$get_calendar->id = $jsonrpc_client->generate_unique_id();
		$jsonrpc_client->method( $get_calendar );

		$jsonrpc_client->send();

		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

		if ( $result[ $get_calendar->id ]['status'] ) {
			$calendar_rows = $result[ $get_calendar->id ]['data'];
		}
     
		if ( $result[ $get_appointments->id ][ 'status' ] ) {
		
		include( "{$_SERVER['DOCUMENT_ROOT']}/includes/contents/calendar.php" );

		$appointments = $result[ $get_appointments->id ]['data'];

	
		$month_year1 = $datetime->format( "F Y" );

		$calendar_index = str_replace( "%MONTH_YEAR%", $month_year1, $calendar_index );


		$calendar_index = str_replace(
			"%TIME_LABEL%",
			calendar::time_marker( TRUE ),
			$calendar_index
		);

		for( $i = 1; $i < 8; ++$i ) {
			// Given week of year and $i, get date of month
				$datetime->setISODate( $year, $week, $i + 6 );

				$day = $datetime->format( "j" );

				if( isset( $appointments[ $day ] )) {
					$calendar_index = str_replace(
						"%CALENDAR_$i%",
						calendar::day_cell( $day, $appointments[ $day ] ),
						$calendar_index
					);
				} else {
					$calendar_index = str_replace(
						"%CALENDAR_$i%",
						calendar::day_cell( $day, array() ),
						$calendar_index
					);
				}
			}

		$calendar_index = str_replace( "%WEEK%", $week + 1, $calendar_index );
		$calendar_index = str_replace( "%YEAR%", $year, $calendar_index );

		$options = '';
		
		foreach ( $calendar_rows as $calendar ) {

			$checkedvalue = 0;
			if ($calendar['access'] == 1) {
				$checkedvalue = "checked";
			} else {
				$checkedvalue = "";
			}

			$options .=	"<label for=\"{$calendar['calendar_id']}\">"
					  . "<span class=\"check\"><input type= \"checkbox\" name=\"calendarlist\" value=\"{$calendar['calendar_id']}\" {$checkedvalue}> {$calendar['name']} </span> "
					  . "</label>";

		}

		$calendar_index = str_replace(
			"%CALENDARS_SELECT%",
			$options,
			$calendar_index
		);




	echo json_encode( [
		'status'  => TRUE,
		'content' => $calendar_index 

	] );

}


?>