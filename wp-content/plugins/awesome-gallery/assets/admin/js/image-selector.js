var $, ImageSelector,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
  __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

window.asg || (window.asg = {});

$ = jQuery;

ImageSelector = (function(_super) {
  __extends(ImageSelector, _super);

  function ImageSelector() {
    this.loadNewImage = __bind(this.loadNewImage, this);
    this.setImageField = __bind(this.setImageField, this);
    this.onImageDeleteClicked = __bind(this.onImageDeleteClicked, this);
    this.onImageSelectClicked = __bind(this.onImageSelectClicked, this);
    this.initialize = __bind(this.initialize, this);
    return ImageSelector.__super__.constructor.apply(this, arguments);
  }

  ImageSelector.prototype.events = {
    'click button.select-image': 'onImageSelectClicked',
    'click a.image-delete': 'onImageDeleteClicked'
  };

  ImageSelector.prototype.initialize = function() {
    ImageSelector.__super__.initialize.apply(this, arguments);
    this.$input = this.$el.find('input');
    this.$image = this.$el.find('img');
    this.$el.hover((function(_this) {
      return function() {
        if (_this.$el.find('img').size() > 0) {
          return _this.$el.find('.actions-wrapper, .overlay').fadeIn('fast');
        }
      };
    })(this), (function(_this) {
      return function() {
        if (_this.$el.find('img').size() > 0) {
          return _this.$el.find('.actions-wrapper, .overlay').fadeOut('fast');
        }
      };
    })(this));
    if (this.$el.find('img').size() > 0) {
      return this.$el.find('.actions-wrapper, .overlay').fadeOut('fast');
    }
  };

  ImageSelector.prototype.onImageSelectClicked = function(event) {
    var flow, id, selector, state;
    selector = this;
    event.preventDefault();
    id = selector.$input.val();
    flow = wp.media({
      title: "Select an image",
      library: {
        type: 'image'
      },
      button: {
        text: "Select Image"
      },
      multiple: false
    }).open();
    state = flow.state();
    if ('' !== id && -1 !== id) {
      state.get('selection').reset([wp.media.model.Attachment.get(id)]);
    }
    state.set('display', false);
    return state.on('select', function(el) {
      var selection;
      selection = this.get('selection').single();
      selector.setImageField(selection.id);
      selector.loadNewImage(selection.get('url'));
      return selector.trigger('changed:selection', selection);
    });
  };

  ImageSelector.prototype.onImageDeleteClicked = function(event) {
    event.preventDefault();
    this.$el.find('img').remove();
    this.$input.val('');
    this.$el.find('.actions-wrapper, .overlay').fadeIn('fast');
    return this.$el.find('.image-delete, .overlay').fadeOut('fast');
  };

  ImageSelector.prototype.setImageField = function(selection) {
    return this.$input.val(selection);
  };

  ImageSelector.prototype.loadNewImage = function(url) {
    var img;
    img = this.$el.find('img');
    if (img.size() === 0) {
      img = $('<img />').prependTo(this.$el);
    }
    img.attr('src', url);
    this.$el.find('.image-delete').fadeIn('fast');
    this.$el.find('.actions-wrapper, .overlay').fadeOut('fast');
    return false;
  };

  return ImageSelector;

})(wp.media.View);

window.asg.ImageSelector = ImageSelector;
