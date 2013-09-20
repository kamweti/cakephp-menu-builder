// Simple JavaScript Templating
// John Resig - http://ejohn.org/ - MIT Licensed
(function(){var cache={};this.tmpl=function tmpl(str,data){var fn=!/\W/.test(str)?cache[str]=cache[str]||tmpl(document.getElementById(str).innerHTML):new Function("obj","var p=[],print=function(){p.push.apply(p,arguments);};"+"with(obj){p.push('"+
str.replace(/[\r\t\n]/g," ").split("<%").join("\t").replace(/((^|%>)[^\t]*)'/g,"$1\r").replace(/\t=(.*?)%>/g,"',$1,'").split("\t").join("');").split("%>").join("p.push('").split("\r").join("\\'")
+"');}return p.join('');");return data?fn(data):fn;};})();


$(function(){

	//show actions for the selected controller
	$('.tab-pane .select_controller').on('change', function(){
		var _parent_tab_pane = $(this).parents('.tab-pane');

		var _selected_option = $(this).find('option:selected'),
				_actions_select_container = _parent_tab_pane.find('.select_action');

		actions = _selected_option.attr('actions');

		if( typeof(actions) === "undefined" ){
			return;
		} else {
			//we have an actions attribute parse the contents
			actions = $.parseJSON(actions);
		}

		var actions_options = "";
		$.each(actions,function(index,action){
			actions_options+='<option>'+action+'</option>';
		});

		//append list elements to select box
		_actions_select_container.find('> select')
			.empty()
			.append(actions_options)
			.parent()
			.hide()
			.fadeIn('fast');

	}).trigger('change');

	$('input[name="has_parent"]').change(function(){
		if($(this).val() == 'yes') {
			$('.specify_parent_container').show();
		} else {
			$('.specify_parent_container').hide();
		}
	});

	$('.menu_points_to_tabs a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	})

	// add new menu item
	$('.add_menu_item').click(function(){

		// find out the menu type
		var _selected_tab = $('.tab-pane:visible'),
				_href 	= '',
				_label  = $.trim($('.menu_label').val());

		if( _label.length === 0 ) {
			$('.menu_label').parent().addClass('error');
			return;
		} else {
			$('.menu_label').parent().removeClass('error');
		}

		if( _selected_tab.attr('id') == 'controller_action' || _selected_tab.attr('id') == 'plugin_action' ) {
			var _controller 		= _selected_tab.find('.select_controller option:selected').val().toLowerCase(),
					_action         = _selected_tab.find('.select_action option:selected').val().toLowerCase(),
					_href           = '/'+_controller +'/'+ _action;

		} else {
			_href = _selected_tab.find('.url').val(); // this is a custom url
		}

		var new_list_obj={
			label			: _label,
			href			: _href
		}

		if($('input[name="has_parent"]:checked').val() == 'yes'){
			var _parentid = $('.the_parent').val();
			var _target_parent = $('.menulistprvw').find('li[data-id='+_parentid+']');
			if( _target_parent.length ) {

				if( _target_parent.find(' > ul').length == 0 )
						_target_parent.append('<ul/>');

				_target_parent.find('ul').append(tmpl("menuitem_tmpl", new_list_obj));
			}
		} else {
			$('.menulistprvw > ul').append(tmpl("menuitem_tmpl", new_list_obj)); //get the new list template and render it
		}

		autosave();

		//reset the form, add the item to list of parents
		$('form.edit_menu').trigger('reset');

	});

	// delete menu item
	$('.menulistprvw').on('click', '.del', function(){
    var _message ='Are you sure you want to delete?';
    var _this = $(this);
    var _parent_li = $(this).parent().parent(); // find grandparent
    var _isparent = 0;

    if( _parent_li.find('ul').length ) {
      _isparent = 1;
      _message = 'Are you sure you want to delete? this will delete children for this menu as well';
    }
    if( window.confirm(_message) ){
    	_parent_li.remove();
    	autosave();
    }

		return false;
	});

	$('.menulistprvw ').on('click','.moveup', function(){

		var li_parent = $(this).parent().parent(),
				ul_parent =  li_parent.parent();

		if( li_parent.index() > 0 ){
			var previndex = li_parent.index() - 1,
				prevelem = ul_parent.find(' > li:eq('+previndex+')');

			li_parent.insertBefore(prevelem); //insert before item above it
		}


		return false;
	});



	/* save changes */
	function autosave(){

		var _create_menu_structure = function(_base){
			var _base_fn = arguments.callee;
			var structure = [];
			_base.each(function(index,elem){

				var _map = {
					'label' : $(elem).data('label'),
					'url' 	: $(elem).data('url'),
					'id' 		: $(elem).data('id')
				}

				if( $(elem).find('>ul').length > 0 ) {
					_map.children = _base_fn($(elem).find(' > ul > li'))
				}
				structure[structure.length] = _map;

			});
			return structure;
		};

		var menu_structure = _create_menu_structure($('.menulistprvw > ul > li'));

		//do ajax post and save menu
		$.post(
			'../ajax_save',
			{
				'items': menu_structure,
				'id' : $('.menu_id').val(),
				'name' : $('.menu_name').val()
			},function(response){
				$('.menu_name')
					.removeAttr('value')
					.attr('value', response.menu_name); //update the menu name
				$('.the_parent').empty().append(response.menu_options_list); //update the parent list
				$('.menulistprvw').empty().append(response.menu_ul_list); //update the ul list
			},
			'json'
			);
	}

});
