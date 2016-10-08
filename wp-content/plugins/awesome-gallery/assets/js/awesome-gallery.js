/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	var jQuery;

	jQuery = __webpack_require__(1);

	if (!jQuery) {
	  alert('Message from Awesome Gallery: jQuery not found!');
	} else {
	  if (parseInt(jQuery().jquery.replace(/\./g, '')) < 172) {
	    alert('Message from Awesome Gallery: You have jQuery < 1.7.2. Please upgrade your jQuery or enable "Force new jQuery version" option at Awesome Gallery settings page.');
	  } else {
	    window.AwesomeGallery = __webpack_require__(2);
	  }
	}


/***/ },
/* 1 */
/***/ function(module, exports) {

	module.exports = window.asgjQuery || window.jQuery || window.$  || jQuery || $;

/***/ },
/* 2 */
/***/ function(module, exports, __webpack_require__) {

	var $, AwesomeGallery, GalleryFilters, GalleryImage, LayoutStrategy, Lightbox,
	  bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

	LayoutStrategy = __webpack_require__(3);

	GalleryFilters = __webpack_require__(9);

	Lightbox = __webpack_require__(10);

	GalleryImage = __webpack_require__(18);

	$ = __webpack_require__(1);

	module.exports = AwesomeGallery = (function() {
	  function AwesomeGallery(id, options) {
	    this.dispose = bind(this.dispose, this);
	    this.loadMore = bind(this.loadMore, this);
	    this.filterCallback = bind(this.filterCallback, this);
	    this.initLoadMore = bind(this.initLoadMore, this);
	    this.loadMoreActive = bind(this.loadMoreActive, this);
	    this.getVisibleImages = bind(this.getVisibleImages, this);
	    this.showWhenVisible = bind(this.showWhenVisible, this);
	    var images;
	    this.$window = $(window);
	    this.id = id;
	    this.options = options;
	    this.page = 1;
	    this.$el = $("#awesome-gallery-" + id);
	    this.$el.data('awesome-gallery', this);
	    this.$images = this.$el.find('.asg-images');
	    if ((this.$filters = this.$el.find('.asg-filters')).size() > 0) {
	      this.filters = new GalleryFilters(this.$filters, this.filterCallback);
	    }
	    this.images = this.buildImages();
	    this.layout = LayoutStrategy.create(this.$images, this.images, this.options.layout);
	    if (this.loadMoreActive()) {
	      this.initLoadMore();
	    }
	    images = this.getVisibleImages();
	    this.lightboxAdapter = Lightbox.create(this.$images, this.options.lightbox);
	    this.showWhenVisible();
	  }

	  AwesomeGallery.prototype.buildImages = function() {
	    var images;
	    images = [];
	    this.$images.find("> .asg-image").each(function(index, el) {
	      return images.push(new GalleryImage(jQuery(el)));
	    });
	    return images;
	  };

	  AwesomeGallery.prototype.showWhenVisible = function() {
	    if (!this.$el.is(':visible') || this.$el.width() < 50) {
	      return setTimeout(this.showWhenVisible, 250);
	    } else {
	      return this.layout.show(this.getVisibleImages());
	    }
	  };

	  AwesomeGallery.prototype.getVisibleImages = function() {
	    var images, index, pageSize;
	    index = 0;
	    images = this.images;
	    images = images.filter((function(_this) {
	      return function(image) {
	        return image.matchesTag(_this.currentFilter);
	      };
	    })(this));
	    if (this.loadMoreActive()) {
	      pageSize = parseInt(this.options.load_more.page_size);
	      images = images.slice(0, this.page * pageSize);
	    }
	    return images;
	  };

	  AwesomeGallery.prototype.loadMoreActive = function() {
	    return this.$el.find('.asg-bottom').size() > 0;
	  };

	  AwesomeGallery.prototype.initLoadMore = function() {
	    if ((this.$loadMore = this.$el.find('.asg-bottom .asg-load-more')).size() > 0) {
	      this.$loadMore.addClass('asg-visible');
	      return this.$loadMore.click((function(_this) {
	        return function() {
	          return _this.loadMore();
	        };
	      })(this));
	    } else {
	      return this.$window.on('scroll.asg', (function(_this) {
	        return function() {
	          if (!_this.allLoaded) {
	            if (_this.$images.height() + _this.$images.offset().top - 400 < _this.$window.scrollTop() + _this.$window.height()) {
	              return _this.loadMore();
	            }
	          }
	        };
	      })(this));
	    }
	  };

	  AwesomeGallery.prototype.filterCallback = function(filter) {
	    var visibleImages;
	    this.currentFilter = filter;
	    this.page = 1;
	    this.layout.show(visibleImages = this.getVisibleImages());
	    this.updateLoadMoreVisibility(visibleImages);
	    if (this.lightboxAdapter) {
	      return this.lightboxAdapter.reset();
	    }
	  };

	  AwesomeGallery.prototype.updateLoadMoreVisibility = function(visibleImages) {
	    var matching;
	    matching = this.images.filter((function(_this) {
	      return function(image) {
	        return image.matchesTag(_this.currentFilter);
	      };
	    })(this));
	    if (visibleImages.length < matching.length) {
	      return this.$el.find('.asg-bottom').show();
	    } else {
	      return this.$el.find('.asg-bottom').hide();
	    }
	  };

	  AwesomeGallery.prototype.loadMore = function() {
	    var visibleImages;
	    if (this.loading || this.allLoaded) {
	      return;
	    }
	    this.page += 1;
	    visibleImages = this.getVisibleImages();
	    this.layout.show(visibleImages);
	    this.updateLoadMoreVisibility(visibleImages);
	    if (this.lightboxAdapter) {
	      return this.lightboxAdapter.reset();
	    }
	  };

	  AwesomeGallery.prototype.dispose = function() {
	    this.$window.off('scroll', this.windowScrolled);
	    if (this.$loadMore) {
	      return this.$loadMore.off('click', this.loadNextPage);
	    }
	  };

	  return AwesomeGallery;

	})();


/***/ },
/* 3 */
/***/ function(module, exports, __webpack_require__) {

	var GridLayoutStrategy, HorizontalFlowLayoutStrategy, VerticalFlowLayoutStrategy;

	GridLayoutStrategy = __webpack_require__(4);

	VerticalFlowLayoutStrategy = __webpack_require__(7);

	HorizontalFlowLayoutStrategy = __webpack_require__(8);

	module.exports = {
	  create: function(wrapper, images, config) {
	    if (config.mode === "usual") {
	      return new GridLayoutStrategy(wrapper, images, config);
	    } else if (config.mode === "vertical-flow") {
	      return new VerticalFlowLayoutStrategy(wrapper, images, config);
	    } else if (config.mode === 'horizontal-flow') {
	      return new HorizontalFlowLayoutStrategy(wrapper, images, config);
	    }
	  }
	};


/***/ },
/* 4 */
/***/ function(module, exports, __webpack_require__) {

	var $, GridLayoutStrategy, LayoutStrategy, jQuery,
	  bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
	  extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
	  hasProp = {}.hasOwnProperty;

	jQuery = __webpack_require__(1);

	$ = __webpack_require__(1);

	LayoutStrategy = __webpack_require__(5);

	module.exports = GridLayoutStrategy = (function(superClass) {
	  extend(GridLayoutStrategy, superClass);

	  function GridLayoutStrategy() {
	    this.getContainerSize = bind(this.getContainerSize, this);
	    this.placeItems = bind(this.placeItems, this);
	    this.reset = bind(this.reset, this);
	    return GridLayoutStrategy.__super__.constructor.apply(this, arguments);
	  }

	  GridLayoutStrategy.prototype.reset = function() {
	    GridLayoutStrategy.__super__.reset.apply(this, arguments);
	    return this.rowHeight = Math.floor(this.options.height * this.columnWidth / this.options.width);
	  };

	  GridLayoutStrategy.prototype.placeItems = function(images) {
	    var border, col, i, image, len, results, row, x, y;
	    if (this.index === 0 && images.length < this.columns) {
	      this.columns = images.length;
	      this.columnWidth = this.options.width;
	      this.rowHeight = this.options.height;
	    }
	    border = this.options.border * 2;
	    results = [];
	    for (i = 0, len = images.length; i < len; i++) {
	      image = images[i];
	      col = this.index % this.columns;
	      row = Math.floor(this.index / this.columns);
	      x = col * (this.columnWidth + this.options.gap);
	      y = row * (this.rowHeight + this.options.gap);
	      this.placeElement(image.$el, x, y, this.columnWidth - border, Math.floor(this.rowHeight - border));
	      results.push(this.index += 1);
	    }
	    return results;
	  };

	  GridLayoutStrategy.prototype.getContainerSize = function() {
	    var size;
	    size = {};
	    size.height = Math.ceil(1.0 * this.index / this.columns) * (this.rowHeight + this.options.gap + this.options.border);
	    if (this.options.hanging === 'hide' && this.index % this.columns !== 0) {
	      size.height -= this.rowHeight + this.options.gap + this.options.border;
	    }
	    if (this.index <= this.columns) {
	      size.width = this.columnWidth * this.index + this.options.gap * this.index - 1;
	    } else {
	      size.width = this.width;
	    }
	    return size;
	  };

	  return GridLayoutStrategy;

	})(LayoutStrategy);


/***/ },
/* 5 */
/***/ function(module, exports, __webpack_require__) {

	var $, AnimationQueue, LayoutStrategy, jQuery,
	  bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

	AnimationQueue = __webpack_require__(6);

	jQuery = __webpack_require__(1);

	$ = __webpack_require__(1);

	LayoutStrategy = (function() {
	  function LayoutStrategy($el, images, options) {
	    this.getColumnWidth = bind(this.getColumnWidth, this);
	    this.getColumns = bind(this.getColumns, this);
	    this.reLayoutRequired = bind(this.reLayoutRequired, this);
	    this.layout = bind(this.layout, this);
	    this.show = bind(this.show, this);
	    this.reset = bind(this.reset, this);
	    this.reLayout = bind(this.reLayout, this);
	    this.onResized = bind(this.onResized, this);
	    this.$el = $el;
	    this.images = images;
	    $(window).smartresize(this.onResized);
	    this.checkLayoutInterval = setInterval(this.onResized, 500);
	    this.options = options;
	    this.visibleImages = [];
	    this.reset();
	    this.queue = new AnimationQueue();
	  }

	  LayoutStrategy.prototype.onResized = function() {
	    if (this.reLayoutRequired()) {
	      return this.reLayout();
	    }
	  };

	  LayoutStrategy.prototype.reLayout = function(images) {
	    if (images == null) {
	      images = null;
	    }
	    if (images) {
	      this.images = images;
	    }
	    this.reset();
	    return this.layout();
	  };

	  LayoutStrategy.prototype.reset = function() {
	    this.index = 0;
	    this.width = this.$el.parent().width();
	    this.columns = this.getColumns();
	    return this.columnWidth = this.getColumnWidth();
	  };

	  LayoutStrategy.prototype.difference = function(array1, array2) {
	    var diff, element, i, len;
	    diff = [];
	    for (i = 0, len = array1.length; i < len; i++) {
	      element = array1[i];
	      if (array2.indexOf(element) === -1) {
	        diff.push(element);
	      }
	    }
	    return diff;
	  };

	  LayoutStrategy.prototype.show = function(images) {
	    var i, image, j, len, len1, toHide, toShow;
	    if (this.visibleImages) {
	      toShow = this.difference(images, this.visibleImages);
	      toHide = this.difference(this.visibleImages, images);
	    } else {
	      toShow = this.images;
	      toHide = [];
	    }
	    for (i = 0, len = toShow.length; i < len; i++) {
	      image = toShow[i];
	      image.show();
	    }
	    for (j = 0, len1 = toHide.length; j < len1; j++) {
	      image = toHide[j];
	      image.hide();
	    }
	    this.visibleImages = images;
	    this.reset();
	    return this.layout();
	  };

	  LayoutStrategy.prototype.layout = function() {
	    var size;
	    this.queue.clear();
	    this.placeItems(this.visibleImages);
	    size = this.getContainerSize();
	    return this.$el.css({
	      width: size.width + "px",
	      height: size.height + "px"
	    });
	  };

	  LayoutStrategy.prototype.placeElement = function(el, x, y, width, height) {
	    var css;
	    css = {
	      left: x,
	      top: y
	    };
	    if (width && height) {
	      css.width = width;
	      css.height = height;
	    }
	    return this.queue.enqueue(el, css);
	  };

	  LayoutStrategy.prototype.reLayoutRequired = function() {
	    return this.$el.parent().width() !== this.width;
	  };

	  LayoutStrategy.prototype.getColumns = function() {
	    var columns, fullWidth, width;
	    width = this.width;
	    columns = Math.floor((width + this.options.gap) / (this.options.width + this.options.gap + this.options.border * 2));
	    fullWidth = columns * (this.options.width + this.options.border * 2) + (columns - 1) * this.options.gap;
	    if (width > fullWidth) {
	      columns = columns + 1;
	    }
	    if (columns === 0) {
	      columns = 1;
	    }
	    return columns;
	  };

	  LayoutStrategy.prototype.getColumnWidth = function() {
	    var columns;
	    columns = this.columns;
	    if (columns > 1) {
	      return Math.floor((this.width + this.options.gap) / columns - this.options.gap);
	    }
	    return this.width;
	  };

	  return LayoutStrategy;

	})();

	module.exports = LayoutStrategy;


/***/ },
/* 6 */
/***/ function(module, exports) {

	var AnimationQueue,
	  bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

	module.exports = AnimationQueue = (function() {
	  function AnimationQueue() {
	    this.flush = bind(this.flush, this);
	    this.clear = bind(this.clear, this);
	    this.enqueue = bind(this.enqueue, this);
	  }

	  AnimationQueue.prototype.queue = [];

	  AnimationQueue.prototype.queues = [];

	  AnimationQueue.prototype.flushInterval = null;

	  AnimationQueue.prototype.flushTimespan = 10;

	  AnimationQueue.prototype.enqueue = function($el, style) {
	    this.queue.push([$el, style]);
	    if (!this.flushInterval) {
	      return this.flushInterval = setTimeout(this.flush, this.flushTimespan);
	    }
	  };

	  AnimationQueue.prototype.clear = function() {
	    this.queue = [];
	    clearTimeout(this.flushInterval);
	    return this.flushInterval = null;
	  };

	  AnimationQueue.prototype.flush = function() {
	    var i, item;
	    i = 50;
	    while (i > 0 && this.queue.length > 0) {
	      item = this.queue.shift();
	      item[0].css(item[1]);
	      i -= 1;
	    }
	    if (this.queue.length > 0) {
	      this.flushInterval = setTimeout(this.flush, this.flushTimespan);
	      return this.flushInterval = null;
	    }
	  };

	  return AnimationQueue;

	})();


/***/ },
/* 7 */
/***/ function(module, exports, __webpack_require__) {

	var $, LayoutStrategy, VerticalFlowLayoutStrategy, jQuery,
	  bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
	  extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
	  hasProp = {}.hasOwnProperty;

	jQuery = __webpack_require__(1);

	$ = __webpack_require__(1);

	LayoutStrategy = __webpack_require__(5);

	module.exports = VerticalFlowLayoutStrategy = (function(superClass) {
	  extend(VerticalFlowLayoutStrategy, superClass);

	  function VerticalFlowLayoutStrategy() {
	    this.placeItems = bind(this.placeItems, this);
	    this.reset = bind(this.reset, this);
	    return VerticalFlowLayoutStrategy.__super__.constructor.apply(this, arguments);
	  }

	  VerticalFlowLayoutStrategy.prototype.reset = function() {
	    var col, j, ref, results;
	    VerticalFlowLayoutStrategy.__super__.reset.apply(this, arguments);
	    this.columnHeights = [];
	    this.columnImages = [];
	    results = [];
	    for (col = j = 0, ref = this.columns - 1; 0 <= ref ? j <= ref : j >= ref; col = 0 <= ref ? ++j : --j) {
	      this.columnImages[col] = [];
	      results.push(this.columnHeights[col] = 0);
	    }
	    return results;
	  };

	  VerticalFlowLayoutStrategy.prototype.placeItems = function(images) {
	    var columnIndex, columnWidth, columns, highestColumn, highestHeight, image, imageHeight, imageWidth, j, lastInHighestColumn, lastInHighestColumnHeight, len, lowestColumn, lowestHeight, newLowestHeight;
	    if (this.index === 0 && images.length < this.columns) {
	      this.columns = images.length;
	      this.columnWidth = this.options.width;
	    }
	    columns = this.columns;
	    columnWidth = this.columnWidth;
	    imageWidth = columnWidth - this.options.border * 2;
	    for (j = 0, len = images.length; j < len; j++) {
	      image = images[j];
	      imageHeight = image.naturalHeight * imageWidth * 1.0 / image.naturalWidth;
	      columnIndex = this.index % columns;
	      this.columnImages[columnIndex].push(image);
	      this.placeElement(image.$el, columnIndex * columnWidth + this.options.gap * columnIndex, this.columnHeights[columnIndex] + this.options.gap, imageWidth, imageHeight);
	      this.columnHeights[columnIndex] += imageHeight + this.options.gap + this.options.border * 2;
	      this.index += 1;
	    }
	    while (true) {
	      lowestColumn = $.inArray(Math.min.apply(null, this.columnHeights), this.columnHeights);
	      highestColumn = $.inArray(Math.max.apply(null, this.columnHeights), this.columnHeights);
	      if (lowestColumn === highestColumn) {
	        return;
	      }
	      lastInHighestColumn = this.columnImages[highestColumn].pop();
	      if (!lastInHighestColumn) {
	        return;
	      }
	      lastInHighestColumnHeight = lastInHighestColumn.naturalHeight * imageWidth / lastInHighestColumn.naturalWidth + this.options.gap + this.options.border * 2;
	      lowestHeight = this.columnHeights[lowestColumn];
	      highestHeight = this.columnHeights[highestColumn];
	      newLowestHeight = lowestHeight + lastInHighestColumnHeight;
	      if (newLowestHeight >= highestHeight) {
	        return;
	      }
	      this.columnImages[lowestColumn].push(lastInHighestColumn);
	      this.placeElement(lastInHighestColumn.$el, lowestColumn * (this.columnWidth + this.options.gap), this.columnHeights[lowestColumn] + this.options.gap);
	      this.columnHeights[highestColumn] -= lastInHighestColumnHeight;
	      this.columnHeights[lowestColumn] += lastInHighestColumnHeight;
	    }
	  };

	  VerticalFlowLayoutStrategy.prototype.getContainerSize = function() {
	    var height, i, j, ref, size;
	    height = 0;
	    size = {};
	    for (i = j = 0, ref = this.columns - 1; 0 <= ref ? j <= ref : j >= ref; i = 0 <= ref ? ++j : --j) {
	      if (this.columnHeights[i] > height) {
	        height = this.columnHeights[i];
	      }
	    }
	    if (this.index < this.columns) {
	      size.width = this.columnWidth * this.index;
	    } else {
	      size.width = this.width;
	    }
	    size.height = height + this.options.gap;
	    return size;
	  };

	  return VerticalFlowLayoutStrategy;

	})(LayoutStrategy);


/***/ },
/* 8 */
/***/ function(module, exports, __webpack_require__) {

	var $, HorizontalFlowLayoutStrategy, LayoutStrategy, jQuery,
	  bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
	  extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
	  hasProp = {}.hasOwnProperty;

	jQuery = __webpack_require__(1);

	$ = __webpack_require__(1);

	LayoutStrategy = __webpack_require__(5);

	module.exports = HorizontalFlowLayoutStrategy = (function(superClass) {
	  extend(HorizontalFlowLayoutStrategy, superClass);

	  function HorizontalFlowLayoutStrategy() {
	    this.getContainerSize = bind(this.getContainerSize, this);
	    this.placeItems = bind(this.placeItems, this);
	    this.startNewRow = bind(this.startNewRow, this);
	    this.shrinkCurrentRow = bind(this.shrinkCurrentRow, this);
	    this.reset = bind(this.reset, this);
	    return HorizontalFlowLayoutStrategy.__super__.constructor.apply(this, arguments);
	  }

	  HorizontalFlowLayoutStrategy.prototype.reset = function() {
	    HorizontalFlowLayoutStrategy.__super__.reset.apply(this, arguments);
	    this.currentRow = [];
	    this.sizes = [];
	    this.currentRowWidth = 0;
	    this.rows = [this.currentRow];
	    this.elementSizes = [this.sizes];
	    this.height = 0;
	    return this.prevWidth = 0;
	  };

	  HorizontalFlowLayoutStrategy.prototype.shrinkCurrentRow = function(newHeight) {
	    var shrinkFactor, x;
	    x = 0;
	    shrinkFactor = this.options.height / newHeight;
	    return $.each(this.currentRow, ((function(_this) {
	      return function(rowIndex, image) {
	        var imageWidth;
	        if (rowIndex !== _this.currentRow.length - 1 || _this.currentRowWidth < _this.prevWidth) {
	          imageWidth = Math.floor(_this.sizes[rowIndex] / shrinkFactor);
	          _this.placeElement(image.$el, x, _this.height, imageWidth, newHeight);
	          return x += imageWidth + _this.options.gap + _this.options.border * 2;
	        } else {
	          return _this.placeElement(image.$el, x, _this.height, _this.width - x - _this.options.border * 2, newHeight);
	        }
	      };
	    })(this)));
	  };

	  HorizontalFlowLayoutStrategy.prototype.startNewRow = function() {
	    this.rows.push(this.currentRow = []);
	    this.elementSizes.push(this.sizes = []);
	    return this.currentRowWidth = 0;
	  };

	  HorizontalFlowLayoutStrategy.prototype.placeItems = function(images) {
	    var elementWidth, height, i, image, index, len, shrinkFactor, width, x;
	    width = this.width;
	    index = 0;
	    for (i = 0, len = images.length; i < len; i++) {
	      image = images[i];
	      this.currentRow.push(image);
	      this.sizes.push(elementWidth = image.naturalWidth / image.naturalHeight * this.options.height);
	      this.currentRowWidth += elementWidth + this.options.gap + this.options.border * 2;
	      if (this.currentRowWidth >= width + this.options.gap) {
	        this.currentRowWidth -= this.options.gap;
	        shrinkFactor = (this.currentRowWidth - (this.currentRow.length - 1) * this.options.gap - this.currentRow.length * this.options.border * 2) / (width - (this.currentRow.length - 1) * this.options.gap - this.currentRow.length * this.options.border * 2);
	        height = Math.floor(this.options.height / shrinkFactor);
	        this.shrinkCurrentRow(height);
	        this.height += height + this.options.gap + this.options.border * 2;
	        this.startNewRow();
	      }
	      index += 1;
	    }
	    if (this.currentRowWidth < this.width) {
	      x = 0;
	      return $.each(this.currentRow, (function(_this) {
	        return function(rowIndex, image) {
	          var imageWidth;
	          imageWidth = Math.floor(_this.sizes[rowIndex]);
	          _this.placeElement(image.$el, x, _this.height, imageWidth, _this.options.height);
	          return x += imageWidth + _this.options.gap + _this.options.border * 2;
	        };
	      })(this));
	    }
	  };

	  HorizontalFlowLayoutStrategy.prototype.getContainerSize = function() {
	    var size;
	    size = {};
	    if (this.rows.length > 1) {
	      size.height = this.height;
	      if (this.options.allowHanging && this.currentRowWidth < this.width && this.currentRowWidth > 0) {
	        size.height += this.options.height + this.options.gap + this.options.border * 2;
	      }
	    } else {
	      if (this.currentRow.length > 0) {
	        size.height = this.options.height + this.options.gap + this.options.border * 2;
	      } else {
	        size.height = 0;
	      }
	    }
	    if (this.rows.length < 2 && this.currentRowWidth > 0) {
	      size.width = this.currentRowWidth;
	    } else {
	      size.width = this.width;
	    }
	    return size;
	  };

	  return HorizontalFlowLayoutStrategy;

	})(LayoutStrategy);


/***/ },
/* 9 */
/***/ function(module, exports, __webpack_require__) {

	var $, GalleryFilters, jQuery,
	  bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

	jQuery = __webpack_require__(1);

	$ = __webpack_require__(1);

	module.exports = GalleryFilters = (function() {
	  function GalleryFilters($el, filterCallback) {
	    this.onFilterClick = bind(this.onFilterClick, this);
	    this.$el = $el;
	    this.tags = [];
	    this.filterCallback = filterCallback;
	    this.current = null;
	    this.$el.on('click', '.asg-filter', this.onFilterClick);
	  }

	  GalleryFilters.prototype.onFilterClick = function(event) {
	    var filter;
	    event.preventDefault();
	    filter = $(event.target);
	    this.$el.find('> div').removeClass('asg-active');
	    $(filter).parent().addClass('asg-active');
	    this.current = $(filter).attr('data-tag');
	    return this.filterCallback(this.current.toLowerCase().trim());
	  };

	  return GalleryFilters;

	})();


/***/ },
/* 10 */
/***/ function(module, exports, __webpack_require__) {

	var JetpackAdapter, MagnificPopupAdapter, PrettyPhotoAdapter, SwipeboxAdapter, UberboxAdapter, iLightboxAdapter;

	MagnificPopupAdapter = __webpack_require__(11);

	SwipeboxAdapter = __webpack_require__(13);

	PrettyPhotoAdapter = __webpack_require__(14);

	iLightboxAdapter = __webpack_require__(15);

	JetpackAdapter = __webpack_require__(16);

	UberboxAdapter = __webpack_require__(17);

	module.exports = {
	  create: function($images, config) {
	    switch (config.name) {
	      case 'magnific-popup':
	        return new MagnificPopupAdapter($images, config);
	      case 'swipebox':
	        return new SwipeboxAdapter($images, config);
	      case 'prettyphoto':
	        return new PrettyPhotoAdapter($images, config);
	      case 'ilightbox':
	        return new iLightboxAdapter($images, config);
	      case 'jetpack':
	        return new JetpackAdapter($images, config);
	      case 'foobox':
	        return new FooBoxAdapter($images, config);
	      case 'uberbox':
	        return new UberboxAdapter($images, config);
	      default:
	        return null;
	    }
	  }
	};


/***/ },
/* 11 */
/***/ function(module, exports, __webpack_require__) {

	var LightboxAdapter, MagnificPopupAdapter, jQuery,
	  extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
	  hasProp = {}.hasOwnProperty;

	LightboxAdapter = __webpack_require__(12);

	jQuery = __webpack_require__(1);

	module.exports = MagnificPopupAdapter = (function(superClass) {
	  extend(MagnificPopupAdapter, superClass);

	  function MagnificPopupAdapter($el, config) {
	    var _this;
	    _this = this;
	    $el.magnificPopup({
	      type: 'image',
	      delegate: this.linkSelector,
	      gallery: {
	        enabled: true
	      },
	      mainClass: 'mfp-asg',
	      image: {
	        titleSrc: function() {
	          var caption_1, caption_2, el;
	          el = this.currItem.el.parent();
	          if (caption_1 = el.find('.asg-lightbox-caption1').html()) {
	            caption_1 = $('<h3 />').html(caption_1)[0].outerHTML;
	          } else {
	            caption_1 = '';
	          }
	          if (caption_2 = el.find('.asg-lightbox-caption2').html()) {
	            caption_2 = $('<div />').html(caption_2)[0].outerHTML;
	          } else {
	            caption_2 = '';
	          }
	          if (caption_1 + caption_2) {
	            return caption_1 + caption_2;
	          }
	          return null;
	        },
	        markup: '<div class="mfp-figure">' + '<div class="mfp-close"></div>' + '<figure>' + '<div class="mfp-img"></div>' + '<div class="mfp-asg-border"></div>' + '<figcaption>' + '<div class="mfp-bottom-bar">' + '<div class="mfp-title"></div>' + '<div class="mfp-counter"></div>' + '</div>' + '</figcaption>' + '</figure>' + '</div>'
	      },
	      callbacks: {
	        open: (function() {
	          jQuery('.mfp-wrap').addClass('mfp-asg');
	          return this._lastFocusedEl = null;
	        }),
	        markupParse: (function(template) {
	          return template.find('.mfp-counter').remove();
	        }),
	        afterClose: (function(_this) {
	          return function() {
	            return _this.resetHash();
	          };
	        })(this),
	        afterChange: function() {
	          return _this.setHash(this.currItem.el.closest('.asg-image'));
	        }
	      }
	    });
	    MagnificPopupAdapter.__super__.constructor.call(this, $el, config);
	  }

	  return MagnificPopupAdapter;

	})(LightboxAdapter);


/***/ },
/* 12 */
/***/ function(module, exports, __webpack_require__) {

	var $, LightboxAdapter, jQuery,
	  bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

	$ = __webpack_require__(1);

	jQuery = __webpack_require__(1);

	module.exports = LightboxAdapter = (function() {
	  function LightboxAdapter($el, config) {
	    this.onPrevSlide = bind(this.onPrevSlide, this);
	    this.onNextSlide = bind(this.onNextSlide, this);
	    this.onAfterClose = bind(this.onAfterClose, this);
	    this.onKeyUp = bind(this.onKeyUp, this);
	    this.setHash = bind(this.setHash, this);
	    this.getInstanceIndex = bind(this.getInstanceIndex, this);
	    this.onImageClicked = bind(this.onImageClicked, this);
	    this.getSlug = bind(this.getSlug, this);
	    this.getId = bind(this.getId, this);
	    this.getAllLightboxLinks = bind(this.getAllLightboxLinks, this);
	    this.getLightboxLinks = bind(this.getLightboxLinks, this);
	    this.getDeeplinkImage = bind(this.getDeeplinkImage, this);
	    this.loadDeepLink = bind(this.loadDeepLink, this);
	    this.checkForDeeplink = bind(this.checkForDeeplink, this);
	    this.addImages = bind(this.addImages, this);
	    this.reset = bind(this.reset, this);
	    this.$el = $el;
	    this.config = config;
	    this.getAllLightboxLinks().off('click').removeClass('prettyphoto').removeClass('thickbox');
	    setTimeout(this.reset, 1);
	    this.reset();
	    this.checkForDeeplink();
	  }

	  LightboxAdapter.prototype.reset = function() {
	    this.getAllLightboxLinks().off('click', this.onImageClicked);
	    return this.getLightboxLinks().on('click', this.onImageClicked);
	  };

	  LightboxAdapter.prototype.addImages = function(images) {
	    return this.reset();
	  };

	  LightboxAdapter.prototype.checkForDeeplink = function() {
	    var gridId, image;
	    if (location.hash.match(/^#\d+\-/)) {
	      gridId = location.hash.replace(/^\#/, '').replace(/\-.*/, '');
	      if (gridId !== this.getId()) {
	        return false;
	      }
	      image = location.hash.replace(/^.*\//, '');
	      this.loadDeepLink(image);
	      return true;
	    }
	  };

	  LightboxAdapter.prototype.loadDeepLink = function(image) {
	    var linkedImage;
	    linkedImage = this.getDeeplinkImage(image);
	    if (linkedImage.length > 0) {
	      return this.clickImage(linkedImage);
	    }
	  };

	  LightboxAdapter.prototype.clickImage = function(image) {
	    return $(image).find('a.asg-lightbox').click();
	  };

	  LightboxAdapter.prototype.getDeeplinkImage = function(id) {
	    return jQuery.grep(this.getLightboxLinks().closest('.asg-image'), function(cell) {
	      return $(cell).data('slug').toString() === id;
	    });
	  };

	  LightboxAdapter.prototype.linkSelector = '.asg-image.asg-match-filter .asg-image-wrapper.asg-lightbox';

	  LightboxAdapter.prototype.allLinkSelector = '.asg-image .asg-image-wrapper.asg-lightbox';

	  LightboxAdapter.prototype.getLightboxLinks = function() {
	    return this.$el.find(this.linkSelector);
	  };

	  LightboxAdapter.prototype.getAllLightboxLinks = function() {
	    return this.$el.find(this.allLinkSelector);
	  };

	  LightboxAdapter.prototype.getId = function() {
	    return this.$el.parent().attr('id').replace(/\-\d+$/, '').replace(/^.*\-/, '');
	  };

	  LightboxAdapter.prototype.getSlug = function() {
	    return this.$el.parent().attr('data-slug');
	  };

	  LightboxAdapter.prototype.getLightboxCaption1 = function(el) {
	    var caption_1;
	    if (caption_1 = el.find('.asg-lightbox-caption1').html()) {
	      caption_1 = $('<h3 />').html(caption_1)[0].outerHTML;
	    } else {
	      caption_1 = '';
	    }
	    return caption_1;
	  };

	  LightboxAdapter.prototype.getLightboxCaption2 = function(el) {
	    var caption_2;
	    if (caption_2 = el.find('.asg-lightbox-caption2').html()) {
	      caption_2 = $('<div />').html(caption_2)[0].outerHTML;
	    } else {
	      caption_2 = '';
	    }
	    return caption_2;
	  };

	  LightboxAdapter.prototype.onImageClicked = function(event) {
	    var cell;
	    cell = jQuery(event.target).closest('.asg-image');
	    this.scrollTop = jQuery(document).scrollTop();
	    return this.setHash(cell);
	  };

	  LightboxAdapter.prototype.getID = function() {
	    return this.$el.parent().attr('id').replace(/\-\d+$/, '').replace(/.+\-/, '');
	  };

	  LightboxAdapter.prototype.getInstanceIndex = function() {
	    return this.$el.parent().attr('id').replace(/.*\-/, '');
	  };

	  LightboxAdapter.prototype.setHash = function(cell) {
	    var cellSlug, id;
	    id = this.getID();
	    cellSlug = cell.data('slug');
	    this.prevHash = location.hash;
	    return location.hash = id + "-" + (this.getSlug()) + "/" + cellSlug;
	  };

	  LightboxAdapter.prototype.resetHash = function() {
	    if (this.prevHash) {
	      location.hash = this.prevHash;
	      delete this.prevHash;
	    } else {
	      location.hash = '#';
	    }
	    if (this.scrollTop && this.scrollTop > 0) {
	      return jQuery(document).scrollTop(this.scrollTop);
	    }
	  };

	  LightboxAdapter.prototype.onKeyUp = function(event) {
	    if (event.keyCode === 37) {
	      return this.onPrevSlide();
	    } else if (event.keyCode === 39) {
	      return this.onNextSlide();
	    } else if (event.keyCode === 27) {
	      return this.onAfterClose();
	    }
	  };

	  LightboxAdapter.prototype.onAfterClose = function() {
	    $(window).off('keyup', this.onKeyup);
	    return this.resetHash();
	  };

	  LightboxAdapter.prototype.onNextSlide = function() {
	    var lightboxLinks;
	    this.currentIndex += 1;
	    lightboxLinks = this.getLightboxLinks();
	    if (this.currentIndex === lightboxLinks.length) {
	      this.currentIndex = lightboxLinks.length - 1;
	    }
	    return this.setHash(this.getLightboxLinks().eq(this.currentIndex).closest('.asg-image'));
	  };

	  LightboxAdapter.prototype.onPrevSlide = function() {
	    this.currentIndex -= 1;
	    if (this.currentIndex < 0) {
	      this.currentIndex = 0;
	    }
	    return this.setHash(this.getLightboxLinks().eq(this.currentIndex).closest('.asg-image'));
	  };

	  return LightboxAdapter;

	})();


/***/ },
/* 13 */
/***/ function(module, exports, __webpack_require__) {

	var LightboxAdapter, SwipeboxAdapter, jQuery,
	  bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
	  extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
	  hasProp = {}.hasOwnProperty;

	jQuery = __webpack_require__(1);

	LightboxAdapter = __webpack_require__(12);

	SwipeboxAdapter = (function(superClass) {
	  extend(SwipeboxAdapter, superClass);

	  function SwipeboxAdapter() {
	    this.onImageClicked = bind(this.onImageClicked, this);
	    return SwipeboxAdapter.__super__.constructor.apply(this, arguments);
	  }

	  SwipeboxAdapter.prototype.onImageClicked = function(event) {
	    var elements, lightboxImages;
	    SwipeboxAdapter.__super__.onImageClicked.call(this, event);
	    event.preventDefault();
	    elements = this.$el.find(this.linkSelector);
	    lightboxImages = $.map(elements, (function(_this) {
	      return function(image) {
	        image = $(image);
	        return {
	          href: image.attr('href'),
	          title: function() {
	            var caption1, caption2, html;
	            image = image.closest('.asg-image');
	            html = $('<div/>');
	            if (caption2 = _this.getLightboxCaption2(image)) {
	              html.append($('<small class="asg-small"/>').html(caption2));
	            }
	            if (caption1 = _this.getLightboxCaption1(image)) {
	              html.append($('<div />').html(caption1));
	            }
	            return html.html();
	          }
	        };
	      };
	    })(this));
	    this.currentIndex = elements.index($(event.target).closest('a.asg-image-wrapper'));
	    $.swipebox(lightboxImages, {
	      initialIndexOnArray: this.currentIndex
	    });
	    jQuery('#swipebox-next').click(this.onNextSlide);
	    jQuery('#swipebox-prev').click(this.onPrevSlide);
	    return jQuery(window).on('keyup', this.onKeyUp);
	  };

	  return SwipeboxAdapter;

	})(LightboxAdapter);

	module.exports = SwipeboxAdapter;


/***/ },
/* 14 */
/***/ function(module, exports, __webpack_require__) {

	var LightboxAdapter, PrettyPhotoAdapter, jQuery,
	  bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
	  extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
	  hasProp = {}.hasOwnProperty;

	jQuery = __webpack_require__(1);

	LightboxAdapter = __webpack_require__(12);

	module.exports = PrettyPhotoAdapter = (function(superClass) {
	  extend(PrettyPhotoAdapter, superClass);

	  function PrettyPhotoAdapter() {
	    this.onPrevSlide = bind(this.onPrevSlide, this);
	    this.onNextSlide = bind(this.onNextSlide, this);
	    this.onKeyUp = bind(this.onKeyUp, this);
	    this.onImageClicked = bind(this.onImageClicked, this);
	    return PrettyPhotoAdapter.__super__.constructor.apply(this, arguments);
	  }

	  PrettyPhotoAdapter.prototype.onImageClicked = function(event) {
	    var descriptions, elements, titles, urls;
	    event.preventDefault();
	    event.stopPropagation();
	    elements = this.$el.find(this.linkSelector);
	    urls = elements.map((function(_this) {
	      return function(index, image) {
	        return $(image).closest('a.asg-image-wrapper').attr('href');
	      };
	    })(this));
	    titles = elements.map((function(_this) {
	      return function(index, image) {
	        return _this.getLightboxCaption1($(image).closest('.asg-image'));
	      };
	    })(this));
	    descriptions = elements.map((function(_this) {
	      return function(index, image) {
	        return _this.getLightboxCaption2($(image).closest('.asg-image'));
	      };
	    })(this));
	    $.fn.prettyPhoto(this.config.settings);
	    $.prettyPhoto.open(urls, titles, descriptions, this.currentIndex = elements.index($(event.target).closest('.asg-image-wrapper')));
	    this.setHash($(event.target).closest('.asg-image'));
	    $(document).on('keydown.prettyphoto', this.onKeyUp);
	    $('.pp_previous').on('click.asg', this.onPrevSlide);
	    return $('.pp_next').on('click.asg', this.onNextSlide);
	  };

	  PrettyPhotoAdapter.prototype.onKeyUp = function(event) {
	    if (event.keyCode === 37) {
	      return this.onPrevSlide();
	    } else if (event.keyCode === 39) {
	      return this.onNextSlide();
	    } else if (event.keyCode === 27) {
	      return this.resetHash();
	    }
	  };

	  PrettyPhotoAdapter.prototype.onNextSlide = function() {
	    var lightboxLinks;
	    this.currentIndex += 1;
	    lightboxLinks = this.getLightboxLinks();
	    if (this.currentIndex === lightboxLinks.length) {
	      this.currentIndex = lightboxLinks.length - 1;
	    }
	    return this.setHash(this.getLightboxLinks().eq(this.currentIndex).closest('.asg-image'));
	  };

	  PrettyPhotoAdapter.prototype.onPrevSlide = function() {
	    this.currentIndex -= 1;
	    if (this.currentIndex < 0) {
	      this.currentIndex = 0;
	    }
	    return this.setHash(this.getLightboxLinks().eq(this.currentIndex).closest('.asg-image'));
	  };

	  return PrettyPhotoAdapter;

	})(LightboxAdapter);


/***/ },
/* 15 */
/***/ function(module, exports, __webpack_require__) {

	var $, LightboxAdapter, iLightboxAdapter, jQuery,
	  bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
	  extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
	  hasProp = {}.hasOwnProperty;

	jQuery = __webpack_require__(1);

	$ = __webpack_require__(1);

	LightboxAdapter = __webpack_require__(12);

	module.exports = iLightboxAdapter = (function(superClass) {
	  extend(iLightboxAdapter, superClass);

	  function iLightboxAdapter($el, config) {
	    this.onImageClicked = bind(this.onImageClicked, this);
	    if (!$.iLightBox) {
	      alert('iLightbox not detected. Please install end enable iLightbox plugin.');
	    }
	    iLightboxAdapter.__super__.constructor.call(this, $el, config);
	  }

	  iLightboxAdapter.prototype.onImageClicked = function(event) {
	    var elements, index, lightboxImages, options;
	    iLightboxAdapter.__super__.onImageClicked.call(this, event);
	    event.preventDefault();
	    elements = this.$el.find(this.linkSelector);
	    lightboxImages = $.map(elements, function(el) {
	      var data, image;
	      data = $(el);
	      image = data.closest('.asg-image').data('asg-image');
	      return {
	        url: data.attr('href'),
	        caption: image.getLightboxCaption1(),
	        thumbnail: data.find('.asg-image-wrapper img').attr('src')
	      };
	    });
	    this.currentIndex = index = elements.index($(event.target).closest('.asg-image-wrapper'));
	    options = $.extend(this.config.settings, ILIGHTBOX.options && eval("(" + rawurldecode(ILIGHTBOX.options) + ")") || {});
	    return $.iLightBox(lightboxImages, $.extend({
	      startFrom: index,
	      callback: {
	        onAfterChange: (function(_this) {
	          return function(instance) {
	            _this.currentIndex = instance.currentItem;
	            return _this.setHash(elements.eq(_this.currentIndex).closest('.asg-image'));
	          };
	        })(this),
	        onHide: (function(_this) {
	          return function() {
	            return _this.resetHash();
	          };
	        })(this)
	      }
	    }, options));
	  };

	  return iLightboxAdapter;

	})(LightboxAdapter);


/***/ },
/* 16 */
/***/ function(module, exports, __webpack_require__) {

	var JetpackAdapter, LightboxAdapter, jQuery,
	  bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
	  extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
	  hasProp = {}.hasOwnProperty;

	jQuery = __webpack_require__(1);

	LightboxAdapter = __webpack_require__(12);

	module.exports = JetpackAdapter = (function(superClass) {
	  extend(JetpackAdapter, superClass);

	  function JetpackAdapter($el, config) {
	    this.onPrevSlide = bind(this.onPrevSlide, this);
	    this.onNextSlide = bind(this.onNextSlide, this);
	    this.setHashFromCurrentIndex = bind(this.setHashFromCurrentIndex, this);
	    this.onAfterClose = bind(this.onAfterClose, this);
	    this.startCarousel = bind(this.startCarousel, this);
	    this.onImageClicked = bind(this.onImageClicked, this);
	    this.addImages = bind(this.addImages, this);
	    JetpackAdapter.__super__.constructor.call(this, $el, config);
	    this.$el.data('carousel-extra', {
	      blog_id: 1,
	      permalink: 'http://awesome-gallery.dev'
	    });
	  }

	  JetpackAdapter.prototype.addImages = function(images) {
	    var i, image, len, url, wrapper;
	    images.addClass('tiled-gallery-item');
	    for (i = 0, len = images.length; i < len; i++) {
	      image = images[i];
	      image = $(image);
	      wrapper = image.closest('.asg-image');
	      url = image.find('.asg-image-wrapper').attr('href');
	      image.find('img').data({
	        'orig-file': url,
	        'orig-size': (wrapper.data('width')) + "," + (wrapper.data('height')),
	        'large-file': url,
	        'medium-file': url,
	        'small-file': url,
	        'image-title': this.getLightboxCaption1(wrapper),
	        'image-description': this.getLightboxCaption2(wrapper),
	        'image-meta': wrapper.data('meta'),
	        'attachment-id': wrapper.data('attachment-id') ? wrapper.data('attachment-id') : 'asg-hack',
	        'comments-opened': wrapper.data('attachment-id') ? 1 : null
	      });
	    }
	    return images.on('click', this.onImageClicked);
	  };

	  JetpackAdapter.prototype.onImageClicked = function(event) {
	    event.preventDefault();
	    if ($.fn.jp_carousel) {
	      return this.startCarousel(event);
	    } else {
	      return $(document).ready(setTimeout(((function(_this) {
	        return function() {
	          return _this.startCarousel(event);
	        };
	      })(this)), 500));
	    }
	  };

	  JetpackAdapter.prototype.startCarousel = function(event) {
	    this.currentIndex = this.$el.find(this.linkSelector).index($(event.target).closest('.asg-image-wrapper'));
	    if (this.$el.jp_carousel) {
	      this.$el.jp_carousel({
	        start_index: this.currentIndex,
	        'items_selector': ".asg-image:not(.asg-hidden) .asg-image-wrapper img"
	      });
	      return setTimeout(this.setHashFromCurrentIndex, 500);
	    } else {
	      return $(document).ready((function(_this) {
	        return function() {
	          return setTimeout(function() {
	            _this.$el.jp_carousel({
	              start_index: _this.currentIndex,
	              'items_selector': ".asg-image:not(.asg-hidden) .asg-image-wrapper img"
	            });
	            return setTimeout(_this.setHashFromCurrentIndex, 500);
	          }, 600);
	        };
	      })(this));
	    }
	  };

	  JetpackAdapter.prototype.onAfterClose = function() {
	    JetpackAdapter.__super__.onAfterClose.apply(this, arguments);
	    jQuery(document).off('keyup', this.onKeyUp);
	    $('.jp-carousel-next-button').off('click', this.onNextSlide);
	    return $('.jp-carousel-previous-button').off('click', this.onPrevSlide);
	  };

	  JetpackAdapter.prototype.setHashFromCurrentIndex = function() {
	    this.setHash(this.getLightboxLinks().eq(this.currentIndex).closest('.asg-image'));
	    $('.jp-carousel-next-button').click(this.onNextSlide);
	    $('.jp-carousel-previous-button').click(this.onPrevSlide);
	    $(document).on('keyup', this.onKeyUp);
	    return $(document).on('click', '.jp-carousel-close-hint', this.onAfterClose);
	  };

	  JetpackAdapter.prototype.onNextSlide = function() {
	    var lightboxLinks;
	    this.currentIndex += 1;
	    lightboxLinks = this.getLightboxLinks();
	    if (this.currentIndex === lightboxLinks.length) {
	      this.currentIndex = 0;
	    }
	    return setTimeout(this.setHashFromCurrentIndex, 400);
	  };

	  JetpackAdapter.prototype.onPrevSlide = function() {
	    this.currentIndex -= 1;
	    if (this.currentIndex < 0) {
	      this.currentIndex = this.getLightboxLinks().size() - 1;
	    }
	    return setTimeout(this.setHashFromCurrentIndex, 400);
	  };

	  return JetpackAdapter;

	})(LightboxAdapter);


/***/ },
/* 17 */
/***/ function(module, exports, __webpack_require__) {

	var $, LightboxAdapter, UberboxAdapter, jQuery,
	  extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
	  hasProp = {}.hasOwnProperty;

	LightboxAdapter = __webpack_require__(12);

	$ = __webpack_require__(1);

	jQuery = __webpack_require__(1);

	module.exports = UberboxAdapter = (function(superClass) {
	  extend(UberboxAdapter, superClass);

	  function UberboxAdapter() {
	    return UberboxAdapter.__super__.constructor.apply(this, arguments);
	  }

	  UberboxAdapter.prototype.onImageClicked = function(e) {
	    var box, index, items;
	    UberboxAdapter.__super__.onImageClicked.call(this, e);
	    e.preventDefault();
	    items = this.getLightboxLinks();
	    index = items.index(jQuery(e.target).closest('a.asg-lightbox'));
	    box = Uberbox.show(this.getItems(), {
	      orientation: 'horizontal',
	      current: index,
	      carousel: true
	    });
	    return box.on('close', (function(_this) {
	      return function() {
	        box.off('close');
	        return _this.resetHash();
	      };
	    })(this));
	  };

	  UberboxAdapter.prototype.getItems = function() {
	    var items;
	    items = [];
	    this.getLightboxLinks().each(function(index, cell) {
	      var image;
	      image = jQuery(cell).closest('.asg-image').data('asg-image');
	      image = {
	        type: 'image',
	        url: image.getImageUrl(),
	        thumbnail: image.getThumbnailUrl(),
	        title: image.getLightboxCaption1(),
	        description: image.getLightboxCaption2(),
	        description_style: 'mini',
	        download_url: image.getImageUrl(),
	        share: true
	      };
	      return items.push(image);
	    });
	    return items;
	  };

	  return UberboxAdapter;

	})(LightboxAdapter);


/***/ },
/* 18 */
/***/ function(module, exports, __webpack_require__) {

	var $, GalleryImage, ImageCaption, ImageOverlay, jQuery,
	  bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

	jQuery = __webpack_require__(1);

	$ = __webpack_require__(1);

	ImageCaption = __webpack_require__(19);

	ImageOverlay = __webpack_require__(22);

	module.exports = GalleryImage = (function() {
	  function GalleryImage($el) {
	    this.slideIn = bind(this.slideIn, this);
	    this.getTags = bind(this.getTags, this);
	    this.show = bind(this.show, this);
	    this.hide = bind(this.hide, this);
	    var $caption, $overlay;
	    this.$el = $el;
	    this.$image = this.$el.find('.asg-image-wrapper img');
	    if (window.devicePixelRatio) {
	      setTimeout(((function(_this) {
	        return function() {
	          return _this.$image.attr('src', _this.$image.attr('src') + ("&zoom=" + window.devicePixelRatio));
	        };
	      })(this)), Math.random() * 1500);
	    }
	    if (this.$image[0].complete || this.$image[0].naturalWidth > 0) {
	      this.$el.addClass('asg-loaded');
	    } else {
	      this.$image.on('load', (function(_this) {
	        return function() {
	          return _this.$el.addClass('asg-loaded');
	        };
	      })(this));
	    }
	    this.naturalWidth = this.$el.data('width');
	    this.naturalHeight = this.$el.data('height');
	    this.$el.data('asg-image', this);
	    if (($overlay = this.$el.find('.asg-image-overlay')).size() > 0) {
	      this.overlay = new ImageOverlay($overlay, this.$el);
	    }
	    if (($caption = this.$el.find('.asg-image-caption-wrapper')).size() > 0) {
	      this.caption = new ImageCaption($caption, this.$el);
	    }
	  }

	  GalleryImage.prototype.hide = function() {
	    return this.$el.removeClass('asg-visible').css({
	      'transform': ''
	    });
	  };

	  GalleryImage.prototype.show = function() {
	    return setTimeout(((function(_this) {
	      return function() {
	        return _this.$el.addClass('asg-visible');
	      };
	    })(this)), 100);
	  };

	  GalleryImage.prototype.matchesTag = function(filter) {
	    var i, len, ref, tag;
	    if (!filter || filter === '') {
	      this.$el.addClass('asg-match-filter');
	      return true;
	    }
	    ref = this.getTags();
	    for (i = 0, len = ref.length; i < len; i++) {
	      tag = ref[i];
	      if (tag.toLowerCase().trim() === filter) {
	        this.$el.addClass('asg-match-filter');
	        return true;
	      }
	    }
	    this.$el.removeClass('asg-match-filter');
	    return false;
	  };

	  GalleryImage.prototype.getTags = function() {
	    if (this.tags) {
	      return this.tags;
	    }
	    return this.tags = this.$el.data('tags').toString().split(', ');
	  };

	  GalleryImage.prototype.getImageUrl = function() {
	    return this.$el.find('.asg-image-wrapper').attr('href');
	  };

	  GalleryImage.prototype.getThumbnailUrl = function() {
	    return this.$el.find('img').attr('src');
	  };

	  GalleryImage.prototype.getTitle = function() {
	    return this.$el.find('.asg-image-caption-wrapper .asg-image-caption1').html();
	  };

	  GalleryImage.prototype.getDescription = function() {
	    return this.$el.find('.asg-image-caption-wrapper .asg-image-caption2').html();
	  };

	  GalleryImage.prototype.getLightboxCaption1 = function() {
	    return this.$el.find('.asg-lightbox-caption1').html();
	  };

	  GalleryImage.prototype.getLightboxCaption2 = function() {
	    return this.$el.find('.asg-lightbox-caption2').html();
	  };

	  GalleryImage.prototype.slideIn = function(event, element, zero) {
	    var css, x, y;
	    x = event.offsetX - this.wrapper.width() / 2;
	    y = event.offsetY - this.wrapper.height() / 2;
	    if (Math.abs(x) > Math.abs(y)) {
	      if (x > 0) {
	        css = {
	          'left': (this.wrapper.width()) + "px",
	          top: 0
	        };
	      } else {
	        css = {
	          'left': "-" + (this.wrapper.width()) + "px",
	          top: 0
	        };
	      }
	    } else {
	      if (y < 0) {
	        css = {
	          'top': "-" + (this.wrapper.height()) + "px",
	          left: 0
	        };
	      } else {
	        css = {
	          'top': (this.wrapper.height()) + "px",
	          left: 0
	        };
	      }
	    }
	    return element.css(css).animate(zero, 'fast');
	  };

	  GalleryImage.prototype.slideOut = function(event, element) {
	    var css, x, y;
	    x = event.offsetX - this.wrapper.width() / 2;
	    y = event.offsetY - this.wrapper.height() / 2;
	    if (x > 0) {
	      if (Math.abs(x) > Math.abs(y)) {
	        css = {
	          'left': (this.wrapper.width()) + "px"
	        };
	      } else {
	        if (y < 0) {
	          css = {
	            'top': "-" + (this.wrapper.height()) + "px"
	          };
	        } else {
	          css = {
	            'top': (this.wrapper.height()) + "px"
	          };
	        }
	      }
	    } else {
	      if (Math.abs(x) > Math.abs(y)) {
	        css = {
	          'left': "-" + (this.wrapper.width()) + "px"
	        };
	      } else {
	        if (y < 0) {
	          css = {
	            'top': "-" + (this.wrapper.height()) + "px"
	          };
	        } else {
	          css = {
	            'top': (this.wrapper.height()) + "px"
	          };
	        }
	      }
	    }
	    return element.animate(css, 'fast');
	  };

	  return GalleryImage;

	})();


/***/ },
/* 19 */
/***/ function(module, exports, __webpack_require__) {

	var $, ImageCaption, SlidingElement, jQuery,
	  bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
	  extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
	  hasProp = {}.hasOwnProperty;

	jQuery = __webpack_require__(1);

	$ = __webpack_require__(1);

	SlidingElement = __webpack_require__(20);

	module.exports = ImageCaption = (function(superClass) {
	  extend(ImageCaption, superClass);

	  function ImageCaption($el, $wrapper, animationQueue) {
	    this.layout = bind(this.layout, this);
	    var h, img;
	    ImageCaption.__super__.constructor.call(this, $el, $wrapper, animationQueue);
	    this.centered = $el.hasClass('asg-position-center');
	    if (this.centered) {
	      $el.on('resize', (function(_this) {
	        return function() {
	          return _this.layout();
	        };
	      })(this));
	    }
	    img = $el.parent().find('img');
	    h = $el.height();
	    if ($el.hasClass('asg-position-bottom') && $el.hasClass('asg-effect-slide') && $el.hasClass('asg-on-hover')) {
	      this.$wrapper.hover(((function(_this) {
	        return function() {
	          h = $el.outerHeight();
	          img.animate({
	            'top': "-" + (h / 2) + "px"
	          }, {
	            'queue': false,
	            duration: 400
	          });
	          return $el.css({
	            bottom: "-" + h + "px"
	          }).animate({
	            bottom: 0
	          }, {
	            queue: false,
	            duration: 350
	          });
	        };
	      })(this)), ((function(_this) {
	        return function() {
	          img.animate({
	            top: 0
	          }, {
	            queue: false,
	            duration: 400
	          });
	          return $el.animate({
	            bottom: "-" + h + "px"
	          }, {
	            queue: false,
	            duration: 350
	          });
	        };
	      })(this)));
	    }
	  }

	  ImageCaption.prototype.layout = function() {
	    if (this.centered) {
	      if ((this.$el.hasClass('asg-mode-on') || this.$el.hasClass('asg-mode-on-hover')) && !this.$el.hasClass('asg-effect-off')) {
	        return this.$el.css({
	          opacity: 0
	        });
	      }
	    }
	  };

	  return ImageCaption;

	})(SlidingElement);


/***/ },
/* 20 */
/***/ function(module, exports, __webpack_require__) {

	var $, SlidingElement, helpers, jQuery,
	  bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

	jQuery = __webpack_require__(1);

	$ = __webpack_require__(1);

	helpers = __webpack_require__(21);

	module.exports = SlidingElement = (function() {
	  function SlidingElement($el, $wrapper) {
	    this.slideOut = bind(this.slideOut, this);
	    this.cleanupClass = bind(this.cleanupClass, this);
	    this.slideIn = bind(this.slideIn, this);
	    this.getTransitionClass = bind(this.getTransitionClass, this);
	    this.$el = $el;
	    this.$wrapper = $wrapper;
	    if (helpers.transitionEndEvent) {
	      this.$el.on(helpers.transitionEndEvent[helpers.transitionProp], this.cleanupClass);
	    } else {
	      setTimeout(((function(_this) {
	        return function() {
	          return _this.cleanupClass();
	        };
	      })(this)), 200);
	    }
	    if (!($el.hasClass('asg-position-bottom') && $el.hasClass('asg-effect-slide') && $el.hasClass('asg-on-hover'))) {
	      if (!this.$el.hasClass('asg-off-hover')) {
	        this.$wrapper.on('mouseout', this.cleanupClass);
	      }
	      if (this.$el.hasClass('asg-on-hover') && this.$el.hasClass('asg-effect-slide')) {
	        this.$wrapper.hover(((function(_this) {
	          return function(event) {
	            return _this.slideIn(event);
	          };
	        })(this)), (function(_this) {
	          return function(event) {
	            return _this.slideOut(event);
	          };
	        })(this));
	      }
	    }
	    if (this.$el.hasClass('asg-off-hover') && this.$el.hasClass('asg-effect-slide')) {
	      this.$wrapper.hover(((function(_this) {
	        return function(event) {
	          return _this.slideOut(event);
	        };
	      })(this)), (function(_this) {
	        return function(event) {
	          return _this.slideIn(event);
	        };
	      })(this));
	    }
	  }

	  SlidingElement.prototype.getTransitionClass = function(event) {
	    var klass, x, y;
	    x = event.offsetX - this.$wrapper.width() / 2;
	    y = event.offsetY - this.$wrapper.height() / 2;
	    if (x > 0) {
	      if (Math.abs(x) > Math.abs(y)) {
	        klass = 'asg-slide-right';
	      } else {
	        if (y < 0) {
	          klass = 'asg-slide-top';
	        } else {
	          klass = 'asg-slide-bottom';
	        }
	      }
	    } else {
	      if (Math.abs(x) > Math.abs(y)) {
	        klass = 'asg-slide-left';
	      } else {
	        if (y < 0) {
	          klass = 'asg-slide-top';
	        } else {
	          klass = 'asg-slide-bottom';
	        }
	      }
	    }
	    return klass;
	  };

	  SlidingElement.prototype.slideIn = function(event) {
	    this.$el.addClass('asg-no-transition');
	    this.cleanupClass();
	    this.$el.addClass(this.getTransitionClass(event));
	    this.$el.height();
	    this.$el.removeClass('asg-no-transition');
	    return this.cleanupClass();
	  };

	  SlidingElement.prototype.cleanupClass = function() {
	    var i, klass, len, ref, results;
	    ref = ['asg-slide-left', 'asg-slide-right', 'asg-slide-top', 'asg-slide-bottom'];
	    results = [];
	    for (i = 0, len = ref.length; i < len; i++) {
	      klass = ref[i];
	      results.push(this.$el.removeClass(klass));
	    }
	    return results;
	  };

	  SlidingElement.prototype.slideOut = function(event) {
	    var klass;
	    klass = this.getTransitionClass(event);
	    return this.$el.addClass(klass);
	  };

	  return SlidingElement;

	})();


/***/ },
/* 21 */
/***/ function(module, exports, __webpack_require__) {

	var $, $event, Modernizr, capitalize, classes, dispatchMethod, document, getStyleProperty, prefixes, resizeTimeout, result, setIsoTransform, testName, tests, transformFnNotations, transformProp, transitionEndEvent, transitionProp;

	$ = __webpack_require__(1);

	document = window.document;

	Modernizr = window.Modernizr;

	transitionEndEvent = null;

	capitalize = function(str) {
	  return str.charAt(0).toUpperCase() + str.slice(1);
	};

	prefixes = "Moz Webkit O Ms".split(" ");

	getStyleProperty = function(propName) {
	  var i, len, prefixed, style;
	  style = document.documentElement.style;
	  prefixed = void 0;
	  if (typeof style[propName] === "string") {
	    return propName;
	  }
	  propName = capitalize(propName);
	  i = 0;
	  len = prefixes.length;
	  while (i < len) {
	    prefixed = prefixes[i] + propName;
	    if (typeof style[prefixed] === "string") {
	      return prefixed;
	    }
	    i++;
	  }
	};

	transformProp = getStyleProperty("transform");

	transitionProp = getStyleProperty("transitionProperty");

	tests = {
	  csstransforms: function() {
	    return !!transformProp;
	  },
	  csstransforms3d: function() {
	    var $div, $style, mediaQuery, test, vendorCSSPrefixes;
	    test = !!getStyleProperty("perspective");
	    if (test) {
	      vendorCSSPrefixes = " -o- -moz- -ms- -webkit- -khtml- ".split(" ");
	      mediaQuery = "@media (" + vendorCSSPrefixes.join("transform-3d),(") + "modernizr)";
	      $style = $("<style>" + mediaQuery + "{#modernizr{height:3px}}" + "</style>").appendTo("head");
	      $div = $("<div id=\"modernizr\" />").appendTo("html");
	      test = $div.height() === 3;
	      $div.remove();
	      $style.remove();
	    }
	    return test;
	  },
	  csstransitions: function() {
	    return !!transitionProp;
	  }
	};

	testName = void 0;

	if (Modernizr) {
	  for (testName in tests) {
	    if (!Modernizr.hasOwnProperty(testName)) {
	      Modernizr.addTest(testName, tests[testName]);
	    }
	  }
	} else {
	  Modernizr = window.Modernizr = {
	    _version: "1.6ish: miniModernizr for Isotope"
	  };
	  classes = " ";
	  result = void 0;
	  for (testName in tests) {
	    result = tests[testName]();
	    Modernizr[testName] = result;
	    classes += " " + (result ? "" : "no-") + testName;
	  }
	  $("html").addClass(classes);
	}


	/*
	provides hooks for .css({ scale: value, translate: [x, y] })
	Progressively enhanced CSS transforms
	Uses hardware accelerated 3D transforms for Safari
	or falls back to 2D transforms.
	 */

	if (Modernizr.csstransforms) {
	  transformFnNotations = (Modernizr.csstransforms3d ? {
	    translate: function(position) {
	      return "translate3d(" + position[0] + "px, " + position[1] + "px, 0) ";
	    },
	    scale: function(scale) {
	      return "scale3d(" + scale + ", " + scale + ", 1) ";
	    }
	  } : {
	    translate: function(position) {
	      return "translate(" + position[0] + "px, " + position[1] + "px) ";
	    },
	    scale: function(scale) {
	      return "scale(" + scale + ") ";
	    }
	  });
	  setIsoTransform = function(elem, name, value) {
	    var data, fnName, newData, scaleFn, transformObj, transformValue, translateFn, valueFns;
	    data = $.data(elem, "isoTransform") || {};
	    newData = {};
	    fnName = void 0;
	    transformObj = {};
	    transformValue = void 0;
	    newData[name] = value;
	    $.extend(data, newData);
	    for (fnName in data) {
	      transformValue = data[fnName];
	      transformObj[fnName] = transformFnNotations[fnName](transformValue);
	    }
	    translateFn = transformObj.translate || "";
	    scaleFn = transformObj.scale || "";
	    valueFns = translateFn + scaleFn;
	    $.data(elem, "isoTransform", data);
	    elem.style[transformProp] = valueFns;
	  };
	  $.cssNumber.scale = true;
	  $.cssHooks.scale = {
	    set: function(elem, value) {
	      setIsoTransform(elem, "scale", value);
	    },
	    get: function(elem, computed) {
	      var transform;
	      transform = $.data(elem, "isoTransform");
	      if (transform && transform.scale) {
	        return transform.scale;
	      } else {
	        return 1;
	      }
	    }
	  };
	  $.fx.step.scale = function(fx) {
	    $.cssHooks.scale.set(fx.elem, fx.now + fx.unit);
	  };
	  $.cssNumber.translate = true;
	  $.cssHooks.translate = {
	    set: function(elem, value) {
	      setIsoTransform(elem, "translate", value);
	    },
	    get: function(elem, computed) {
	      var transform;
	      transform = $.data(elem, "isoTransform");
	      if (transform && transform.translate) {
	        return transform.translate;
	      } else {
	        return [0, 0];
	      }
	    }
	  };
	}

	transitionEndEvent = void 0;

	if (Modernizr.csstransitions) {
	  transitionEndEvent = {
	    WebkitTransitionProperty: "webkitTransitionEnd",
	    MozTransitionProperty: "transitionend",
	    OTransitionProperty: "oTransitionEnd otransitionend",
	    transitionProperty: "transitionend"
	  };
	}

	$event = $.event;

	dispatchMethod = ($.event.handle ? "handle" : "dispatch");

	resizeTimeout = void 0;

	$event.special.smartresize = {
	  setup: function() {
	    $(this).bind("resize", $event.special.smartresize.handler);
	  },
	  teardown: function() {
	    $(this).unbind("resize", $event.special.smartresize.handler);
	  },
	  handler: function(event, execAsap) {
	    var args, context;
	    context = this;
	    args = arguments;
	    event.type = "smartresize";
	    if (resizeTimeout) {
	      clearTimeout(resizeTimeout);
	    }
	    resizeTimeout = setTimeout(function() {
	      $event[dispatchMethod].apply(context, args);
	    }, (execAsap === "execAsap" ? 0 : 100));
	  }
	};

	$.fn.smartresize = function(fn) {
	  if (fn) {
	    return this.bind("smartresize", fn);
	  } else {
	    return this.trigger("smartresize", ["execAsap"]);
	  }
	};

	module.exports = {
	  transitionEndEvent: transitionEndEvent,
	  transformProp: transformProp,
	  transitionProp: transitionProp
	};


/***/ },
/* 22 */
/***/ function(module, exports, __webpack_require__) {

	var $, ImageOverlay, SlidingElement, jQuery,
	  extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
	  hasProp = {}.hasOwnProperty;

	jQuery = __webpack_require__(1);

	$ = __webpack_require__(1);

	SlidingElement = __webpack_require__(20);

	module.exports = ImageOverlay = (function(superClass) {
	  extend(ImageOverlay, superClass);

	  function ImageOverlay($el, $wrapper, animationQueue) {
	    ImageOverlay.__super__.constructor.call(this, $el, $wrapper, animationQueue);
	  }

	  return ImageOverlay;

	})(SlidingElement);


/***/ }
/******/ ]);