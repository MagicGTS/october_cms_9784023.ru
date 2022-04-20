$.oc.module.register('editor.extension.filesystemfunctions', function () {
    'use strict';

    var onCreateDirectoryConfirmed = function () {
        var _ref = babelHelpers.asyncToGenerator( /*#__PURE__*/regeneratorRuntime.mark(function _callee(handlerName, name, parent, payload, metadataExtraData, documentController) {
            return regeneratorRuntime.wrap(function _callee$(_context) {
                while (1) {
                    switch (_context.prev = _context.next) {
                        case 0:
                            metadataExtraData = metadataExtraData || {};

                            _context.prev = 1;
                            _context.next = 4;
                            return $.oc.editor.application.ajaxRequest('onCommand', {
                                extension: documentController.editorNamespace,
                                command: handlerName,
                                documentData: { name: name, parent: parent },
                                documentMetadata: metadataExtraData
                            });

                        case 4:

                            documentController.editorStore.refreshExtensionNavigatorNodes(documentController.editorNamespace, documentController.documentType).then(function () {
                                payload.treeNode.expand();
                            });
                            _context.next = 11;
                            break;

                        case 7:
                            _context.prev = 7;
                            _context.t0 = _context['catch'](1);

                            $.oc.editor.page.showAjaxErrorAlert(_context.t0, documentController.trans('editor::lang.common.error'));
                            return _context.abrupt('return', false);

                        case 11:
                        case 'end':
                            return _context.stop();
                    }
                }
            }, _callee, this, [[1, 7]]);
        }));

        return function onCreateDirectoryConfirmed(_x, _x2, _x3, _x4, _x5, _x6) {
            return _ref.apply(this, arguments);
        };
    }();

    var onRenameConfirmed = function () {
        var _ref2 = babelHelpers.asyncToGenerator( /*#__PURE__*/regeneratorRuntime.mark(function _callee2(handlerName, name, originalPath, payload, metadataExtraData, documentController) {
            return regeneratorRuntime.wrap(function _callee2$(_context2) {
                while (1) {
                    switch (_context2.prev = _context2.next) {
                        case 0:
                            metadataExtraData = metadataExtraData || {};

                            _context2.prev = 1;
                            _context2.next = 4;
                            return $.oc.editor.application.ajaxRequest('onCommand', {
                                extension: documentController.editorNamespace,
                                command: handlerName,
                                documentData: { name: name, originalPath: originalPath },
                                documentMetadata: metadataExtraData
                            });

                        case 4:

                            documentController.editorStore.refreshExtensionNavigatorNodes(documentController.editorNamespace, documentController.documentType);
                            _context2.next = 11;
                            break;

                        case 7:
                            _context2.prev = 7;
                            _context2.t0 = _context2['catch'](1);

                            $.oc.editor.page.showAjaxErrorAlert(_context2.t0, documentController.trans('editor::lang.common.error'));
                            return _context2.abrupt('return', false);

                        case 11:
                        case 'end':
                            return _context2.stop();
                    }
                }
            }, _callee2, this, [[1, 7]]);
        }));

        return function onRenameConfirmed(_x7, _x8, _x9, _x10, _x11, _x12) {
            return _ref2.apply(this, arguments);
        };
    }();

    var onFilesSelected = function () {
        var _ref3 = babelHelpers.asyncToGenerator( /*#__PURE__*/regeneratorRuntime.mark(function _callee3(handlerName, input, path, documentController, requestExtraData) {
            var uploaderUtils, extraData;
            return regeneratorRuntime.wrap(function _callee3$(_context3) {
                while (1) {
                    switch (_context3.prev = _context3.next) {
                        case 0:
                            uploaderUtils = $.oc.module.import('backend.vuecomponents.uploader.utils');

                            requestExtraData = requestExtraData || {};

                            _context3.prev = 2;
                            extraData = {
                                extension: documentController.editorNamespace,
                                command: handlerName,
                                destination: path
                            };


                            $.extend(extraData, requestExtraData);

                            _context3.next = 7;
                            return uploaderUtils.uploadFile('onCommand', input.get(0).files, 'file', extraData);

                        case 7:
                            _context3.next = 11;
                            break;

                        case 9:
                            _context3.prev = 9;
                            _context3.t0 = _context3['catch'](2);

                        case 11:

                            documentController.editorStore.refreshExtensionNavigatorNodes(documentController.editorNamespace, documentController.documentType);
                            input.remove();

                        case 13:
                        case 'end':
                            return _context3.stop();
                    }
                }
            }, _callee3, this, [[2, 9]]);
        }));

        return function onFilesSelected(_x13, _x14, _x15, _x16, _x17) {
            return _ref3.apply(this, arguments);
        };
    }();

    var FileSystemFunctions = function () {
        function FileSystemFunctions(documentController) {
            babelHelpers.classCallCheck(this, FileSystemFunctions);

            this.documentController = documentController;
        }

        babelHelpers.createClass(FileSystemFunctions, [{
            key: 'createDirectoryFromNavigatorMenu',
            value: function createDirectoryFromNavigatorMenu(handlerName, cmd, payload, metadataExtraData) {
                var _this = this;

                var inspectorConfiguration = this.documentController.editorStore.getGlobalInspectorConfiguration('dir-create');
                var data = {
                    name: ''
                };
                var parent = cmd.hasParameter ? cmd.parameter : '';

                $.oc.vueComponentHelpers.inspector.host.showModal(inspectorConfiguration.title, data, inspectorConfiguration.config, 'directory-name', {
                    beforeApplyCallback: function beforeApplyCallback(updatedData) {
                        return onCreateDirectoryConfirmed(handlerName, updatedData.name, parent, payload, metadataExtraData, _this.documentController);
                    }
                }).then($.noop, $.noop);
            }
        }, {
            key: 'renameFileOrDirectoryFromNavigatorMenu',
            value: function renameFileOrDirectoryFromNavigatorMenu(handlerName, cmd, payload, metadataExtraData) {
                var _this2 = this;

                var inspectorConfiguration = this.documentController.editorStore.getGlobalInspectorConfiguration('file-dir-rename');
                var data = {
                    name: cmd.userData.fileName
                };
                var originalPath = cmd.hasParameter ? cmd.parameter : '';

                $.oc.vueComponentHelpers.inspector.host.showModal(inspectorConfiguration.title, data, inspectorConfiguration.config, 'file-dir-rename', {
                    beforeApplyCallback: function beforeApplyCallback(updatedData) {
                        return onRenameConfirmed(handlerName, updatedData.name, originalPath, payload, metadataExtraData, _this2.documentController);
                    }
                }).then($.noop, $.noop);
            }
        }, {
            key: 'deleteFileOrDirectoryFromNavigatorMenu',
            value: function () {
                var _ref4 = babelHelpers.asyncToGenerator( /*#__PURE__*/regeneratorRuntime.mark(function _callee4(handlerName, cmd, payload, metadataExtraData) {
                    var _this3 = this;

                    var itemsDetails, files, deletedUris, message;
                    return regeneratorRuntime.wrap(function _callee4$(_context4) {
                        while (1) {
                            switch (_context4.prev = _context4.next) {
                                case 0:
                                    metadataExtraData = metadataExtraData || {};

                                    itemsDetails = cmd.userData.itemsDetails;
                                    files = [];
                                    deletedUris = [];


                                    if (!itemsDetails.clickedIsSelected) {
                                        files.push(itemsDetails.clickedNode.userData.path);
                                        deletedUris.push(itemsDetails.clickedNode.uniqueKey);
                                    } else {
                                        itemsDetails.selectedNodes.forEach(function (selectedNode) {
                                            files.push(selectedNode.nodeData.userData.path);
                                            deletedUris.push(selectedNode.nodeData.uniqueKey);
                                        });
                                    }

                                    message = files.length > 1 ? $.oc.editor.getLangStr('editor::lang.filesystem.delete_confirm') : $.oc.editor.getLangStr('editor::lang.filesystem.delete_confirm_single');
                                    _context4.prev = 6;
                                    _context4.next = 9;
                                    return $.oc.vueComponentHelpers.modalUtils.showConfirm($.oc.editor.getLangStr('backend::lang.form.delete'), message, {
                                        isDanger: true,
                                        buttonText: $.oc.editor.getLangStr('backend::lang.form.confirm')
                                    });

                                case 9:
                                    _context4.next = 14;
                                    break;

                                case 11:
                                    _context4.prev = 11;
                                    _context4.t0 = _context4['catch'](6);
                                    return _context4.abrupt('return');

                                case 14:
                                    _context4.prev = 14;
                                    _context4.next = 17;
                                    return $.oc.editor.application.ajaxRequest('onCommand', {
                                        extension: this.documentController.editorNamespace,
                                        command: handlerName,
                                        documentData: {
                                            files: files
                                        },
                                        documentMetadata: metadataExtraData
                                    });

                                case 17:

                                    deletedUris.forEach(function (deletedUri) {
                                        _this3.documentController.editorStore.deleteNavigatorNode(deletedUri);
                                        _this3.documentController.editorStore.tabManager.closeTabByKey(deletedUri);
                                    });
                                    _context4.next = 24;
                                    break;

                                case 20:
                                    _context4.prev = 20;
                                    _context4.t1 = _context4['catch'](14);

                                    $.oc.editor.page.showAjaxErrorAlert(_context4.t1, this.documentController.trans('editor::lang.common.error'));
                                    this.documentController.editorStore.refreshExtensionNavigatorNodes(this.documentController.editorNamespace);

                                case 24:
                                case 'end':
                                    return _context4.stop();
                            }
                        }
                    }, _callee4, this, [[6, 11], [14, 20]]);
                }));

                function deleteFileOrDirectoryFromNavigatorMenu(_x18, _x19, _x20, _x21) {
                    return _ref4.apply(this, arguments);
                }

                return deleteFileOrDirectoryFromNavigatorMenu;
            }()
        }, {
            key: 'handleNavigatorNodeMove',
            value: function () {
                var _ref5 = babelHelpers.asyncToGenerator( /*#__PURE__*/regeneratorRuntime.mark(function _callee5(handlerName, cmd, metadataExtraData) {
                    var movingMessageId, movedNodePaths;
                    return regeneratorRuntime.wrap(function _callee5$(_context5) {
                        while (1) {
                            switch (_context5.prev = _context5.next) {
                                case 0:
                                    metadataExtraData = metadataExtraData || {};
                                    cmd.userData.event.preventDefault();

                                    $.oc.editor.application.setNavigatorReadonly(true);
                                    movingMessageId = $.oc.snackbar.show(this.documentController.trans('editor::lang.filesystem.moving'), {
                                        timeout: 5000
                                    });
                                    movedNodePaths = [];


                                    cmd.userData.movedNodes.map(function (movedNode) {
                                        movedNodePaths.push(movedNode.nodeData.userData.path);
                                    });

                                    _context5.prev = 6;
                                    _context5.next = 9;
                                    return $.oc.editor.application.ajaxRequest('onCommand', {
                                        extension: this.documentController.editorNamespace,
                                        command: handlerName,
                                        documentData: {
                                            source: movedNodePaths,
                                            destination: cmd.userData.movedToNodeData.userData.path
                                        },
                                        documentMetadata: metadataExtraData
                                    });

                                case 9:
                                    _context5.next = 11;
                                    return this.documentController.editorStore.refreshExtensionNavigatorNodes(this.documentController.editorNamespace, this.documentController.documentType);

                                case 11:
                                    $.oc.snackbar.show(this.documentController.trans('editor::lang.filesystem.moved'), { replace: movingMessageId });
                                    $.oc.editor.application.setNavigatorReadonly(false);
                                    _context5.next = 22;
                                    break;

                                case 15:
                                    _context5.prev = 15;
                                    _context5.t0 = _context5['catch'](6);
                                    _context5.next = 19;
                                    return this.documentController.editorStore.refreshExtensionNavigatorNodes(this.documentController.editorNamespace, this.documentController.documentType);

                                case 19:
                                    $.oc.editor.application.setNavigatorReadonly(false);
                                    $.oc.snackbar.hide(movingMessageId);
                                    $.oc.editor.page.showAjaxErrorAlert(_context5.t0, this.documentController.trans('editor::lang.common.error'));

                                case 22:
                                case 'end':
                                    return _context5.stop();
                            }
                        }
                    }, _callee5, this, [[6, 15]]);
                }));

                function handleNavigatorNodeMove(_x22, _x23, _x24) {
                    return _ref5.apply(this, arguments);
                }

                return handleNavigatorNodeMove;
            }()
        }, {
            key: 'uploadDocument',
            value: function uploadDocument(allowedExtensions, handlerName, cmd, requestExtraData) {
                var _this4 = this;

                var input = $('<input type="file" style="display:none" name="file" multiple/>');
                input.attr('accept', allowedExtensions);

                $(document.body).append(input);

                input.one('change', function () {
                    onFilesSelected(handlerName, input, cmd.userData.path ? cmd.userData.path : '/', _this4.documentController, requestExtraData);
                });

                input.click();
            }
        }, {
            key: 'handleNavigatorExternalDrop',
            value: function handleNavigatorExternalDrop(handlerName, cmd, requestExtraData) {
                var _this5 = this;

                var uploaderUtils = $.oc.module.import('backend.vuecomponents.uploader.utils');
                var dataTransfer = cmd.userData.ev.dataTransfer;
                requestExtraData = requestExtraData || {};

                if (!dataTransfer || !dataTransfer.files || !dataTransfer.files.length) {
                    return;
                }

                var targetNodeData = cmd.userData.nodeData;
                var extraData = {
                    extension: this.documentController.editorNamespace,
                    command: handlerName,
                    destination: targetNodeData.userData.path
                };

                $.extend(extraData, requestExtraData);

                uploaderUtils.uploadFile('onCommand', dataTransfer.files, 'file', extraData).then(function () {
                    _this5.documentController.editorStore.refreshExtensionNavigatorNodes(_this5.documentController.editorNamespace, _this5.documentController.documentType);
                }, function () {
                    _this5.documentController.editorStore.refreshExtensionNavigatorNodes(_this5.documentController.editorNamespace, _this5.documentController.documentType);
                });
            }
        }]);
        return FileSystemFunctions;
    }();

    ;

    return FileSystemFunctions;
});
