jQuery ($)->
	class FilterView extends wp.media.View
		initialize: (params)=>
			super params
			@template = params.template
			@addButton = @$el.find('button.add')
			@addButton.on('click', @onAddClick)
			@list = @$el.find('ul')
			@$el.find('button.remove').on 'click', ->  $(this).closest('li').remove(); false
		onAddClick: (event)=>
			event.preventDefault()
			@template.clone().appendTo(@list).find('button').on 'click', -> $(this).closest('li').remove(); false


	class FilteringView extends wp.media.View
		el: $('#post-settings')
	window.asgRegisteredSourceEditors.posts = class PostsEditor extends window.asgSourceEditor
		constructor: (editor)->
			super editor
			new FilterView(el: $('#posts-taxonomy-filters'), template: $('#posts-taxonomy-filter-template li'))
			new FilterView(el: $('#posts-custom-field-filters'), template: $('#posts-custom-field-filter-template li'))


