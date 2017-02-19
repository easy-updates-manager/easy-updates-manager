var webpack = require('webpack');
var path = require('path');

var JS_SOURCE_DIR = path.resolve(__dirname, 'js/source');
var JS_BUILD_DIR = path.resolve(__dirname, 'js');

var config = {
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
				loader : 'babel-loader'
			},
		]
	}
};

module.exports = config;