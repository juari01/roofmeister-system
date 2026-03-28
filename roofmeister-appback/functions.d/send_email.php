<?php

	function send_email( $params, $presend = false ) {
	/**
	 * A wrapper function to simplify the use of PHPMailer.
	 *
	 * @param array params - An array containing some optional and some required parameters. Optional parameters are
	 *        either not needed, or default to configuration parameters.
	 * - from            - The sender email address.
	 * - from_name       - The sender name.
	 * - subject         - The subject of the email.
	 * - body            - The body of the email.
	 * - is_html         - TRUE/FALSE indicating whether or not to send as HTML.
	 * - replyto         - An array containing the email address and name for setting the Reply To header.
	 * - replyto_address - The reply to email address.
	 * - replyto_name    - The reply to name.
	 * - to              - An array containing the email address and name for setting the To header.
	 * - to_address      - The to email address.
	 * - to_name         - The to name.
	 * - cc              - An array of CC recipients, or a string containing one CC recipient.
	 * - attachments     - An array containing the name and path of one or more attachments.
	 *
	 * @return bool - TRUE if the messasge is sent successfully, FALSE otherwise.
	 */

		require( \env::$paths['methods'] . '/../config.php' );

		if ( !empty( $params['from'] )) {
		// 'from' is specified, we'll use that here

			$smtp_from = $params['from'];

			if ( !empty( $params['from_name'] )) {
			// 'from_name' is specified, we'll use that here

				$smtp_from_name = $params['from_name'];
			} else {
			// 'from_name' not specified, set it blank

				$smtp_from_name = '';
			}
		} else {
		// 'from not specified, let's use the defaults from the config
			if ( empty( $config_server['smtp']['from'] )) {
				error_log( 'SMTP From not set or specified.' );

				return [ 'status' => FALSE, 'message' => 'SMTP From not set or specified.' ];
			} else {
				$smtp_from      = $config_server['smtp']['from'];
				$smtp_from_name = !empty( $config_server['smtp']['from_name'] ) ? $config_server['smtp']['from_name'] : '';
			}
		}

		if ( !empty( $params['subject'] ) ) {
		// 'subject' specified, we'll use that here

			$smtp_subject = $params['subject'];
		} else {
		// 'subject' not specified, let's use the defaults from the config

			if ( empty( $config_server['smtp']['subject'] )) {
				error_log( 'SMTP Subject not set or specified.' );

				return [ 'status' => FALSE, 'message' => 'SMTP Subject not set or specified.' ];
			} else {
				$smtp_subject = !empty( $config_server['smtp']['subject'] ) ? $config_server['smtp']['subject'] : '';
			}
		}

		try {
			if ( !empty( $config_server['smtp'] )) {
				if ( empty( $config_server['smtp']['host'] )) {
				// 'host' not specified, we can't send anything

					error_log( 'SMTP Host not set.' );

					return [ 'status' => FALSE, 'message' => 'SMTP Host not set.' ];
				} else {
					$smtp_host = $config_server['smtp']['host'];
				}

				$smtp_port     = !empty( $config_server['smtp']['port'] )     ? $config_server['smtp']['port']     : '25';
				$smtp_auth     = !empty( $config_server['smtp']['auth'] )     ? $config_server['smtp']['auth']     : 0;
				$smtp_secure   = !empty( $config_server['smtp']['secure'] )   ? $config_server['smtp']['secure']   : '';
				$smtp_username = !empty( $config_server['smtp']['username'] ) ? $config_server['smtp']['username'] : '';
				$smtp_password = !empty( $config_server['smtp']['password'] ) ? $config_server['smtp']['password'] : '';

				include_once( $config_server['paths']['libs'] . '/phpmailer/class.phpmailer.php' );
				include_once( $config_server['paths']['libs'] . '/phpmailer/class.smtp.php' );

				$mail = new PHPMailer( TRUE );
				$mail->Host   = $smtp_host;
				$mail->Mailer = 'smtp';
				$mail->Port   = $smtp_port;

				if ( !empty( $smtp_secure )) {
					$mail->SMTPSecure = $smtp_secure;
				}

				if ( $smtp_auth ) {
					$mail->SMTPAuth = $smtp_auth;
					$mail->Username = $smtp_username;
					$mail->Password = $smtp_password;
				}

				$mail->From     = $smtp_from;
				$mail->FromName = $smtp_from_name;
				$mail->Subject  = html_entity_decode( $smtp_subject );

				$body = $params['body'];

				preg_match_all( '/img.*?>/', $body, $images );

				if ( !empty( $images ) ) {
					$cid = 1;
					foreach ( $images[0] as $img ) {
						$cid++;
						
						preg_match( '/src="(.*?)"/', $img, $m );

						$imgdata      = explode( ',', $m[1] );
						$mime         = explode( ';', $imgdata[0] );
						$img_type     = explode( ':', $mime[0] );  
						$encoded_data = isset( $imgdata[1] ) ? str_replace( ' ','+',$imgdata[1] ) : '';
						$decoded_data = base64_decode( $encoded_data );  

						if ( $decoded_data != "" ) {
							$mail->AddStringEmbeddedImage( $decoded_data, $cid, $cid, $mime[1], $img_type[1] );

							$params['body'] = str_replace( $img, 'img alt="" src="cid:'.$cid.'" style="border: none; />"', $params['body'] );
						}
					}
				}

				$mail->Body = $params['body'];

				if ( isset( $params['is_html'] )) {
					$mail->IsHTML( $params['is_html'] );
				} else {
				// Set as FALSE by default
					$mail->IsHTML( FALSE );
				}

				$mail->CharSet = 'UTF-8';

				if ( isset( $params['replyto'] ) && is_array( $params['replyto'] )) {
				// We're specifying multiple reply-to headers

					foreach( $params['replyto'] as $replyto ) {
						if ( empty( $replyto['email'] )) {
						// We sent a 'replyto' array without any email addresses

							error_log( 'Empty Reply-To specified.' );

							return [ 'status' => FALSE, 'message' => 'Empty Reply-To specified.' ];
						} else {
							$mail->AddReplyTo( $replyto['email'], isset( $replyto['name'] ) ? $replyto['name'] : '' );
						}
					}
				} elseif ( isset( $params['replyto_address'] ) ) {
				// We're specifying only a single reply-to header

					$mail->AddReplyTo( $params['replyto_address'], isset( $params['replyto_name'] ) ? $params['replyto_name'] : '' );
				}

				if ( isset( $params['to'] ) && is_array( $params['to'] )) {
				// We're specifying multiple recipients

					foreach( $params['to'] as $to ) {
						if ( empty( $to['email'] )) {
						// We sent a 'to' array without any email addresses

							error_log( 'Empty recipient specified.' );

							return [ 'status' => FALSE, 'message' => 'Empty recipient specified.' ];
						} else {
							$mail->AddAddress( $to['email'], isset( $to['name'] ) ? $to['name'] : '' );
						}
					}
				} elseif ( isset( $params['to_address'] ) ) {
				// We're specifying only a single recipient

					$mail->AddAddress( $params['to_address'], isset( $params['to_name'] ) ? $params['to_name'] : '' );
				} else {
				// No recipients specified, we can't send anything

					error_log( 'No recipients specified.' );

					return [ 'status' => FALSE, 'message' => 'No recipients specified.' ];
				}

			// Add CC addresses
				if ( isset( $params['cc'] ) && is_array( $params['cc'] ) ) {
					foreach( $params['cc'] as $cc ) {
						$mail->AddCC( $cc );
					}
				} elseif ( isset( $params['cc'] ) ) {
					$mail->AddCC( $params['cc'] );
				}

			// Add BCC addresses
				if ( isset( $params['bcc'] ) && is_array( $params['bcc'] ) ) {
					foreach( $params['bcc'] as $bcc ) {
						$mail->addBcc( $bcc );
					}
				} elseif ( isset( $params['bcc'] ) ) {
					$mail->addBcc( $params['bcc'] );
				}

			// Add the email attachments
				if ( !empty( $params['attachments'] ) ) {
					foreach( $params['attachments'] as $attachment ) {
						$mail->AddAttachment( $attachment['path'], $attachment['name'] );
					}
				}

				$mail->CharSet = 'UTF-8';

				if ( $presend == true ) {
					if ( $mail->preSend() ) {
						$mime_message = $mail->getSentMIMEMessage();

						try {
							if ( !isset( $params['sendmail'] ) || ( isset( $params['sendmail'] ) && $params['sendmail'] === true ) ) {
								$mail->postSend();
							}
							return [ 'status' => TRUE, 'message' => $mime_message ];
						} catch ( Exception $e ) {
						// Something else bad happened
							error_log( 'Failed to send notificaton emails: ' . $e->getMessage() );

							return [ 'status' => FALSE, 'message' => 'Failed to send notificaton emails: ' . $e->getMessage() ];
						}
					}
				} else {
					if ( !isset( $params['sendmail'] ) || ( isset( $params['sendmail'] ) && $params['sendmail'] === true ) ) {
						$mail->Send();
					}
				}               

			} else {
		// There's no SMTP configuration

				error_log( 'SMTP configuration is not present.' );

				return [ 'status' => FALSE, 'message' => 'SMTP configuration is not present.' ];
			}
		} catch ( Exception $e ) {
		// Something else bad happened
			error_log( 'Failed to send notificaton emails: ' . $e->getMessage() );

			return [ 'status' => FALSE, 'message' => 'Failed to send notificaton emails: ' . $e->getMessage() ];
		}

	// If we made it this far, the email was sent
		return [ 'status' => TRUE ];
	}

?>
