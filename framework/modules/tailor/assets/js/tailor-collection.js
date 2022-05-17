$.oc.module.register('tailor.app', function () {
    'use strict';

    const TailorAppBase = $.oc.module.import('tailor.app.base');
    
    class TailorApp extends TailorAppBase {
        constructor() {
            super();
        }

        registerMethods() {
            super.registerMethods();
        }

        handleFormSaved(data) {
            super.handleFormSaved(data);
            this.state.initial.statusCode = data.result.statusCode;
            this.state.initial.fullSlug = data.result.fullSlug;
        }

        onBeforeContainersInit() {
            this.state.toolbarElements = [
                {
                    type: 'button',
                    icon: 'octo-icon-save',
                    label: this.getLangString('FormSave'),
                    tooltip: this.getLangString('FormSave'),
                    hotkey: 'ctrl+s, cmd+s',
                    tooltipHotkey: '⌃S, ⌘S',
                    command: 'form:onSave',
                    customData: {
                        request: {
                            data: { 'redirect': 0 }
                        }
                    }
                },
                {
                    type: 'button',
                    icon: 'octo-icon-keyboard-return',
                    label: this.state.initial.isCreateAction ? this.getLangString('FormCreateClose') : this.getLangString('FormSaveClose'),
                    tooltip: this.state.initial.isCreateAction ? this.getLangString('FormCreateClose') : this.getLangString('FormSaveClose'),
                    hotkey: 'ctrl+enter, cmd+enter',
                    tooltipHotkey: '⌃S, ⌘S',
                    command: 'form:onSave',
                    customData: {
                        request: {
                            data: { 'close': 1 }
                        }
                    }
                },
                {
                    type: 'button',
                    icon: 'octo-icon-location-target',
                    command: 'onPreview',
                    label: this.getLangString('Preview')
                },
                this.state.toolbarExtensionPoint
            ];

            if (!this.state.initial.isCreateAction) {
                this.state.toolbarElements.push(
                    {
                        type: 'button',
                        icon: 'octo-icon-delete',
                        command: 'form:onDelete',
                        hotkey: 'shift+option+d',
                        label: this.getLangString('FormDelete'),
                        fixedRight: true,
                        tooltip: this.getLangString('FormDelete'),
                        tooltipHotkey: '⇧⌥D',
                        customData: {
                            confirm: this.getLangString('FormConfirmDelete')
                        }
                    }
                );
            }

            super.onBeforeContainersInit();
        }

        onAfterCommandSuccess(command, data) {
            if (command == 'form:onSave') {
                this.handleFormSaved(data);
            }
        }

        async onCommand(command, isHotkey, ev, targetElement, customData, throwOnError) {
            if (command === 'onPreview') {
                this.onPreview(targetElement);
                return;
            }

            if (!super.isFormCommand(command)) {
                return;
            }

            await super.onCommand(command, isHotkey, ev, targetElement, customData, throwOnError);
        }
    }

    return new TailorApp();
});