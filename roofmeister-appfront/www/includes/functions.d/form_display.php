<?php

/**
 * Form Display
 * This function generates HTML form input fields based on an array and
 * a template.
 */

	function form_display( $form, $formats ) {
		if ( is_array( $form )) {
			$html = '';

			foreach ( $form as $element ) {
			if ( $element['type'] == 'parent-container' ) {
					$replace = $formats['parent-container'];
					$replace = str_replace( '%CLASS%',   isset( $element['class'] )   ? $element['class']   : '', $replace );
					$replace = str_replace( '%ID%',      isset( $element['id'] )      ? $element['id']      : '', $replace );
					$replace = str_replace( '%CONTENT%', isset( $element['content'] ) ? $element['content'] : '', $replace );

					$data_values = '';

					if ( isset( $element['data'] ) && is_array( $element['data'] )) {
						foreach ( $element['data'] as $data ) {
							$data_values .= " data-{$data['name']}=\"{$data['value']}\"";
						}
					}

					$replace = str_replace( '%DATA%', $data_values, $replace );

					$replace = str_replace( '%CHILDREN%', isset( $element['children'] ) ? form_display( $element['children'], $formats ) : '', $replace );

					$html .= $replace;
				} elseif ( $element['type'] == 'autosuggest' ) {
					$replace = $formats['autosuggest'];
					$replace = str_replace( '%LABEL%',       isset( $element['label'] )       ? $element['label']       : '', $replace );
					$replace = str_replace( '%NAME%',        isset( $element['name'] )        ? $element['name']        : '', $replace );
					$replace = str_replace( '%VALUE%',       isset( $element['value'] )       ? $element['value']       : '', $replace );
					$replace = str_replace( '%PLACEHOLDER%', isset( $element['placeholder'] ) ? $element['placeholder'] : '', $replace );
					$replace = str_replace( '%PARAMS%',      isset( $element['params'] )      ? $element['params']      : '', $replace );
					$replace = str_replace( '%TOOLTIP%',     isset( $element['tooltip'] )     ? $element['tooltip']     : '', $replace );

					if ( isset( $element['class'] )) {
						$replace = str_replace( '%CLASS%', ' ' . $element['class'], $replace );
					} else {
						$replace = str_replace( '%CLASS%', '', $replace );
					}

					$data_values = '';

					if ( isset( $element['data'] ) && is_array( $element['data'] )) {
						foreach ( $element['data'] as $data ) {
							$data_values .= " data-{$data['name']}=\"{$data['value']}\"";
						}
					}

					$replace = str_replace( '%DATA%', $data_values, $replace );

					$html .= $replace;
				} elseif( $element['type'] == 'tinymce' ) {
					$replace = $formats['tinymce'];
					$replace = str_replace( '%LABEL%',       isset( $element['label'] )       ? $element['label']       : '', $replace );
					$replace = str_replace( '%NAME%',        isset( $element['name'] )        ? $element['name']        : '', $replace );
					$replace = str_replace( '%VALUE%',       isset( $element['value'] )       ? $element['value']       : '', $replace );
					$replace = str_replace( '%PARAMS%',      isset( $element['params'] )      ? $element['params']      : '', $replace );

					if ( isset( $element['class'] )) {
						$replace = str_replace( '%CLASS%', ' ' . $element['class'], $replace );
					} else {
						$replace = str_replace( '%CLASS%', '', $replace );
					}

					$data_values = '';

					if ( isset( $element['data'] ) && is_array( $element['data'] )) {
						foreach ( $element['data'] as $data ) {
							$data_values .= " data-{$data['name']}=\"{$data['value']}\"";
						}
					}

					$replace = str_replace( '%DATA%', $data_values, $replace );

					$html .= $replace;
				} elseif ( $element['type'] == 'button' ) {
					$replace = $formats['button'];
					$replace = str_replace( '%NAME%',     isset( $element['name'] )        ? $element['name']        : '', $replace );
					$replace = str_replace( '%VALUE%',    isset( $element['value'] )       ? $element['value']       : '', $replace );
					$replace = str_replace( '%CLASS%',    isset( $element['class'] )       ? $element['class']       : '', $replace );
					$replace = str_replace( '%TOOLTIP%',  isset( $element['tooltip'] )     ? $element['tooltip']     : '', $replace );
					$replace = str_replace( '%TYPE%',     isset( $element['button_type'] ) ? $element['button_type'] : 'button', $replace );
					$replace = str_replace( '%DISABLED%', ( isset( $element['disabled'] ) && $element['disabled'] ) ? 'disabled' : '', $replace );

					$data_values = '';

					if ( isset( $element['data'] ) && is_array( $element['data'] )) {
						foreach ( $element['data'] as $data ) {
							$data_values .= " data-{$data['name']}=\"{$data['value']}\"";
						}
					}

					$replace = str_replace( '%DATA%', $data_values, $replace );

					$attr_values = '';

					if ( isset( $element['attr'] ) && is_array( $element['attr'] )) {
						foreach ( $element['attr'] as $attr ) {
							$attr_values .= " {$attr['name']}=\"{$attr['value']}\"";
						}
					}

					$replace = str_replace( '%ATTR%', $attr_values, $replace );

					$html .= $replace;
				} elseif ( $element['type'] == 'checkbox' ) {
					$replace = $formats['checkbox'];
					$replace = str_replace( '%LABEL%',   isset( $element['label'] )   ? $element['label']   : '', $replace );
					$replace = str_replace( '%CLASS%',   isset( $element['class'] )   ? $element['class']   : '', $replace );
					$replace = str_replace( '%TOOLTIP%', isset( $element['tooltip'] ) ? $element['tooltip'] : '', $replace );

					preg_match( '/%%OPTIONS%%(.*)%%OPTIONS%%/s', $replace, $matches );

					if ( is_array( $element['options'] )) {
						$options = [];

						foreach ( $element['options'] as $i => $option ) {
							$options[ $i ] = $matches[1];
							$options[ $i ] = str_replace( '%DISPLAY%',  isset( $option['display'] )   ? $option['display']  : '', $options[ $i ] );
							$options[ $i ] = str_replace( '%NAME%',     isset( $option['name'] )      ? $option['name']     : '', $options[ $i ] );
							$options[ $i ] = str_replace( '%VALUE%',    isset( $option['value'] )     ? $option['value']    : '', $options[ $i ] );
							$options[ $i ] = str_replace( '%CHECKED%',  !empty( $option['checked'] )  ? ' checked'          : '', $options[ $i ] );
							$options[ $i ] = str_replace( '%DISABLED%', !empty( $option['disabled'] ) ? ' disabled'         : '', $options[ $i ] );
							$options[ $i ] = str_replace( '%TOOLTIP%',  isset( $element['tooltip'] )  ? $element['tooltip'] : '', $options[ $i ] );

							$option_data = '';
							if( isset( $option['data'] ) && is_array( $option['data'] )) {
								foreach( $option['data'] as $data ) {
									$option_data .= " data-{$data['name']}=\"{$data['value']}\"";
								}
							}
							$options[ $i ] = str_replace( '%DATA%', $option_data, $options[ $i ] );

							$option_attr = '';
							if( isset( $option['attr'] ) && is_array( $option['attr'] )) {
								foreach( $option['attr'] as $attr ) {
									$option_attr .= " {$attr['name']}=\"{$attr['value']}\"";
								}
							}
							$options[ $i ] = str_replace( '%ATTR%', $option_attr, $options[ $i ] );
						}

						$replace = str_replace( $matches[0], implode( '', $options ), $replace );
					}

					$data_values = '';

					if ( isset( $element['data'] ) && is_array( $element['data'] )) {
						foreach ( $element['data'] as $data ) {
							$data_values .= " data-{$data['name']}=\"{$data['value']}\"";
						}
					}

					$replace = str_replace( '%DATA%', $data_values, $replace );

					$attr_values = '';

					if ( isset( $element['attr'] ) && is_array( $element['attr'] )) {
						foreach ( $element['attr'] as $attr ) {
							$attr_values .= " {$attr['name']}=\"{$attr['value']}\"";
						}
					}

					$replace = str_replace( '%ATTR%', $attr_values, $replace );

					$html .= $replace;
				} elseif ( $element['type'] == 'single-checkbox' ) {
					$replace = $formats['single-checkbox'];
					$replace = str_replace( '%LABEL%',    isset( $element['label'] )     ? $element['label']   : '', $replace );
					$replace = str_replace( '%CLASS%',    isset( $element['class'] )     ? $element['class']   : '', $replace );
					$replace = str_replace( '%TOOLTIP%',  isset( $element['tooltip'] )   ? $element['tooltip'] : '', $replace );
					$replace = str_replace( '%NAME%',     isset( $element['name'] )      ? $element['name']    : '', $replace );
					$replace = str_replace( '%VALUE%',    isset( $element['value'] )     ? $element['value']   : '', $replace );
					$replace = str_replace( '%CHECKED%',  !empty( $element['checked'] )  ? ' checked'          : '', $replace );
					$replace = str_replace( '%DISABLED%', !empty( $element['disabled'] ) ? ' disabled'         : '', $replace );
					$replace = str_replace( '%TOOLTIP%',  isset( $element['tooltip'] )   ? $element['tooltip'] : '', $replace );

					$element_data = '';
					if( isset( $element['data'] ) && is_array( $element['data'] )) {
						foreach( $element['data'] as $data ) {
							$element_data .= " data-{$data['name']}=\"{$data['value']}\"";
						}
					}
					$replace = str_replace( '%DATA%', $element_data, $replace );

					$element_attr = '';
					if( isset( $element['attr'] ) && is_array( $element['attr'] )) {
						foreach( $element['attr'] as $attr ) {
							$element_attr .= " {$attr['name']}=\"{$attr['value']}\"";
						}
					}
					$replace = str_replace( '%ATTR%', $element_attr, $replace );

					$html .= $replace;
				} elseif ( $element['type'] == 'container' ) {
					$replace = $formats['container'];
					$replace = str_replace( '%ID%',      isset( $element['id'] )      ? $element['id']      : '', $replace );
					$replace = str_replace( '%CLASS%',   isset( $element['class'] )   ? $element['class']   : '', $replace );
					$replace = str_replace( '%TOOLTIP%', isset( $element['tooltip'] ) ? $element['tooltip'] : '', $replace );
					$replace = str_replace( '%HTML%',    isset( $element['html'] )    ? $element['html']    : '', $replace );

					$data_values = '';

					if ( isset( $element['data'] ) && is_array( $element['data'] )) {
						foreach ( $element['data'] as $data ) {
							$data_values .= " data-{$data['name']}=\"{$data['value']}\"";
						}
					}

					$replace = str_replace( '%DATA%', $data_values, $replace );

					$attr_values = '';

					if ( isset( $element['attr'] ) && is_array( $element['attr'] )) {
						foreach ( $element['attr'] as $attr ) {
							$attr_values .= " {$attr['name']}=\"{$attr['value']}\"";
						}
					}

					$replace = str_replace( '%ATTR%', $attr_values, $replace );

					$html .= $replace;
				} elseif ( $element['type'] == 'hidden' ) {
					$replace = $formats['hidden'];
					$replace = str_replace( '%NAME%',    isset( $element['name'] )  ? $element['name']  : '', $replace );
					$replace = str_replace( '%VALUE%',   isset( $element['value'] ) ? $element['value'] : '', $replace );
					$replace = str_replace( '%CLASS%',   isset( $element['class'] ) ? $element['class'] : '', $replace );

					$data_values = '';

					if ( isset( $element['data'] ) && is_array( $element['data'] )) {
						foreach ( $element['data'] as $data ) {
							$data_values .= " data-{$data['name']}=\"{$data['value']}\"";
						}
					}

					$replace = str_replace( '%DATA%', $data_values, $replace );

					$html .= $replace;
				} elseif ( $element['type'] == 'hr' ) {
					$replace = $formats['hr'];

					$html .= $replace;
				} elseif ( $element['type'] == 'linebreak' ) {
					$replace = $formats['linebreak'];
					$replace = str_replace( '%CLASS%', isset( $element['class'] ) ? $element['class'] : '', $replace );

					$html .= $replace;
				} elseif ( $element['type'] == 'multicheck' ) {
					$replace = $formats['multicheck'];
					$replace = str_replace( '%LABEL%',   isset( $element['label'] )   ? $element['label']   : '', $replace );
					$replace = str_replace( '%CLASS%',   isset( $element['class'] )   ? $element['class']   : '', $replace );
					$replace = str_replace( '%TOOLTIP%', isset( $element['tooltip'] ) ? $element['tooltip'] : '', $replace );

					preg_match( '/%%OPTIONS%%(.*)%%OPTIONS%%/s', $replace, $matches );

					if ( is_array( $element['options'] )) {
						$options = [];

						foreach ( $element['options'] as $i => $option ) {
							$options[ $i ] = $matches[1];
							$options[ $i ] = str_replace( '%DISPLAY%', isset( $option['display'] ) ? $option['display'] : '', $options[ $i ] );
							$options[ $i ] = str_replace( '%NAME%',    "{$element['name']}[{$option['value']}]",              $options[ $i ] );
							$options[ $i ] = str_replace( '%VALUE%',   '1',                                                   $options[ $i ] );

							if ( $option['selected'] ) {
								$options[ $i ] = str_replace( '%CHECKED%', ' checked', $options[ $i ] );
							} else {
								$options[ $i ] = str_replace( '%CHECKED%', '', $options[ $i ] );
							}
						}

						$replace = str_replace( $matches[0], implode( '', $options ), $replace );
					}

					$data_values = '';

					if ( isset( $element['data'] ) && is_array( $element['data'] )) {
						foreach ( $element['data'] as $data ) {
							$data_values .= " data-{$data['name']}=\"{$data['value']}\"";
						}
					}

					$replace = str_replace( '%DATA%', $data_values, $replace );

					$html .= $replace;
				} elseif ( $element['type'] == 'password' ) {
					$replace = $formats['password'];
					$replace = str_replace( '%LABEL%',       isset( $element['label'] )       ? $element['label']       : '', $replace );
					$replace = str_replace( '%NAME%',        isset( $element['name'] )        ? $element['name']        : '', $replace );
					$replace = str_replace( '%VALUE%',       isset( $element['value'] )       ? $element['value']       : '', $replace );
					$replace = str_replace( '%PLACEHOLDER%', isset( $element['placeholder'] ) ? $element['placeholder'] : '', $replace );
					$replace = str_replace( '%CLASS%',       isset( $element['class'] )       ? $element['class']       : '', $replace );
					$replace = str_replace( '%TOOLTIP%',     isset( $element['tooltip'] )     ? $element['tooltip']     : '', $replace );

					$data_values = '';

					if ( isset( $element['data'] ) && is_array( $element['data'] )) {
						foreach ( $element['data'] as $data ) {
							$data_values .= " data-{$data['name']}=\"{$data['value']}\"";
						}
					}

					$replace = str_replace( '%DATA%', $data_values, $replace );

					$html .= $replace;
				} elseif ( $element['type'] == 'radio' ) {
					$replace = $formats['radio'];
					$replace = str_replace( '%LABEL%',   isset( $element['label'] )   ? $element['label']   : '', $replace );
					$replace = str_replace( '%CLASS%',   isset( $element['class'] )   ? $element['class']   : '', $replace );
					$replace = str_replace( '%NAME%',    isset( $element['name'] )    ? $element['name']    : '', $replace );
					$replace = str_replace( '%TOOLTIP%', isset( $element['tooltip'] ) ? $element['tooltip'] : '', $replace );

					preg_match( '/%%OPTIONS%%(.*)%%OPTIONS%%/s', $replace, $matches );

					if ( is_array( $element['options'] )) {
						$options = [];

						foreach ( $element['options'] as $i => $option ) {
							$options[ $i ] = $matches[1];
							$options[ $i ] = str_replace( '%DISPLAY%', isset( $option['display'] )  ? $option['display']  : '', $options[ $i ] );
							$options[ $i ] = str_replace( '%VALUE%',   isset( $option['value'] )    ? $option['value']    : '', $options[ $i ] );
							$options[ $i ] = str_replace( '%TOOLTIP%', isset( $element['tooltip'] ) ? $element['tooltip'] : '', $options[ $i ] );

							if ( $element['selected'] == $option['value'] ) {
								$options[ $i ] = str_replace( '%CHECKED%', ' checked', $options[$i] );
							} else {
								$options[ $i ] = str_replace( '%CHECKED%', '', $options[$i] );
							}
						}

						$replace = str_replace( $matches[0], implode( '', $options ), $replace );
					}

					$data_values = '';

					if ( isset( $element['data'] ) && is_array( $element['data'] )) {
						foreach ( $element['data'] as $data ) {
							$data_values .= " data-{$data['name']}=\"{$data['value']}\"";
						}
					}

					$replace = str_replace( '%DATA%', $data_values, $replace );

					$html .= $replace;
				} elseif ( $element['type'] == 'select' ) {
					$replace = $formats['select'];
					$replace = str_replace( '%LABEL%',   isset( $element['label'] )   ? $element['label']   : '', $replace );
					$replace = str_replace( '%NAME%',    isset( $element['name'] )    ? $element['name']    : '', $replace );
					$replace = str_replace( '%CLASS%',   isset( $element['class'] )   ? $element['class']   : '', $replace );
					$replace = str_replace( '%TOOLTIP%', isset( $element['tooltip'] ) ? $element['tooltip'] : '', $replace );
					$replace = str_replace( "%DISABLED%", isset( $element['disabled'] ) ? $element['disabled'] : '', $replace );

					preg_match( '/%%OPTIONS%%(.*)%%OPTIONS%%/s', $replace, $matches );

					if ( is_array( $element['options'] )) {
						$options = [];

						foreach ( $element['options'] as $i => $option ) {
							$options[ $i ] = $matches[1];
							$options[ $i ] = str_replace( '%DISPLAY%', isset( $option['display'] )  ? $option['display']  : '', $options[ $i ] );
							$options[ $i ] = str_replace( '%VALUE%',   isset( $option['value'] )    ? $option['value']    : '', $options[ $i ] );
							$options[ $i ] = str_replace( '%TOOLTIP%', isset( $element['tooltip'] ) ? $element['tooltip'] : '', $options[ $i ] );

							$option_data = '';
							if( isset( $option['data'] ) && is_array( $option['data'] )) {
								foreach( $option['data'] as $data ) {
									$option_data .= " data-{$data['name']}=\"{$data['value']}\"";
								}
							}
							$options[ $i ] = str_replace( '%DATA%', $option_data, $options[ $i ] );

							if ( $element['selected'] == $option['value'] ) {
								$options[ $i ] = str_replace( '%SELECTED%', ' selected', $options[ $i ] );
							} else {
								$options[ $i ] = str_replace( '%SELECTED%', '', $options[ $i ] );
							}
						}

						$replace = str_replace( $matches[0], implode( '', $options ), $replace );
					}

					$data_values = '';

					if ( isset( $element['data'] ) && is_array( $element['data'] )) {
						foreach ( $element['data'] as $data ) {
							$data_values .= " data-{$data['name']}=\"{$data['value']}\"";
						}
					}

					$replace = str_replace( '%DATA%', $data_values, $replace );

					$html .= $replace;
				}  elseif ( $element['type'] == 'chosen-optgroup' ) {
					$replace = $formats['chosen-optgroup'];
					$replace = str_replace( "%LABEL%",    isset( $element['label'] )    ? $element['label']    : "", $replace );
					$replace = str_replace( "%NAME%",     isset( $element['name'] )     ? $element['name']     : "", $replace );
					$replace = str_replace( "%PARAMS%",   isset( $element['params'] )   ? $element['params']   : "", $replace );
					$replace = str_replace( "%CLASS%",    isset( $element['class'] )    ? $element['class']    : "", $replace );
					$replace = str_replace( "%MULTIPLE%", isset( $element['multiple'] ) ? "multiple"           : "", $replace );
					$replace = str_replace( "%DISABLED%", isset( $element['disabled'] ) ? $element['disabled'] : "", $replace );

					preg_match( "/%%OPTIONS%%(.*)%%OPTIONS%%/s", $replace, $matches );

					if( is_array( $element['options'] )) {
						$groups = [];
						foreach( $element['options'] as $label => $options ) {
							$opts = [];
							foreach( $options as $i => $option ) {
								$opts[$i] = $matches[1];
								$opts[$i] = str_replace( "%DISPLAY%", isset( $option['display'] ) ? $option['display'] : "", $opts[$i] );
								$opts[$i] = str_replace( "%VALUE%",   isset( $option['value'] )   ? $option['value']   : "", $opts[$i] );

								$option_data = '';
								if( isset( $option['data'] ) && is_array( $option['data'] )) {
									foreach( $option['data'] as $data ) {
										$option_data .= " data-{$data['name']}=\"{$data['value']}\"";
									}
								}
								$opts[ $i ] = str_replace( '%DATA%', $option_data, $opts[ $i ] );

								if ( is_array( $element['selected'] ) ) {
									if( in_array( $option['value'], $element['selected'] ) ) {
										$opts[ $i ] = str_replace( "%SELECTED%", " selected", $opts[$i] );
									} else {
										$opts[ $i ] = str_replace( "%SELECTED%", "", $opts[$i] );
									}
								} else {
									if( $element['selected'] == $option['value'] ) {
										$opts[ $i ] = str_replace( "%SELECTED%", " selected", $opts[$i] );
									} else {
										$opts[ $i ] = str_replace( "%SELECTED%", "", $opts[$i] );
									}
								}
							}
							$groups[] = '<optgroup label="' . $label . '">' . implode( "", $opts ) . '</optgroup>';
						}

						$replace = str_replace( $matches[0], implode( "", $groups ), $replace );
					}

					$data_values = "";

					if( isset( $element['data'] ) && is_array( $element['data'] )) {
						foreach( $element['data'] as $data ) {
							$data_values .= " data-{$data['name']}=\"{$data['value']}\"";
						}
					}

					$replace = str_replace( "%DATA%", $data_values, $replace );

					$attr_values = '';

					if ( isset( $element['attr'] ) && is_array( $element['attr'] )) {
						foreach ( $element['attr'] as $attr ) {
							$attr_values .= " {$attr['name']}=\"{$attr['value']}\"";
						}
					}

					$replace = str_replace( '%ATTR%', $attr_values, $replace );

					$html .= $replace;
				} elseif ( $element['type'] == 'textarea' ) {
					$replace = $formats['textarea'];
					$replace = str_replace( '%LABEL%',       isset( $element['label'] )       ? $element['label']       : '', $replace );
					$replace = str_replace( '%NAME%',        isset( $element['name'] )        ? $element['name']        : '', $replace );
					$replace = str_replace( '%VALUE%',       isset( $element['value'] )       ? $element['value']       : '', $replace );
					$replace = str_replace( '%PLACEHOLDER%', isset( $element['placeholder'] ) ? $element['placeholder'] : '', $replace );
					$replace = str_replace( '%WRAP%',        isset( $element['wrap'] )        ? ' wrap'                 : '', $replace );
					$replace = str_replace( '%TOOLTIP%',     isset( $element['tooltip'] )     ? $element['tooltip']     : '', $replace );
					$replace = str_replace( '%READONLY%',    ( isset( $element['readonly'] ) && $element['readonly'] )    ? 'readonly'              : '', $replace );

					if ( isset( $element['class'] )) {
						$replace = str_replace( '%CLASS%', ' ' . $element['class'], $replace );
					} else {
						$replace = str_replace( '%CLASS%', '', $replace );
					}

					$data_values = '';

					if ( isset( $element['data'] ) && is_array( $element['data'] )) {
						foreach ( $element['data'] as $data ) {
							$data_values .= " data-{$data['name']}=\"{$data['value']}\"";
						}
					}

					$replace = str_replace( '%DATA%', $data_values, $replace );

					$attr_values = '';

					if ( isset( $element['attr'] ) && is_array( $element['attr'] )) {
						foreach ( $element['attr'] as $attr ) {
							$attr_values .= " {$attr['name']}=\"{$attr['value']}\"";
						}
					}

					$replace = str_replace( '%ATTR%', $attr_values, $replace );

					$html .= $replace;
				} elseif ( $element['type'] == 'datepicker' ) {
					$replace = $formats['datepicker'];
					$replace = str_replace( '%LABEL%',   isset( $element['label'] )   ? $element['label']   : '', $replace );
					$replace = str_replace( '%NAME%',    isset( $element['name'] )    ? $element['name']    : '', $replace );
					$replace = str_replace( '%VALUE%',   isset( $element['value'] )   ? $element['value']   : '', $replace );
					$replace = str_replace( '%PARAMS%',  isset( $element['params'] )  ? $element['params']  : '', $replace );
					$replace = str_replace( '%TOOLTIP%', isset( $element['tooltip'] ) ? $element['tooltip'] : '', $replace );

					if ( isset( $element['class'] )) {
						$replace = str_replace( '%CLASS%', ' ' . $element['class'], $replace );
					} else {
						$replace = str_replace( '%CLASS%', '', $replace );
					}

					$data_values = '';

					if ( isset( $element['data'] ) && is_array( $element['data'] )) {
						foreach ( $element['data'] as $data ) {
							$data_values .= " data-{$data['name']}=\"{$data['value']}\"";
						}
					}

					$replace = str_replace( '%DATA%', $data_values, $replace );

					$html .= $replace;
				} elseif( $element['type'] == 'file' ) {
					$replace = $formats['file'];
					$replace = str_replace( '%LABEL%',    isset( $element['label'] )    ? $element['label']  : '', $replace );
					$replace = str_replace( '%NAME%',     isset( $element['name'] )     ? $element['name']   : '', $replace );
					$replace = str_replace( '%PARAMS%',   isset( $element['params'] )   ? $element['params'] : '', $replace );
					$replace = str_replace( '%ID%',       isset( $element['id'] )       ? $element['id']     : '', $replace );
					$replace = str_replace( "%MULTIPLE%", isset( $element['multiple'] ) ? "multiple"         : "", $replace );


					if ( isset( $element['class'] )) {
						$replace = str_replace( '%CLASS%', ' ' . $element['class'], $replace );
					} else {
						$replace = str_replace( '%CLASS%', '', $replace );
					}

					$data_values = '';

					if ( isset( $element['data'] ) && is_array( $element['data'] )) {
						foreach ( $element['data'] as $data ) {
							$data_values .= " data-{$data['name']}=\"{$data['value']}\"";
						}
					}

					$replace = str_replace( '%DATA%', $data_values, $replace );

					$html .= $replace;
				} elseif( $element['type'] == 'static' ) {
					$replace = $formats['static'];
					$replace = str_replace( '%LABEL%', isset( $element['label'] ) ? $element['label'] : '', $replace );
					$replace = str_replace( '%VALUE%', isset( $element['value'] ) ? $element['value'] : '', $replace );
					$replace = str_replace( '%TITLE%', isset( $element['title'] ) ? $element['title'] : '', $replace );

					if ( isset( $element['class'] )) {
						$replace = str_replace( '%CLASS%', ' ' . $element['class'], $replace );
					} else {
						$replace = str_replace( '%CLASS%', '', $replace );
					}

					$data_values = '';

					if ( isset( $element['data'] ) && is_array( $element['data'] )) {
						foreach ( $element['data'] as $data ) {
							$data_values .= " data-{$data['name']}=\"{$data['value']}\"";
						}
					}

					$replace = str_replace( '%DATA%', $data_values, $replace );

					$attr_values = '';

					if ( isset( $element['attr'] ) && is_array( $element['attr'] )) {
						foreach ( $element['attr'] as $attr ) {
							$attr_values .= " {$attr['name']}=\"{$attr['value']}\"";
						}
					}

					$replace = str_replace( '%ATTR%', $attr_values, $replace );

					$html .= $replace;
				} elseif ( $element['type'] == 'chosen' ) {
					$replace = $formats['chosen'];
					$replace = str_replace( "%LABEL%",    isset( $element['label'] )    ? $element['label']    : "", $replace );
					$replace = str_replace( "%NAME%",     isset( $element['name'] )     ? $element['name']     : "", $replace );
					$replace = str_replace( "%PARAMS%",   isset( $element['params'] )   ? $element['params']   : "", $replace );
					$replace = str_replace( "%CLASS%",    isset( $element['class'] )    ? $element['class']    : "", $replace );
					$replace = str_replace( "%MULTIPLE%", isset( $element['multiple'] ) ? "multiple"           : "", $replace );
					$replace = str_replace( "%DISABLED%", isset( $element['disabled'] ) ? $element['disabled'] : "", $replace );

					preg_match( "/%%OPTIONS%%(.*)%%OPTIONS%%/s", $replace, $matches );

					if( is_array( $element['options'] )) {
						$options = [];

						foreach( $element['options'] as $i => $option ) {
							$options[$i] = $matches[1];
							$options[$i] = str_replace( "%DISPLAY%", isset( $option['display'] ) ? $option['display'] : "", $options[$i] );
							$options[$i] = str_replace( "%VALUE%",   isset( $option['value'] )   ? $option['value']   : "", $options[$i] );

							if( $element['selected'] == $option['value'] || $option['selected'] ) {
								$options[$i] = str_replace( "%SELECTED%", " selected", $options[$i] );
							} else {
								$options[$i] = str_replace( "%SELECTED%", "", $options[$i] );
							}

							$option_data = '';
							if( isset( $option['data'] ) && is_array( $option['data'] )) {
								foreach( $option['data'] as $data ) {
									$option_data .= " data-{$data['name']}=\"{$data['value']}\"";
								}
							}
							$options[ $i ] = str_replace( '%DATA%', $option_data, $options[ $i ] );	
						}

						$replace = str_replace( $matches[0], implode( "", $options ), $replace );
					}

					$data_values = "";

					if( isset( $element['data'] ) && is_array( $element['data'] )) {
						foreach( $element['data'] as $data ) {
							$data_values .= " data-{$data['name']}=\"{$data['value']}\"";
						}
					}

					$replace = str_replace( "%DATA%", $data_values, $replace );

					$attr_values = '';

					if ( isset( $element['attr'] ) && is_array( $element['attr'] )) {
						foreach ( $element['attr'] as $attr ) {
							$attr_values .= " {$attr['name']}=\"{$attr['value']}\"";
						}
					}

					$replace = str_replace( '%ATTR%', $attr_values, $replace );

					$html .= $replace;
				} elseif ( $element['type'] == 'datetimepicker' ) {
					$replace = $formats['datetimepicker'];
					$replace = str_replace( "%LABEL%",       isset( $element['label'] )       ? $element['label']                   : "", $replace );
					$replace = str_replace( "%NAME%",        isset( $element['name'] )        ? $element['name']                    : "", $replace );
					$replace = str_replace( "%VALUE%",       isset( $element['value'] )       ? $element['value']                   : "", $replace );
					$replace = str_replace( "%PLACEHOLDER%", isset( $element['placeholder'] ) ? $element['placeholder']             : "", $replace );
					$replace = str_replace( '%READONLY%',    ( isset( $element['readonly'] ) && $element['readonly'] ) ? 'readonly' : '', $replace );
					$replace = str_replace( "%PARAMS%",      isset( $element['params'] )      ? $element['params']                  : $formats['datetimepicker_default_params'], $replace );

					if( isset( $element['class'] )) {
						$replace = str_replace( "%CLASS%", " " . $element['class'], $replace );
					} else {
						$replace = str_replace( "%CLASS%", "", $replace );
					}

					$data_values = "";

					if( isset( $element['data'] ) && is_array( $element['data'] )) {
						foreach( $element['data'] as $data ) {
							$data_values .= " data-{$data['name']}=\"{$data['value']}\"";
						}
					}

					$replace = str_replace( "%DATA%", $data_values, $replace );

					$html .= $replace;
				} elseif ( $element['type'] == 'daterangepicker' ) {
					$replace = $formats['daterangepicker'];
					$replace = str_replace( "%LABEL%",       isset( $element['label'] )       ? $element['label']       : "", $replace );
					$replace = str_replace( "%NAME%",        isset( $element['name'] )        ? $element['name']        : "", $replace );
					$replace = str_replace( "%VALUE%",       isset( $element['value'] )       ? $element['value']       : "", $replace );
					$replace = str_replace( "%PLACEHOLDER%", isset( $element['placeholder'] ) ? $element['placeholder'] : "", $replace );
					$replace = str_replace( "%PARAMS%",      isset( $element['params'] )      ? $element['params']      : $formats['daterangepicker_default_params'], $replace );

					if( isset( $element['class'] )) {
						$replace = str_replace( "%CLASS%", " " . $element['class'], $replace );
					} else {
						$replace = str_replace( "%CLASS%", "", $replace );
					}

					$data_values = "";

					if( isset( $element['data'] ) && is_array( $element['data'] )) {
						foreach( $element['data'] as $data ) {
							$data_values .= " data-{$data['name']}=\"{$data['value']}\"";
						}
					}

					$replace = str_replace( "%DATA%", $data_values, $replace );

					$attr_values = '';

					if ( isset( $element['attr'] ) && is_array( $element['attr'] )) {
						foreach ( $element['attr'] as $attr ) {
							$attr_values .= " {$attr['name']}=\"{$attr['value']}\"";
						}
					}

					$replace = str_replace( '%ATTR%', $attr_values, $replace );

					$html .= $replace;
				} elseif( $element['type'] == 'number' ) {
					$replace = $formats['number'];
					$replace = str_replace( '%LABEL%',    isset( $element['label'] )    ? $element['label']  : '', $replace );
					$replace = str_replace( '%NAME%',     isset( $element['name'] )     ? $element['name']   : '', $replace );
					$replace = str_replace( '%VALUE%',    isset( $element['value'] )    ? $element['value']  : '', $replace );

					if ( isset( $element['tooltip'] )) {
						$tooltip = str_replace( '%TOOLTIP%', $element['tooltip']['value'], $element['tooltip']['html'] );
						$replace = str_replace( '%TOOLTIP%', $tooltip,                     $replace );
					} else {
						$replace = str_replace( '%TOOLTIP%', '', $replace );
					}

					if ( isset( $element['class'] )) {
						$replace = str_replace( '%CLASS%', ' ' . $element['class'], $replace );
					} else {
						$replace = str_replace( '%CLASS%', '', $replace );
					}

					$data_values = '';

					if ( isset( $element['data'] ) && is_array( $element['data'] )) {
						foreach ( $element['data'] as $data ) {
							$data_values .= " data-{$data['name']}=\"{$data['value']}\"";
						}
					}

					$replace = str_replace( '%DATA%', $data_values, $replace );

					$attr_values = '';

					if ( isset( $element['attr'] ) && is_array( $element['attr'] )) {
						foreach ( $element['attr'] as $attr ) {
							$attr_values .= " {$attr['name']}=\"{$attr['value']}\"";
						}
					}

					$replace = str_replace( '%ATTR%', $attr_values, $replace );

					$replace = str_replace( '%DISABLED%', ( isset( $element['disabled'] ) && $element['disabled'] ) ? 'disabled' : '', $replace );

					$html .= $replace;
				} else {
					$replace = $formats['text'];
					$replace = str_replace( '%LABEL%',       isset( $element['label'] )       ? $element['label']       : '', $replace );
					$replace = str_replace( '%NAME%',        isset( $element['name'] )        ? $element['name']        : '', $replace );
					$replace = str_replace( '%VALUE%',       isset( $element['value'] )       ? $element['value']       : '', $replace );
					$replace = str_replace( '%PLACEHOLDER%', isset( $element['placeholder'] ) ? $element['placeholder'] : '', $replace );

					if ( isset( $element['tooltip'] )) {
						$tooltip = str_replace( '%TOOLTIP%', $element['tooltip']['value'], $element['tooltip']['html'] );
						$replace = str_replace( '%TOOLTIP%', $tooltip,                     $replace );
					} else {
						$replace = str_replace( '%TOOLTIP%', '', $replace );
					}

					if ( isset( $element['class'] )) {
						$replace = str_replace( '%CLASS%', ' ' . $element['class'], $replace );
					} else {
						$replace = str_replace( '%CLASS%', '', $replace );
					}

					$data_values = '';

					if ( isset( $element['data'] ) && is_array( $element['data'] )) {
						foreach ( $element['data'] as $data ) {
							$data_values .= " data-{$data['name']}=\"{$data['value']}\"";
						}
					}

					$replace = str_replace( '%DATA%', $data_values, $replace );

					$attr_values = '';

					if ( isset( $element['attr'] ) && is_array( $element['attr'] )) {
						foreach ( $element['attr'] as $attr ) {
							$attr_values .= " {$attr['name']}=\"{$attr['value']}\"";
						}
					}

					$replace = str_replace( '%ATTR%', $attr_values, $replace );

					$replace = str_replace( '%DISABLED%', ( isset( $element['disabled'] ) && $element['disabled'] ) ? 'disabled' : '', $replace );

					$replace = str_replace( '%READONLY%', ( isset( $element['readonly'] ) && $element['readonly'] ) ? 'readonly' : '', $replace );

					$html .= $replace;
				}
			}

			return $html;
		} else {
			return FALSE;
		}
	}

?>
