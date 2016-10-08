var $, SettingsView, SourceEditor,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
  __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

SourceEditor = (function() {
  function SourceEditor($editor) {
    this.hide = __bind(this.hide, this);
    this.show = __bind(this.show, this);
    this.editor = $editor;
  }

  SourceEditor.prototype.show = function() {
    jQuery.each(window.asgSourceEditors, function(name, editor) {
      return editor.hide();
    });
    return this.editor.show();
  };

  SourceEditor.prototype.hide = function() {
    return this.editor.hide();
  };

  return SourceEditor;

})();

window.asgSourceEditors = {};

window.asgRegisteredSourceEditors = {};

window.asgSourceEditor = SourceEditor;

SettingsView = (function(_super) {
  __extends(SettingsView, _super);

  function SettingsView() {
    this.preview = __bind(this.preview, this);
    this.refreshLightboxVisibility = __bind(this.refreshLightboxVisibility, this);
    this.initialize = __bind(this.initialize, this);
    return SettingsView.__super__.constructor.apply(this, arguments);
  }

  SettingsView.prototype.events = {
    'click .button-hero': 'preview'
  };

  SettingsView.prototype.initialize = function() {
    this.$lightbox_options = this.$('.lightbox-options');
    this.listenTo(this.model, 'change:link', (function(_this) {
      return function() {
        return _this.refreshLightboxVisibility();
      };
    })(this));
    return rivets.bind(this.el, {
      model: this.model
    }).publish();
  };

  SettingsView.prototype.refreshLightboxVisibility = function() {
    if (this.model.get('link') === 'lightbox') {
      return this.$lightbox_options.show();
    } else {
      return this.$lightbox_options.hide();
    }
  };

  SettingsView.prototype.preview = function() {
    return Preview.show();
  };

  return SettingsView;

})(Backbone.View);

window.asgSettingsView = SettingsView;
