let mix = require('laravel-mix');
let path = require('path');

const config = {
    dev: process.env.NODE_ENV === 'development',
    src: __dirname + '/node_modules/',
    res: __dirname + '/Resources/assets/',
    out: __dirname + '/Assets/'
};

// Global instance for all product related stuff.
mix.js(path.join(config.res, 'js/app.js'), path.join(config.out, 'js/app.js'));

// Configure mix.
//mix.js(path.join(config.res, 'js/fields/index.js'), path.join(config.out, 'js/fields/index.js'));
//mix.js(path.join(config.res, 'js/products/index.js'), path.join(config.out, 'js/products/index.js'));
//mix.js(path.join(config.res, 'js/parameters/index.js'), path.join(config.out, 'js/parameters/index.js'));

mix.webpackConfig({
    externals: {
        jquery: 'jQuery'
    }
});

mix.disableNotifications();

