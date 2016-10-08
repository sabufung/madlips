$ = jQuery
class PreviewView extends wp.media.View
	id: 'asg-preview'
	initialize: =>
		$.post 'admin-ajax.php?action=asg-preview', {data: $('#post').serialize()}, (response)=>
			@$el.html(response)
			@data = @$('.asg').data('awesome-gallery')
	render: =>
		super
		@$el.append($('<div class="asg-spinner-large"></div>').show())
	remove: =>
		@data.dispose() if @data
		super
		
class Toolbar extends wp.media.view.Toolbar
	initialize: =>
		@options.items = _.defaults( @options.items || {}, {
			select: {
				style:    'primary',
				text:     'Close',
				priority: 80,
				click:    @close,
				requires: @options.requires
			}
		})
		super
	close: => 
		@controller.close()
		false
class PreviewFrame extends wp.media.view.MediaFrame
	initialize: =>
		_.defaults( this.options, {
			modal:    true,
			uploader: false,
		});
		super
		@on 'toolbar:create', (t)=> t.view = new Toolbar(controller: this)
		@on 'content:create', (t)=>  t.view = new PreviewView(controller: this)
		@on 'close', => @content.view.remove()
window.Preview = {
	show: (data)=>
		new PreviewFrame(state: 'preview', states: [new wp.media.controller.State({
			id: 'preview',
			menu: 'default',
			toolbar: true,
			router: null,
			content: true,
			title: 'Preview'
		})]).open(data)
}