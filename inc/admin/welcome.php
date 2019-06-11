<?php
	/*
	 * Master Addons : Welcome Screen by Jewel Theme
	 */
?>

<div class="master_addons">
	<div class="wrap about-wrap">

		<div class="intro_wrapper">

			<header class="header">
				<h1 class="master_addons">
					<?php printf( __( '%s <small>v %s</small>', MELA_TD ), MELA, MELA_VERSION ); ?>
				</h1>
				<div class="about-text">
					<?php printf( __( "Ultimate and Easy Gutenberg Blocks Page Builder for WordPress.", MELA_TD ),
                        MELA_VERSION ); ?>
				</div>
				<a href="https://wordpress.org/plugins/ultimate-blocks-for-gutenberg">
					<div class="wp-badge welcome__logo"></div>
				</a>
			</header>

			<div class="waveWrapper waveAnimation">
				<div class="waveWrapperInner bgTop">
					<div class="wave waveTop" style="background-image: url('<?php echo MELA_IMAGE_DIR. "wave-top.png";?>')"></div>
				</div>
				<div class="waveWrapperInner bgMiddle">
					<div class="wave waveMiddle" style="background-image: url('<?php echo MELA_IMAGE_DIR. "wave-mid.png";?>')"></div>
				</div>
				<div class="waveWrapperInner bgBottom">
					<div class="wave waveBottom" style="background-image: url('<?php echo MELA_IMAGE_DIR. "wave-bot.png";?>')"></div>
				</div>
			</div>

		</div>


		<?php require_once MELA_PLUGIN_PATH . '/inc/admin/welcome/navigation.php';?>

		<div class="master_addons_contents">

			<?php require MELA_PLUGIN_PATH . '/inc/admin/welcome/how-to.php'; ?>

            <div class="wp-tab-panel" id="docs" style="display: none;">
			    <?php require ( MELA_PLUGIN_PATH . '/inc/admin/welcome/docs.php' ); ?>
            </div>

            <?php require MELA_PLUGIN_PATH . '/inc/admin/welcome/free-themes.php'; ?>

            <?php require MELA_PLUGIN_PATH . '/inc/admin/welcome/changelogs.php'; ?>

		</div>


	</div>
</div>


<script>
	jQuery(document).ready(function(){
		jQuery( "#accordion" ).accordion();
	});
	
</script>