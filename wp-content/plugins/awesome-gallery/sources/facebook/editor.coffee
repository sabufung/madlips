jQuery ($)->
	class FacebookSettings extends Backbone.Model
		validate: =>
			if !@get('app_id')
				return 'Please enter app ID'
			if !@get('app_secret')
				return 'Please enter app secret'
			null

	class CopyKeysView extends Backbone.View
		el: $('#facebook-copy-keys-block')
		initialize: (params)->
			super params
			@binding = rivets.bind(@el, model: @model)
			@binding.publish()
			@$el.find('#facebook-id').on('keyup', => @binding.publish())

	class AuthenticateView extends Backbone.View
		el: $('#facebook-authenticate-block')
		events:
			'click #facebook-authenticate': 'authenticate'
		initialize: (params)=>
			super params
			@redirect_url = @$el.attr('data-redirect-url')
			@$el.removeAttr('data-redirect-url')
			rivets.bind(@el, model: @model).publish()
			@listenTo(@model, 'change', @onModelChanged)

			@onModelChanged()
		onModelChanged: =>
			if @model.get('app_id') && @model.get('app_id') != '' and @model.get('app_secret') and @model.get('app_secret') != ''
				@$el.find('.auth-url').text(@getAuthUrl2())
			else @$el.find('.auth-url').text('Please enter APP ID and APP SECRET')
		getAuthUrl: =>
			"https://www.facebook.com/dialog/oauth?client_id=#{@model.get('app_id')}&redirect_uri=#{@redirect_url}&state=#{@model.get('app_id')}|#{@model.get('app_secret')}&scope=user_photos"
		getAuthUrl2: =>
			"https://www.facebook.com/dialog/oauth?client_id=#{@model.get('app_id')}&redirect_uri=#{@$el.attr('friend-redirect-url')}&state=#{@model.get('app_id')}|#{@model.get('app_secret')}&scope=user_photos"

		authenticate: (event)=>
			event.preventDefault()
			window.open @getAuthUrl()
	class CopyOAuthBlock extends Backbone.View
		el: $('#faceboook-oauth-block')
		initialize: =>
			super
			@listenTo @model, 'change:app_id', =>
				$('#facebook-oauth-block .button-hero').attr('href', "https://developers.facebook.com/apps/#{@model.get('app_id')}/advanced?ref=nav")
	class CheckAccessTokenView extends Backbone.View
		el: $('#facebook-check-access-token-block')
		events:
			'click #facebook-check-access-token': 'checkToken'
		initialize: =>
			super
			@spinner = @$('.spinner')
		checkToken: =>
			if !@model.isValid()
				alert @model.validate
				return
			@spinner.css('display', 'inline-block')
			$.post 'admin-ajax.php?action=asg-facebook-check-access-token', @model.toJSON(), (response)=>
				@spinner.css('display', 'none')
				alert(response)
			false

	class CheckUserView extends Backbone.View
		el: $('#facebook-check-user-block')
		events: {
			'click #facebook-check-user': 'checkUser'
		}
		initialize: =>
			super
			rivets.bind(@el, model: @model).publish()
			@spinner = @$('.spinner')
		checkUser: =>
			if !@model.isValid()
				alert @model.validate
				return

			@spinner.css('display', 'inline-block')
			$.post 'admin-ajax.php?action=asg-facebook-ping-user', @model.toJSON(), (response)=>
				@spinner.css('display', 'none')
				alert(response)
			false


	class AlbumView extends Backbone.View
		el: $('#facebook-select-album-block')
		events: {
			'click #facebook-preview': 'preview',
			'click #facebook-select-album': 'selectAlbum',
			'change #facebook-select-source-type': 'updateSelectorButtons'
		}
		initialize: =>
			super
			rivets.bind(@el, model: @model).publish()
			@$select_album = $('#facebook-select-album')

			@$facebook_source_type = $('#facebook-select-source-type')
			@$facebook_source = $('#facebook-source')
			@$facebook_source_name = $('#facebook-source-name')
			@$facebook_source_name_input = $('#facebook-source-name-input')
			@$facebook_source_name_label = $('#facebook-current-source-label')
			@album_selector = new window.asg.ExternalGallerySelector
			@updateSelectorButtons(true)
		selectAlbum: =>
			@album_selector.select(ajax_action: 'asg-facebook-get-albums', value: @model.get('source'), ajax_data: @model.toJSON(), title: 'Select Album').done( (val)=>
				@model.set('source', val.id);
				@model.set('source_name', val.get('title'))
				)
			false
		updateSelectorButtons: (init)=>
			@model.set('source_type', @$facebook_source_type.val())
			@$select_album.hide()
			@$select_album.show() and @$facebook_source_name.show() if @model.get('source_type') == 'album'
			if @model.get('source_type') == 'album'
				@$facebook_source_name_label.show()
				@$facebook_source_name.show()
				if (!init)
					@$facebook_source_name.text('Please select').show()
					@$facebook_source_name_input.val('')
			else
				@$facebook_source_name.hide()
				@$facebook_source_name_label.hide()
				@$facebook_source_name_input.val('')
		preview: =>
			Preview.show()

	class FacebookEditor extends window.asgSourceEditor
		constructor: ->
			super
			@model = new FacebookSettings
			@model.set('id', $('#post_ID').val())
			new CopyKeysView(model: @model)
			new AuthenticateView(model: @model)
			new CheckAccessTokenView(model: @model)
			new CheckUserView(model: @model)
			new asgSettingsView(model: @model, el: $('#facebook-settings-block'))
			new AlbumView(model: @model)
			new CopyOAuthBlock(model: @model)
	window.asgRegisteredSourceEditors.facebook = FacebookEditor
