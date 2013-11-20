var layout_count;

var current_layout = new Object();
current_layout.name="";
current_layout.sqlstatement="";

var current_row_id=1;
var current_layout_id=1;
var current_insid_id=0;

window.onresize = function(event) {
   	var height_layout = document.getElementById('status').offsetTop - 130; 
	$('#xml-area-arkisto'+current_layout_id).height( height_layout );
	$('#xml-area-yllapito'+current_layout_id).height( height_layout );
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


function refresh_columns(){
	$("#sarakkeet_muok").html("");
	var count = parseInt($("#sarakkeiden_maara").val());
	for(var i=0; i<count; i++){
		var clr="";		
		if(i%2==0){
			clr="background-color:rgb(255,255,255);";
		}
		else{
			clr="background-color:rgb(245,245,245);";
		}
		$("#sarakkeet_muok").html($("#sarakkeet_muok").html() + '<div style="'+clr+';padding:5px;width:100%"><span>Sarakkeen nimi:</span><input class="sarakkeen_nimi" style="width:25%" type="text" />	<select class="sarakkeen_tyyppi"><option value="-1">Tyyppi</option><option value="TEXT">TEXT</option><option value="INT">INT</option><option value="DATE">DATE</option><option value="DOUBLE">DOUBLE</option></select></div>');
	}

}

function create_table(){
	var columns=new Array();
	var column_types=new Array();	

	$(".sarakkeen_nimi").each(function( index ) {
			columns.push($( this ).val());
	});
	$(".sarakkeen_tyyppi").each(function( index ) {
			column_types.push($( this ).val());
	});

	var column_str="";

	for(var i=0; i<columns.length; i++){
		column_str=column_str+columns[i]+":"+column_types[i];
		if(i<columns.length-1){
			column_str=column_str+",";
		}
	}

	$.post( "php/handle_post.php", { type:5, table_name: $("#taulun_nimi").val(), table_columns:column_str})
	.done(function(data ) {
		
	});

}

function set_current_layout(id){
	current_layout_id=id;
	$.post( "php/handle_post.php", { type:3, layout_id: current_layout_id, row: 1})
	.done(function(data ) {
		var asetelma = jQuery.parseJSON( data );	
		current_layout.name=asetelma[1].name;
		current_layout.sqlstatement=asetelma[1].sqlstatement;
		current_layout.sqlstatement=asetelma[1].sqlstatement;
		set_xml_browse_data(current_layout_id, asetelma[1].xml_browse);
		set_xml_insert_data(current_layout_id, asetelma[1].xml_insert);
		set_html_browse(current_layout_id, asetelma[0].browse_html);
		set_column_data(current_layout_id, asetelma[2][0]);
	});

	var height_layout = document.getElementById('status').offsetTop - 130; 
	$('#xml-area-arkisto'+current_layout_id).height( height_layout );
	$('#xml-area-yllapito'+current_layout_id).height( height_layout );
	$('#arkisto'+current_layout_id).height( height_layout );
	$('#yllapito'+current_layout_id).height( height_layout );
}

function display_to(id, attr){
	document.getElementById(id).style.display=attr;
}

function peita_kaikki_valilehdet(nmn){
		display_to("arkisto-xml"+nmn, "none");
		display_to("yllapito-xml"+nmn, "none");
		display_to("yllapito"+nmn, "none");
		display_to("arkisto"+nmn, "none");
}

function avaa_valilehti(index, nmn){	
	if(index==0){
		peita_kaikki_valilehdet(nmn);
		display_to("arkisto-xml"+nmn, "block");
	}
	if(index==1){
		peita_kaikki_valilehdet(nmn);
		display_to("yllapito-xml"+nmn, "block");
	}
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
	$.post( "php/handle_post.php", { type:2, row: current_row_id, layout_id: current_layout_id, xml_browse: $('#xml-area-arkisto'+current_layout_id).val(), xml_insert:$('#xml-area-yllapito'+current_layout_id).val(), layout_name:current_layout.name, layout_sqlstatement:current_layout.sqlstatement })
	.done(function(data ) {
		alert(data);
		var asetelma = jQuery.parseJSON( data );
		current_layout.name=asetelma[1].name;
		current_layout.sqlstatement=asetelma[1].sqlstatement;
		set_xml_browse_data(current_layout_id, asetelma[1].xml_browse);
		set_xml_insert_data(current_layout_id, asetelma[1].xml_insert);
		set_html_browse(current_layout_id, asetelma[0].browse_html);
		set_html_insert(current_layout_id, asetelma[0].insert_html);
		set_column_data(current_layout_id, asetelma[2][0]);
	});
}

function next(){
	current_row_id++;
	$("#row_span").html(current_row_id);

	$.post( "php/handle_post.php", { type:0, layout_id: current_layout_id, row: current_row_id, layout_name:current_layout.name, layout_sqlstatement:current_layout.sqlstatement })
	.done(function(data ) {
		var asetelma = jQuery.parseJSON( data );	
		current_layout.name=asetelma[1].name;
		current_layout.sqlstatement=asetelma[1].sqlstatement;
		set_xml_browse_data(current_layout_id, asetelma[1].xml_browse);
		set_xml_insert_data(current_layout_id, asetelma[1].xml_insert);
		set_html_browse(current_layout_id, asetelma[0].browse_html);
		set_html_insert(current_layout_id, asetelma[0].insert_html);
		set_column_data(current_layout_id, asetelma[2][0]);
		 $('#yllapito-tabit'+current_layout_id+' li:eq('+current_insid_id+') a').tab('show');
	});
}

function previous(){
	if(current_row_id>1){
		current_row_id--;
	}
	$("#row_span").html(current_row_id);

	$.post( "php/handle_post.php", { type:1, layout_id: current_layout_id, row: current_row_id, layout_name:current_layout.name, layout_sqlstatement:current_layout.sqlstatement })
	.done(function(data ) {
		var asetelma = jQuery.parseJSON( data );
		current_layout.name=asetelma[1].name;
		current_layout.sqlstatement=asetelma[1].sqlstatement;
		set_xml_browse_data(current_layout_id, asetelma[1].xml_browse);
		set_xml_insert_data(current_layout_id, asetelma[1].xml_insert);
		set_html_browse(current_layout_id, asetelma[0].browse_html);
		set_html_insert(current_layout_id, asetelma[0].insert_html);
		set_column_data(current_layout_id, asetelma[2][0]);
		$('#yllapito-tabit'+current_layout_id+' li:eq('+current_insid_id+') a').tab('show');
	});
}

function create_new_layout(){
	$.post( "php/handle_post.php", { type:4, layout_name:$("#asetelman_nimi").val(), layout_sqlstatement:$("#asetelman_sql").val() })
	.done(function(data ) {
		alert(data);
	});	
}

function set_xml_browse_data(layout_id, data){
	var browse_xml_element = document.getElementById('xml-area-arkisto'+layout_id);
	browse_xml_element.value=data;
}

function set_xml_insert_data(layout_id, data){
	var insert_xml_element = document.getElementById('xml-area-yllapito'+layout_id);
	insert_xml_element.value=data;
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
		insert_tabs=insert_tabs+'<li><a onclick="change_insid('+c+')" href="#inserter'+(i-1)+'" data-toggle="tab">'+data[i][0]+'</a></li>';
		c++;
	}
	insert_tabs=insert_tabs+"</ul>";

	var insert_divs='<div class="tab-content">';
	for(var i=0; i<data.length; i+=3){
		insert_divs=insert_divs+'<div class="tab-pane" id="inserter'+i+'">'+data[i]+'</div>';
		//set_column_data_insert(data[i+2][0]);
	}
	insert_divs=insert_divs+"</div>";

	insert_div.innerHTML=insert_tabs+insert_divs;
	for(var i=2; i<data.length; i+=3){
		set_column_data_insert(data[i][0]);
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
	$("div#inserter"+$(painike).data("insid")+" input:text").each(function(){
		post_string_keys.push($(this).attr('class').substring(13));
		post_string_data.push($(this).val()); 
		post_string_lengths.push($(this).val().length); 
	});
	
	$("div#inserter"+$(painike).data("insid")+" textarea").each(function(){
		post_string_keys.push($(this).attr('class').substring(13));
		post_string_data.push($(this).val()); 
		post_string_lengths.push($(this).val().length); 
	});

	$.post( "php/handle_post.php", { type:6, table: $(painike).data("table"), row: current_row_id, data_keys:post_string_keys.toString(), data_data:post_string_data.toString(), data_lengths:post_string_lengths.toString() })
	.done(function(data ) {
		alert(data);
	});
}

function delete_data_from_database(painike, insid){
	$.post( "php/handle_post.php", { type:7, table: $(painike).data("table"), row: current_row_id })
	.done(function(data ) {
		alert(data);
	});
}

function sign_out(){
	$.post( "php/handle_post.php", { type:9 })
	.done(function(data ) {
			window.location="login.php";
	});
}

function get_table_list(){
	$("#taulujen_nimet").html("");
	$.post( "php/handle_post.php", { type:10 })
	.done(function(data ) {
			
			var tables = jQuery.parseJSON( data );
			for(var i=0; i<tables.length; i++){
				if(tables[i] !== undefined){
					$("#taulujen_nimet").html( $("#taulujen_nimet").html() + "<option data-listtype='table' value='"+tables[i].table_name+"'>"+tables[i].table_name+"</option>" );
					for(var j=0; j<tables[i].columns.length; j++){
						$("#taulujen_nimet").html( $("#taulujen_nimet").html() + "<option data-listtype='column' data-table='"+tables[i].table_name+"' style='color:rgb(100,100,150)' value='"+tables[i].columns[j].sarakkeen_nimi+"'>-"+tables[i].columns[j].sarakkeen_nimi+":"+tables[i].columns[j].sarakkeen_tyyppi+"</option>" );
					}
				}
			}
	});
}

function column_list(id,table_name){
		$.post( "php/handle_post.php", { type:11, table:table_name})
		.done(function(data ) {
			alert(data);
			var columns = jQuery.parseJSON( data );
			for(var i=0; i<columns.length; i++){			
				$(id).html($(id).html()+"<option>"+columns[i]+"</option>");
			}
		});	
}

function add_column(){
	if( $("#taulujen_nimet option:selected").data("listtype") === "table" ){
		var table_name=$("#taulujen_nimet").val();
		var column_name=$("#uusi_sarakkeen_nimi").val();
		var column_type=$("#uusi_sarakkeen_tyyppi").val();
		$.post( "php/handle_post.php", { type:12, table:table_name, column_name:column_name,column_type:column_type })
		.done(function(data ) {
			alert(data);
		});
	}
}

function change_table_name(){
	if( $("#taulujen_nimet option:selected").data("listtype") === "table" ){
		var table_name=$("#taulujen_nimet").val();
		var new_table_name=$("#uusi_taulun_nimi").val();
		$.post( "php/handle_post.php", { type:13, table:table_name, new_table_name:new_table_name })
		.done(function(data ) {
			alert(data);
		});
	}	
}

function change_column_name(){
	if( $("#taulujen_nimet option:selected").data("listtype") === "column" ){
		var column_name=$("#taulujen_nimet").val();
		var new_column_name=$("#uusi_sarakkeen_nimi_muuta").val();
		var table_name=$("#taulujen_nimet option:selected").data("table");
		var new_column_type=$("#muuta_sarakkeen_tyyppi").val();
		$.post( "php/handle_post.php", { type:14, table:table_name, column_name:column_name, new_column_name:new_column_name, new_column_type:new_column_type })
		.done(function(data ) {
			alert(data);
		});
	}	
}

function destroy_column(){
	if( $("#taulujen_nimet option:selected").data("listtype") === "column" ){
		var column_name=$("#taulujen_nimet").val();
		var table_name=$("#taulujen_nimet option:selected").data("table");
		$.post( "php/handle_post.php", { type:15, table:table_name, column_name:column_name})
		.done(function(data ) {
			alert(data);
		});
	}	
}

function destroy_table(){
	if( $("#taulujen_nimet option:selected").data("listtype") === "table" ){
		var table_name=$("#taulujen_nimet").val();
		$.post( "php/handle_post.php", { type:16, table:table_name})
		.done(function(data ) {
			alert(data);
		});
	}	
}

function get_layout_list(){
	$("#asetelmien_nimet").html("");
	$.post( "php/handle_post.php", { type:17 })
	.done(function(data ) {
			var layouts = jQuery.parseJSON( data );
			for(var i=0; i<layouts.length; i++){
				if(layouts[i] !== undefined){
					$("#asetelmien_nimet").html( $("#asetelmien_nimet").html() + "<option value='"+layouts[i].layout_name+"'>"+layouts[i].layout_name+"("+layouts[i].sql+")</option>" );
				}
			}
	});
}


function change_layout_name(){
	var new_layout_name=$("#uusi_asetelman_nimi").val();
	var layout=$("#asetelmien_nimet").val();
	$.post( "php/handle_post.php", { type:18, layout:layout, new_layout_name:new_layout_name })
	.done(function(data ) {
		alert(data);
	});
}


function change_layout_sql(){
	var sql=$("#uusi_sqllauseke").val();
	var layout=$("#asetelmien_nimet").val();
	$.post( "php/handle_post.php", { type:19, layout:layout, sql:sql })
	.done(function(data ) {
		alert(data);
	});
}

function destroy_layout(){
	var layout=$("#asetelmien_nimet").val();
	$.post( "php/handle_post.php", { type:20, layout:layout })
	.done(function(data ) {
		alert(data);
	});
}
