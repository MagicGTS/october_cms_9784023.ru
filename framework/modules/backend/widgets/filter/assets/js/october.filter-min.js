+function($){"use strict";var Base=$.oc.foundation.base,BaseProto=Base.prototype,FilterWidget=function(element,options){this.$el=$(element),this.options=options||{},this.popoverContent={},this.$activeScope=null,$.oc.foundation.controlUtils.markDisposable(element),Base.call(this),this.init()};FilterWidget.prototype=Object.create(BaseProto),FilterWidget.prototype.constructor=FilterWidget,FilterWidget.prototype.init=function(){this.$el.on("change",'.filter-scope input[type="checkbox"]',this.proxy(this.onToggleCheckbox)),this.$el.on("change",".filter-scope select",this.proxy(this.onToggleDropdown)),this.$el.on("click","a.filter-scope",this.proxy(this.onClickScopePopover)),this.$el.on("hide.oc.popover","a.filter-scope",this.proxy(this.onHideScopePopover)),this.bindCheckboxes(),this.preloadContent()},FilterWidget.prototype.dispose=function(){this.$el.off("change",'.filter-scope input[type="checkbox"]',this.proxy(this.onToggleCheckbox)),this.$el.off("click","a.filter-scope",this.proxy(this.onClickScopePopover)),this.$el.off("hide.oc.popover","a.filter-scope",this.proxy(this.onHideScopePopover)),this.$el.off("dispose-control",this.proxy(this.dispose)),this.$el.removeData("oc.filterwidget"),this.$el=null,this.options=null,BaseProto.dispose.call(this)},FilterWidget.prototype.preloadContent=function(){try{var self=this;this.$el.request(this.options.updateHandler,{data:{preload:!0},success:function(data){self.popoverContent=data.popoverContent},error:function(){}})}catch(e){}},FilterWidget.prototype.initContainer=function(el){$(el).on("click",'[data-filter-action="apply"]',this.proxy(this.onClickScopeApply)),$(el).on("click",'[data-filter-action="clear"]',this.proxy(this.onClickScopeClear))},FilterWidget.prototype.disposeContainer=function(el){$(el).off("click",'[data-filter-action="apply"]',this.proxy(this.onClickScopeApply)),$(el).off("click",'[data-filter-action="clear"]',this.proxy(this.onClickScopeClear))},FilterWidget.prototype.onClickScopePopover=function(ev){var $el=$(ev.target),$scope=$el.closest(".filter-scope");$scope.data("scope-name");$scope.hasClass("filter-scope-open")||($scope.addClass("filter-scope-open"),this.$activeScope&&this.hidePopover(this.$activeScope),this.$activeScope=$scope,this.showPopover($scope))},FilterWidget.prototype.onClickScopeApply=function(ev){ev.preventDefault();var $el=$(ev.target),$form=$el.closest("form");this.submitUpdate($form,this.$activeScope),this.hidePopover(this.$activeScope)},FilterWidget.prototype.onClickScopeClear=function(ev){ev.preventDefault();var $el=$(ev.target),$form=$el.closest("form");this.submitUpdate($form,this.$activeScope,{clearScope:!0}),this.hidePopover(this.$activeScope)},FilterWidget.prototype.onHideScopePopover=function(ev){var $el=$(ev.target),$scope=$el.closest(".filter-scope");$scope.data("scope-name");this.$activeScope=null,setTimeout(function(){$scope.removeClass("filter-scope-open")},200)},FilterWidget.prototype.hidePopover=function($scope){var scopeName=$scope.data("scope-name");this.popoverContent[scopeName]=null,$scope.ocPopover("hide")},FilterWidget.prototype.showPopover=function($scope){var self=this,scopeName=$scope.data("scope-name"),container=!1,modalParent=$scope.parents(".modal-dialog");modalParent.length>0&&(container=modalParent[0]);var data={scopeName:scopeName};$scope.data("oc.popover",null),$scope.ocPopover({content:Mustache.render(self.getPopoverTemplate(),data),modal:!1,highlightModalTarget:!0,closeOnPageClick:!0,placement:"bottom",container:container,onCheckDocumentClickTarget:function(target){return self.onCheckDocumentClickTargetDatePicker(target)}});var $container=$scope.ocPopover("getContainer"),$form=$("form:first",$container);this.popoverContent[scopeName]?(self.setPopoverContent($container,this.popoverContent[scopeName]),$(document).trigger("render")):$form.request(this.options.loadHandler,{success:function(data){this.success(data),self.setPopoverContent($container,data.result),self.popoverContent[scopeName]=data.result}}),this.initContainer($container)},FilterWidget.prototype.setPopoverContent=function($container,html){$(".control-filter-popover",$container).html(html)},FilterWidget.prototype.onToggleDropdown=function(ev){var $el=$(ev.target),$scope=$el.closest(".filter-scope");this.$activeScope&&this.hidePopover(this.$activeScope),this.submitUpdate(this.$el,$scope,{value:$el.val()})},FilterWidget.prototype.bindCheckboxes=function(){$('.filter-scope input[type="checkbox"]',this.$el).each(function(){$(this).closest(".filter-scope").toggleClass("active",$(this).is(":checked"))})},FilterWidget.prototype.onToggleCheckbox=function(ev){var $el=$(ev.target),$scope=$el.closest(".filter-scope");this.$activeScope&&this.hidePopover(this.$activeScope),$scope.hasClass("is-indeterminate")?this.switchToggle($el):this.checkboxToggle($el)},FilterWidget.prototype.checkboxToggle=function($el){var isChecked=$el.is(":checked"),$scope=$el.closest(".filter-scope");this.submitUpdate(this.$el,$scope,{value:isChecked}),$scope.toggleClass("active",isChecked)},FilterWidget.prototype.switchToggle=function($el){var switchValue=$el.data("checked"),$scope=$el.closest(".filter-scope");this.submitUpdate(this.$el,$scope,{value:switchValue}),$scope.toggleClass("active",!!switchValue)},FilterWidget.prototype.submitUpdate=function($el,$scope,data){if(this.options.updateHandler){var self=this,scopeName=$scope.data("scope-name");$.oc.stripeLoadIndicator.show(),data||(data={}),data.scopeName=scopeName,$el.request(this.options.updateHandler,{data:data}).always(function(){$.oc.stripeLoadIndicator.hide()}).done(function(data){self.$el.find('[data-scope-name="'+scopeName+'"]').trigger("change.oc.filterScope")})}},FilterWidget.prototype.updatePopoverContent=function(content){var self=this;$.each(content,function(key,val){self.popoverContent[key]=val})},FilterWidget.prototype.getPopoverTemplate=function(){return $(this.options.popoverTemplate).html()},FilterWidget.prototype.onCheckDocumentClickTargetDatePicker=function(target){var $target=$(target);return $target.hasClass("pika-next")||$target.hasClass("pika-prev")||$target.hasClass("pika-select")||$target.hasClass("pika-button")||$target.parents(".pika-table").length||$target.parents(".pika-title").length},FilterWidget.DEFAULTS={popoverTemplate:null,optionsHandler:null,updateHandler:null,loadHandler:null};var old=$.fn.filterWidget;$.fn.filterWidget=function(option){var result;return this.each(function(){var $this=$(this),data=$this.data("oc.filterwidget"),options=$.extend({},FilterWidget.DEFAULTS,$this.data(),"object"==typeof option&&option);if(data||$this.data("oc.filterwidget",data=new FilterWidget(this,options)),"string"==typeof option&&(result=data[option].call($this)),void 0!==result)return!1}),result||this},$.fn.filterWidget.Constructor=FilterWidget,$.fn.filterWidget.noConflict=function(){return $.fn.filterWidget=old,this},$(document).render(function(){$('[data-control="filterwidget"]').filterWidget()})}(window.jQuery);