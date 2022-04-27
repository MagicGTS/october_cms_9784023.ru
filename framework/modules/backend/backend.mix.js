/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your theme assets. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

module.exports = (mix) => {
    // Backend LESS
    mix.less('modules/backend/assets/less/october.less', 'modules/backend/assets/css/');
    mix.less('modules/backend/behaviors/relationcontroller/assets/less/relation.less', 'modules/backend/behaviors/relationcontroller/assets/css/');

    // Component LESS
    mix.lessList('modules/backend/vuecomponents');
    mix.lessList('modules/backend/formwidgets', ['richeditor']);
    mix.lessList('modules/backend/behaviors');
    mix.lessList('modules/backend/widgets');

    // Backend Source
    mix.combine([
        'modules/backend/assets/js/vendor/jquery.touchwipe.js',
        'modules/backend/assets/js/vendor/jquery.autoellipsis.js',
        'modules/backend/assets/js/vendor/jquery.waterfall.js',
        'modules/backend/assets/js/vendor/jquery.cookie.js',
        'modules/backend/assets/vendor/dropzone/dropzone.js',
        'modules/backend/assets/vendor/jcrop/js/jquery.Jcrop.js',
        'modules/backend/assets/vendor/sortablejs/sortable.js',
        'modules/system/assets/vendor/prettify/prettify.js',
        'modules/backend/assets/js/october.lang.js',
        'modules/backend/assets/js/october.alert.js',
        'modules/backend/assets/js/october.button.js',
        'modules/backend/assets/js/october.snackbar.js',
        'modules/backend/assets/js/october.scrollpad.js',
        'modules/backend/assets/js/october.sidenav.js',
        'modules/backend/assets/js/october.scrollbar.js',
        'modules/backend/assets/js/october.filelist.js',
        'modules/backend/assets/js/october.layout.js',
        'modules/backend/assets/js/october.sidepaneltab.js',
        'modules/backend/assets/js/october.simplelist.js',
        'modules/backend/assets/js/october.treelist.js',
        'modules/backend/assets/js/october.sidenav-tree.js',
        'modules/backend/assets/js/october.datetime.js',
        'modules/backend/assets/js/october.responsivemenu.js',
        'modules/backend/assets/js/october.mainmenu.js',
        'modules/backend/assets/js/october.modalfocusmanager.js',
        'modules/backend/assets/js/october.domidmanager.js',
        'modules/backend/assets/js/october.vueutils.js',
        'modules/backend/assets/js/october.tooltip.js',
        'modules/backend/assets/js/october.jsmodule.js',
        'modules/backend/assets/js/october.vueapp.js',
        'modules/backend/assets/js/backend.js',
        'modules/backend/assets/js/backend.fixes.js'
    ], 'modules/backend/assets/js/october-min.js');

    // Repeater Widget
    mix.combine([
        'modules/backend/formwidgets/repeater/assets/js/repeater.js',
        'modules/backend/formwidgets/repeater/assets/js/repeater.builder.js',
        'modules/backend/formwidgets/repeater/assets/js/repeater.accordion.js'
    ], 'modules/backend/formwidgets/repeater/assets/js/repeater-min.js');
};
