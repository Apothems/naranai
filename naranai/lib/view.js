window.addEvent('domready', function(){
		
		$('edit').setStyle('display', 'none');
							
		$('editclick').addEvent('click', function(){
			$('edit').style.display = ($('edit').style.display == 'none') ? 'block' : 'none';
		});
																		  
							
		new FormCheck('comment_form', {
			display : {
				errorsLocation : 1,
				indicateErrors : 2
			}
		});
		
		new FormCheck('tagform', {
			display : {
				errorsLocation : 1,
				indicateErrors : 2
			}
		});
							
		// init
		var tlist2 = new FacebookList('img_tags', 'taglist');
							  
		// fetch and feed
		new Request.JSON({'url': base_url + '/tag_list.php', 'onComplete': function(j) {
			j.each(tlist2.autoFeed, tlist2);
		}}).send();
						
		$('tagform').addEvent('submit', function(){
			tlist2.update();
			this.action = base_url + "/save";
		});
		$('add_note').addEvent('click', function(e) {
			
			scale($('main_image'), 1);
			
			note_id = note_id + 1
			
			new Event(e).stop;
			
			var main = new Element('div', {
				'id': 'new_note_' + note_id,
				'class': 'image_note'
			});
			
			main.inject('image_holder', 'top');
			
			var added = new Element('div', {
				'id': 'new_drag_' + note_id,
				'class': 'drag'
			});
			
			added.inject(main, 'top');
			
			var sizer = new Element('div', {
				'class': 'resize',
			});
			
			sizer.inject(added);
			
			var dragger = main.makeDraggable({
				handle: added,
				container: 'image_holder'
			});

			
			//Make the element resizable. Resizing starts on the element with class = resize
			main.makeResizable({
				handle: added.getElement('.resize')
			});
			
			added.getElement('.resize').addEvent('mousedown', function(e) {
				dragger.detach();		   
			});
			
			added.getElement('.resize').addEvent('mouseup', function(e) {
				dragger.attach();		   
			});
			
			var spacer = new Element('div', {
				'class': 'tip_space'				
			});
			
			spacer.inject(main);
			
			var tip = new Element('div', {
				'id': 'new_tip_' + note_id,
				'title': 'Click to edit.',
				'class': 'tip',
				'style': 'width: 175px; height: 125px;'
			});
			
			tip.inject(main);
			tip.hide();
			
			main.addEvent('mouseover', function(e) {
				tip.show();		   
			});
			
			main.addEvent('mouseout', function(e) {
				tip.hide();
			});
			
			tip.addEvent('click', function(e) {
				y = tip.getPosition($('image_holder')).y;
				if(y > 300)
				{
					newtop = y - 15;
					$('note-holder').setStyle('top', newtop + 'px');
				}
				$('note-holder').show();
				$('note_id').value = note_id;
				$('note_new').value = 'true';
			});
			
		});
		
		$('note_cancel').addEvent('click', function(e) {
			$('note-holder').hide();
		});
		
		$('note_save').addEvent('click', function(e) {
			id = $('note_id').value;
			initial = $('note_new').value;
			var bacon;
			if(initial == 'true')
			{
				bacon = 'new_';	
			}
			thing = $(bacon + 'drag_' + id);
			main = $(bacon + 'note_' + id);
			tip = $(bacon + 'tip_' + id);
			x = thing.getPosition($('image_holder')).x;
			y = thing.getPosition($('image_holder')).y;
			width = thing.getSize().x;
			height = thing.getSize().y;
			user_id = $('note_user_id').value;
			text = $('note_text').value;
			image_id = $('note_image_id').value;
			var saver = new Request.HTML({
				url: base_url + '/note/save',
				method: 'post',
				update: tip
			});
			saver.send('x=' + x + '&y=' + y + '&width=' + width + '&height=' + height + '&text=' + text + '&new=' + initial + '&id=' + id + '&user_id=' + user_id + '&image_id=' + image_id);
			$('note_text').value = '';
			$('note-holder').hide();
			tip.removeEvents();
			main.removeEvents();
			thing.removeEvents();
			tip.setStyle('cursor', 'default');
			tip.setStyle('height', 'auto');
			tip.setStyle('width', 'auto');
			main.setStyle('cursor', 'default');
			main.addEvent('mouseover', function(e) {
				tip.show();		   
			});
			
			main.addEvent('mouseout', function(e) {
				tip.hide();
			});
		});
	
});

function scale(img, up) 
{
 var size = holder.parentNode.getSize();
 if(orig_width >= size.x * 0.9) 
 {
 	if(holder.style.width != "90%") 
	{
		holder.style.width = "90%";
		img.style.width = "90%";
		$('alert').innerHTML = "Note: Image has been scaled to fit the screen; click to enlarge";	
		$('alert').style.display = "block";
	 	$$('.image_note').each(function (e){
			e.hide();								 
		});
	 }
	 else 
	 {
		 holder.style.width = orig_width + 'px';
		 img.style.width = orig_width + 'px';
		 $('alert').style.display = "none";
		 $('alert').innerHTML = "";
		 $$('.image_note').each(function (e){
			e.show();								 
		});
	 }
 }

 if(up == 1)
 {
	 $$('.image_note').each(function (e){
		e.show();								 
	 });
	 holder.style.width = orig_width + 'px';
	 img.style.width = orig_width + 'px';
	 $('alert').style.display = "none";
	 $('alert').innerHTML = "";
 }
}