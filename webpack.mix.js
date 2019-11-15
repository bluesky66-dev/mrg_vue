const mix = require('laravel-mix');
const webpack = require('webpack');
const path = require('path');
// const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
/*
|--------------------------------------------------------------------------
| Mix Asset Management
|--------------------------------------------------------------------------
|
| Mix provides a clean, fluent API for defining some Webpack build steps
| for your Laravel application. By default, we are compiling the Sass
| file for the application as well as bundling up all the JS files.
|
*/

if (mix.inProduction()) {
  mix.version();
}

mix 
  .options({})
  .disableNotifications()
  .webpackConfig({
    module: {
      rules: [
        { 
          test: /\.vue-template$/, 
          loader: 'vue-template-loader'
        }
      ]
    },
    plugins: [
      new webpack.DefinePlugin({
        '__THEME': JSON.stringify('mat'),
        'DEV': JSON.stringify(process.env.NODE_ENV !== 'production'),
        'PROD': JSON.stringify(process.env.NODE_ENV === 'production')
      }),
      new webpack.LoaderOptionsPlugin({
        debug: true
      }),
      //new BundleAnalyzerPlugin()
    ],
    resolve: {
      alias: {
        '@': path.resolve('resources/assets/sass')
      }
    }
  })
  .js('resources/assets/js/momentum.js', 'public/js')
  .js('resources/assets/js/login.js', 'public/js')
  .sourceMaps()
  .extract([
    'vue',
    'axios',
    'vue-i18n',
    'quasar-framework',
    'es6-promise',
    'lodash/cloneDeepWith',
    'lodash/debounce',
    'lodash/find',
    'lodash/get',
    'lodash/has',
    'lodash/isArray',
    'lodash/isEmpty',
    'lodash/isNil',
    'lodash/isString',
    'lodash/map',
    'lodash/merge',
    'lodash/omit',
    'lodash/set',
    'lodash/setWith',
    'lodash/throttle',
    'lodash/transform',
  ])
  .sass('resources/assets/sass/app.scss', 'public/css')
  .copyDirectory('resources/assets/images', 'public/images', false)
  .copy('resources/assets/js/messages.js', 'public/js')
  .copy('node_modules/quasar-framework/dist/quasar.mat.css', 'public/css')
  .copy('node_modules/quasar-framework/dist/quasar.ie.mat.css', 'public/css')
  .copy('node_modules/quasar-extras/roboto-font', 'public/css/roboto-font')
  .copy('node_modules/quasar-extras/material-icons', 'public/css/material-icons');


//console.log(mix)
