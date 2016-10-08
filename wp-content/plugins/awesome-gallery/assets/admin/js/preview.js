var $, PreviewFrame, PreviewView, Toolbar,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
  __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

$ = jQuery;

PreviewView = (function(_super) {
  __extends(PreviewView, _super);

  function PreviewView() {
    this.remove = __bind(this.remove, this);
    this.render = __bind(this.render, this);
    this.initialize = __bind(this.initialize, this);
    return PreviewView.__super__.constructor.apply(this, arguments);
  }

  PreviewView.prototype.id = 'asg-preview';

  PreviewView.prototype.initialize = function() {
    return $.post('admin-ajax.php?action=asg-preview', {
      data: $('#post').serialize()
    }, (function(_this) {
      return function(response) {
        _this.$el.html(response);
        return _this.data = _this.$('.asg').data('awesome-gallery');
      };
    })(this));
  };

  PreviewView.prototype.render = function() {
    PreviewView.__super__.render.apply(this, arguments);
    return this.$el.append($('<div class="asg-spinner-large"></div>').show());
  };

  PreviewView.prototype.remove = function() {
    if (this.data) {
      this.data.dispose();
    }
    return PreviewView.__super__.remove.apply(this, arguments);
  };

  return PreviewView;

})(wp.media.View);

Toolbar = (function(_super) {
  __extends(Toolbar, _super);

  function Toolbar() {
    this.close = __bind(this.close, this);
    this.initialize = __bind(this.initialize, this);
    return Toolbar.__super__.constructor.apply(this, arguments);
  }

  Toolbar.prototype.initialize = function() {
    this.options.items = _.defaults(this.options.items || {}, {
      select: {
        style: 'primary',
        text: 'Close',
        priority: 80,
        click: this.close,
        requires: this.options.requires
      }
    });
    return Toolbar.__super__.initialize.apply(this, arguments);
  };

  Toolbar.prototype.close = function() {
    this.controller.close();
    return false;
  };

  return Toolbar;

})(wp.media.view.Toolbar);

PreviewFrame = (function(_super) {
  __extends(PreviewFrame, _super);

  function PreviewFrame() {
    this.initialize = __bind(this.initialize, this);
    return PreviewFrame.__super__.constructor.apply(this, arguments);
  }

  PreviewFrame.prototype.initialize = function() {
    _.defaults(this.options, {
      modal: true,
      uploader: false
    });
    PreviewFrame.__super__.initialize.apply(this, arguments);
    this.on('toolbar:create', (function(_this) {
      return function(t) {
        return t.view = new Toolbar({
          controller: _this
        });
      };
    })(this));
    this.on('content:create', (function(_this) {
      return function(t) {
        return t.view = new PreviewView({
          controller: _this
        });
      };
    })(this));
    return this.on('close', (function(_this) {
      return function() {
        return _this.content.view.remove();
      };
    })(this));
  };

  return PreviewFrame;

})(wp.media.view.MediaFrame);

window.Preview = {
  show: (function(_this) {
    return function(data) {
      return new PreviewFrame({
        state: 'preview',
        states: [
          new wp.media.controller.State({
            id: 'preview',
            menu: 'default',
            toolbar: true,
            router: null,
            content: true,
            title: 'Preview'
          })
        ]
      }).open(data);
    };
  })(this)
};
