Vue.component('editor-component-application', {
    props: {
        store: Object,
        customLogo: String
    },
    data: function data() {
        return {
            tabContextMenuItems: [{
                type: 'text',
                command: 'reveal-in-sidebar',
                label: ''
            }, {
                type: 'separator'
            }],
            navigatorReadonly: false,
            sidebarHidden: false,
            directDocumentNotFound: false,
            quickViewHotkey: 'ctrl+shift+a',
            toggleSidebarHotkey: 'ctrl+shift+b'
        };
    },
    computed: {
        directDocumentName: function computeDirectDocumentName() {
            if (typeof this.store.state.params.directModeDocument !== 'string') {
                return null;
            }

            return this.store.state.params.directModeDocument;
        },

        isDirectDocumentMode: function computeIsDirectDocumentMode() {
            return !!this.directDocumentName;
        },

        documentNameToOpen: function computeDocumentNameToOpen() {
            if (typeof this.store.state.params.openDocument !== 'string') {
                return null;
            }

            return this.store.state.params.openDocument;
        },

        isDocumentNameToOpenProvided: function isDocumentNameToOpenProvided() {
            return !!this.documentNameToOpen;
        }
    },
    methods: {
        ajaxRequest: function ajaxRequest(handler, requestData) {
            return new Promise(function (resolve, reject, onCancel) {
                var request = $.request(handler, {
                    data: requestData,
                    success: function success(data) {
                        resolve(data);
                    },
                    error: function error(data) {
                        reject(data);
                    }
                });

                onCancel(function () {
                    request.abort();
                });
            });
        },

        openTab: function openTab(tabData) {
            var key = this.store.tabManager.createTab(tabData);
            this.$refs.tabs.selectTab(key);
        },

        showEditorDocumentInfoPopup: function showEditorDocumentInfoPopup(items, title) {
            this.$refs.infoPopup.show(items, title);
        },

        openDocument: function openDocument(documentUriStr) {
            this.$refs.navigator.openDocument(documentUriStr);
        },

        setNavigatorReadonly: function setNavigatorReadonly(value) {
            this.navigatorReadonly = value;
        },


        hasChangedTabs: function hasChangedTabs() {
            return this.store.tabManager.hasChangedTabs();
        },

        revealNavigatorNode: function revealNavigatorNode(uniqueKey) {
            this.$refs.navigator.reveal(uniqueKey);
        },

        getCurrentDocumentComponent: function getCurrentDocumentComponent() {
            return this.$refs.tabs.getSelectedTabComponent();
        },

        getAllOpenDocumentComponents: function getAllOpenDocumentComponents() {
            var _this = this;

            var tabKeys = this.store.tabManager.getTabKeys();
            var result = [];

            tabKeys.forEach(function (tabKey) {
                var component = _this.$refs.tabs.getTabComponent(tabKey);
                component && result.push(component);
            });

            return result;
        },

        navigatorNodeKeyChanged: function navigatorNodeKeyChanged(oldValue, newValue) {
            this.$refs.navigator.navigatorNodeKeyChanged(oldValue, newValue);
        },

        runCurrentDocumentComponentCommand: function runCurrentDocumentComponentCommand(command, payload) {
            var component = this.getCurrentDocumentComponent();
            if (!component) {
                return;
            }

            if (typeof component.onApplicationCommand === 'function') {
                component.onApplicationCommand(command, payload);
            }
        },

        closeAllTabs: function closeAllTabs(onlySaved) {
            this.$refs.tabs.closeAllTabs(null, onlySaved);
        },

        postDirectDocumentSavedMessage: function postDirectDocumentSavedMessage() {
            if (window.parent) {
                window.parent.postMessage('october-editor-saved', '*');
            }
        },

        onTabSelected: function onTabSelected(key) {
            this.store.state.navigatorSelectedUniqueKey = key;

            this.store.dispatchCommand('global:application-tab-selected', key);
        },

        onTabClose: function onTabClose(tabKey, ev) {
            var index = this.$refs.tabs.getTabIndex(tabKey);
            this.store.tabManager.closeTab(index);
        },

        onTabContextMenu: function onTabContextMenu(command, tab) {
            if (command === 'reveal-in-sidebar') {
                this.$refs.navigator.reveal(tab.key);
            }
        },

        onShowQuickAccess: function onShowQuickAccess(ev) {
            ev.preventDefault();
            ev.stopPropagation();

            if (!$.oc.modalFocusManager.hasHotkeyBlockingAbove(null)) {
                this.$refs.navigator.showQuickAccess();
            }
        },

        onToggleSidebar: function onToggleSidebar(ev) {
            ev.preventDefault();
            ev.stopPropagation();

            this.sidebarHidden = !this.sidebarHidden;
        },

        onCloseDirectDocumentClick: function onCloseDirectDocumentClick() {
            if (window.parent) {
                window.parent.postMessage('october-editor-close', '*');
            }
        }
    },
    watch: {
        'store.state.editorTabs': {
            deep: true,
            handler: function watchStoreTabs(value) {
                if (!this.isDirectDocumentMode) {
                    this.store.tabManager.storePersistentTabs(value);
                }
            }
        }
    },
    mounted: function mounted() {
        var _this2 = this;

        Vue.nextTick(function () {
            if (!_this2.isDirectDocumentMode) {
                var tabKeys = _this2.store.tabManager.getPersistentTabKeys();
                if (tabKeys.length) {
                    tabKeys = _this2.$refs.navigator.openTabs(tabKeys);

                    _this2.store.tabManager.setPersistentTabKeys(tabKeys);
                }

                if (_this2.isDocumentNameToOpenProvided) {
                    if (_this2.$refs.navigator.openTabs([_this2.documentNameToOpen]).length) {
                        _this2.revealNavigatorNode(_this2.documentNameToOpen);
                    }
                    var url = window.location.href;
                    url = url.split("?")[0];
                    window.history.replaceState({}, "", url);
                }
            } else {
                if (!_this2.$refs.navigator.openTabs([_this2.directDocumentName]).length) {
                    _this2.directDocumentNotFound = true;
                }
            }
        });

        var menuUtils = $.oc.module.import('backend.component.dropdownmenu.utils');
        var item = menuUtils.findMenuItem(this.tabContextMenuItems, ['reveal-in-sidebar'], 'command');
        if (item) {
            item.label = this.$el.getAttribute('data-lang-reveal-in-sidebar');
        }
    },
    template: '#editor_vuecomponents_application'
});
