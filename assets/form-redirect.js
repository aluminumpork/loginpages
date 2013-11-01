;(function($, window, document, undefined){
  var pluginName = 'formRedirect',
      defaults = {
        'useTimer': true,
        'redirectTimer': 4000
      };
  
  function Plugin(element, options){
    this.element = element;
    this.options = $.extend({}, defaults, options);
    this._defaults = defaults;
    this._name = pluginName;
    
    this.init();
  }
  
  Plugin.prototype.init = function(){
    this.$element = $(this.element);
    this.addFormInputs();

    if(this.options.useTimer){
      this.setRedirectTimer();
    }
  }
  
  Plugin.prototype.setRedirectTimer = function(){
    this.timer = setTimeout((function(scope){
      var _scope = scope;
      function closure(){
        _scope.submitForm();
      }
      
      return closure;
    })(this), this.options['redirectTimer']);
        
  };
  
  Plugin.prototype.submitForm = function(){
    this.$element.submit();
  };
  
  Plugin.prototype.addFormInputs = function(){
    var $el, _this = this;
    $.each(this.options['params'], function(k, v){
      if(!$("input[name='" + k + "']", _this.$element).length){
        $el = $(document.createElement('input'));
        $el.attr('type','hidden');
        $el.attr('name', k);
        $el.val(v);
        
        _this.$element.append($el);
      }
    });
  };
  
  $.fn[pluginName] = function(options){
    return this.each(function(){
      new Plugin(this, options);
    });
  }
})(jQuery, window, document);


    