const mix = require('laravel-mix');
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.options({
	postCss: [
		require('autoprefixer'),
	],
});

mix.setPublicPath('public');

mix.webpackConfig({
	resolve: {
		extensions: ['.js', '.vue'],
		alias: {
			'@': __dirname + 'resources'
		}
	},
	output: {
		chunkFilename: 'js/chunks/[name].js',
	},
}).react();

// used to run app using reactjs
mix.ts('resources/react-app/src/index.tsx', 'public/js/app.js').version();
mix.copy('resources/react-app/public', 'public');
