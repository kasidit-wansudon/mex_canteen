const mix = require('laravel-mix');
const path = require('path');

mix.js('resources/js/app.js', 'public/js')
    .vue()
    .postCss('resources/css/app.css', 'public/css');

mix.webpackConfig({
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
            '@api': path.resolve(__dirname, 'resources/js/api'),
            '@css': path.resolve(__dirname, 'resources/css'),
        }
    }
});