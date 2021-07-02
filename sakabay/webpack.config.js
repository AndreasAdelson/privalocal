var Encore = require('@symfony/webpack-encore');
var dotenv = require('dotenv');
const VuetifyLoaderPlugin = require('vuetify-loader/lib/plugin')
// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
  // directory where compiled assets will be stored
  .setOutputPath('public/build/')
  // public path used by the web server to access the output path
  .setPublicPath('/build')
  .cleanupOutputBeforeBuild()
  .enableVueLoader()
  .enableSourceMaps(!Encore.isProduction())
  // uncomment to create hashed filenames (e.g. app.abc123.css)
  .enableVersioning(Encore.isProduction())
  // only needed for CDN's or sub-directory deploy
  //.setManifestKeyPrefix('build/')

  /*
   * ENTRY CONFIG
   *
   * Add 1 entry for each "page" of your app
   * (including one that's included on every page - e.g. "app")
   *
   * Each entry will result in one JavaScript file (e.g. app.js)
   * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
   */

  .addEntry('js/app', './assets/js/app.js')
  .addStyleEntry('css/bootstrap', './node_modules/bootstrap/dist/css/bootstrap.min.css')
  .addStyleEntry('css/bootstrap-vue', './node_modules/bootstrap-vue/dist/bootstrap-vue.min.css')
  .addStyleEntry('css/vue-multiselect', './node_modules/vue-multiselect/dist/vue-multiselect.min.css')
  // .addStyleEntry('css/leaflet', './node_modules/leaflet/dist/leaflet.css')
  .addStyleEntry('css/main', './assets/scss/main.scss')
  .addStyleEntry('css/app', './assets/css/app.css')
  .enableSassLoader()
  .addPlugin(new VuetifyLoaderPlugin())
  //.addStyleEntry('css/main', './assets/scss/main.scss')

  ////images
  .copyFiles({
    from: './assets/pictures',
    // to: 'images'
    // optional target path, relative to the output dir
    //to: 'images/[path][name].[ext]',

    // if versioning is enabled, add the file hash too
    //to: 'images/[path][name].[hash:8].[ext]',

    // only copy files matching this pattern
    //pattern: /\.(png|jpg|jpeg)$/
  })
  // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
  // .splitEntryChunks()

  // will require an extra script tag for runtime.js
  // but, you probably want this, unless you're building a single-page app
  .configureDefinePlugin(options => {
    const env = dotenv.config();
    if (env.error) {
      throw env.error;
    }
    options['process.env'].STRIPE_PUBLISHABLE_KEY = JSON.stringify(env.parsed.STRIPE_PUBLISHABLE_KEY);
  })
  .disableSingleRuntimeChunk()
  .autoProvidejQuery()


/*
 * FEATURE CONFIG
 *
 * Enable & configure other features below. For a full
 * list of features, see:
 * https://symfony.com/doc/current/frontend.html#adding-more-features
 */
// .enableBuildNotifications()

// uncomment if you use TypeScript
//.enableTypeScriptLoader()

// uncomment to get integrity="..." attributes on your script & link tags
// requires WebpackEncoreBundle 1.4 or higher
//.enableIntegrityHashes(Encore.isProduction())
;
var config = Encore.getWebpackConfig();
config.resolve.extensions.push('.json');
config.resolve.modules = ['assets/js', 'node_modules'];
module.exports = config;
