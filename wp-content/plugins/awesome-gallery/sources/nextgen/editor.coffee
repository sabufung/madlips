jQuery ($)->
	class ChooseGalleryView extends Backbone.View
		initialize: (params) ->
			super params
			@selectGalleryButton = $('#nextgen-select-gallery')
			@resetGalleryButton = $('#nextgen-reset-gallery')
			@selectGalleryButton.on('click', @onSelectClick)
			@resetGalleryButton.on('click', @onResetClick)
			@galleryInput = @$el.find('#nextgen-gallery')
			@galleryNameInput = @$el.find('#nextgen-gallery-name')
			@gallerySelector = new window.asg.ExternalGallerySelector
		onResetClick: (event)=>
			event.preventDefault()
			@model.set('gallery', '')
			@model.set('gallery_name', '')
		onSelectClick: (event)=>
			event.preventDefault()
			@gallerySelector.select(ajax_action: 'asg-nextgen-get-galleries', value: @galleryInput.val(), ajax_data: @model.attributes, title: 'Select gallery').done( (val)=>
				@model.set('gallery', val.id)
				@model.set('gallery_name', val.get('title'))
				)
			false
	class NextgenEditor extends window.asgSourceEditor
		constructor: (editor)->
			super editor
			@model = new Backbone.Model()
			rivets.bind(editor, {model: @model}).publish()
			new ChooseGalleryView(el: $('#nextgen-select-gallery-block'), model: @model)
			$('#nextgen-settings-block .button').on('click', => Preview.show())



	window.asgRegisteredSourceEditors.nextgen = NextgenEditor

