<?php
/**
 * Plugin helpers
 *
 * @package PardotMarketing
 * @since 1.0.0
 */

/**
 * Returns the plugin settings.
 */
if ( ! function_exists( 'pardotmarketing_options' ) ) {
	function pardotmarketing_options() {
		$options = get_option( 'pardotmarketing' );

		if ( empty( $options['api_url'] ) ) { $options['api_url'] = 'https://pi.pardot.com/api'; }

		return $options;
	}
}

/**
 * Returns the plugin settings.
 */
if ( ! function_exists( 'pardotmarketing_request' ) ) {
	function pardotmarketing_request( $action, $args = [] ) {
		$options = pardotmarketing_options();

		if (
			empty( $options['api_email'] ) ||
			empty( $options['api_password'] ) ||
			empty( $options['api_user_key'] )
		) {
			return false;
		}

		$pardot_api = new PardotAPI([
			'api_email'    => $options['api_email'],
			'api_password' => $options['api_password'],
			'api_user_key' => $options['api_user_key']
		]);

		switch ( $action ) {
			case 'getProspects':
				return $pardot_api->getProspects( $args );
			break;
			case 'getForms':
				return $pardot_api->getForms( $args );
			break;
		}

		return false;
	}
}

/**
 * Returns a Pardot forms
 */
if ( ! function_exists( 'pardotmarketing_get_forms' ) ) {
	function pardotmarketing_get_forms( $args = [] ) {
		$forms = pardotmarketing_request( 'getForms', $args );
		if ( ! $forms ) { return false; }
		$parsed = [ 'total_results' => $forms['total_results'], 'forms' => [] ];

		foreach( $forms['form'] as $key => $form ) {
			// Available fields
			$parsed['forms'][ $form['id'] ] = [
				'id'         => '',
				'name'       => '',
				'campaign'   => [],
				'crm_fid'  => '',
				'embedCode'  => '',
				'created_at' => '',
				'updated_at' => ''
			];

			$parsed['forms'][ $form['id'] ]['id'] = $form['id'];

			// Name
			if ( ! empty( $form['name'] ) ) {
				$parsed['forms'][ $form['id'] ]['name'] = $form['name'];
			}

			// Campaign
			if ( ! empty( $form['campaign'] ) ) {
				$parsed['forms'][ $form['id'] ]['campaign'] = $form['campaign'];
			}

			// CRM field
			if ( ! empty( $form['crm_fid'] ) ) {
				$parsed['forms'][ $form['id'] ]['crm_fid'] = $form['crm_fid'];
			}

			// Embed code
			if ( ! empty( $form['embedCode'] ) ) {
				$parsed['forms'][ $form['id'] ]['embedCode'] = $form['embedCode'];
			}

			// Created
			if ( ! empty( $form['created_at'] ) ) {
				$parsed['forms'][ $form['id'] ]['created_at'] = strtotime( $form['created_at'] );
			}

			// Updated
			if ( ! empty( $form['updated_at'] ) ) {
				$parsed['forms'][ $form['id'] ]['updated_at'] = strtotime( $form['updated_at'] );
			}
		}

		return $parsed;
	}
}

/**
 * Returns a Pardot prospects
 */
