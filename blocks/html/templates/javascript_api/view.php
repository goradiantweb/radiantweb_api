
<script type="text/javascript">
$(document).ready(function(){
	var base_url = 'http://sandbox.concrete5.6/api';

	/*
	//List Users and UserInfo by filtered criteria API methods
	var method = 'User';
	var id = 2;
	var url = base_url+'/'+method+'/'+id+'/';
	
	$.ajax({
		url: url,
		data: {type: "GET"}
	}).done( function(response){

		var data = $.parseJSON(response);
		
		$('#response').append('<h2>'+data.uName+'</h2><p>UserID is: '+data.uID+'</p>');

		var uimethod = 'UserInfo';
		var attributes = {'Email':'UserEmail','First Name':'UserFirstName','Last Name':'UserLastName'};
		var uiurl = base_url+'/'+uimethod+'/'+data.uID+'/';
		
		$.ajax({
			url: uiurl,
			data: {
				type: "GET", 
				attributes: attributes
			}
		}).done( function(uiresponse){
			var uidata = $.parseJSON(uiresponse);
			$.each(uidata,function(k,v){
				$('#response').append('<p><strong>User '+k+'</strong>: '+v+'</p>');
			});
		});
		
		
		$.each(data.uGroups,function(k,v){
			//alert(v);
			var gmethod = 'Group';
			var gurl = base_url+'/'+gmethod+'/'+v+'/';
			$.get(gurl,function(gresponse){
				var gdata = $.parseJSON(gresponse);
				$('#response').append('<p><strong>'+gdata.gName+'</strong>: '+gdata.gDescription+'</p>');
			});
		});
	},'html');
	*/

	/*
	//List Pages by filtered criteria API method
	var method = 'PageList';
	var url = base_url+'/'+method+'/';
	
	var filters = new Array();
	filters.push({'column':'ak_tags','modifier':'LIKE','value':'%\nTags\n%'});
	
	var attributes = new Array();
	
	$.ajax({
		url: url,
		data: {
			type: "GET", 
			filters: filters,
			attributes: attributes
		}
	}).done( function(plresponse){
		$('#response').append(plresponse);
	});
	*/
	
	
	/*
	//List a Custom model API method
	var method = 'Custom';
	var url = base_url+'/'+method+'/';

	$.ajax({
		url: url,
		data: {
			type: "GET", 
			token: 'SKD6DG2VQ55KPHJ15J3RS5JXL881RCFK',
			model: 'homegroups_list',
			package: 'homegroups',
			class: 'HomegroupsList',
			funct: 'get'
		}
	}).done( function(customresponse){
		$('#response').append( customresponse );
	});
	*/
	

	//SKD6DG2VQ55KPHJ15J3RS5JXL881RCFK
	//update user API method
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
		data: {type: "UPDATE", 
		token: 'SKD6DG2VQ55KPHJ15J3RS5JXL881RCFK',
		attributes: attributes
		}
	}).done( function(plresponse){
		$('#response').append(plresponse);
	});
	*/
	
	
	/*
	//generate a token key
	var method = 'Authenticate';
	var url = base_url+'/'+method+'/';
	$.ajax({
		url: url,
		data: {type: "REQUEST", 
		user: 'ChadStrat',
		pass: 'IIbnBliss'
		}
	}).done( function(plresponse){
		$('#response').append(plresponse);
	});
	*/
});
</script>

<div id="response"></div>