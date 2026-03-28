<div class="timeout-message">
	You will be logged out in <span id="timeout-seconds"></span> seconds due to inactivity.
</div>
<div class="timeout-button">
	<input type="button" value="Stay Logged In" data-function="cancel">
</div>
<script type="text/javascript">

	$( document ).ready( function() {
	// Set countdown
		var seconds = 30;

	// Process countdown
		$( '#timeout-seconds' ).html( seconds );
		var timeout = setInterval(
			function() {
				--seconds;

				$( '#timeout-seconds' ).html( seconds );

				if ( seconds == 0 ) {
					clearTimeout( timeout );

					logout();
				}
			}, 1000
		);

	// Attach event to cancel button
		$( '[data-function=cancel]' ).on( 'click', function( ev ) {
			clearTimeout( timeout );

			$( document ).idleTimer( 'reset' );

			create_popup( 'idle_timeout' );
		} );
	} );

</script>
