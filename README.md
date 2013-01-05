radiantweb_api
==============

A Flexible Passthrough RESTfull API for Concrete5 with User,Page,Files, and Custom function support

	var base_url = 'http://sandbox.concrete5.6/api';
	var token = 'SKD6DG2VQ55KPHJ15J3RS5JXL881RCFJ';
	
	/**********************************************/
	//Javascript Athentication API example
	/**********************************************/

	//generate a token key
	/*
	var method = 'Authenticate';
	var url = base_url+'/'+method+'/';
	$.ajax({
		url: url,
		type: "GET",
		data: { 
			user: 'ChadStrat',
			pass: 'SomePass2'
		}
	}).done( function(plresponse){
		$('#response').append(plresponse);
	});
	*/
	

	/**********************************************/
	//Javascript User API example
	/**********************************************/
	
	//List Users and UserInfo by filtered criteria API methods
	/*
	var method = 'User';
	var id = 2;
	var url = base_url+'/'+method+'/'+id+'/';
	
	var xhr = $.ajax({
		url: url,
		type: "GET"
	}).done( function(response){
		
		//alert(xhr.status); /* could be used with .ajax statusCode: for client-side returns

		var data = $.parseJSON(response);
		
		$('#response').append('<h2>'+data.uName+'</h2><p>UserID is: '+data.uID+'</p>');

		var uimethod = 'UserInfo';
		var attributes = {
			'Email':'UserEmail',
			'First Name':'UserFirstName',
			'Last Name':'UserLastName'
		};
		
		var uiurl = base_url+'/'+uimethod+'/'+data.uID+'/';
		
		$.ajax({
			url: uiurl,
			type: "GET",
			data: { 
				attributes: attributes
			}
		}).done( function(uiresponse){
			var uidata = $.parseJSON(uiresponse);
			$.each(uidata,function(k,v){
				$('#response').append('<p><strong>User '+k+'</strong>: '+v+'</p>');
			});
		});
		
		
		$.each(data.uGroups,function(k,v){
			var gmethod = 'Group';
			var gurl = base_url+'/'+gmethod+'/'+v+'/';
			$.get(gurl,function(gresponse){
				var gdata = $.parseJSON(gresponse);
				$('#response').append('<p><strong>'+gdata.gName+'</strong>: '+gdata.gDescription+'</p>');
			});
		});
		
	},'html');
	*/
	
	//Update a User and it's related Info API methods
	/*
	var method = 'User';
	var id = 2;
	
	var url = base_url+'/'+method+'/'+id+'/';	
	
	var attributes = new Array();
	var attributes = {
		'first_name':'Chad',
		'last_name':'Cantrell'
	};
	
	$.ajax({
		url: url,
		type: "PUT",
		data: { 
			token: token,
			attributes: attributes
		}
	}).done( function(plresponse){
		$('#response').append(plresponse);
	});
	
	*/
	
	
	/**********************************************/
	//Javascript PageList API example
	/**********************************************/
	
	//List Pages by filtered criteria API method
	/*
	var method = 'PageList';
	var url = base_url+'/'+method+'/';
	
	var filters = new Array();
	filters.push({'column':'ak_tags','modifier':'LIKE','value':'%\nTags\n%'});
	
	var attributes = new Array();
	
	var xhr = $.ajax({
		url: url,
		type: "GET",
		data: { 
			filters: filters,
			attributes: attributes
		}
	}).done( function(plresponse){
		/alert(xhr.status); /* could be used with .ajax statusCode: for client-side returns
		$('#response').append(plresponse);
	});
	
	*/
	
	
	/**********************************************/
	//Javascript Passthrough API example
	// where custom package models and functions can
	// be utilized.
	// all custom API hooks must be token Authenticated
	/**********************************************/
	
	/*
	//List a Custom model API method
	var method = 'Custom';
	var url = base_url+'/'+method+'/';

	var xhr = $.ajax({
		url: url,
		type: "GET",
		data: { 
			token: token,
			model: 'homegroups_list',
			package: 'homegroups',
			class: 'HomegroupsList',
			funct: 'get',
			return: 'html', //returns respons as text instead of object
			persist: true //will force ccm_token. example - connecting to API when site is in Maintanence mode. 
		}
	}).done( function(customresponse){
		/alert(xhr.status); /* could be used with .ajax statusCode: for client-side returns
		$('#response').append( customresponse );
	});
	*/
