/* admin-suite frontend webpack builder */
const Encore = require('@symfony/webpack-encore');

Encore
    // set build path
    .setOutputPath('public/assets/')
    .setPublicPath('/assets')

    // register css
    .addEntry('paste-add-css', './assets/css/paste-add.css')
    .addEntry('scrollbar-css', './assets/css/scrollbar.css')
    .addEntry('paste-view-css', './assets/css/paste-view.css')
    .addEntry('error-page-css', './assets/css/error-page.css')
    .addEntry('atom-one-dark-css', './assets/css/atom-one-dark.css')

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
