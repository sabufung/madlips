class SourceEditor
		constructor: ($editor) ->
			@editor = $editor
		show: =>
			jQuery.each(window.asgSourceEditors, (name, editor)-> editor.hide())
			@editor.show()
		hide: =>
			@editor.hide()
	window.asgSourceEditors = {}
	window.asgRegisteredSourceEditors = {}
	window.asgSourceEditor = SourceEditor
class SettingsView extends Backbone.View
	events:
		'click .button-hero': 'preview'
	initialize: =>
		@$lightbox_options = @$('.lightbox-options')
		@listenTo @model, 'change:link', => @refreshLightboxVisibility()
		rivets.bind(@el, model: @model).publish()
	refreshLightboxVisibility: =>
		if @model.get('link') == 'lightbox'
			@$lightbox_options.show()
		else
			@$lightbox_options.hide()
	preview: =>
		Preview.show()
window.asgSettingsView = SettingsView





