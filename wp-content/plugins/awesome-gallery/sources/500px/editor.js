var bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
  extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
  hasProp = {}.hasOwnProperty;

jQuery(function($) {
  var Px500CheckKeys, Px500CopyKeys, Px500Editor, Px500Model, Px500Settings;
  Px500Model = (function(superClass) {
    extend(Px500Model, superClass);

    function Px500Model() {
      this.validate = bind(this.validate, this);
      return Px500Model.__super__.constructor.apply(this, arguments);
    }

    Px500Model.prototype.validate = function() {
      if (!this.get('consumer_key')) {
        return 'Please enter consumer key';
      }
      return false;
    };

    return Px500Model;

  })(Backbone.Model);
  Px500CopyKeys = (function(superClass) {
    extend(Px500CopyKeys, superClass);

    function Px500CopyKeys() {
      this.initialize = bind(this.initialize, this);
      return Px500CopyKeys.__super__.constructor.apply(this, arguments);
    }

    Px500CopyKeys.prototype.el = $('#500px-copy-keys-block');

    Px500CopyKeys.prototype.initialize = function(params) {
      Px500CopyKeys.__super__.initialize.call(this, params);
      return rivets.bind(this.el, {
        model: this.model
      }).publish();
    };

    return Px500CopyKeys;

  })(Backbone.View);
  Px500CheckKeys = (function(superClass) {
    extend(Px500CheckKeys, superClass);

    function Px500CheckKeys() {
      this.checkKeys = bind(this.checkKeys, this);
      this.initialize = bind(this.initialize, this);
      return Px500CheckKeys.__super__.constructor.apply(this, arguments);
    }

    Px500CheckKeys.prototype.el = $('#500px-check-keys-block');

    Px500CheckKeys.prototype.events = {
      'click .button-hero': 'checkKeys'
    };

    Px500CheckKeys.prototype.initialize = function() {
      Px500CheckKeys.__super__.initialize.apply(this, arguments);
      return this.spinner = this.$('.spinner');
    };

    Px500CheckKeys.prototype.checkKeys = function(event) {
      event.preventDefault();
      if (!this.model.isValid()) {
        alert(this.model.validate());
        return;
      }
      this.spinner.css('display', 'inline-block');
      return $.post('admin-ajax.php?action=asg-500px-check-keys', this.model.toJSON(), (function(_this) {
        return function(response) {
          _this.spinner.css('display', 'none');
          return alert(response);
        };
      })(this));
    };

    return Px500CheckKeys;

  })(Backbone.View);
  Px500Settings = (function(superClass) {
    extend(Px500Settings, superClass);

    function Px500Settings() {
      this.selectCollection = bind(this.selectCollection, this);
      this.onCheckTokenClicked = bind(this.onCheckTokenClicked, this);
      this.onCollectionNameChanged = bind(this.onCollectionNameChanged, this);
      this.onAuthenticateClicked = bind(this.onAuthenticateClicked, this);
      this.onSourceTypeChanged = bind(this.onSourceTypeChanged, this);
      this.initialize = bind(this.initialize, this);
      return Px500Settings.__super__.constructor.apply(this, arguments);
    }

    Px500Settings.prototype.el = $('#500px-data-block');

    Px500Settings.prototype.events = {
      'click #500px-select-collection': 'selectCollection'
    };

    Px500Settings.prototype.initialize = function() {
      Px500Settings.__super__.initialize.apply(this, arguments);
      this.collection_selector = new window.asg.ExternalGallerySelector;
      this.$user_options = this.$('#500px-user-options');
      this.$user_collection = this.$('#500px-user-collection');
      this.$authenticate = this.$('#500px-oauth-authenticate');
      this.$checkToken = this.$('#500px-oauth-check-token');
      this.$sorting = this.$('#asg-500px-sorting');
      this.$category = this.$('#asg-500px-category');
      this.$authenticate.click(this.onAuthenticateClicked);
      this.$checkToken.click(this.onCheckTokenClicked);
      this.listenTo(this.model, 'change:source_type', this.onSourceTypeChanged);
      this.listenTo(this.model, 'change:collection_name', this.onCollectionNameChanged);
      return rivets.bind(this.el, {
        model: this.model
      }).publish();
    };

    Px500Settings.prototype.onSourceTypeChanged = function() {
      var ref, ref1;
      if ((ref = this.model.get('source_type')) === 'user' || ref === 'user_collection' || ref === 'user_favorites' || ref === 'user_friends') {
        this.$user_options.show();
      } else {
        this.$user_options.hide();
      }
      if ((ref1 = this.model.get('source_type')) === 'user_collection' || ref1 === 'user_favorites' || ref1 === 'user') {
        this.$user_collection.show();
        this.$sorting.hide();
        return this.$category.hide();
      } else {
        this.$user_collection.hide();
        this.$sorting.show();
        return this.$category.show();
      }
    };

    Px500Settings.prototype.onAuthenticateClicked = function(event) {
      event.preventDefault();
      return window.open("admin.php?action=asg-500px-oauth-get-token&consumer_key=" + (this.model.get('consumer_key')) + "&consumer_secret=" + (this.model.get('consumer_secret')));
    };

    Px500Settings.prototype.onCollectionNameChanged = function() {};

    Px500Settings.prototype.onCheckTokenClicked = function(event) {
      event.preventDefault();
      return $.post('admin-ajax.php', {
        action: 'asg-500px-check-token',
        data: this.model.toJSON()
      }, (function(_this) {
        return function(response) {
          return alert(response);
        };
      })(this));
    };

    Px500Settings.prototype.selectCollection = function(event) {
      event.preventDefault();
      this.collection_selector.select({
        ajax_action: 'asg-500px-get-collections',
        value: this.model.get('collection'),
        ajax_data: this.model.toJSON(),
        title: 'Select a collection'
      }).done((function(_this) {
        return function(val) {
          _this.model.set('collection', val.id);
          return _this.model.set('collection_name', val.get('title'));
        };
      })(this));
      return false;
    };

    return Px500Settings;

  })(Backbone.View);
  Px500Editor = (function(superClass) {
    extend(Px500Editor, superClass);

    function Px500Editor(view) {
      Px500Editor.__super__.constructor.call(this, view);
      this.model = new Px500Model();
      new Px500CopyKeys({
        model: this.model
      });
      new Px500CheckKeys({
        model: this.model
      });
      new Px500Settings({
        model: this.model
      });
      new asgSettingsView({
        model: this.model,
        el: $('#500px-settings-block')
      });
    }

    return Px500Editor;

  })(window.asgSourceEditor);
  return window.asgRegisteredSourceEditors['500px'] = Px500Editor;
});

// ---
// generated by coffee-script 1.9.2