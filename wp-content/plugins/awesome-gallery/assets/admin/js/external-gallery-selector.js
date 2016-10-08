var $, ExternalGalleryFrame, ExternalGallerySelector, ExternalGalleryState, GalleriesModel, GalleriesView, GalleryImageView, ImageModel,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
  __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

window.asg || (window.asg = {});

$ = jQuery;

ExternalGallerySelector = (function() {
  function ExternalGallerySelector() {
    this.select = __bind(this.select, this);
  }

  ExternalGallerySelector.prototype.select = function(options) {
    var state;
    state = new ExternalGalleryState({
      title: options.title
    });
    state.set('selected', options.value);
    options = _.defaults({
      state: 'external-gallery',
      states: [state]
    }, options);
    this.frame = new ExternalGalleryFrame(options);
    this.frame.open();
    this.deferred = new $.Deferred();
    state.on('change:selected', (function(_this) {
      return function() {
        return _this.deferred.resolveWith(_this, [state.get('selected')]);
      };
    })(this));
    return this.deferred.promise();
  };

  return ExternalGallerySelector;

})();

ExternalGalleryState = (function(_super) {
  __extends(ExternalGalleryState, _super);

  function ExternalGalleryState() {
    this.initialize = __bind(this.initialize, this);
    return ExternalGalleryState.__super__.constructor.apply(this, arguments);
  }

  ExternalGalleryState.prototype.defaults = {
    id: 'external-gallery',
    menu: 'default',
    title: 'wea',
    toolbar: true,
    router: null,
    content: true
  };

  ExternalGalleryState.prototype.initialize = function() {
    return ExternalGalleryState.__super__.initialize.apply(this, arguments);
  };

  return ExternalGalleryState;

})(wp.media.controller.State);

GalleriesModel = (function(_super) {
  __extends(GalleriesModel, _super);

  function GalleriesModel() {
    this.fetchGalleries = __bind(this.fetchGalleries, this);
    return GalleriesModel.__super__.constructor.apply(this, arguments);
  }

  GalleriesModel.prototype.fetchGalleries = function() {
    var promise;
    promise = wp.ajax.post(this.attributes.ajax_action, {
      data: this.attributes.ajax_data
    });
    promise.done((function(_this) {
      return function(response) {
        if (response.length > 0) {
          _this.set('images', response);
          return _this.trigger('load');
        } else {
          return _this.trigger('none');
        }
      };
    })(this));
    return promise.fail((function(_this) {
      return function(data) {
        if (console) {
          return console.info(data);
        }
      };
    })(this));
  };

  return GalleriesModel;

})(Backbone.Model);

GalleriesView = (function(_super) {
  __extends(GalleriesView, _super);

  function GalleriesView() {
    this.modelSelected = __bind(this.modelSelected, this);
    this.showNone = __bind(this.showNone, this);
    this.buildTheList = __bind(this.buildTheList, this);
    this.initialize = __bind(this.initialize, this);
    return GalleriesView.__super__.constructor.apply(this, arguments);
  }

  GalleriesView.prototype.tagName = 'ul';

  GalleriesView.prototype.id = 'select-galleries';

  GalleriesView.prototype.initialize = function() {
    GalleriesView.__super__.initialize.apply(this, arguments);
    this.model = new GalleriesModel({
      ajax_action: this.options.ajax_action,
      selected: this.options.value,
      ajax_data: this.options.ajax_data
    });
    this.model.on('load', this.buildTheList);
    this.model.on('none', this.showNone);
    return this.model.fetchGalleries();
  };

  GalleriesView.prototype.buildTheList = function() {
    var image, view, _i, _len, _ref, _results;
    _ref = this.model.get('images');
    _results = [];
    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
      image = _ref[_i];
      view = new GalleryImageView({
        model: new ImageModel(image)
      });
      this.views.add(view);
      view.model.on('change:selected', this.modelSelected);
      view.on('close', (function(_this) {
        return function() {
          return _this.trigger('close');
        };
      })(this));
      if (view.model.get('id') === this.model.get('selected')) {
        view.model.set('selected', true);
        _results.push($('.media-frame-content').scrollTop($(view.el).offset().top));
      } else {
        _results.push(void 0);
      }
    }
    return _results;
  };

  GalleriesView.prototype.showNone = function() {
    return $(this.el).append($('<h3 class="asg-no-galleries">No items found</h3>'));
  };

  GalleriesView.prototype.modelSelected = function(image) {
    var view, _i, _len, _ref, _results;
    if (image.get('selected')) {
      this.model.set('selected', image);
      _ref = this.views.get();
      _results = [];
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        view = _ref[_i];
        if (view.model !== image) {
          _results.push(view.model.set('selected', false));
        } else {
          _results.push(void 0);
        }
      }
      return _results;
    }
  };

  return GalleriesView;

})(wp.media.View);

