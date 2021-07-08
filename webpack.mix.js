const mix = require('laravel-mix');

const wpPot = require('wp-pot');

mix.options({
    autoprefixer: {
        remove: false
    },
    processCssUrls: false,
});



// Disable notification on dev mode
if ( process.env.NODE_ENV.trim() !== 'production' ) mix.disableNotifications();

if ( process.env.NODE_ENV.trim() === 'production' ) {
	// Language pot file generator
	wpPot({
		destFile: 'languages/master-addons.pot',
		domain: 'master-addons',
		package: 'MasterAddons',
		src: '**/*.php'
	});

}

mix.webpackConfig({
	target: 'web',
	externals: {
		jquery: "window.jQuery",
		$: "window.jQuery",
		wp: 'window.wp',
		jltma_master_addons: 'window.jltma_master_addons'
	}
});

// Main Stylesheet
mix.sass( 'assets/scss/style.scss', 'assets/css/master-addons.css' );
mix.sass( 'assets/scss/admin/master-addons-admin.scss', './././inc/admin/assets/css/master-addons-admin.css' );
mix.sass( 'assets/scss/modules/header-footer/header-footer.scss', './././inc/modules/header-footer-comment/assets/css/header-footer.css' );
mix.sass( 'assets/scss/modules/mega-menu/mega-menu.scss', 'assets/megamenu/css/megamenu.css' );
mix.sass( 'assets/scss/addons/addons-styles.scss', 'assets/css/master-addons-styles.css' );


// Element Stylesheet
// mix.sass( 'assets/scss/dual-heading.scss', 'assets/css/elements/dual-heading.css' );
