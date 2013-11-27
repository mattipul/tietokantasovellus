var layout_count;

var current_layout = new Object();
current_layout.name="";

var current_row_id=1;
var current_layout_id=1;
var current_insid_id=0;

function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

window.onresize = function(event) {
   	var height_layout = document.getElementById('status').offsetTop - 130; 
	$('#arkisto'+current_layout_id).height( height_layout );
	$('#yllapito'+current_layout_id).height( height_layout );
}

function do_on_load(){

}

function change_insid(insid){
	current_insid_id=insid;
}

function open_new_layout_dialog(){
	$("#uusi_asetelma").show();
}

function close_new_layout_dialog(){
	$("#uusi_asetelma").hide();
}

function set_current_layout(id){
	current_layout_id=id;
	
	$.post( "php/handle_post.php", { type:3, layout_id: current_layout_id, row: 1})
	.done(function(data ) {
		var asetelma = jQuery.parseJSON( data );	
		current_layout.name=asetelma[1].name;
		set_html_browse(current_layout_id, asetelma[0].browse_html);
		set_html_insert(current_layout_id, asetelma[0].insert_html);
		set_column_data(current_layout_id, asetelma[2]);
	});

	var height_layout = document.getElementById('status').offsetTop - 130; 
	$('#arkisto'+current_layout_id).height( height_layout );
	$('#yllapito'+current_layout_id).height( height_layout );
}

function display_to(id, attr){
	document.getElementById(id).style.display=attr;
}

function peita_kaikki_valilehdet(nmn){
		display_to("yllapito"+nmn, "none");
		display_to("arkisto"+nmn, "none");
}

function avaa_valilehti(index, nmn){	
	if(index==2){
		peita_kaikki_valilehdet(nmn);
		display_to("yllapito"+nmn, "block");
	}
	if(index==3){
		peita_kaikki_valilehdet(nmn);
		display_to("arkisto"+nmn, "block");
	}
}

function refresh(){

	$.post( "php/handle_post.php", { type:2, row: current_row_id, layout_id: current_layout_id })
	.done(function(data ) {

		var asetelma = jQuery.parseJSON( data );
		current_layout.name=asetelma[1].name;

		set_html_browse(current_layout_id, asetelma[0].browse_html);
		set_html_insert(current_layout_id, asetelma[0].insert_html);
		set_column_data(current_layout_id, asetelma[2]);
	});
}

function next(){
	current_row_id++;
	$("#row_span").html(current_row_id);

	$.post( "php/handle_post.php", { type:0, layout_id: current_layout_id, row: current_row_id })
	.done(function(data ) {
		var asetelma = jQuery.parseJSON( data );	
		current_layout.name=asetelma[1].name;

		set_html_browse(current_layout_id, asetelma[0].browse_html);
		set_html_insert(current_layout_id, asetelma[0].insert_html);
		set_column_data(current_layout_id, asetelma[2]);
		 $('#yllapito-tabit'+current_layout_id+' li:eq('+current_insid_id+') a').tab('show');
	});
}

function previous(){
	if(current_row_id>1){
		current_row_id--;
	}
	$("#row_span").html(current_row_id);

	$.post( "php/handle_post.php", { type:1, layout_id: current_layout_id, row: current_row_id})
	.done(function(data ) {
		var asetelma = jQuery.parseJSON( data );
		current_layout.name=asetelma[1].name;

		set_html_browse(current_layout_id, asetelma[0].browse_html);
		set_html_insert(current_layout_id, asetelma[0].insert_html);
		set_column_data(current_layout_id, asetelma[2]);
		$('#yllapito-tabit'+current_layout_id+' li:eq('+current_insid_id+') a').tab('show');
	});
}



function set_html_browse(layout_id, data){
	var browse_div=document.getElementById('arkisto'+layout_id);
	browse_div.innerHTML=data;
}

function set_html_insert(layout_id, data){

	var insert_div=document.getElementById('yllapito'+layout_id);
	var insert_tabs='<ul id="yllapito-tabit'+current_layout_id+'" class="nav nav-tabs">';
	var c=0;
	for(var i=1; i<data.length; i+=3){
		insert_tabs=insert_tabs+'<li><a onclick="change_insid('+c+')" href="#'+current_layout_id+'inserter'+c+'" data-toggle="tab">'+data[i][0]+'</a></li>';
		c++;
	}
	insert_tabs=insert_tabs+"</ul>";

	var insert_divs='<div class="tab-content">';
	c=0;
	for(var i=0; i<data.length; i+=3){
		insert_divs=insert_divs+'<div class="tab-pane" id="'+current_layout_id+'inserter'+c+'">'+data[i]+'</div>';
		c++;
		//set_column_data_insert(data[i+2][0]);
	}
	insert_divs=insert_divs+"</div>";

	insert_div.innerHTML=insert_tabs+insert_divs;
	for(var i=2; i<data.length; i+=3){
		set_column_data_insert(data[i]);
	}
}

