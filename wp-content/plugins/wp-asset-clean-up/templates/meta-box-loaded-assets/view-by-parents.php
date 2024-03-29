<?php
// no direct access
if (! isset($data)) {
	exit;
}

$listAreaStatus = $data['plugin_settings']['assets_list_layout_areas_status'];

/*
* ----------------------------------------------
* [START] BY EACH HANDLE STATUS (Parent or Not)
* ----------------------------------------------
*/
?>
    <div>
    <?php
    if (! empty($data['all']['styles']) || ! empty($data['all']['scripts'])) {
    ?>
        <p><?php echo sprintf(__('The following styles &amp; scripts are loading on this page. Please select the ones that are %sNOT NEEDED%s. If you are not sure which ones to unload, it is better to enable "Test Mode" (to make the changes apply only to you), while you are going through the trial &amp; error process.', 'wp-asset-clean-up'), '<span style="color: #CC0000;"><strong>', '</strong></span>'); ?></p>
        <p><?php echo __('"Load in on this page (make an exception)" will take effect when a bulk unload rule is used. Otherwise, the asset will load anyway unless you select it for unload.', 'wp-asset-clean-up'); ?></p>
        <?php
        if ($data['plugin_settings']['hide_core_files']) {
            ?>
            <div class="wpacu_note"><span class="dashicons dashicons-info"></span> WordPress CSS &amp; JavaScript core files are hidden as requested in the plugin's settings. They are meant to be managed by experienced developers in special situations.</div>
            <div class="wpacu-clearfix" style="margin-top: 10px;"></div>
            <?php
        }

        if ( ( (isset($data['core_styles_loaded']) && $data['core_styles_loaded']) || (isset($data['core_scripts_loaded']) && $data['core_scripts_loaded']) ) && ! $data['plugin_settings']['hide_core_files']) {
            ?>
            <div class="wpacu_note wpacu_warning"><em><?php
                    echo sprintf(
                        __('Assets that are marked with %s are part of WordPress core files. Be careful if you decide to unload them! If you are not sure what to do, just leave them loaded by default and consult with a developer.', 'wp-asset-clean-up'),
                        '<span class="dashicons dashicons-warning"></span>'
                    );
                    ?>
                </em></div>
            <?php
        }
        ?>
    </div>

    <div style="margin: 10px 0;">
        <?php
        echo $data['assets_list_layout_output'];
        ?>
    </div>

    <div style="margin-bottom: 20px;" class="wpacu-contract-expand-area">
        <div class="col-left">
            <strong>&#10141; Total enqueued files (including core files): <?php echo (int)$data['total_styles'] + (int)$data['total_scripts']; ?></strong>
        </div>
        <div class="col-right">
            <a href="#" id="wpacu-assets-contract-all" class="wpacu-wp-button wpacu-wp-button-secondary">Contract All Areas</a>&nbsp;
            <a href="#" id="wpacu-assets-expand-all" class="wpacu-wp-button wpacu-wp-button-secondary">Expand All Areas</a>
        </div>
        <div class="wpacu-clearfix"></div>
    </div>

    <?php
	$data['view_by_parents'] =
	$data['rows_build_array'] =
	$data['rows_by_parents'] = true;

	$data['rows_assets'] = array();

	require_once __DIR__.'/_asset-style-rows.php';
	require_once __DIR__.'/_asset-script-rows.php';

    $handleStatusesText = array(
        'parent'      => '<span class="dashicons dashicons-groups"></span>&nbsp; \'Parents\' with \'children\' (.css &amp; .js)',
        'child'       => '<span class="dashicons dashicons-admin-users"></span>&nbsp; \'Children\' of \'parents\' (.css &amp; .js)',
        'independent' => '<span class="dashicons dashicons-admin-users"></span>&nbsp; Independent (.css &amp; .js)'
    );


	if (! empty($data['rows_assets'])) {
		// Sorting: parent & non_parent
		$rowsAssets = array('parent' => array(), 'child' => array(), 'independent' => array());

		foreach ($data['rows_assets'] as $handleStatus => $values) {
			$rowsAssets[$handleStatus] = $values;
		}

		foreach ($rowsAssets as $handleStatus => $values) {
			ksort($values);

			$assetRowIndex = 1;

			$assetRowsOutput = '';

			$totalFiles = 0;

			foreach ($values as $assetType => $assetRows) {
				foreach ($assetRows as $assetRow) {
					$assetRowsOutput .= $assetRow . "\n";
					$totalFiles++;
				}
			}
			?>
            <div class="wpacu-assets-collapsible-wrap wpacu-by-parents wpacu-wrap-area wpacu-<?php echo $handleStatus; ?>">
                <a class="wpacu-assets-collapsible <?php if ($listAreaStatus !== 'contracted') { ?>wpacu-assets-collapsible-active<?php } ?>" href="#wpacu-assets-collapsible-content-<?php echo $handleStatus; ?>">
					<?php echo $handleStatusesText[$handleStatus]; ?> &#10141; Total files: <?php echo $totalFiles; ?>
                </a>

                <div class="wpacu-assets-collapsible-content <?php if ($listAreaStatus !== 'contracted') { ?>wpacu-open<?php } ?>">
					<?php if ($handleStatus === 'parent') { ?>
                        <p class="wpacu-assets-note">If you unload any of the files below (if any listed), their 'children' (as listed in green bold font below the handle) will also be unloaded.</p>
					<?php } elseif ($handleStatus === 'child') { ?>
                        <p class="wpacu-assets-note">The following files (if any listed) are 'children' linked to the 'parent' files.</p>
					<?php } elseif ($handleStatus === 'independent') { ?>
                        <p class="wpacu-assets-note">The following files (if any listed) are independent as they are not 'children' or 'parents'.</p>
                    <?php } ?>

                    <?php if (count($values) > 0) { ?>
                        <table class="wpacu_list_table wpacu_list_by_parents wpacu_widefat wpacu_striped">
                            <tbody>
                            <?php
                            echo $assetRowsOutput;
                            ?>
                            </tbody>
                        </table>
                    <?php } ?>
                </div>
            </div>
			<?php
		}
	}
}
/*
* --------------------------------------------
* [END] BY EACH HANDLE STATUS (Parent or Not)
* --------------------------------------------
*/

include '_inline_js.php';
