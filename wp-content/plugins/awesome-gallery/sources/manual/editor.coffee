jQuery ->
	$ = jQuery
	class ManualEditor extends window.asgSourceEditor
		$images = null
		$view = null
		$template = null
		constructor: (view) ->
			super view
			$images = $('#manual-images')
			$view = $(view)
			$template = $('#manual-source-image-template')
			$images.find('li').each (index, li) ->
				new ImageEditor($(li));
			$('#add-new-image').removeAttr('disabled').click =>
				html = $($template.html());
				title = "Image " + ($images.find('li').size() + 1);
				html.find('input.title').val(title);
				html.find('h3 .heading').text(title);
				$images.append(html);

				new ImageEditor(html);
				renameInputs();
				false;
			$('ul#manual-images').sortable({update: renameInputs})
			renameInputs()
		renameInputs = ->
			$images.find('>li').each (index, cell) ->
				$(cell).find('*:input').each (inputIndex, input) ->
					name = $(input).attr('name');
					if (name)
						$(input).attr('name', name.replace(/\[images\]\[\d*\]/, "[images][" + index + "]"))


		class ImageEditor
			constructor: (view) ->
				@$view = $(view)
				@$content = @$view.find('>.content');
				@$remove = @$view.find('a.cell-delete');
				@$cancel = @$view.find('a.cell-cancel');
				@$title = @$view.find('input.title')
				@$description = @$view.find('textarea.description')
				@$lightboxTitle = @$view.find('input.asg-lightbox-title')
				@$lightboxDescription = @$view.find('textarea.asg-lightbox-description')
				@$view.find('label.huge :checkbox').each( (index, el) =>
					if (!$(el).is(':checked'))
						$(el).parent().parent().find('.column-1, .columns-2').hide();
				).click( ->
					$(this).parent().parent().find('.column-1, .columns-2').toggle();
				)
				@mainImageSelector = new asg.ImageSelector(el: @$view.find('.asg-manual-main-image'))
				@mainImageSelector.on 'changed:selection', (event)=>
					unless @$title.val() and @$title.val() != ''
						@$title.val(event.attributes.title)
					unless @$description.val() && @$description.val() != ''
						@$description.val(event.attributes.caption)
				@lightboxImageSelector = new asg.ImageSelector(el: @$view.find('.asg-manual-lightbox-image'))
				@lightboxImageSelector.on 'changed:selection', (event)=>
					unless @$lightboxTitle.val() and @$lightboxTitle.val() != ''
						@$lightboxTitle.val(event.attributes.title)
					unless @$lightboxDescription.val() && @$lightboxDescription.val() != ''
						@$lightboxDescription.val(event.attributes.caption)

				@$view.find('h3').on('click', this.toggle);
				@$cancel.click @toggle
				@$remove.click @remove
				@$title.keyup (event) =>
					text = $(event.target).val();
					if (text)
						@$view.find('h3 .heading').text(text);
					else
						i = -1
						$('#manual-images > li').each (index, element) ->
							if (element == $(event.target).closest('li')[0])
								i = index + 1;
						@$view.find('h3 .heading').html("Image " + i);

			toggle: (callback) =>
				@$view.toggleClass('expanded');
				@$content.toggle(callback);
				false
			remove: =>
				@$view.remove();
				renameInputs();
				false
	window.asgRegisteredSourceEditors.manual = ManualEditor
