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
    // System LESS
    mix.less('modules/system/assets/less/styles.less', 'modules/system/assets/css/');

    // Component LESS
    mix.lessList('modules/system/widgets');

    // Storm LESS
    mix.less('modules/system/assets/ui/storm.less', 'modules/system/assets/ui/');

    // AJAX Framework
    mix.less('modules/system/assets/less/framework.extras.less', 'modules/system/assets/css/');
    mix.combine(['modules/system/assets/js/framework.js'], 'modules/system/assets/js/framework-min.js');
    mix.combine([
        'modules/system/assets/js/framework.js',
        'modules/system/assets/js/framework.extras.js'
    ], 'modules/system/assets/js/framework.combined-min.js');

    // Code Editor Form Widget
    mix.combine([
        'modules/backend/formwidgets/codeeditor/assets/vendor/emmet/emmet.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/ace.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/ext-emmet.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/ext-language_tools.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/mode-php.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/mode-twig.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/mode-markdown.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/mode-plain_text.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/mode-html.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/mode-less.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/mode-css.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/mode-scss.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/mode-sass.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/mode-yaml.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/mode-javascript.js',
        'modules/backend/formwidgets/codeeditor/assets/js/codeeditor.js',
    ], 'modules/backend/formwidgets/codeeditor/assets/js/build-min.js');

    // Table Widget
    mix.combine([
        'modules/backend/widgets/table/assets/js/table.js',
        'modules/backend/widgets/table/assets/js/table.helper.navigation.js',
        'modules/backend/widgets/table/assets/js/table.helper.search.js',
        'modules/backend/widgets/table/assets/js/table.datasource.base.js',
        'modules/backend/widgets/table/assets/js/table.datasource.client.js',
        'modules/backend/widgets/table/assets/js/table.datasource.server.js',
        'modules/backend/widgets/table/assets/js/table.processor.base.js',
        'modules/backend/widgets/table/assets/js/table.processor.string.js',
        'modules/backend/widgets/table/assets/js/table.processor.checkbox.js',
        'modules/backend/widgets/table/assets/js/table.processor.dropdown.js',
        'modules/backend/widgets/table/assets/js/table.processor.autocomplete.js',
        'modules/backend/widgets/table/assets/js/table.validator.base.js',
        'modules/backend/widgets/table/assets/js/table.validator.required.js',
        'modules/backend/widgets/table/assets/js/table.validator.basenumber.js',
        'modules/backend/widgets/table/assets/js/table.validator.integer.js',
        'modules/backend/widgets/table/assets/js/table.validator.float.js',
        'modules/backend/widgets/table/assets/js/table.validator.length.js',
        'modules/backend/widgets/table/assets/js/table.validator.regex.js',
    ], 'modules/backend/widgets/table/assets/js/build-min.js');

    // Vue Source
    mix.combine([
        'modules/system/assets/vendor/vue/vue.min.js',
        'modules/system/assets/vendor/vue-router/vue-router.js',
        'modules/system/assets/vendor/bluebird/bluebird.min.js',
        'modules/system/assets/vendor/promise-queue/promise-queue.js',
        'modules/system/assets/js/vue.hotkey.js',
        'modules/system/assets/js/vue.main.js'
    ], 'modules/system/assets/js/vue.bundle-min.js');

    // Storm Source
    mix.combine([
        'modules/system/assets/ui/vendor/mustache/mustache.js',
        'modules/system/assets/ui/vendor/modernizr/modernizr.js',
        'modules/system/assets/ui/vendor/bootstrap/js/dropdown.js',
        'modules/system/assets/ui/vendor/bootstrap/js/transition.js',
        'modules/system/assets/ui/vendor/bootstrap/js/tab.js',
        'modules/system/assets/ui/vendor/bootstrap/js/modal.js',
        'modules/system/assets/ui/vendor/bootstrap/js/tooltip.js',
        'modules/system/assets/ui/vendor/raphael/raphael.js',
        'modules/system/assets/ui/vendor/flot/jquery.flot.js',
        'modules/system/assets/ui/vendor/flot/jquery.flot.tooltip.js',
        'modules/system/assets/ui/vendor/flot/jquery.flot.resize.js',
        'modules/system/assets/ui/vendor/flot/jquery.flot.time.js',
        'modules/system/assets/ui/vendor/select2/js/select2.full.js',
        'modules/system/assets/ui/vendor/mousewheel/mousewheel.js',
        'modules/system/assets/ui/vendor/sortable/jquery-sortable.js',
        'modules/system/assets/ui/vendor/moment/moment.js',
        'modules/system/assets/ui/vendor/moment/moment-timezone-with-data.js',
        'modules/system/assets/ui/vendor/pikaday/js/pikaday.js',
        'modules/system/assets/ui/vendor/pikaday/js/pikaday.jquery.js',
        'modules/system/assets/ui/vendor/clockpicker/js/jquery-clockpicker.js',

        'modules/system/assets/ui/js/foundation.baseclass.js',
        'modules/system/assets/ui/js/foundation.element.js',
        'modules/system/assets/ui/js/foundation.event.js',
        'modules/system/assets/ui/js/foundation.controlutils.js',
        'modules/system/assets/ui/js/flashmessage.js',
        'modules/system/assets/ui/js/autocomplete.js',
        'modules/system/assets/ui/js/checkbox.js',
        'modules/system/assets/ui/js/checkbox.balloon.js',
        'modules/system/assets/ui/js/dropdown.js',
        'modules/system/assets/ui/js/callout.js',
        'modules/system/assets/ui/js/contextmenu.js',
        'modules/system/assets/ui/js/datepicker.js',
        'modules/system/assets/ui/js/tooltip.js',
        'modules/system/assets/ui/js/toolbar.js',
        'modules/system/assets/ui/js/select.js',
        'modules/system/assets/ui/js/loader.base.js',
        'modules/system/assets/ui/js/loader.cursor.js',
        'modules/system/assets/ui/js/loader.stripe.js',
        'modules/system/assets/ui/js/popover.js',
        'modules/system/assets/ui/js/popup.js',
        'modules/system/assets/ui/js/chart.utils.js',
        'modules/system/assets/ui/js/chart.line.js',
        'modules/system/assets/ui/js/chart.bar.js',
        'modules/system/assets/ui/js/chart.pie.js',
        'modules/system/assets/ui/js/chart.meter.js',
        'modules/system/assets/ui/js/list.rowlink.js',
        'modules/system/assets/ui/js/input.monitor.js',
        'modules/system/assets/ui/js/input.hotkey.js',
        'modules/system/assets/ui/js/input.presetengine.js',
        'modules/system/assets/ui/js/input.preset.js',
        'modules/system/assets/ui/js/input.trigger.js',
        'modules/system/assets/ui/js/drag.value.js',
        'modules/system/assets/ui/js/drag.sort.js',
        'modules/system/assets/ui/js/drag.scroll.js',
        'modules/system/assets/ui/js/tab.js',
        'modules/system/assets/ui/js/inspector.surface.js',
        'modules/system/assets/ui/js/inspector.manager.js',
        'modules/system/assets/ui/js/inspector.wrapper.base.js',
        'modules/system/assets/ui/js/inspector.wrapper.popup.js',
        'modules/system/assets/ui/js/inspector.wrapper.container.js',
        'modules/system/assets/ui/js/inspector.groups.js',
        'modules/system/assets/ui/js/inspector.engine.js',
        'modules/system/assets/ui/js/inspector.editor.base.js',
        'modules/system/assets/ui/js/inspector.editor.string.js',
        'modules/system/assets/ui/js/inspector.editor.checkbox.js',
        'modules/system/assets/ui/js/inspector.editor.dropdown.js',
        'modules/system/assets/ui/js/inspector.editor.popupbase.js',
        'modules/system/assets/ui/js/inspector.editor.text.js',
        'modules/system/assets/ui/js/inspector.editor.set.js',
        'modules/system/assets/ui/js/inspector.editor.objectlist.js',
        'modules/system/assets/ui/js/inspector.editor.object.js',
        'modules/system/assets/ui/js/inspector.editor.stringlist.js',
        'modules/system/assets/ui/js/inspector.editor.stringlistautocomplete.js',
        'modules/system/assets/ui/js/inspector.editor.dictionary.js',
        'modules/system/assets/ui/js/inspector.editor.autocomplete.js',
        'modules/system/assets/ui/js/inspector.helpers.js',
        'modules/system/assets/ui/js/inspector.validationset.js',
        'modules/system/assets/ui/js/inspector.validator.base.js',
        'modules/system/assets/ui/js/inspector.validator.basenumber.js',
        'modules/system/assets/ui/js/inspector.validator.required.js',
        'modules/system/assets/ui/js/inspector.validator.regex.js',
        'modules/system/assets/ui/js/inspector.validator.integer.js',
        'modules/system/assets/ui/js/inspector.validator.float.js',
        'modules/system/assets/ui/js/inspector.validator.length.js',
        'modules/system/assets/ui/js/inspector.externalparametereditor.js',
        'modules/system/assets/ui/js/list.sortable.js'
    ], 'modules/system/assets/ui/storm-min.js');
};