if ( ! function_exists( 'pardotmarketing_get_prospects' ) ) {
	function pardotmarketing_get_prospects( $args = [] ) {
		$prospects = pardotmarketing_request( 'getProspects', $args );
		if ( ! $prospects ) { return false; }
		$parsed = [ 'total_results' => $prospects['total_results'], 'prospects' => [] ];
		foreach( $prospects['prospect'] as $key => $prospect ) {
			// Available fields
			$parsed['prospects'][ $prospect['id'] ] = [
				'id'          => '',
				'campaign_id' => '',
				'first_name'  => '',
				'last_name'   => '',
				'email'       => '',
				'company'     => '',
				'job_title'   => '',
				'country'     => '',
				'created_at'  => '',
				'updated_at'  => ''
			];

			$parsed['prospects'][ $prospect['id'] ]['id'] = $prospect['id'];

			// Campaign ID
			if ( ! empty( $prospect['campaign_id'] ) ) {
				$parsed['prospects'][ $prospect['id'] ]['campaign_id'] = $prospect['campaign_id'];
			}

			// First name
			if ( ! empty( $prospect['first_name'] ) ) {
				$parsed['prospects'][ $prospect['id'] ]['first_name'] = $prospect['first_name'];
			}

			// Last name
			if ( ! empty( $prospect['last_name'] ) ) {
				$parsed['prospects'][ $prospect['id'] ]['last_name'] = $prospect['last_name'];
			}

			// Email
			if ( ! empty( $prospect['email'] ) ) {
				$parsed['prospects'][ $prospect['id'] ]['email'] = $prospect['email'];
			}

			// Company
			if ( ! empty( $prospect['company'] ) ) {
				$parsed['prospects'][ $prospect['id'] ]['company'] = $prospect['company'];
			}

			// Job title
			if ( ! empty( $prospect['job_title'] ) ) {
				$parsed['prospects'][ $prospect['id'] ]['job_title'] = $prospect['job_title'];
			}

			// Country
			if ( ! empty( $prospect['country'] ) ) {
				$parsed['prospects'][ $prospect['id'] ]['country'] = $prospect['country'];
			}

			// Created
			if ( ! empty( $prospect['created_at'] ) ) {
				$parsed['prospects'][ $prospect['id'] ]['created_at'] = strtotime( $prospect['created_at'] );
			}

			// Updated
			if ( ! empty( $prospect['updated_at'] ) ) {
				$parsed['prospects'][ $prospect['id'] ]['updated_at'] = strtotime( $prospect['updated_at'] );
			}
		}

		return $parsed;
	}
}

/**
 * Output for a Pardot Form Handler form.
 */
