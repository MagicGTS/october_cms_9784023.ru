$.oc.module.register('cms.editor.extension.documentcontroller.asset', function () {
    'use strict';

    var DocumentControllerBase = $.oc.module.import('editor.extension.documentcontroller.base');
    var treeviewUtils = $.oc.vueComponentHelpers.treeviewUtils;
    var EditorCommand = $.oc.module.import('editor.command');
    var FileSystemFunctions = $.oc.module.import('editor.extension.filesystemfunctions');

    var DocumentControllerAsset = function (_DocumentControllerBa) {
        babelHelpers.inherits(DocumentControllerAsset, _DocumentControllerBa);

        function DocumentControllerAsset() {
            babelHelpers.classCallCheck(this, DocumentControllerAsset);
            return babelHelpers.possibleConstructorReturn(this, (DocumentControllerAsset.__proto__ || Object.getPrototypeOf(DocumentControllerAsset)).apply(this, arguments));
        }

        babelHelpers.createClass(DocumentControllerAsset, [{
            key: 'beforeDocumentOpen',
            value: function beforeDocumentOpen(commandObj, nodeData) {
                if (!nodeData.userData) {
                    return false;
                }

                if (nodeData.userData.isFolder) {
                    return false;
                }

                if (nodeData.userData.isEditable) {
                    return true;
                }

                return false;
            }
        }, {
            key: 'initListeners',
            value: function initListeners() {
                this.on('cms:navigator-context-menu-display', this.getNavigatorContextMenuItems);
                this.on('cms:cms-asset-create-directory', this.onCreateDirectory);
                this.on('cms:cms-asset-delete', this.onDeleteAssetOrDirectory);
                this.on('cms:cms-asset-rename', this.onRenameAssetOrDirectory);
                this.on('cms:navigator-node-moved', this.onNavigatorNodeMoved);
                this.on('cms:navigator-external-drop', this.onNavigatorExternalDrop);
                this.on('cms:cms-asset-upload', this.onUploadDocument);
                this.on('cms:cms-asset-open', this.onOpenAsset);
                this.on('cms:navigator-nodes-updated', this.onNavigatorNodesUpdated);
            }
        }, {
            key: 'getNavigatorContextMenuItems',
            value: function getNavigatorContextMenuItems(commandObj, payload) {
                var DocumentUri = $.oc.module.import('editor.documenturi');
                var uri = DocumentUri.parse(payload.nodeData.uniqueKey);
                var parentPath = payload.nodeData.userData.path;

                if (uri.documentType !== this.documentType) {
                    return;
                }

                if (payload.nodeData.userData.isFolder) {
                    payload.menuItems.push({
                        type: 'text',
                        icon: 'octo-icon-create',
                        command: new EditorCommand('cms:create-document@' + this.documentType, {
                            path: parentPath
                        }),
                        label: this.trans('cms::lang.asset.new')
                    });

                    payload.menuItems.push({
                        type: 'text',
                        icon: 'octo-icon-upload',
                        command: new EditorCommand('cms:cms-asset-upload@' + this.documentType, {
                            path: parentPath
                        }),
                        label: this.trans('cms::lang.asset.upload_files')
                    });

                    payload.menuItems.push({
                        type: 'text',
                        icon: 'octo-icon-folder',
                        command: 'cms:cms-asset-create-directory@' + parentPath,
                        label: this.trans('cms::lang.asset.create_directory')
                    });

                    payload.menuItems.push({
                        type: 'separator'
                    });
                } else {
                    if (!payload.nodeData.userData.isEditable) {
                        payload.menuItems.push({
                            type: 'text',
                            icon: 'octo-icon-fullscreen',
                            command: new EditorCommand('cms:cms-asset-open@' + parentPath, {
                                url: payload.nodeData.userData.url
                            }),
                            label: this.trans('cms::lang.asset.open')
                        });
                    }
                }

                payload.menuItems.push({
                    type: 'text',
                    icon: 'octo-icon-terminal',
                    command: new EditorCommand('cms:cms-asset-rename@' + parentPath, {
                        fileName: payload.nodeData.userData.filename
                    }),
                    label: this.trans('cms::lang.asset.rename')
                });

                payload.menuItems.push({
                    type: 'text',
                    icon: 'octo-icon-delete',
                    command: new EditorCommand('cms:cms-asset-delete@' + parentPath, {
                        itemsDetails: payload.itemsDetails
                    }),
                    label: this.trans('cms::lang.asset.delete')
                });
            }
        }, {
            key: 'getAllAssetFilenames',
            value: function getAllAssetFilenames() {
                if (this.cachedAssetList) {
                    return this.cachedAssetList;
                }

                var assetsNavigatorNode = treeviewUtils.findNodeByKeyInSections(this.parentExtension.state.navigatorSections, 'cms:cms-asset');

                var assetList = [];

                if (assetsNavigatorNode) {
                    assetList = treeviewUtils.getFlattenNodes(assetsNavigatorNode.nodes).filter(function (assetNode) {
                        return !assetNode.userData.isFolder;
                    }).map(function (assetNode) {
                        return assetNode.userData.path;
                    });
                } else {
                    assetList = this.parentExtension.state.customData.assets;
                }

                this.cachedAssetList = assetList;
                return assetList;
            }
        }, {
            key: 'onBeforeDocumentCreated',
            value: function onBeforeDocumentCreated(commandObj, payload, documentData) {
                var parentPath = '';

                if (commandObj.userData && commandObj.userData.path) {
                    parentPath = commandObj.userData.path;
                }

                if (parentPath.length > 0) {
                    documentData.document.fileName = parentPath + '/' + documentData.document.fileName;
                }
            }
        }, {
            key: 'onCreateDirectory',
            value: function onCreateDirectory(cmd, payload) {
                var fs = new FileSystemFunctions(this);
                var theme = this.parentExtension.cmsTheme;

                fs.createDirectoryFromNavigatorMenu('onAssetCreateDirectory', cmd, payload, {
                    theme: theme
                });
            }
        }, {
            key: 'onDeleteAssetOrDirectory',
            value: function () {
                var _ref = babelHelpers.asyncToGenerator( /*#__PURE__*/regeneratorRuntime.mark(function _callee(cmd, payload) {
                    var fs, theme;
                    return regeneratorRuntime.wrap(function _callee$(_context) {
                        while (1) {
                            switch (_context.prev = _context.next) {
                                case 0:
                                    fs = new FileSystemFunctions(this);
                                    theme = this.parentExtension.cmsTheme;
                                    _context.next = 4;
                                    return fs.deleteFileOrDirectoryFromNavigatorMenu('onAssetDelete', cmd, payload, {
                                        theme: theme
                                    });

                                case 4:
                                case 'end':
                                    return _context.stop();
                            }
                        }
                    }, _callee, this);
                }));

                function onDeleteAssetOrDirectory(_x, _x2) {
                    return _ref.apply(this, arguments);
                }

                return onDeleteAssetOrDirectory;
            }()
        }, {
            key: 'onRenameAssetOrDirectory',
            value: function onRenameAssetOrDirectory(cmd, payload) {
                var fs = new FileSystemFunctions(this);
                var theme = this.parentExtension.cmsTheme;

                fs.renameFileOrDirectoryFromNavigatorMenu('onAssetRename', cmd, payload, {
                    theme: theme
                });
            }
        }, {
            key: 'onNavigatorNodeMoved',
            value: function () {
                var _ref2 = babelHelpers.asyncToGenerator( /*#__PURE__*/regeneratorRuntime.mark(function _callee2(cmd) {
                    var fs, theme;
                    return regeneratorRuntime.wrap(function _callee2$(_context2) {
                        while (1) {
                            switch (_context2.prev = _context2.next) {
                                case 0:
                                    fs = new FileSystemFunctions(this);
                                    theme = this.parentExtension.cmsTheme;


                                    fs.handleNavigatorNodeMove('onAssetMove', cmd, {
                                        theme: theme
                                    });

                                case 3:
                                case 'end':
                                    return _context2.stop();
                            }
                        }
                    }, _callee2, this);
                }));

                function onNavigatorNodeMoved(_x3) {
                    return _ref2.apply(this, arguments);
                }

                return onNavigatorNodeMoved;
            }()
        }, {
            key: 'onNavigatorExternalDrop',
            value: function onNavigatorExternalDrop(cmd) {
                var fs = new FileSystemFunctions(this);
                var theme = this.parentExtension.cmsTheme;

                fs.handleNavigatorExternalDrop('onAssetUpload', cmd, {
                    theme: theme
                });
            }
        }, {
            key: 'onUploadDocument',
            value: function onUploadDocument(cmd) {
                var fs = new FileSystemFunctions(this);
                var theme = this.parentExtension.cmsTheme;

                fs.uploadDocument(this.parentExtension.customData['assetExtensionList'], 'onAssetUpload', cmd, {
                    theme: theme
                });
            }
        }, {
            key: 'onOpenAsset',
            value: function onOpenAsset(cmd, payload) {
                window.open(cmd.userData.url);
            }
        }, {
            key: 'onNavigatorNodesUpdated',
            value: function onNavigatorNodesUpdated(cmd) {
                this.cachedAssetList = null;
            }
        }, {
            key: 'documentType',
            get: function get() {
                return 'cms-asset';
            }
        }, {
            key: 'vueEditorComponentName',
            get: function get() {
                return 'cms-editor-component-asset-editor';
            }
        }]);
        return DocumentControllerAsset;
    }(DocumentControllerBase);

    return DocumentControllerAsset;
});