ImageModel = (function(_super) {
  __extends(ImageModel, _super);

  function ImageModel() {
    this.getData = __bind(this.getData, this);
    this.initialize = __bind(this.initialize, this);
    return ImageModel.__super__.constructor.apply(this, arguments);
  }

  ImageModel.prototype.initialize = function() {
    return ImageModel.__super__.initialize.apply(this, arguments);
  };

  ImageModel.prototype.getData = function() {
    return {
      buttons: {
        check: true,
        close: false
      },
      can: {
        save: true
      },
      type: 'image',
      size: {
        url: this.get('cover')
      },
      describe: true,
      image: {
        src: this.get('cover')
      },
      caption: this.get('title')
    };
  };

  return ImageModel;

})(Backbone.Model);

GalleryImageView = (function(_super) {
  __extends(GalleryImageView, _super);

  function GalleryImageView() {
    this.forceSelect = __bind(this.forceSelect, this);
    this.select = __bind(this.select, this);
    this.unselect = __bind(this.unselect, this);
    this.render = __bind(this.render, this);
    this.initialize = __bind(this.initialize, this);
    return GalleryImageView.__super__.constructor.apply(this, arguments);
  }

  GalleryImageView.prototype.tagName = 'li';

  GalleryImageView.prototype.className = 'attachment';

  GalleryImageView.prototype.template = wp.media.template('attachment');

  GalleryImageView.prototype.events = {
    'click .attachment-preview': 'select',
    'dblclick .attachment-preview': 'forceSelect'
  };

  GalleryImageView.prototype.initialize = function() {
    GalleryImageView.__super__.initialize.apply(this, arguments);
    return this.model.on('change:selected', (function(_this) {
      return function(value) {
        if (value.get('selected')) {
          return _this.select();
        } else {
          return _this.unselect();
        }
      };
    })(this));
  };

  GalleryImageView.prototype.render = function() {
    var html;
    html = this.template(this.model.getData());
    return this.$el.html(html);
  };

  GalleryImageView.prototype.unselect = function() {
    return this.$el.removeClass('selected');
  };

  GalleryImageView.prototype.select = function() {
    this.$el.addClass('selected');
    if (!this.model.get('selected')) {
      return this.model.set('selected', true);
    }
  };

  GalleryImageView.prototype.forceSelect = function() {
    this.select();
    return this.trigger('close');
  };

  return GalleryImageView;

})(wp.media.View);

ExternalGalleryFrame = (function(_super) {
  var Toolbar;

  __extends(ExternalGalleryFrame, _super);

  function ExternalGalleryFrame() {
    this.commitAndClose = __bind(this.commitAndClose, this);
    this.initialize = __bind(this.initialize, this);
    return ExternalGalleryFrame.__super__.constructor.apply(this, arguments);
  }

  Toolbar = (function(_super1) {
    __extends(Toolbar, _super1);

    function Toolbar() {
      this.clickSelect = __bind(this.clickSelect, this);
      this.initialize = __bind(this.initialize, this);
      return Toolbar.__super__.constructor.apply(this, arguments);
    }

    Toolbar.prototype.initialize = function() {
      this.options.items = _.defaults(this.options.items || {}, {
        select: {
          style: 'primary',
          text: this.options.text,
          priority: 80,
          click: this.clickSelect,
          requires: this.options.requires
        }
      });
      return Toolbar.__super__.initialize.apply(this, arguments);
    };

    Toolbar.prototype.clickSelect = function() {
      return this.trigger('selected');
    };

    return Toolbar;

  })(wp.media.view.Toolbar);

  ExternalGalleryFrame.prototype.initialize = function() {
    _.defaults(this.options, {
      modal: true,
      uploader: false
    });
    ExternalGalleryFrame.__super__.initialize.apply(this, arguments);
    this.on('toolbar:create', (function(_this) {
      return function(t) {
        t.view = new Toolbar({
          controller: _this,
          text: _this.options.title
        });
        return t.view.on('selected', _this.commitAndClose);
      };
    })(this));
    this.on('content:create', (function(_this) {
      return function(t) {
        t.view = new GalleriesView({
          controller: _this,
          ajax_action: _this.options.ajax_action,
          ajax_data: _this.options.ajax_data,
          value: _this.options.value
        });
        t.view.model.on('change:selected', function(test) {
          return _this.state().set('selectedInFrame', test.get('selected'));
        });
        return t.view.on('close', function() {
          return _this.close();
        });
      };
    })(this));
    return this.on('close', this.commitAndClose);
  };

  ExternalGalleryFrame.prototype.commitAndClose = function() {
    if (this.state().get('selectedInFrame')) {
      this.state().set('selected', this.state().get('selectedInFrame'));
    }
    return this.close();
  };

  return ExternalGalleryFrame;

})(wp.media.view.MediaFrame);

window.asg.ExternalGallerySelector = ExternalGallerySelector;