function pardotmarket_form_handler_form( $settings, $elementor = true ) {
	if ( empty( $settings['fields'] ) ) {
		return;
	}

	if ( empty( $settings['endpoint']['url'] ) ) {
		return __( 'Pardot Form Handler Error: Missing Pardot endpoint URL.', 'pardotmarketing' );
	}

	$plugin_settings = pardotmarketing_options();
	$form_id         = ! empty( $settings['form_id'] ) ? $settings['form_id'] : wp_generate_uuid4();

	ob_start();
	do_action( 'pardotmarketing_before_form_handler' );
	?>

	<?php
	if ( ! $elementor ) {
		// Get the spacing.
		$column_spacing      = ! empty( $settings['column_gap']['size'] ) ? $settings['column_gap']['size'] / 2 : 10;
		$column_spacing_unit = ! empty( $settings['column_gap']['unit'] ) ? $settings['column_gap']['unit'] : 'px';

		$row_spacing      = ! empty( $settings['row_gap']['size'] ) ? $settings['row_gap']['size'] : 10;
		$row_spacing_unit = ! empty( $settings['row_gap']['unit'] ) ? $settings['row_gap']['unit'] : 'px';
		?>
		<style>
		#pardotmarketing-<?php echo $form_id; ?> .pardotmarketing-form-handler-fields {
			margin-left: -<?php echo $column_spacing . $column_spacing_unit; ?>;
			margin-right: -<?php echo $column_spacing . $column_spacing_unit; ?>;
		}

		#pardotmarketing-<?php echo $form_id; ?> .pardotmarketing-form-handler-field {
			margin-bottom: <?php echo $row_spacing . $row_spacing_unit; ?>;
			padding-left: <?php echo $column_spacing . $column_spacing_unit; ?>;
			padding-right: <?php echo $column_spacing . $column_spacing_unit; ?>;
		}
		</style>
		<?php
	}
	?>

	<div class="pardotmarketing-form-handler">

		<?php if ( ! empty( $_REQUEST['success'] ) ): ?>
			<div class="pardotmarketing-form-handler-message pardotmarketing-form-handler-success">
				<?php if ( 'yes' === $settings['custom_messages'] && $settings['success_message'] ) : ?>
					<p><?php echo $settings['success_message']; ?></p>
				<?php else: ?>
					<p><?php _e( 'Your submission has been successfully sent.', 'pardotmarketing' ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php
		if ( ! empty( $_REQUEST['success'] ) && 'yes' === $settings['success_hide_form'] ):
			echo '</div>';
			return ob_get_clean();
		endif;

		do_action( 'pardotmarketing_pre_form' );
		?>

		<form
			class="pardotmarketing-form-handler-form"
			method="post"
			data-url="<?php echo esc_url( $settings['endpoint']['url'] ); ?>"
			id="pardotmarketing-<?php echo esc_attr( $form_id ); ?>"
		>
			<?php do_action( 'pardotmarketing_form' ); ?>

			<?php
			if ( ! empty( $_REQUEST['errors'] ) ) :
				do_action( 'pardotmarketing_pre_error_msg' );
				?>
				<div class="pardotmarketing-form-handler-message pardotmarketing-form-handler-error">
					<?php do_action( 'pardotmarketing_error_msg' ); ?>
					<?php if ( 'yes' === $settings['custom_messages'] && $settings['error_message'] ): ?>
						<p><?php echo $settings['error_message']; ?></p>
					<?php elseif( ! empty( $_REQUEST['errorMessage'] ) ): ?>
						<p><?php echo sanitize_text_field( $_REQUEST['errorMessage'] ); ?></p>
					<?php else: ?>
						<p><?php _e( 'There was a problem submitting the form. Please try again.', 'pardotmarketing' ); ?></p>
					<?php endif; ?>
					<?php do_action( 'pardotmarketing_error_post_msg' ); ?>
				</div>
			<?php endif; ?>

			<div class="pardotmarketing-form-handler-fields">
				<?php
				foreach ( $settings['fields'] as $field ) :
					if ( empty( $field['_id'] ) ) {
						$field['_id'] = wp_generate_uuid4();
					}

					// Get the field value.
					$value = ! empty( $field['value'] ) ? $field['value'] : false;
					if ( ! empty( $_REQUEST[ $field['key'] ] ) ):
						$value = sanitize_text_field( $_REQUEST[ $field['key'] ] );
					endif;

					// Add the field container.
					if( $field['type'] !== 'hidden' ) :
						$label_classes = array( 'pardotmarketing-form-handler-label' );
						?>
						<div class="pardotmarketing-form-handler-field elementor-repeater-item-<?php echo esc_attr( $field['_id'] ); ?>">
							<label class="pardotmarketing-form-handler-label <?php if ( ! $settings['show_label'] ) : ?> pardotmarketing-screen-reader-text<?php endif; ?>" for="pardotmarketing-form-handler-<?php echo esc_attr( $field['key'] ); ?>">
								<?php echo $field['label']; ?>
							</label>
					<?php endif; ?>
					<?php
					$recaptcha_enabled = false;

					// Output the field.
					switch ( $field['type'] ) :
						case 'recaptcha':
							wp_enqueue_script( 'pardotmarketing-recaptcha' );
							$recaptcha_enabled = true;
							break;
						case 'country':
							?>
							<select
								name="<?php echo esc_attr( $field['key'] ); ?>"
								class="pardotmarketing-form-handler-select"
								<?php if ( $field['required'] ): ?> required<?php endif; ?>
							>
								<?php if ( ! empty( $field['placeholder'] ) ): ?><option value=""><?php echo $field['placeholder']; ?></option><?php endif; ?>
								<?php
								$countries = apply_filters(
									'pardot_marketing_country_options_' . $form_id,
									array(
										'AF' => 'Afghanistan',
										'AL' => 'Albania',
										'DZ' => 'Algeria',
										'AS' => 'American Samoa',
										'AD' => 'Andorra',
										'AO' => 'Angola',
										'AI' => 'Anguilla',
										'AQ' => 'Antarctica',
										'AG' => 'Antigua and Barbuda',
										'AR' => 'Argentina',
										'AM' => 'Armenia',
										'AW' => 'Aruba',
										'AU' => 'Australia',
										'AT' => 'Austria',
										'AZ' => 'Azerbaijan',
										'BS' => 'Bahamas',
										'BH' => 'Bahrain',
										'BD' => 'Bangladesh',
										'BB' => 'Barbados',
										'BY' => 'Belarus',
										'BE' => 'Belgium',
										'BZ' => 'Belize',
										'BJ' => 'Benin',
										'BM' => 'Bermuda',
										'BT' => 'Bhutan',
										'BO' => 'Bolivia',
										'BA' => 'Bosnia and Herzegovina',
										'BW' => 'Botswana',
										'BV' => 'Bouvet Island',
										'BR' => 'Brazil',
										'IO' => 'British Indian Ocean Territory',
										'BN' => 'Brunei Darussalam',
										'BG' => 'Bulgaria',
										'BF' => 'Burkina Faso',
										'BI' => 'Burundi',
										'KH' => 'Cambodia',
										'CM' => 'Cameroon',
										'CA' => 'Canada',
										'CV' => 'Cape Verde',
										'KY' => 'Cayman Islands',
										'CF' => 'Central African Republic',
										'TD' => 'Chad',
										'CL' => 'Chile',
										'CN' => 'China',
										'CX' => 'Christmas Island',
										'CC' => 'Cocos (Keeling) Islands',
										'CO' => 'Colombia',
										'KM' => 'Comoros',
										'CG' => 'Congo',
										'CD' => 'Congo, the Democratic Republic of the',
										'CK' => 'Cook Islands',
										'CR' => 'Costa Rica',
										'CI' => 'Cote D\'Ivoire',
										'HR' => 'Croatia',
										'CU' => 'Cuba',
										'CY' => 'Cyprus',
										'CZ' => 'Czech Republic',
										'DK' => 'Denmark',
										'DJ' => 'Djibouti',
										'DM' => 'Dominica',
										'DO' => 'Dominican Republic',
										'EC' => 'Ecuador',
										'EG' => 'Egypt',
										'SV' => 'El Salvador',
										'GQ' => 'Equatorial Guinea',
										'ER' => 'Eritrea',
										'EE' => 'Estonia',
										'ET' => 'Ethiopia',
										'FK' => 'Falkland Islands (Malvinas)',
										'FO' => 'Faroe Islands',
										'FJ' => 'Fiji',
										'FI' => 'Finland',
										'FR' => 'France',
										'GF' => 'French Guiana',
										'PF' => 'French Polynesia',
										'TF' => 'French Southern Territories',
										'GA' => 'Gabon',
										'GM' => 'Gambia',
										'GE' => 'Georgia',
										'DE' => 'Germany',
										'GH' => 'Ghana',
										'GI' => 'Gibraltar',
										'GR' => 'Greece',
										'GL' => 'Greenland',
										'GD' => 'Grenada',
										'GP' => 'Guadeloupe',
										'GU' => 'Guam',
										'GT' => 'Guatemala',
										'GN' => 'Guinea',
										'GW' => 'Guinea-Bissau',
										'GY' => 'Guyana',
										'HT' => 'Haiti',
										'HM' => 'Heard Island and Mcdonald Islands',
										'VA' => 'Holy See (Vatican City State)',
										'HN' => 'Honduras',
										'HK' => 'Hong Kong',
										'HU' => 'Hungary',
										'IS' => 'Iceland',
										'IN' => 'India',
										'ID' => 'Indonesia',
										'IR' => 'Iran, Islamic Republic of',
										'IQ' => 'Iraq',
										'IE' => 'Ireland',
										'IL' => 'Israel',
										'IT' => 'Italy',
										'JM' => 'Jamaica',
										'JP' => 'Japan',
										'JO' => 'Jordan',
										'KZ' => 'Kazakhstan',
										'KE' => 'Kenya',
										'KI' => 'Kiribati',
										'KP' => 'Korea, Democratic People\'s Republic of',
										'KR' => 'Korea, Republic of',
										'KW' => 'Kuwait',
										'KG' => 'Kyrgyzstan',
										'LA' => 'Lao People\'s Democratic Republic',
										'LV' => 'Latvia',
										'LB' => 'Lebanon',
										'LS' => 'Lesotho',
										'LR' => 'Liberia',
										'LY' => 'Libyan Arab Jamahiriya',
										'LI' => 'Liechtenstein',
										'LT' => 'Lithuania',
										'LU' => 'Luxembourg',
										'MO' => 'Macao',
										'MK' => 'Macedonia, the Former Yugoslav Republic of',
										'MG' => 'Madagascar',
										'MW' => 'Malawi',
										'MY' => 'Malaysia',
										'MV' => 'Maldives',
										'ML' => 'Mali',
										'MT' => 'Malta',
										'MH' => 'Marshall Islands',
										'MQ' => 'Martinique',
										'MR' => 'Mauritania',
										'MU' => 'Mauritius',
										'YT' => 'Mayotte',
										'MX' => 'Mexico',
										'FM' => 'Micronesia, Federated States of',
										'MD' => 'Moldova, Republic of',
										'MC' => 'Monaco',
										'MN' => 'Mongolia',
										'MS' => 'Montserrat',
										'MA' => 'Morocco',
										'MZ' => 'Mozambique',
										'MM' => 'Myanmar',
										'NA' => 'Namibia',
										'NR' => 'Nauru',
										'NP' => 'Nepal',
										'NL' => 'Netherlands',
										'AN' => 'Netherlands Antilles',
										'NC' => 'New Caledonia',
										'NZ' => 'New Zealand',
										'NI' => 'Nicaragua',
										'NE' => 'Niger',
										'NG' => 'Nigeria',
										'NU' => 'Niue',
										'NF' => 'Norfolk Island',
										'MP' => 'Northern Mariana Islands',
										'NO' => 'Norway',
										'OM' => 'Oman',
										'PK' => 'Pakistan',
										'PW' => 'Palau',
										'PS' => 'Palestinian Territory, Occupied',
										'PA' => 'Panama',
										'PG' => 'Papua New Guinea',
										'PY' => 'Paraguay',
										'PE' => 'Peru',
										'PH' => 'Philippines',
										'PN' => 'Pitcairn',
										'PL' => 'Poland',
										'PT' => 'Portugal',
										'PR' => 'Puerto Rico',
										'QA' => 'Qatar',
										'RE' => 'Reunion',
										'RO' => 'Romania',
										'RU' => 'Russian Federation',
										'RW' => 'Rwanda',
										'SH' => 'Saint Helena',
										'KN' => 'Saint Kitts and Nevis',
										'LC' => 'Saint Lucia',
										'PM' => 'Saint Pierre and Miquelon',
										'VC' => 'Saint Vincent and the Grenadines',
										'WS' => 'Samoa',
										'SM' => 'San Marino',
										'ST' => 'Sao Tome and Principe',
										'SA' => 'Saudi Arabia',
										'SN' => 'Senegal',
										'CS' => 'Serbia and Montenegro',
										'SC' => 'Seychelles',
										'SL' => 'Sierra Leone',
										'SG' => 'Singapore',
										'SK' => 'Slovakia',
										'SI' => 'Slovenia',
										'SB' => 'Solomon Islands',
										'SO' => 'Somalia',
										'ZA' => 'South Africa',
										'GS' => 'South Georgia and the South Sandwich Islands',
										'ES' => 'Spain',
										'LK' => 'Sri Lanka',
										'SD' => 'Sudan',
										'SR' => 'Suriname',
										'SJ' => 'Svalbard and Jan Mayen',
										'SZ' => 'Swaziland',
										'SE' => 'Sweden',
										'CH' => 'Switzerland',
										'SY' => 'Syrian Arab Republic',
										'TW' => 'Taiwan, Province of China',
										'TJ' => 'Tajikistan',
										'TZ' => 'Tanzania, United Republic of',
										'TH' => 'Thailand',
										'TL' => 'Timor-Leste',
										'TG' => 'Togo',
										'TK' => 'Tokelau',
										'TO' => 'Tonga',
										'TT' => 'Trinidad and Tobago',
										'TN' => 'Tunisia',
										'TR' => 'Turkey',
										'TM' => 'Turkmenistan',
										'TC' => 'Turks and Caicos Islands',
										'TV' => 'Tuvalu',
										'UG' => 'Uganda',
										'UA' => 'Ukraine',
										'AE' => 'United Arab Emirates',
										'GB' => 'United Kingdom',
										'US' => 'United States',
										'UM' => 'United States Minor Outlying Islands',
										'UY' => 'Uruguay',
										'UZ' => 'Uzbekistan',
										'VU' => 'Vanuatu',
										'VE' => 'Venezuela',
										'VN' => 'Viet Nam',
										'VG' => 'Virgin Islands, British',
										'VI' => 'Virgin Islands, U.s.',
										'WF' => 'Wallis and Futuna',
										'EH' => 'Western Sahara',
										'YE' => 'Yemen',
										'ZM' => 'Zambia',
										'ZW' => 'Zimbabwe',
									)
								);
								foreach( $countries as $key => $name ):
									$opt_val = $key;
									if ( ! empty( $field['submitted_value'] ) && 'name' === $field['submitted_value'] ) {
										$opt_val = $name;
									}
									?>
									<option
										value="<?php echo $opt_val; ?>"
										<?php if ( $value == $opt_val ): ?> selected="selected"<?php endif; ?>
									>
										<?php echo $name; ?>
									</option>
									<?php
								endforeach;
								?>
							</select>
							<?php
						break;
						case 'tel':
						case 'text':
						case 'url':
						case 'email':
						case 'hidden':
							?>
							<input
								class="pardotmarketing-form-handler-input"
								type="<?php echo $field['type']; ?>"
								name="<?php echo esc_attr( $field['key'] ); ?>"
								id="pardotmarketing-form-handler-<?php echo esc_attr( $field['key'] ); ?>"
								<?php if ( ! empty( $field['minlength'] ) ): ?>minlength="<?php echo intval( $field['minlength'] ); ?>"<?php endif; ?>
								<?php if ( ! empty( $field['maxlength'] ) ): ?>maxlength="<?php echo intval( $field['maxlength'] ); ?>"<?php endif; ?>
								<?php if ( $field['placeholder'] ): ?> placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"<?php endif; ?>
								<?php if ( $value ): ?> value="<?php echo esc_attr( $value ); ?>"<?php endif; ?>
								<?php if ( $field['required'] ): ?> required<?php endif; ?>
							>
							<?php
						break;
						case 'textarea':
							?>
							<textarea
								rows="<?php echo $field['rows']; ?>"
								class="pardotmarketing-form-handler-input"
								name="<?php echo esc_attr( $field['key'] ); ?>"
								id="pardotmarketing-form-handler-<?php echo esc_attr( $field['key'] ); ?>"
								<?php if ( ! empty( $field['minlength'] ) ): ?>minlength="<?php echo intval( $field['minlength'] ); ?>"<?php endif; ?>
								<?php if ( ! empty( $field['maxlength'] ) ): ?>maxlength="<?php echo intval( $field['maxlength'] ); ?>"<?php endif; ?>
								<?php if ( $field['placeholder'] ): ?> placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"<?php endif; ?>
								<?php if ( $field['required'] ): ?> required<?php endif; ?>><?php echo trim( $value ); ?></textarea>
							<?php
						break;
						case 'select':
							?>
							<select
								name="<?php echo esc_attr( $field['key'] ); ?>"
								class="pardotmarketing-form-handler-select"
								<?php if ( $field['required'] ): ?> required<?php endif; ?>
							>
								<?php
								$options = explode( "\n", $field['options'] );
								foreach( $options as $key => $val ):
									$option = explode( '|', $val );
									?>
									<option
										value="<?php echo esc_attr( $option[0] ); ?>"
										<?php if ( $value == $option[0] ): ?> selected="selected"<?php endif; ?>
									>
										<?php echo ! empty( $option[1] ) ? esc_attr( $option[1] ) : esc_attr( $option[0] ); ?>
									</option>
									<?php
								endforeach;
								?>
							</select>
							<?php
						break;
						case 'checkbox':
						case 'radio':
							$options_classes = array( 'pardotmarketing-form-handler-options' );

							if ( 'yes' === $field['inline'] ) :
								$options_classes[] = 'pardotmarketing-form-handler-options-inline';
							endif;
							?>
							<div class="<?php echo esc_attr( implode( ' ', $options_classes ) ); ?>">
								<?php
								$options = explode( "\n", $field['options'] );
								$name    = $field['key'];

								// If more than one option, send as an array.
								if ( count( $options ) > 1 && 'radio' !== $field['type'] ) :
									$name = $field['key'] . '[]';
								endif;

								$option_count = 0;
								foreach ( $options as $key => $val ) :
									$option_count++;
									$option    = explode( '|', $val );
									$option_id = 'pardotmarketing-form-handler-' . esc_attr( $field['key'] . '-' . $option_count );
									?>
									<div class="pardotmarketing-form-handler-option">
										<input
											type="<?php echo $field['type']; ?>"
											id="<?php echo $option_id; ?>"
											value="<?php echo esc_attr( $option[0] ); ?>"
											<?php if ( $value === $option[0] ) : ?> checked="checked"<?php endif; ?>
											name="<?php echo esc_attr( $name ); ?>"
											class="pardotmarketing-form-handler-option-input"
										>
										<label for="<?php echo $option_id; ?>">
											<?php echo ! empty( $option[1] ) ? esc_attr( $option[1] ) : esc_attr( $option[0] ); ?>
										</label>
									</div>
									<?php
								endforeach;
								?>
							</div>
							<?php
						break;
					endswitch;
					?>
					<?php if( $field['type'] != 'hidden' ): ?></div><?php endif; ?>

					<?php
					if ( ! $elementor ) :
						?>
						<style>
						#pardotmarketing-<?php echo $form_id; ?> .elementor-repeater-item-<?php echo $field['_id']; ?> {
							width: <?php echo ! empty( $field['width_mobile'] ) ? $field['width_mobile'] : 100; ?>%;
						}

						@media (min-width: 768px) {
							#pardotmarketing-<?php echo $form_id; ?> .elementor-repeater-item-<?php echo $field['_id']; ?> {
								width: <?php echo ! empty( $field['width_tablet'] ) ? $field['width_tablet'] : 100; ?>%;
							}
						}

						@media (min-width: 1025px) {
							#pardotmarketing-<?php echo $form_id; ?> .elementor-repeater-item-<?php echo $field['_id']; ?> {
								width: <?php echo ! empty( $field['width'] ) ? $field['width'] : 100; ?>%;
							}
						}
						</style>
						<?php
					endif;
					?>
				<?php endforeach; ?>
				<div class="pardotmarketing-form-handler-field pardotmarketing-form-handler-field-submit">
					<button
						type="submit"
						class="pardotmarketing-form-handler-button"
					>
						<?php echo $settings['submit_text']; ?>
				</button>
				</div>
			</div>
		</form>
		<script>
		(function( $ ) {
			'use strict';

			$(function() {
				var $form = $('#pardotmarketing-<?php echo esc_attr( $form_id ); ?>');
				$form.attr( 'data-pardot-marketing', 'processed' );

				$form.validate({
					<?php
					$submit_handler = '
						event.preventDefault();

						$(form).addClass("pardotmarketing-form-handler-form-is-submitting");
						$(form).attr("action", $(form).data("url"));

						$(".pardotmarketing-form-handler-button", $(form)).html("' . $settings['submit_text_processing'] . '");
						form.submit();
					';

					if ( $recaptcha_enabled ) {
						$submit_handler = '
						event.preventDefault();

						grecaptcha.ready(function() {
							grecaptcha.execute(\'' . $plugin_settings['recaptchav3'] . '\', {action: \'submit\'}).then(function(token) {
								if(token) {
									$(form).addClass("pardotmarketing-form-handler-form-is-submitting");
									$(form).attr("action", $(form).data("url"));

									$(".pardotmarketing-form-handler-button", $(form)).html("' . $settings['submit_text_processing'] . '");
									form.submit();
								} else {
									$form.prepend(\'There was a problem submitting the form. Please reload the page and try again.\');
								}
							});
						});
						';

						/*$submit_handler = '
						grecaptcha.ready(function() {
							if ( grecaptcha.getResponse() ) {
								$(form).addClass("pardotmarketing-form-handler-form-is-submitting");
								$(form).attr("action", $(form).data("url"));

								$(".pardotmarketing-form-handler-button", $(form)).html("' . $settings['submit_text_processing'] . '");
								//form.submit();
							} else {
								$(\'pardotmarketing-' . esc_attr( $form_id ) . '\').prepend(\'There was an error submitting the form. Please refresh the page and try again.\');
								return false;
							}
						});';*/
					}

					$options = apply_filters(
						'pardotmarketing_form_handler_validation_options_filter_' . $form_id,
						array(
							'errorClass'    => '"pardotmarketing-form-handler-field-error"',
							'validClass'    => '"pardotmarketing-form-handler-field-valid"',
							'submitHandler' => 'function(form, event) {
								' . $submit_handler . '
							}',
						)
					);

					if ( $options ):
						foreach( $options as $key => $value ):
							?>
							'<?php echo $key; ?>': <?php echo $value; ?>,
							<?php
						endforeach;
					endif;
					?>
				});
			});
		})(jQuery);
		</script>
	</div>
	<?php

	return ob_get_clean();
}
