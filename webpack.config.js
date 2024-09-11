/* frontend webpack builder */
const Encore = require('@symfony/webpack-encore');

Encore
    // set build path
    .setOutputPath('public/assets/')
    .setPublicPath('/assets')

    // register css
    .addEntry('loading-css', './assets/css/loading.css')
    .addEntry('scrollbar-css', './assets/css/scrollbar.css')
    .addEntry('code-paste-css', './assets/css/code-paste.css')
    .addEntry('error-page-css', './assets/css/error-page.css')

    .addEntry('loading-js', './assets/js/loading.js')
    .addEntry('code-paste-js', './assets/js/code-paste.js')

    // copy static assets
    .copyFiles({
            from: './assets/images', 
            to: 'images/[path][name].[ext]' 
        }
    )

    // other webpack configs
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })
;

module.exports = Encore.getWebpackConfig();
