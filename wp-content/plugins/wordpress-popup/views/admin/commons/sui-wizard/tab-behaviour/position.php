<div class="sui-box-settings-row">

	<div class="sui-box-settings-col-1">
		<span class="sui-settings-label"><?php printf( esc_html__( '%s Position', 'wordpress-popup' ), esc_html( $capitalize_singular ) ); ?></span>
		<span class="sui-description"><?php printf( esc_html__( 'Choose the position from which your %s will appear on screen.', 'wordpress-popup' ), esc_html( $smallcaps_singular ) ); ?></span>
	</div>

	<div class="sui-box-settings-col-2 wpmudev-ui">

		<label class="sui-settings-label"><?php printf( esc_html__( 'Choose %s position', 'wordpress-popup' ), esc_html( $smallcaps_singular ) ); ?></label>

		<span class="sui-description"><?php printf( esc_html__( 'Select the position from which your %s will appear on the browser window.', 'wordpress-popup' ), esc_html( $smallcaps_singular ) ); ?></span>

		<div class="hui-browser" style="margin-top: 10px;">

			<div class="hui-browser-bar" aria-hidden="true">
				<span></span>
				<span></span>
				<span></span>
			</div>

			<ul class="hui-browser-content">

				<li class="hui-first-row"><label for="hustle-module-position--nw">
					<input
						type="radio"
						value="nw"
						name="display_position"
						id="hustle-module-position--nw"
						data-attribute="display_position"
						{{ _.checked( 'nw'=== display_position, true ) }}
					/>
					<span class="hui-browser-position--north-west" aria-hidden="true"></span>
					<span class="sui-screen-reader-text"><?php printf( esc_html__( 'Show %s from top left', 'wordpress-popup' ), esc_html( $smallcaps_singular ) ); ?></span>
				</label></li>

				<li class="hui-first-row"><label for="hustle-module-position--n">
					<input
						type="radio"
						value="n"
						name="display_position"
						id="hustle-module-position--n"
						data-attribute="display_position"
						{{ _.checked( 'n'=== display_position, true ) }}
					/>
					<span class="hui-browser-position--north" aria-hidden="true"></span>
					<span class="sui-screen-reader-text"><?php printf( esc_html__( 'Show %s from top', 'wordpress-popup' ), esc_html( $smallcaps_singular ) ); ?></span>
				</label></li>

				<li class="hui-first-row"><label for="hustle-module-position--ne">
					<input
						type="radio"
						value="ne"
						name="display_position"
						id="hustle-module-position--ne"
						data-attribute="display_position"
						{{ _.checked( 'ne'=== display_position, true ) }}
					/>
					<span class="hui-browser-position--north-east" aria-hidden="true"></span>
					<span class="sui-screen-reader-text"><?php printf( esc_html__( 'Show %s from top right', 'wordpress-popup' ), esc_html( $smallcaps_singular ) ); ?></span>
				</label></li>

				<li><label for="hustle-module-position--w">
					<input
						type="radio"
						value="w"
						name="display_position"
						id="hustle-module-position--w"
						data-attribute="display_position"
						{{ _.checked( 'w'=== display_position, true ) }}
					/>
					<span class="hui-browser-position--west" aria-hidden="true"></span>
					<span class="sui-screen-reader-text"><?php printf( esc_html__( 'Show %s from left', 'wordpress-popup' ), esc_html( $smallcaps_singular ) ); ?></span>
				</label></li>

				<li aria-hidden="true"></li>

				<li><label for="hustle-module-position--e">
					<input
						type="radio"
						value="e"
						name="display_position"
						id="hustle-module-position--e"
						data-attribute="display_position"
						{{ _.checked( 'e'=== display_position, true ) }}
					/>
					<span class="hui-browser-position--east" aria-hidden="true"></span>
					<span class="sui-screen-reader-text"><?php printf( esc_html__( 'Show %s from right', 'wordpress-popup' ), esc_html( $smallcaps_singular ) ); ?></span>
				</label></li>

				<li class="hui-last-row"><label for="hustle-module-position--sw">
					<input
						type="radio"
						value="sw"
						name="display_position"
						id="hustle-module-position--sw"
						data-attribute="display_position"
						{{ _.checked( 'sw'=== display_position, true ) }}
					/>
					<span class="hui-browser-position--south-west" aria-hidden="true"></span>
					<span class="sui-screen-reader-text"><?php printf( esc_html__( 'Show %s from bottom left', 'wordpress-popup' ), esc_html( $smallcaps_singular ) ); ?></span>
				</label></li>

				<li class="hui-last-row"><label for="hustle-module-position--s">
					<input
						type="radio"
						value="s"
						name="display_position"
						id="hustle-module-position--s"
						data-attribute="display_position"
						{{ _.checked( 's'=== display_position, true ) }}
					/>
					<span class="hui-browser-position--south" aria-hidden="true"></span>
					<span class="sui-screen-reader-text"><?php printf( esc_html__( 'Show %s from bottom', 'wordpress-popup' ), esc_html( $smallcaps_singular ) ); ?></span>
				</label></li>

				<li class="hui-last-row"><label for="hustle-module-position--se">
					<input
						type="radio"
						value="se"
						name="display_position"
						id="hustle-module-position--se"
						data-attribute="display_position"
						{{ _.checked( 'se'=== display_position, true ) }}
					/>
					<span class="hui-browser-position--south-east" aria-hidden="true"></span>
					<span class="sui-screen-reader-text"><?php printf( esc_html__( 'Show %s from bottom right', 'wordpress-popup' ), esc_html( $smallcaps_singular ) ); ?></span>
				</label></li>

			</ul>

		</div>

	</div>

</div>
