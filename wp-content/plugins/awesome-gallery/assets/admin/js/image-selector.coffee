window.asg ||= {}
$ = jQuery
class ImageSelector extends wp.media.View
	events:
		'click button.select-image': 'onImageSelectClicked',
		'click a.image-delete': 'onImageDeleteClicked'
	initialize: =>
		super
		@$input = @$el.find('input')
		@$image = @$el.find('img')

		@$el.hover( =>
			if(@$el.find('img').size() > 0)
				@$el.find('.actions-wrapper, .overlay').fadeIn('fast');
		, =>
			if (@$el.find('img').size() > 0)
				@$el.find('.actions-wrapper, .overlay').fadeOut('fast');
		)
		if (@$el.find('img').size() > 0)
			@$el.find('.actions-wrapper, .overlay').fadeOut('fast');

	onImageSelectClicked: (event)=>
		selector = this
		event.preventDefault()
		id = selector.$input.val()
		flow =  wp.media({
			title: "Select an image",
			library: {type: 'image'},
			button: { text: "Select Image"},
			multiple: false
		}).open()
		state = flow.state()
		if ( '' != id && -1 != id )
			state.get('selection').reset( [wp.media.model.Attachment.get( id )])
		state.set('display', false)

		state.on('select', (el) ->
			selection = this.get('selection').single()
			selector.setImageField(selection.id)
			selector.loadNewImage(selection.get('url'))
			selector.trigger('changed:selection', selection)
		);

	onImageDeleteClicked: (event)=>
		event.preventDefault()
		@$el.find('img').remove();
		@$input.val('');
		@$el.find('.actions-wrapper, .overlay').fadeIn('fast');
		@$el.find('.image-delete, .overlay').fadeOut('fast');


	setImageField: (selection) => @$input.val(selection)

	loadNewImage: (url) =>
		img = @$el.find('img')
		if (img.size() == 0)
			img = $('<img />').prependTo(@$el)
		img.attr('src', url);
		@$el.find('.image-delete').fadeIn('fast');
		@$el.find('.actions-wrapper, .overlay').fadeOut('fast');
		false
window.asg.ImageSelector = ImageSelector