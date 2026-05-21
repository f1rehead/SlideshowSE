const path = require( 'path' );
const DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );

// Shared bits for webpack configs below.
const config = {
	module: {
		rules: [
			{
				test: /\.(sass|scss)$/,
				use: [
					MiniCssExtractPlugin.loader,
					'css-loader',
					'sass-loader',
				],
			},
		],
	},
	plugins: [
		new MiniCssExtractPlugin( {
			filename: './css/build/main.min.[fullhash].css',
		} ),
	],
};

// Bundles legacy admin + frontend jQuery slideshow scripts into js/min/.
const plugin = Object.assign( {}, config, {
	name: 'plugin',
	entry: {
		frontend: './js/all.frontend.js',
		backend: './js/all.backend.js',
	},
	output: {
		filename: 'min/all.[name].min.js',
		path: path.resolve( __dirname, 'js' ),
		environment: {
			arrowFunction: false,
		},
		iife: false,
	},
	optimization: {
		minimize: true,
		chunkIds: false,
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				loader: 'babel-loader',
			},
		],
	},
} );

// Gutenberg block: outputs block/index.js and block/index.asset.php for PHP registration.
const block = Object.assign( {}, config, {
	name: 'block',
	entry: {
		index: './src/index.js',
	},
	output: {
		filename: '[name].js',
		path: path.resolve( __dirname, 'block' ),
	},
	optimization: {
		minimize: true,
	},
	plugins: [
		new MiniCssExtractPlugin( {
			filename: '[name].css',
		} ),
		new DependencyExtractionWebpackPlugin(),
	],
	module: {
		rules: [
			{
				test: /\.(js|jsx)$/,
				exclude: /node_modules/,
				loader: 'babel-loader',
				options: {
					presets: [ '@babel/preset-react' ],
				},
			},
			...config.module.rules,
		],
	},
} );

module.exports = [ plugin, block ];
