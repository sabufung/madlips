window.asg ||= {}
rivets.configure({
	adapter: {
		subscribe: (obj, keypath, callback) ->
			callback.wrapped = (m, v)-> callback(v)
			obj.on('change:' + keypath, callback.wrapped);
		,
		unsubscribe: (obj, keypath, callback)->
			obj.off('change:' + keypath, callback.wrapped);
		,
		read: (obj, keypath)->
			return obj.get(keypath);
		,
		publish: (obj, keypath, value)->
			obj.set(keypath, value);
	},
	preloadData: false
})

jQuery ($)->
	$('#publish').removeAttr('disabled').removeClass('button-primary-disabled')
	$('#preview').click -> Preview.show()

	class SourceTabsController extends wp.media.View
		el: $('#sources-tabs')
		events: {
			'click a.nav-tab': 'changeTab'
		}
		initialize: =>
			super
			currentEditor = @createEditor($('#current-source').val())
		changeTab: (event)=>
			event.preventDefault()
			slug = $(event.target).attr('href').replace('#', '')
			$.each window.asgSourceEditors, (name, editor) -> editor.hide();
			if (window.asgSourceEditors[slug])
				window.asgSourceEditors[slug].show();
			else
				@createEditor(slug).show();
			$('.nav-tab-wrapper a').removeClass('nav-tab-active');
			$(event.target).addClass('nav-tab-active');
			$('#current-source').val(slug)
		createEditor: (slug)->
			window.asgSourceEditors[slug] = new window.asgRegisteredSourceEditors[slug]($('#source-' + slug + '-settings'))
	class OverlayView extends wp.media.View
		el: $('#asg-overlay')
		initialize: =>
			super
			new asg.ImageSelector(el: $('#asg-image-overlay').find('.image-selector'))
	class LayoutOptionsView extends wp.media.View
		el: $('#asg-layout')
		events:
			'change #asg-layout-mode': 'updateVisibility'
		initialize: =>
			super
			@width = @$('#asg-image-width')
			@height = @$('#asg-image-height')
			@select = @$('#asg-layout-mode')
			@hanging = @$('#asg-layout-hanging')
			@updateVisibility()
		updateVisibility: =>
			switch @select.val()
				when 'horizontal-flow' then @width.fadeOut('fast');  @height.fadeIn('fast'); @width.removeClass('last'); @hanging.show()
				when 'vertical-flow' then @width.fadeIn('fast'); @height.fadeOut('fast'); @width.addClass('last'); @hanging.hide()
				when 'usual' then @width.fadeIn('fast'); @height.fadeIn('fast'); @width.removeClass('last'); @hanging.show()
	class LoadMoreView extends wp.media.View
		el: $('#asg-load-more')
		events:
			'change select': 'updateVisibility'

		initialize: =>
			super
			@loadMoreMode = @$('#asg-load-more-mode')
			@select = @$('select')
			@perPage = @$('#load-more-per-page')
			@updateVisibility()
		updateVisibility: =>
			if @select.val() == 'load-more'
				@loadMoreMode.fadeIn('fast')
				@perPage.removeClass('last')
			else
				@loadMoreMode.fadeOut('fast')
				@perPage.addClass('last')
	class CaptionView extends wp.media.View
		el: $('#asg-image-caption')
		events:
			'change select[role=font]': 'selectStyles'
		initialize: =>
			super
			for font in window.asgGoogleFonts.items
				@$el.find('select[role=font]').append($('<option/ >').attr('value', font.family).text(font.family))
			for item in @$el.find('select[role=font]')
				item = $(item)
				item.val(item.attr('data-font')) if item.attr('data-font')
			@selectStyles()
		selectStyles: (event) =>
			if event
				subject = $(event.target)
			else
				subject = @$el.find('select[role=font]')
			for item in subject
				item = $(item)
				style = item.parent().find('select[role=style]')
				defaultText = style.find('option:first-child').text()
				style.empty()
				fontFound = false
				style.append($('<option />').attr('value', '').text(defaultText))
				for font in window.asgGoogleFonts.items
					if font.family == item.val()
						fontFound = true
						for variant in font.variants
							style.append($('<option />').attr('value', variant).text(variant))
						style.val(style.attr('data-font'))
				unless fontFound
					style.append($('<option value="regular">Regular</option>'))
					style.append($('<option value="light">Light</option>'))
					style.append($('<option value="bold">Bold</option>'))
					style.append($('<option value="italic">Italic</option>'))
					style.find('option[value="' + style.attr('data-font') + '"]').attr('selected', 'selected')
	class CategoriesView extends wp.media.View
		initialize: (params)=>
			super params
			@links = @$el.find('.asg-tabs li a')
			@panels = @$el.find('.asg-panels li')
			@links.on 'click', @onLinkClick
			@panels.eq(0).addClass('asg-current')
			@links.eq(0).parent().addClass('asg-current')

		onLinkClick: (event)=>
			event.preventDefault()
			@panels.removeClass('asg-current')
			index = @links.index(event.target)
			@panels.eq(index).addClass('asg-current')
			@links.parent().removeClass('asg-current')
			$(event.target).parent().addClass('asg-current')
	new SourceTabsController()
	new LayoutOptionsView()
	new LoadMoreView()
	new OverlayView()
	new CaptionView()
	new CategoriesView(el: $('#asg-custom-css'))
	new CategoriesView(el: $('#asg-image'))

	$('#post').submit -> $('#asg-hack').val($('#post').serialize());
