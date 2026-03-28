<?php

	class App {
	/**
	 * A class to hold static generic functions related to the application.
	 */

		public static function form_wrapper( $form_name, $sections, $encode_form = false, $no_form = false, $form_method = '', $action_url = '' ) {
		/**
		 * Generates form information wrapped by headers and appropriate
		 * design elements.
		 */

			$encode = $encode_form ? ' enctype="multipart/form-data"' : '';
			$action = $action_url  ? ' action="' . $action_url . '"'  : '';
			$method = $form_method ? ' method="' . $form_method . '"' : '';

			if ( $no_form ) {

				$html = "<div class=\"form\" data-form=\"$form_name\">\n";
			} else {

				$html = "<form name=\"$form_name\"$encode$method$action id=\"$form_name\">\n<div class=\"form\" data-form=\"$form_name\">\n";
			}

			$container = "<div class=\"form-container\">";

			if ( is_array( $sections )) {

				$html .= "<ul class=\"form-wrapper\">\n";

				foreach ( $sections as $tab => $section ) {

					reset( $sections );

					$start_tags = '';
					$end_tags   = '';
					$data       = '';
					$class      = '';

					if ( is_array( $section )) {

						if ( array_key_exists( 'tags', $section ) ) {

							foreach ( $section['tags'] as $tag ) {

								$start_tags .= '<' . $tag . '>';
								$end_tags .= '</' . $tag . '>';
							}
						}

						if ( array_key_exists( 'data', $section ) ) {

							foreach( $section['data'] as $name => $value ) {

								$data .= " data-$name=\"$value\"";
							}
						}

						if ( array_key_exists( 'class', $section ) ) {

							$class = $section['class'];
						}
					}

					if ( $tab == key( $sections )) {

						$html      .= "<li data-form=\"$form_name\" class=\"active\" $data data-name=\"$tab\"><span>$start_tags $tab $end_tags</span>\n";
						$container .= "<div data-form=\"$form_name\" class=\"section active\" data-name=\"$tab\">\n";
					} else {

						$html      .= "<li data-form=\"$form_name\" data-name=\"$tab\" $data><span>$start_tags $tab $end_tags</span>\n";
						$container .= "<div data-form=\"$form_name\" class=\"section\" data-name=\"$tab\">\n";
					}

					if ( is_array( $section )) {

						foreach ( $section['content'] as $label => $element ) {

							$elem_class = 'content-' . trim( strtolower( $label ) );

							$container .= "<h2 class=\"$elem_class\">$label</h2>\n";
							$container .= "<div class=\"elements $elem_class $class\">$element</div><!-- /.elements -->\n";
						}
					}

					$html      .= "</li>\n";
					$container .= "</div><!-- /.section -->\n";
				}

				$html .= "</ul>\n";
			}

			$container .= "</div><!-- /.form-container -->\n";

			$html .= $container . "</div><!-- /.form -->\n</form>\n";

			$html .= <<<JS
<script type="text/javascript">
	$( '.form>ul>li' ).on( 'click', function() {
		let parent_form = $( this ).parent( 'ul' ).parent( 'div.form' );
		let form_name = parent_form.data('form');

		parent_form.find( 'li[data-form="' + form_name + '"]' ).removeClass( 'active' );
		$( this ).addClass( 'active' );

		var data_name = $( this ).data( 'name' );

		parent_form.find( 'div.section[data-form="' + form_name + '"]' ).removeClass( 'active' );
		parent_form.find( 'div.section[data-name="' + data_name + '"]' ).addClass( 'active' );
	} );
</script>
JS;
			return $html;
		}

		public static function image_display( $image ) {
		/**
		 * Converts the supplied array into an HTML-formatted image and returns
		 * the result.
		 *
		 * @param array image - An array containing keys for specific attributes of the image.
		 *        string src - The source of the image.
		 *        string alt - The optional alt text.
		 *
		 * @return string
		 */

			$attr = '';

			if ( isset( $image['attr'] ) && is_array( $image['attr'] )) {

				foreach ( $image['attr'] as $name => $value ) {

					$attr .= " $name=\"$value\"";
				}
			}

			if ( isset( $image['data'] ) && is_array( $image['data'] )) {

				foreach ( $image['data'] as $data ) {

					$attr .= " data-{$data['attr']}=\"{$data['value']}\"";
				}
			}

			$alt = '';

			if ( isset( $image['alt'] ) ) {

				$alt = "alt=\"{$image['alt']}\" ";
			}

			$html = "<img src=\"{$image['src']}\" " . $alt. " " . $attr . ">";

			return $html;
	    }

		public static function js_bind_element( $element, $function, $event ) {
		/**
		 * Generates the JS code to bind the specified function and event to
		 * the specified element.
		 *
		 * @param string element  - The element to bind to.
		 * @param string function - The name of the function to call on the event.
		 * @param string event    - The event to bind to.
		 *
		 * @return string - The generated JS to attach the event.
		 */

			$js  = "<script type=\"type/javascript\">\n";
			$js .= "\t$( '$element' ).on( '$event', function( e ) {\n";
			$js .= "\t\t$function;\n";
			$js .= "\t} );\n";
			$js .= "</script>\n";

			return $js;
	    }

		public static function table_display( $table, $class = null, $header = null ) {
		/**
		 * Converts the supplied array into an HTML-formatted table and returns
		 * the result.
		 *
		 * @param array table   - An array containing the table elements.
		 * @param string class  - An optional class for the table.
		 * @param string header - An optional header for the table.
		 *
		 * @return string - The compiled HTML content of the generated table.
		 */

			$html = '';

			if ( isset( $header )) {

				$html .= "<h2 class=\"$class\">$header</h2>\n";
			}

			if ( isset( $class )) {

				$html .= "<table class=\"$class\">\n";
			} else {

				$html .= "<table>\n";
			}

		// Always have a <thead> section
			$html .= "<thead>\n";

			if ( isset( $table['header'] ) && is_array( $table['header'] )) {

				$html .= "<tr>\n";

				foreach ( $table['header'] as $header ) {

					$attr = '';

					if ( isset( $header['attr'] ) && is_array( $header['attr'] )) {

						foreach( $header['attr'] as $name => $value ) {

							$attr .= " $name=\"$value\"";
						}
					}

					$html .= "<th $attr>{$header['value']}</th>\n";
				}

				$html .= "</tr>\n";
			}

			$html .= '</thead>';

			if ( isset( $table['footer'] ) && is_array( $table['footer'] )) {

				$html .= "<tfoot>\n<tr>\n";

				foreach( $table['footer'] as $footer ) {

					$html .= "<th>$footer</th>\n";
				}

				$html .= "</tr>\n</tfoot>\n";
			}

		// Always have a <tbody> section
			$html .= "<tbody>\n";

			if ( isset( $table['body'] ) && is_array( $table['body'] )) {

				$html .= "<tr>\n";

				foreach ( $table['body'] as $body ) {

					$attr = '';

					if ( isset( $body['attr'] ) && is_array( $body['attr'] )) {

						foreach( $body['attr'] as $name => $value ) {

							$attr .= " $name=\"$value\"";
						}
					}

					if ( isset( $body['data'] ) && is_array( $body['data'] )) {

						foreach ( $body['data'] as $data ) {

							$attr .= " data-{$data['attr']}=\"{$data['value']}\"";
						}
					}

					$html .= '<tr' . $attr . ">\n";

					foreach ( $body['cells'] as $row ) {

						$attr = '';

						if ( isset( $row['attr'] ) && is_array( $row['attr'] )) {

							foreach ( $row['attr'] as $name => $value ) {
								$attr .= " $name=\"$value\"";
							}
						}

						$html .= "<td $attr>{$row['value']}</td>\n";
					}

					$html .= "</tr>\n";
				}

				$html .= "</tr>\n";
			}

			$html .= "</tbody>\n";

			$html .= "</table>\n";

			return $html;
	    }


	Public static function format_date( $date, $format = NULL, $tz_offset = '' ) {
			/**
			 * Formats the specified date according to the configured format with either the specified timezone, or the timezone
			 * stored in the SESSION applied. If neither are specified, the returned date will have the server's timezone
			 * applied.
			 *
			 * @param string date      - The date to be formatted. Must be either "YYYY-MM-DD HH:ii:ss" or "YYYY-MM-DD". The
			 *        passed format will tell the function if it's just a date, or a date with a time.
			 * @param string format    - An optional format to override that set in the conifg. Note that this can either be a
			 *        date or datetime format. It's up to the caller of the function to know which is correct in context.
			 * @param string tz_offset - An optional timezone offset from GMT in hours.
			 *
			 * @return string - The formatted time.
			 */
		
			// Read in configuration
			
		

				
// Include Atlas autoloader
		require( __DIR__ . '/../autoloader_atlas.php' );

	// Include App autoloader
		require( __DIR__ . '/../autoloader_app.php' );
	
	// Read application configuration
		$config = new \Atlas\Config( file_get_contents( __DIR__ . '/../config.ini' ));

		// global $config;
			
			// Check to see if this is a date, datetime, or neither
				if ( $date == date( 'Y-m-d', strtotime( $date ))) {
					$type = 'date';
				} elseif ( $date = date( 'Y-m-d H:i:s', strtotime( $date ))) {
					$type = 'datetime';
				} else {
					throw new Exception( "Invalid date $date specified, must be 'YYYY-MM-DD HH:ii:ss' or 'YYYY-MM-DD'." );
				}
		
			// Calculate the server's offset
				$server_offset = date( 'P' );
		
				$sign = substr( $server_offset, 0, 1 );
				$hour = substr( $server_offset, 1, 2 );
				$min  = substr( $server_offset, 4, 2 );
		
				$server_offset = $sign . intval( $hour ) . '.' . round( $min / 60, 2 );
		
			// Determine if we're using a specified value, a session value, or the server's value
				if ( $tz_offset == 'LOCAL' ) {
				// Use the specified timezone value
		
					$offset = 0;
				} elseif ( $tz_offset != '' ) {
				// Explicitly use the server's local timezone
		
					$offset = $tz_offset - $server_offset;
				} elseif ( isset( $_SESSION['tz_offset'] )) {
				// Use the timezone stored in the session
		
					$offset = $_SESSION['tz_offset'] - $server_offset;
				} else {
				// Use the server's local timezone
		
					$offset = 0;
				}
		
			// Format the date and return
				$date_o = new DateTime( $date );
		
				$date_offset = strpos( $offset, '.' ) !== FALSE ? 'PT' . ( $offset * 60 ) . 'M' : 'PT' . abs( $offset ) . 'H';
		
				$interval = new DateInterval( $date_offset );
				if ( ( strpos( $offset, '.' ) === FALSE ) && $offset < 0 ) {
					$interval->invert = 1;
				}
		
				$date_o->add( $interval );
		
				if ( is_null( $format )) {
					if ( !isset( $config->get['formats'][ $type ] )) {
						throw new Exception( "Unable to format date, be sure to set \$config->get['formats'][ $type ] in config.ini" );
					} else {
						$this_format = $config->get['formats'][ $type ];
					}
				} else {
					$this_format = $format;
				}
		
				return $date_o->format( $this_format );
			}
	
	Public static function filesize_suffix( $bytes ) {
			/**
			 * Filesize Suffix
			 * Calculates the appropriate filesize suffix for the given number
			 * of bytes.
			 */
				if( $bytes > 1024 * 1024 * 1024 * 1024 ) {
					$bytes = ( floor( $bytes / ( 1024 * 1024 * 1024 * 1024 ) * 100 ) / 100 ) . 'TB';
				} elseif( $bytes > 1024 * 1024 * 1024 ) {
					$bytes = ( floor( $bytes / ( 1024 * 1024 * 1024 ) * 100 ) / 100 ) . 'GB';
				} elseif( $bytes > 1024 * 1024 ) {
					$bytes = ( floor( $bytes / ( 1024 * 1024 ) * 100 ) / 100 ) . 'MB';
				} elseif( $bytes > 1024 ) {
					$bytes = ( floor( $bytes / ( 1024 ) * 100 ) / 100 ) . 'KB';
				} else {
					$bytes = $bytes . 'B';
				}
		
				return $bytes;
			}
	
	
	}


	
?>
