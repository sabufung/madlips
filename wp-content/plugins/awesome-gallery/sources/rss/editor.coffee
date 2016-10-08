jQuery ($)->
	class URLView extends Backbone.View
		el: $('#rss-url-block')
		initialize: ->
			super
			@$el.find('.button-hero').on('click', @onButtonClicked)
			rivets.bind(@el, {model: @model}).publish()
		onButtonClicked: =>
			@$el.find('.spinner').show()
			$.post 'admin-ajax.php?action=asg-rss-check-url', @model.toJSON(), (response)=>
				@$el.find('.spinner').hide()
				alert(response)

	class RSSEditor extends window.asgSourceEditor
		constructor: (editor)->
			super editor
			$('#rss-settings-block .button').on('click', @onPreviewClick)
			@model = new Backbone.Model()
			new asgSettingsView(model: @model, el: $('#rss-settings-block'))
			new URLView(model: @model)
		onPreviewClick: => Preview.show()
	window.asgRegisteredSourceEditors.rss = RSSEditor