function set_column_data(layout_id, data){
	if(data!==undefined){
	var a_node=data[0];
	var laskuri=0;
	var keys=Object.keys(data);
	while(a_node!==undefined){
		$("."+keys[((keys.length/2)+laskuri)]).each(function( index ) {
			$( this ).html(a_node) ;
		});

		laskuri++;
		a_node=data[laskuri];
	}
	}
}

function set_column_data_insert(data){
	if(data!==undefined){
	var a_node=data[0];
	var laskuri=0;
	var keys=Object.keys(data);
	while(a_node!==undefined){
		$(".insert_entry_"+keys[((keys.length/2)+laskuri)]).each(function( index ) {
			$( this ).val(a_node) ;
		});

		laskuri++;
		a_node=data[laskuri];
	}
	}
}

function insert_data_to_database(painike, insid){
	var post_string_keys=new Array();
	var post_string_data=new Array();
	var post_string_lengths=new Array();
	$("div#"+current_layout_id+"inserter"+$(painike).data("insid")+" input:text").each(function(){
		post_string_keys.push($(this).attr('class').substring(13));
		post_string_data.push($(this).val()); 
		post_string_lengths.push($(this).val().length); 
	});
	
	$("div#"+current_layout_id+"inserter"+$(painike).data("insid")+" textarea").each(function(){
		post_string_keys.push($(this).attr('class').substring(13));
		post_string_data.push($(this).val()); 
		post_string_lengths.push($(this).val().length); 
	});
	$.post( "php/handle_post.php", { type:6,layout_id:current_layout_id, table: $(painike).data("table"), row: current_row_id, data_keys:post_string_keys.toString(), data_data:post_string_data.toString(), data_lengths:post_string_lengths.toString() })
	.done(function(data ) {
		
	});
}

function delete_data_from_database(painike, insid){
	$.post( "php/handle_post.php", { type:7,layout_id:current_layout_id, table: $(painike).data("table"), row: current_row_id })
	.done(function(data ) {
		
	});
}

function sign_out(){
	$.post( "php/handle_post.php", { type:9 })
	.done(function(data ) {
			window.location="login_auth.php";
	});
}

function search(painike){
	var post_string_keys=new Array();
	var post_string_data=new Array();
	var post_string_lengths=new Array();
	var identifier="";
	var results;
	$( ".search_section_div" ).each(function() {
		identifier=$(this).data("searchidentifier");
		$(this).children("input:text").each(function() {
			post_string_keys.push($(this).attr("class").substring(13));
			post_string_data.push($(this).val());
			post_string_lengths.push($(this).val().length);
		});
	});
	
	$( ".search_results_div" ).each(function() {
		if( $(this).data("searchidentifier") === identifier ){
			results=this;
		}
	});
	
	
	$.post( "php/handle_post.php", { type:21, layout_id:current_layout_id, identifier: $(painike).data("searchidentifier"), data_keys:post_string_keys.toString(), data_data:post_string_data.toString(), data_lengths:post_string_lengths.toString() })
	.done(function(data ) {
		var results_data = jQuery.parseJSON( data );

		var keys1=Object.keys(results_data[0]);
		var tablehtml;
		tablehtml="<div style='display:table;width:100%;table-layout: fixed;'><table width='100%'><tr>";

		for(var j=keys1.length/2; j<keys1.length; j++){
			tablehtml=tablehtml+"<td class='search_tab'>"+keys1[j]+"</span></td>";
		}
		
		tablehtml=tablehtml+"</tr>";
		
		for(var i=0; i<results_data.length; i++){
			var keys=Object.keys(results_data[i]);
			tablehtml=tablehtml+"<tr>";
			for(var j=keys.length/2; j<keys.length; j++){
				//alert(results_data[i].asetelman_nimi);
				tablehtml=tablehtml+"<td class='search_tab_b'><span>"+htmlEntities(results_data[i][keys[j]]).substring(0,20)+"...</span><span style='display:none'>"+htmlEntities(results_data[i][keys[j]]).substring(20,htmlEntities(results_data[i][keys[j]]).length)+"</span></td>";
			}
			tablehtml=tablehtml+"</tr>";
		}
		
		tablehtml=tablehtml+"</table></div>";
		
		results.innerHTML=tablehtml;
		
	});
	
}


