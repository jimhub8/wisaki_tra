<div class="sui-form-field">

	<label class="sui-settings-label"><?php esc_html_e( 'Language', 'wordpress-popup' ); ?></label>
	<span class="sui-description"><?php esc_html_e( "By default, we'll show the reCAPTCHA in your website's default language.", 'wordpress-popup' ); ?></span>

	<div style="width: 100%; max-width: 240px; margin-top: 10px;">

		<select
			id="hustle-recaptcha-language"
			class="sui-select"
			name="language"
		>
			<option value="automatic" <?php selected( !empty( $settings['language'] ) && 'automatic' === $settings['language'] ); ?>>
				<?php esc_attr_e( "Automatic", 'wordpress-popup' ); ?>
			</option>

			<?php
			$languages = Opt_In_Utils::get_captcha_languages();

			foreach ( $languages as $key => $language ) : ?>

				<option value="<?php echo esc_attr( $key ); ?>" <?php selected( !empty( $settings['language'] ) && $settings['language'] === $key ); ?>>
					<?php echo esc_attr( $language ); ?>
				</option>

			<?php endforeach; ?>

		</select>

	</div>

</div>
