var layout_count;

var current_layout = new Object();
current_layout.name="";
current_layout.sqlstatement="";

var current_row_id=1;
var current_layout_id=1;

window.onresize = function(event) {
   	var height_layout = document.getElementById('status').offsetTop - 130; 
	$('#xml-area-arkisto'+current_layout_id).height( height_layout );
	$('#xml-area-yllapito'+current_layout_id).height( height_layout );
	$('#arkisto'+current_layout_id).height( height_layout );
	$('#yllapito'+current_layout_id).height( height_layout );
}

function do_on_load(){

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
		var asetelma = jQuery.parseJSON( data );
		current_layout.name=asetelma[1].name;
		current_layout.sqlstatement=asetelma[1].sqlstatement;
		set_xml_browse_data(current_layout_id, asetelma[1].xml_browse);
		set_xml_insert_data(current_layout_id, asetelma[1].xml_insert);
		set_html_browse(current_layout_id, asetelma[0].browse_html);
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
		set_column_data(current_layout_id, asetelma[2][0]);
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
		set_column_data(current_layout_id, asetelma[2][0]);
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

function set_column_data(layout_id, data){
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
