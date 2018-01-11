var webpack = require('webpack');
var path = require('path');
var ExtractTextPlugin = require("extract-text-webpack-plugin");


var CSS_SOURCE_DIR = path.resolve(__dirname, 'css/source');
var CSS_BUILD_DIR = path.resolve(__dirname, 'css');
var JS_SOURCE_DIR = path.resolve(__dirname, 'js/source');
var JS_BUILD_DIR = path.resolve(__dirname, 'js');

var config = [
	{
		entry: {
			app: JS_SOURCE_DIR + '/main.js',
		},
		output: {
			path: JS_BUILD_DIR,
			filename: 'admin.js'
		},
		module : {
			loaders : [
				{
					test : /\.jsx?/,
					include : JS_SOURCE_DIR,
					loader : 'babel-loader',
					query: {
						presets: ['es2015','react','stage-0'],
						plugins: ['transform-runtime']
					}
				}
			]
		},
		plugins: [
			new webpack.DefinePlugin({
				'process.env': {
					NODE_ENV: JSON.stringify(process.env.NODE_ENV)
				}
			}),
		]
	},
	{
		entry: {
			css: CSS_SOURCE_DIR + '/style.scss'
		},
		output: {
			path: CSS_BUILD_DIR,
			filename: 'style.css'
		},
		module : {
			loaders : [
				{
					test : /\.scss/,
					include : CSS_SOURCE_DIR,
					loader : ExtractTextPlugin.extract( {
						use: [
							"css-loader",
							"sass-loader"
						]
					})
				}
			]
		},
		plugins: [
			new ExtractTextPlugin( 'style.css' ),new webpack.DefinePlugin({
      'process.env.NODE_ENV': JSON.stringify(process.env.NODE_ENV)
    }),
		]
	}

];

module.exports = config;
