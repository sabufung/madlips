window.asg ||= {}
$ = jQuery
class ExternalGallerySelector
	select: (options)=>
		state = new ExternalGalleryState(title: options.title)
		state.set('selected', options.value)
		options = _.defaults({state: 'external-gallery', states: [state]}, options)
		@frame = new ExternalGalleryFrame(options)
		@frame.open()
		@deferred = new $.Deferred()
		state.on 'change:selected', =>
			@deferred.resolveWith(this, [state.get('selected')])
		@deferred.promise()

class ExternalGalleryState extends wp.media.controller.State
	defaults: {
		id: 'external-gallery',
		menu: 'default',
		title: 'wea',
		toolbar: true,
		router: null,
		content: true
	}
	initialize: =>
		super

class GalleriesModel extends Backbone.Model
	fetchGalleries: =>
		promise = wp.ajax.post(@attributes.ajax_action, {data: @attributes.ajax_data})
		promise.done (response)=>
			if response.length > 0
				@set('images', response)
				@trigger('load')
			else
				@trigger 'none'
		promise.fail (data)=> console.info(data) if console
class GalleriesView extends wp.media.View
	tagName: 'ul',
	id: 'select-galleries'
	initialize: =>
		super
		@model = new GalleriesModel(ajax_action: @options.ajax_action, selected: @options.value, ajax_data: @options.ajax_data)
		@model.on('load', @buildTheList)
		@model.on('none', @showNone)
		@model.fetchGalleries()
	buildTheList: =>
		for image in @model.get('images')
			view = new GalleryImageView(model: new ImageModel(image))
			@views.add(view)
			view.model.on('change:selected', @modelSelected)
			view.on 'close', => @trigger('close')
			if view.model.get('id') == @model.get('selected')
				view.model.set('selected', true)
				# Scroll to the selected item
				$('.media-frame-content').scrollTop($(view.el).offset().top)
	showNone: =>
		$(@el).append($('<h3 class="asg-no-galleries">No items found</h3>'))
	modelSelected: (image)=>
		if image.get('selected')
			@model.set('selected', image)
			for view in @views.get()
				view.model.set('selected', false) if view.model != image

class ImageModel extends Backbone.Model
		initialize: =>
			super
		getData: => {
			buttons: {check: true, close: false},
			can: {save: true},
			type: 'image',
			size: {url: @get('cover')}
			describe: true
			image: {src: @get('cover')}
			caption: @get('title')
		}
class GalleryImageView extends wp.media.View
	tagName:   'li',
	className: 'attachment',
	template:  wp.media.template('attachment')
	events: {
		'click .attachment-preview' : 'select',
		'dblclick .attachment-preview': 'forceSelect'
	}
	initialize: =>
		super
		@model.on 'change:selected', (value)=>
			if value.get('selected')
				@select()
			else
				@unselect()
	render: =>
		html = @template(@model.getData())
		@$el.html(html)
	unselect: =>
		@$el.removeClass('selected')
	select: =>
		@$el.addClass('selected')
		@model.set('selected', true) unless @model.get('selected')
	forceSelect: =>
		@select()
		@trigger('close')

class ExternalGalleryFrame extends wp.media.view.MediaFrame
	class Toolbar extends wp.media.view.Toolbar
		initialize: =>
			@options.items = _.defaults( @options.items || {}, {
				select: {
					style:    'primary',
					text:     @options.text,
					priority: 80,
					click:    @clickSelect,
					requires: @options.requires
				}
			})
			super
		clickSelect: => @trigger('selected')
	initialize: =>
		_.defaults( this.options, {
			modal:    true,
			uploader: false
		});
		super
		@on 'toolbar:create', (t)=>
			t.view = new Toolbar(controller: this, text: @options.title)
			t.view.on 'selected', @commitAndClose
		@on 'content:create', (t)=>
			t.view = new GalleriesView(controller: this, ajax_action: @options.ajax_action, ajax_data: @options.ajax_data, value: @options.value)
			t.view.model.on 'change:selected', (test) => @state().set('selectedInFrame', test.get('selected'))
			t.view.on 'close', => @close()
		@on 'close', @commitAndClose

	commitAndClose: =>
		@state().set('selected', @state().get('selectedInFrame')) if @state().get('selectedInFrame')
		@close()





window.asg.ExternalGallerySelector = ExternalGallerySelector
