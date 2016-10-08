jQuery ($)->
	class Px500Model extends Backbone.Model
		validate: =>
			if !@get('consumer_key')
				return 'Please enter consumer key'
			false

	class Px500CopyKeys extends Backbone.View
		el: $('#500px-copy-keys-block')
		initialize: (params)=>
			super params
			rivets.bind(@el, model: @model).publish()

	class Px500CheckKeys extends Backbone.View
		el: $('#500px-check-keys-block')
		events:
			'click .button-hero': 'checkKeys'
		initialize: =>
			super
			@spinner = @$('.spinner')
		checkKeys: (event)=>
			event.preventDefault()
			if !@model.isValid()
				alert @model.validate()
				return
			@spinner.css('display', 'inline-block')
			$.post 'admin-ajax.php?action=asg-500px-check-keys', @model.toJSON(), (response)=>
				@spinner.css('display', 'none')
				alert(response)

	class Px500Settings extends Backbone.View
		el: $('#500px-data-block')
		events:
			'click #500px-select-collection': 'selectCollection'
		initialize: =>
			super
			@collection_selector = new window.asg.ExternalGallerySelector
			@$user_options = @$('#500px-user-options')
			@$user_collection = @$('#500px-user-collection')
			@$authenticate = @$('#500px-oauth-authenticate')
			@$checkToken = @$('#500px-oauth-check-token')
			@$sorting = @$ '#asg-500px-sorting'
			@$category = @$ '#asg-500px-category'
			@$authenticate.click @onAuthenticateClicked
			@$checkToken.click @onCheckTokenClicked
			@listenTo(@model, 'change:source_type', @onSourceTypeChanged)
			@listenTo(@model, 'change:collection_name', @onCollectionNameChanged)
			rivets.bind(@el, model: @model).publish()
		onSourceTypeChanged: =>
			if @model.get('source_type') in ['user', 'user_collection', 'user_friends', 'user_favorites']
				@$user_options.show()
			else
				@$user_options.hide()
			if @model.get('source_type') in ['user_collection', 'user_favorites', 'user']
				@$user_collection.show()
				@$sorting.hide()
				@$category.hide()
			else
				@$user_collection.hide()
				@$sorting.show()
				@$category.show()
		onAuthenticateClicked: (event)=>
			event.preventDefault()
			window.open("admin.php?action=asg-500px-oauth-get-token&consumer_key=#{@model.get('consumer_key')}&consumer_secret=#{@model.get('consumer_secret')}")
		onCollectionNameChanged: =>

		onCheckTokenClicked: (event)=>
			event.preventDefault()
			$.post 'admin-ajax.php', {action: 'asg-500px-check-token', data: @model.toJSON()}, (response)=>
				alert(response)

		selectCollection: (event)=>
			event.preventDefault()
			@collection_selector.select(ajax_action: 'asg-500px-get-collections', value: @model.get('collection'), ajax_data: @model.toJSON(), title: 'Select a collection').done( (val)=>
				@model.set('collection', val.id)
				@model.set('collection_name', val.get('title'))
				)
			false

	class Px500Editor extends window.asgSourceEditor
		constructor: (view)->
			super view
			@model = new Px500Model()
			new Px500CopyKeys(model: @model)
			new Px500CheckKeys(model: @model)
			new Px500Settings(model: @model)

			new asgSettingsView(model: @model, el: $('#500px-settings-block'))
	window.asgRegisteredSourceEditors['500px'] = Px500Editor
