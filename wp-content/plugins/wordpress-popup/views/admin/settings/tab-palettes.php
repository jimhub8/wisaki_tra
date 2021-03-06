<div id="palettes-box" class="sui-box" data-tab="palettes" <?php if ( 'palettes' !== $section ) echo 'style="display: none;"'; ?>>

	<div class="sui-box-header">
		<h2 class="sui-box-title"><?php esc_html_e( 'Color Palettes', 'wordpress-popup' ); ?></h2>
	</div>

	<div class="sui-box-body">

		<div class="sui-box-settings-row">

			<div class="sui-box-settings-col-1">
				<span class="sui-settings-label"><?php esc_html_e( 'Custom Color Palettes', 'wordpress-popup' ); ?></span>
				<span class="sui-description"><?php esc_html_e( 'Create custom color palettes and apply them directly on your pop-ups, slide-ins, and embeds.', 'wordpress-popup' ); ?></span>
			</div>

			<div class="sui-box-settings-col-2">

				<label class="sui-label"><?php esc_html_e( 'Custom Palettes', 'wordpress-popup' ); ?></label>

				<?php if ( ! empty( $palettes ) ) : ?>

					<ul id="hustle-palettes-container" class="hui-palette-list">

						<?php foreach( $palettes as $slug => $data ) : ?>
							<li>

								<i class="sui-icon-paint-bucket hui-palette-icon" aria-hidden="true"></i>

								<span class="hui-palette-name" aria-hidden="true"><?php echo esc_attr( $data['name'] ); ?></span>

								<button
									class="hustle-create-palette sui-button-icon sui-tooltip"
									data-slug="<?php echo esc_attr( $slug ); ?>"
									data-name="<?php echo esc_attr( $data['name'] ); ?>"
									data-hustle-action="go-to-step"
									data-step="2"
									data-tooltip="<?php esc_attr_e( 'Edit Palette', 'wordpress-popup' ); ?>"
								>
									<span class="sui-loading-text">
										<i class="sui-icon-pencil" aria-hidden="true"></i>
									</span>
									<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
									<span class="sui-screen-reader-text"><?php echo esc_html( $data['name'] ); ?></span>
								</button>

								<button
									class="sui-button-icon sui-button-red sui-tooltip hustle-delete-button"
									data-id="<?php echo esc_attr( $slug ); ?>"
									data-title="<?php esc_attr_e( 'Delete Color Palette', 'wordpress-popup' ); ?>"
									data-description="<?php printf( esc_html__( 'Are you sure you want to delete the %s color palette permanently? Note that the modules using this color palette will fallback to the default color palette.', 'wordpress-popup' ), esc_attr( $data['name'] ) ); ?>"
									data-tooltip="<?php esc_attr_e( 'Delete Palette', 'wordpress-popup' ); ?>"
								>
									<i class="sui-icon-trash" aria-hidden="true"></i>
									<span class="sui-screen-reader-text"><?php echo esc_attr( $data['name'] ); ?></span>
								</button>

							</li>

						<?php endforeach; ?>

					</ul>

				<?php else : ?>

					<?php
					$this->render(
						'admin/elements/notice-inline',
						[
							'type'    => 'info',
							'style'   => 'margin-top: 5px; margin-bottom: 10px;',
							'content' => array(
								sprintf( esc_html__( 'You have not created any custom color palette yet. Click on the %1$s“Create Color Palette”%2$s button to create your first custom palette.', 'wordpress-popup' ), '<strong>', '</strong>' )
							),
						]
					);
					?>

				<?php endif; ?>

				<button class="hustle-create-palette sui-button sui-button-ghost">
					<i class="sui-icon-plus" aria-hidden="true"></i> <?php esc_html_e( 'Create custom palette', 'wordpress-popup' ); ?>
				</button>

			</div>

		</div>

	</div>

</div>
