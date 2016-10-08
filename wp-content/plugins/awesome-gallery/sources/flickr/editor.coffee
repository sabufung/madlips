jQuery ($)->

	class FlickrSettings extends Backbone.Model

	class CopyKeysView extends Backbone.View
		el: $('#flickr-copy-keys-block')
		initialize: (params)=>
			super params
			rivets.bind(@el, model: @model).publish()

	class CheckKeysView extends Backbone.View
		el: $("#flickr-check-keys-block")
		events: {
			'click #flickr-check-keys': 'checkKeys'
		}
		initialize: =>
			super
			@spinner = @$('.spinner')
		checkKeys: =>
			@spinner.css('display', 'inline-block')
			$.post 'admin-ajax.php?action=asg-flickr-ping', @model.toJSON(), (response)=>
				@spinner.css('display', 'none')
				alert(response)
			false
	class CheckUserView extends Backbone.View
		el: $('#flickr-check-user-block')
		events: {
			'click #flickr-check-user': 'checkUser'
		}
		initialize: =>
			super
			@spinner = @$('.spinner')
			rivets.bind(@el, model: @model).publish()
		checkUser: =>
			@spinner.css('display', 'inline-block')
			$.post 'admin-ajax.php?action=asg-flickr-ping-user', @model.toJSON(), (response)=>
				@spinner.css('display', 'none')
				alert(response)
			false
	class PhotosetView extends Backbone.View
		el: $('#flickr-select-gallery')
		events: {
			'click #flickr-preview': 'preview'
			'click #flickr-select-photoset': 'selectPhotoset'
			'click #flickr-select-group': 'selectGroup'
		}
		initialize: =>
			super
			rivets.bind(@el, model: @model)
			@$select_group = $('#flickr-select-group')
			@$select_photoset = $('#flickr-select-photoset')

			@$flickr_source_type = $('#flickr-source-type')
			@$flickr_source = $('#flickr-source')
			@$flickr_source_name = $('#flickr-source-name')
			@$flickr_source_name_input = $('#flickr-source-name-input')
			@$flickr_source_name_label = $('#flickr-current-source-label')

			@photoset_selector = new window.asg.ExternalGallerySelector
			@group_selector = new window.asg.ExternalGallerySelector
			@$flickr_source_type.change @updateSelectorButtons
			@updateSelectorButtons(true)
		selectPhotoset: (event)=>
			event.preventDefault()
			@photoset_selector.select(ajax_action: 'asg-flickr-get-photosets', value: @$flickr_source.val(), ajax_data: @model.attributes, title: 'Select photoset').done( (val)=>
				@$flickr_source.val(val.id);
				@$flickr_source_name.text(val.get('title'))
				@$flickr_source_name_input.val(val.get('title'))
				)
			false
		selectGroup: (event)=>
			event.preventDefault()
			@group_selector.select(ajax_action: 'asg-flickr-get-groups', value: @$flickr_source.val(), ajax_data: @model.attributes, title: 'Select group').done( (val)=>
				@$flickr_source.val(val.id);
				@$flickr_source_name.text(val.get('title'))
				@$flickr_source_name_input.val(val.get('title'))
				)
			false
		updateSelectorButtons: (init)=>
			@$select_photoset.hide()
			@$select_group.hide()
			@$select_photoset.show() and @$flickr_source_name.show() if @$flickr_source_type.val() == 'photoset'
			@$select_group.show() and @$flickr_source_name.show() if @$flickr_source_type.val() == 'group'
			@$flickr_source_name.text('')
			if @$flickr_source_type.val() == 'photoset' or @$flickr_source_type.val() == 'group'
				@$flickr_source_name_label.show()
				if (!init)
					@$flickr_source_name.text('Please select').show()
					@$flickr_source_name_input.val('')
				else
					@$flickr_source_name.text(@$flickr_source_name_input.val())
				if @$flickr_source_type.val() == 'group'
					@$flickr_source.show()
			else
				@$flickr_source.hide()
				@$flickr_source_name.hide()
				@$flickr_source_name_label.hide()
				@$flickr_source_name_input.val('')
		preview: =>
			Preview.show()

	class FlickrEditor extends window.asgSourceEditor
		constructor: (view) ->
			super view
			model = new FlickrSettings
			model.set('id', $('#post_ID').val())
			new CopyKeysView(model: model)
			new CheckKeysView(model: model)
			new CheckUserView(model: model)
			new PhotosetView(model: model)
			new asgSettingsView(model: model, el: $('#flickr-settings-block'))

	window.asgRegisteredSourceEditors.flickr = FlickrEditor
