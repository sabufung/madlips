jQuery ->
	$ = jQuery
	class InstagramModel extends Backbone.Model
		validate: =>
			if (!@get('client_id'))
				return 'Please enter Instagram client ID'
			if !@get('client_secret')
				return 'Please enter Instagram client secret'
			if @get('feed_type') == 'other-user' and !@get('other_user_login')
				return 'Please enter other user\'s login'
	class CopyKeysView extends Backbone.View
		el: $('#instagram-copy-keys-block')
		initialize: (params)=>
			super params
			rivets.bind(@el, model: @model).publish()
			
	class AuthorizeView extends Backbone.View
		el: $('#instagram-authorize-block')
		events:
			'click #instagram-authorize': 'authorize'
		initialize: (params)=>
			super params
			@redirect_uri = $('#instagram-redirect-uri').text()
			rivets.bind(@el, model: @model).publish()
		authorize: (event)=>
			event.preventDefault()
			if error = @model.validate()
				alert(error)
				return
			href = "https://api.instagram.com/oauth/authorize/?client_id=#{@model.get('client_id')}&response_type=code&redirect_uri=#{encodeURIComponent(@redirect_uri)}"
			href= "admin.php?action=asg_instagram_save_client_data&" + $.param(@model.toJSON()) + "&redirect=" + encodeURIComponent(href)
			window.open(href)
	class CheckAuthView extends Backbone.View
		el: $('#instagram-check-auth-block')
		events:
			'click #instagram-check-auth': 'checkAuth'
		initialize: =>
			@$auth_spinner = @$('.spinner')
			@$auth_result = @$('#instagram-auth-result')
		checkAuth: (event)=>
			event.preventDefault() if event
			if error = @model.validate()
				alert(error)
				return
			@$auth_spinner.css('display', 'inline-block')
			data = @model.toJSON()
			$.post 'admin-ajax.php', {action: 'asg_instagram_ping', data: data}, (response)=>
				@$auth_spinner.css('display', 'none')
				if response == 'OK'
					@$auth_result.text('Valid').addClass('asg-valid').removeClass('asg-invalid')
				else
					@$auth_result.text('Invalid').addClass('asg-invalid').removeClass('asg-valid')
	class CheckDataView extends Backbone.View
		el: $('#instagram-check-data-block')
		events:
			'click #instagram-check-data': 'checkData'
		initialize: =>
			super
			@$data_spinner = @$('.spinner')
			@$other_user_wrapper = @$('#instagram-other-user-wrapper')
			@$hashtag_wrapper = @$('#instagram-hashtag-wrapper')
			@listenTo @model, 'change:feed_type', =>
				if @model.get('feed_type') == 'other-user'
					@$other_user_wrapper.show()
				else
					@$other_user_wrapper.hide()
				if @model.get('feed_type') == 'hashtag'
					@$hashtag_wrapper.show()
				else
					@$hashtag_wrapper.hide()
			rivets.bind(@el, model: @model).publish()

		checkData: (event)=>
			event.preventDefault()
			if error = @model.validate()
				alert(error)
				return
			@$data_spinner.css('display', 'inline-block')
			$.post 'admin-ajax.php', {action: 'asg_instagram_data_check', data: @model.toJSON()}, (response) =>
				@$data_spinner.css('display', 'none')
				alert(response)

	class InstagramEditor extends window.asgSourceEditor
		constructor: (view) ->
			super view
			@model = new InstagramModel
			new CopyKeysView(model: @model)
			new AuthorizeView(model: @model)
			@check_auth = new CheckAuthView(model: @model)
			new CheckDataView(model: @model)
			new asgSettingsView(model: @model, el: $('#instagram-settings-block'))

			if @model.isValid()
				@check_auth.checkAuth()

		other_user_changed: =>

		link_mode_changed: =>

	window.asgRegisteredSourceEditors.instagram = InstagramEditor
