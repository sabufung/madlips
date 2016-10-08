var __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
  __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

window.asg || (window.asg = {});

rivets.configure({
  adapter: {
    subscribe: function(obj, keypath, callback) {
      callback.wrapped = function(m, v) {
        return callback(v);
      };
      return obj.on('change:' + keypath, callback.wrapped);
    },
    unsubscribe: function(obj, keypath, callback) {
      return obj.off('change:' + keypath, callback.wrapped);
    },
    read: function(obj, keypath) {
      return obj.get(keypath);
    },
    publish: function(obj, keypath, value) {
      return obj.set(keypath, value);
    }
  },
  preloadData: false
});

jQuery(function($) {
  var CaptionView, CategoriesView, LayoutOptionsView, LoadMoreView, OverlayView, SourceTabsController;
  $('#publish').removeAttr('disabled').removeClass('button-primary-disabled');
  $('#preview').click(function() {
    return Preview.show();
  });
  SourceTabsController = (function(_super) {
    __extends(SourceTabsController, _super);

    function SourceTabsController() {
      this.changeTab = __bind(this.changeTab, this);
      this.initialize = __bind(this.initialize, this);
      return SourceTabsController.__super__.constructor.apply(this, arguments);
    }

    SourceTabsController.prototype.el = $('#sources-tabs');

    SourceTabsController.prototype.events = {
      'click a.nav-tab': 'changeTab'
    };

    SourceTabsController.prototype.initialize = function() {
      var currentEditor;
      SourceTabsController.__super__.initialize.apply(this, arguments);
      return currentEditor = this.createEditor($('#current-source').val());
    };

    SourceTabsController.prototype.changeTab = function(event) {
      var slug;
      event.preventDefault();
      slug = $(event.target).attr('href').replace('#', '');
      $.each(window.asgSourceEditors, function(name, editor) {
        return editor.hide();
      });
      if (window.asgSourceEditors[slug]) {
        window.asgSourceEditors[slug].show();
      } else {
        this.createEditor(slug).show();
      }
      $('.nav-tab-wrapper a').removeClass('nav-tab-active');
      $(event.target).addClass('nav-tab-active');
      return $('#current-source').val(slug);
    };

    SourceTabsController.prototype.createEditor = function(slug) {
      return window.asgSourceEditors[slug] = new window.asgRegisteredSourceEditors[slug]($('#source-' + slug + '-settings'));
    };

    return SourceTabsController;

  })(wp.media.View);
  OverlayView = (function(_super) {
    __extends(OverlayView, _super);

    function OverlayView() {
      this.initialize = __bind(this.initialize, this);
      return OverlayView.__super__.constructor.apply(this, arguments);
    }

    OverlayView.prototype.el = $('#asg-overlay');

    OverlayView.prototype.initialize = function() {
      OverlayView.__super__.initialize.apply(this, arguments);
      return new asg.ImageSelector({
        el: $('#asg-image-overlay').find('.image-selector')
      });
    };

    return OverlayView;

  })(wp.media.View);
  LayoutOptionsView = (function(_super) {
    __extends(LayoutOptionsView, _super);

    function LayoutOptionsView() {
      this.updateVisibility = __bind(this.updateVisibility, this);
      this.initialize = __bind(this.initialize, this);
      return LayoutOptionsView.__super__.constructor.apply(this, arguments);
    }

    LayoutOptionsView.prototype.el = $('#asg-layout');

    LayoutOptionsView.prototype.events = {
      'change #asg-layout-mode': 'updateVisibility'
    };

    LayoutOptionsView.prototype.initialize = function() {
      LayoutOptionsView.__super__.initialize.apply(this, arguments);
      this.width = this.$('#asg-image-width');
      this.height = this.$('#asg-image-height');
      this.select = this.$('#asg-layout-mode');
      this.hanging = this.$('#asg-layout-hanging');
      return this.updateVisibility();
    };

    LayoutOptionsView.prototype.updateVisibility = function() {
      switch (this.select.val()) {
        case 'horizontal-flow':
          this.width.fadeOut('fast');
          this.height.fadeIn('fast');
          this.width.removeClass('last');
          return this.hanging.show();
        case 'vertical-flow':
          this.width.fadeIn('fast');
          this.height.fadeOut('fast');
          this.width.addClass('last');
          return this.hanging.hide();
        case 'usual':
          this.width.fadeIn('fast');
          this.height.fadeIn('fast');
          this.width.removeClass('last');
          return this.hanging.show();
      }
    };

    return LayoutOptionsView;

  })(wp.media.View);
  LoadMoreView = (function(_super) {
    __extends(LoadMoreView, _super);

    function LoadMoreView() {
      this.updateVisibility = __bind(this.updateVisibility, this);
      this.initialize = __bind(this.initialize, this);
      return LoadMoreView.__super__.constructor.apply(this, arguments);
    }

    LoadMoreView.prototype.el = $('#asg-load-more');

    LoadMoreView.prototype.events = {
      'change select': 'updateVisibility'
    };

    LoadMoreView.prototype.initialize = function() {
      LoadMoreView.__super__.initialize.apply(this, arguments);
      this.loadMoreMode = this.$('#asg-load-more-mode');
      this.select = this.$('select');
      this.perPage = this.$('#load-more-per-page');
      return this.updateVisibility();
    };

    LoadMoreView.prototype.updateVisibility = function() {
      if (this.select.val() === 'load-more') {
        this.loadMoreMode.fadeIn('fast');
        return this.perPage.removeClass('last');
      } else {
        this.loadMoreMode.fadeOut('fast');
        return this.perPage.addClass('last');
      }
    };

    return LoadMoreView;

  })(wp.media.View);
  CaptionView = (function(_super) {
    __extends(CaptionView, _super);

    function CaptionView() {
      this.selectStyles = __bind(this.selectStyles, this);
      this.initialize = __bind(this.initialize, this);
      return CaptionView.__super__.constructor.apply(this, arguments);
    }

    CaptionView.prototype.el = $('#asg-image-caption');

    CaptionView.prototype.events = {
      'change select[role=font]': 'selectStyles'
    };

    CaptionView.prototype.initialize = function() {
      var font, item, _i, _j, _len, _len1, _ref, _ref1;
      CaptionView.__super__.initialize.apply(this, arguments);
      _ref = window.asgGoogleFonts.items;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        font = _ref[_i];
        this.$el.find('select[role=font]').append($('<option/ >').attr('value', font.family).text(font.family));
      }
      _ref1 = this.$el.find('select[role=font]');
      for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
        item = _ref1[_j];
        item = $(item);
        if (item.attr('data-font')) {
          item.val(item.attr('data-font'));
        }
      }
      return this.selectStyles();
    };

    CaptionView.prototype.selectStyles = function(event) {
      var defaultText, font, fontFound, item, style, subject, variant, _i, _j, _k, _len, _len1, _len2, _ref, _ref1, _results;
      if (event) {
        subject = $(event.target);
      } else {
        subject = this.$el.find('select[role=font]');
      }
      _results = [];
      for (_i = 0, _len = subject.length; _i < _len; _i++) {
        item = subject[_i];
        item = $(item);
        style = item.parent().find('select[role=style]');
        defaultText = style.find('option:first-child').text();
        style.empty();
        fontFound = false;
        style.append($('<option />').attr('value', '').text(defaultText));
        _ref = window.asgGoogleFonts.items;
        for (_j = 0, _len1 = _ref.length; _j < _len1; _j++) {
          font = _ref[_j];
          if (font.family === item.val()) {
            fontFound = true;
            _ref1 = font.variants;
            for (_k = 0, _len2 = _ref1.length; _k < _len2; _k++) {
              variant = _ref1[_k];
              style.append($('<option />').attr('value', variant).text(variant));
            }
            style.val(style.attr('data-font'));
          }
        }
        if (!fontFound) {
          style.append($('<option value="regular">Regular</option>'));
          style.append($('<option value="light">Light</option>'));
          style.append($('<option value="bold">Bold</option>'));
          style.append($('<option value="italic">Italic</option>'));
          _results.push(style.find('option[value="' + style.attr('data-font') + '"]').attr('selected', 'selected'));
        } else {
          _results.push(void 0);
        }
      }
      return _results;
    };

    return CaptionView;

  })(wp.media.View);
  CategoriesView = (function(_super) {
    __extends(CategoriesView, _super);

    function CategoriesView() {
      this.onLinkClick = __bind(this.onLinkClick, this);
      this.initialize = __bind(this.initialize, this);
      return CategoriesView.__super__.constructor.apply(this, arguments);
    }

    CategoriesView.prototype.initialize = function(params) {
      CategoriesView.__super__.initialize.call(this, params);
      this.links = this.$el.find('.asg-tabs li a');
      this.panels = this.$el.find('.asg-panels li');
      this.links.on('click', this.onLinkClick);
      this.panels.eq(0).addClass('asg-current');
      return this.links.eq(0).parent().addClass('asg-current');
    };

    CategoriesView.prototype.onLinkClick = function(event) {
      var index;
      event.preventDefault();
      this.panels.removeClass('asg-current');
      index = this.links.index(event.target);
      this.panels.eq(index).addClass('asg-current');
      this.links.parent().removeClass('asg-current');
      return $(event.target).parent().addClass('asg-current');
    };

    return CategoriesView;

  })(wp.media.View);
  new SourceTabsController();
  new LayoutOptionsView();
  new LoadMoreView();
  new OverlayView();
  new CaptionView();
  new CategoriesView({
    el: $('#asg-custom-css')
  });
  new CategoriesView({
    el: $('#asg-image')
  });
  return $('#post').submit(function() {
    return $('#asg-hack').val($('#post').serialize());
  });
});
