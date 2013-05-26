// Simple JavaScript Templating
// John Resig - http://ejohn.org/ - MIT Licensed
(function(){var cache={};this.tmpl=function tmpl(str,data){var fn=!/\W/.test(str)?cache[str]=cache[str]||tmpl(document.getElementById(str).innerHTML):new Function("obj","var p=[],print=function(){p.push.apply(p,arguments);};"+"with(obj){p.push('"+
str.replace(/[\r\t\n]/g," ").split("<%").join("\t").replace(/((^|%>)[^\t]*)'/g,"$1\r").replace(/\t=(.*?)%>/g,"',$1,'").split("\t").join("');").split("%>").join("p.push('").split("\r").join("\\'")
+"');}return p.join('');");return data?fn(data):fn;};})();


$(function(){


	$('.menulistprvw li').live({
		mouseenter:function(){
			$(this).find('.actions').show();
		},
		mouseleave:function(){
			$(this).find('.actions').hide();
		}
	});

	$('.menulistprvw .moveup').live('click',function(){

		var ul_parent=$('.menulistprvw'),
			li_parent=$(this).parents('li');

		if(li_parent.index()!=0){
			var previndex=li_parent.index()-1,
				prevelem=ul_parent.find('li:eq('+previndex+')');

			li_parent.insertBefore(prevelem); //insert before item above it
		}

		//li_index=

		return false;
	});

	$('.menulistprvw .del').live('click',function(){
		if(window.confirm('Are you sure')){
			$(this).parents('li').remove();
		}
		return false;
	});

	//show actions of selected controller
	$('.select_controller').change(function(){
		var option=$('.select_controller option[actions]:selected'),
			actions=option.attr('actions');

		if(typeof(actions)==="undefined"){
			return;
		}

		//parse json actions
		actions=$.parseJSON(actions);
		var actions_options="<option value='null' selected>none</option>";
		$.each(actions,function(index,action){
			actions_options+='<option>'+action+'</option>';
		});

		//append list elements to select box
		$('.select_action').empty().append(actions_options);

	});

	//add item to the menu
	$('.add_menu_item').click(function(){
		var _label=$('.menu_label').val(),
			_controller=$('.select_controller option:selected').val(),
			_action=$('.select_action option:selected').val(),
			_menuparent=$('.the_parent');

			if($.trim(_label).length==0) return; //return if not menu label entered

			/* reset if no controller is selected */
			if(_controller=='null'){
				_controller=''
			}
			if(_action=='null'){
				_action=''
			}




		//if is admin action ,prefix controller with admin
		var regex = new RegExp('^admin_'),
			_linkurl='';

		if(regex.test(_action)==true){
			// this is an admin action
			var _actioninurl=_action.replace(regex,''); //removes 'admin_' from action
			_linkurl	= '/admin/'+_controller+'/'+_actioninurl; //prefixes url with admin
		}else{
			_linkurl	= '/'+_controller+'/'+_action;
		}



		var new_list_tpl_obj={
			label		:_label,
			controller	:_controller,
			action		:_action,
			url			:_linkurl,
			index		:$('.menulistprvw >li').length
		}


		if(_menuparent.val()=='null'){
			$('.menulistprvw').append(tmpl("menuitem_tmpl", new_list_tpl_obj)); //get the new list template and append it
			_menuparent.prepend('<option index="'+new_list_tpl_obj.index+'">'+_label+'</option>'); //menu item can be parent
		}else{
			var _index=_menuparent.find('option:selected').attr('index');
			$('.menulistprvw').find('>li:eq('+_index+') ul').append(tmpl("menu_child_item_tmpl", new_list_tpl_obj)); //get the new list template and append it
		}

		$('.menu_label').val(''); //clears input after appending

		return false;
	});


	//save menu
	$('.save_menu').click(function(){

		var _menuname=$('.menu_name').val(),
			_slug=$('.menu_slug').val()
			_modal=$('#myModal');

		//show saving... popup
		_modal
		.empty()
		.append(tmpl("saving_menu"),[])
		.modal({
			'backdrop':'static'
		});


		//menu structure
		var menu_structure=[];
		$('.menulistprvw > li').each(function(index,elem){
			menu_structure[menu_structure.length]=makemenu($(elem));

			if($(elem).find('li').length){
				menu_structure[menu_structure.length-1].children=[];
				//menu has kids
				$(elem).find('li').each(function(index,elem){
					menu_structure[menu_structure.length-1].children.push(makemenu($(elem)));
				});
			}
		});

		var url=$('.ajax_target').val(),
			postdata={
				name:_menuname,
				slug:_slug,
				menuitems:menu_structure
			};

		if($('.menuid').length) postdata.menuid=$('.menuid').val();


		//do ajax post and save menu
		$.post(url,postdata,function(response){
			response=$.trim(response);
			if(response=='success'){
				//reset form
				$('.new_menu').trigger('reset')
				_modal
				.empty()
				.append(tmpl("success_menu_saved"),[])
				.modal({
					'backdrop':'static'
				});
			}

		});
	});

	//make menu
	function makemenu(elem){
		anchor=elem.find('>a');
		return 	{
					"url"				  :	anchor.attr('href').toLowerCase(), //lowercase urls
					"label"			  :	anchor.text().replace('-',''), //strip hyphen from label
					"controller"	:	anchor.attr('controller'),
					"action"		  :	anchor.attr('action')
				}
	}

});
