<script type="text/javascript">

/*(function () {
	var input = document.getElementById("attachment"), 
		formdata = false;

	function showUploadedItem (id,fileName) {
  		var list = document.getElementById("image-list"),
	  		li   = document.createElement("li"),

	  		div  = document.createElement("div");
	  		div.className = "img";
  			li.appendChild(div);

  		// img.src = source;
  		
  			div  = document.createElement("div");
	  		div.className = "delete";
  			li.appendChild(div);

  		// preview
  		 	div  = document.createElement("div");
			div.className = "preview";
  			li.appendChild(div);

  		// id
  		var input  = document.createElement("input");
  		input.type = "hidden";
	  	input.className = "id";
  		input.value = id;
  		li.appendChild(input);

  		// id
  		var input  = document.createElement("input");
  		input.type = "hidden";
	  	input.className = "fileName";
  		input.value = fileName;
  		li.appendChild(input);

		list.appendChild(li);
	}   

	if (window.FormData) {
  		formdata = new FormData();
  		// document.getElementById("btn").style.display = "none";
	}
	
 	input.addEventListener("change", function (evt) {

 	var url="<?php echo base_url();?>file/doUploadFile/";

	formdata = false;
	formdata = new FormData();
	var attachment=[];

 		// document.getElementById("response").innerHTML = "Uploading . . ."
 		var i = 0, len = this.files.length, img, reader, file;
	
		for (i = 0 ; i < len; i++ ) {
			file = this.files[i];
			// si quiero que sea imagen pongo image
			// if (!!file.type.match(/text.xls/)) {
				if ( window.FileReader ) {
					reader = new FileReader();
					reader.onloadend = function (e) { 
						// showUploadedItem(e.target.result, file.fileName);
					};
					reader.readAsDataURL(file);
				}

		attachment[i]=file;
		// formdata.append("attachment[]", file);
			// }	
		}

		console.log(attachment);

		if (formdata) {
		var showContainer="div.action_excel_import > div.show_image";

		var id=$("form.dinamic > div.idContainer > div.area2 > input#id").val();

			$.ajax({
				url: url+"?process=CmdbImportXml&id="+id,
				type: "POST",
				data: {attachment:attachment,msg:"ching"},
				// data: formdata,
				processData: false,
				contentType: false,
				beforeSend:function(res){
					console.log(res);

				// ajax
				$("input").prop("disabled",true);
				$("button").prop("disabled",true);
				$("div#ajax_loading").addClass("ajax_loading");
				// ...	

				},
				success: function (res) {
				var requests=JSON.parse(res), request=( requests && typeof requests ==='object' ? requests : null ), class_name="", msg="";

						// showUploadedItem(request.id_reg,request.file_name);
					
					// if(!id)
					// {attachedFiles.chargeData(request.id_reg,request.file_name)}


				}
			});
		}
	}, false);
}());

	attachedFiles=new Object();
	attachedFiles.d3lete=new Object();
	attachedFiles.d3lete.process=function(item) {

		// visual and confirm

		$(item).addClass("itemDeleting");

		if(!confirm("eliminar definitivamente?"))
		 { $(item).removeClass("itemDeleting");  return; }

		// ...id de compras 
	var id=$("form.dinamic > div.idContainer > div.area2 > input#id").val();

		// var elementsDisabledByDefault=$("form#dinamic :input:disabled|button:disabled").get(),
			var query_string="request[0][name]=upload-delete"
				+"&request[0][params][type]=purchase-attachment"
				+"&request[0][params][file]="+encodeURIComponent($(item).children("input.fileName").val())
				+"&request[0][params][id_tmp_files]="+encodeURIComponent($(item).children("input.id").val())
				+"&request[0][params][associated_reg_id]="+id;

		// d0!!

		$.ajax({

			type:"POST",
			url:"../../_resources/scripts/php/system/rdp/handler.php",
			dataType:"text",
			processData:false,
			data:query_string,
			beforeSend:function(XMLHttpRequest) {
			},
			complete:function(XMLHttpRequest,textstatus) {

				var requests=JSON.parse(XMLHttpRequest.responseText), request=( requests && typeof requests ==='object' ? requests[0] : null ), class_name="", msg="";

				if(request && typeof(request.status)!="undefined" && request.status===1) {

					$(item).remove();

				}
				else {

					msg=typeof(request.msg)!="undefined" ? request.msg : "error al realizar la petición { "+textstatus+" }" ;

				}

				// ...

				if(msg)
				 alert(msg);

				$(item).removeClass("itemDeleting");

				// if(!$(regs.attachedFiles.container).children("div.item").length)
				//  $(regs.attachedFiles.container).parent().hide();


			}

		});

	};

$(document).on("click","#image-list >li >div.delete",function(){

		attachedFiles.d3lete.process($(this).parent().get(0));

});

// cargar datos del emisor en factura 
attachedFiles.chargeData=function(id,fileName){

container=new Object;
container.div="form.dinamic > div.container";

	var query_string="request[0][name]=purchase-xml-get-data"
		+"&request[0][params][fileName]="+fileName
		+"&request[0][params][id_tmp_files]="+id
		;

		$.ajax({

			type:"POST",
			url:"../../_resources/scripts/php/system/rdp/handler.php",
			dataType:"text",
			processData:false,
			data:query_string,
			beforeSend:function(XMLHttpRequest) {
				// visual

			},
			complete:function(XMLHttpRequest,textstatus) {
				var requests=JSON.parse(XMLHttpRequest.responseText), request=( requests && typeof requests ==='object' ? requests[0] : null ), class_name="", msg="";
				
				// proveedor 
				$(container.div).find("div.area2 > input#provider").val(request.data.id);
				$(container.div).find("div.area2 > input#provider_name").val(request.data.name);

				// comprador
				$(container.div).find("div.area2 > select#buyer").val(request.data.buyer);

				// sucursal
				providerSubsidiaryLoad(request.data.subsidiaries.is_main);
			}

		});
}

// imprimir el XML 
$(document).on("click","#image-list > li > div.preview",function(){

container=new Object;
container.div="form.dinamic > div.container";
var id=$("form.dinamic > div.idContainer > div.area2 > input#id").val();

	var query_string="module=purchase"
		+"&fileName="+encodeURIComponent($(this).parent().children("input.fileName").val())
		+"&id_tmp_files="+encodeURIComponent($(this).parent().children("input.id").val())
		+"&id="+encodeURIComponent(id)
		;

		$.ajax({

			type:"POST",
			url:"../print-xml/template/estandar/get-pdf.php",
			dataType:"text",
			processData:false,
			data:query_string,
			beforeSend:function(XMLHttpRequest) {
				// visual
				console.log(XMLHttpRequest);
			},
			complete:function(XMLHttpRequest,textstatus) {
				// var requests=JSON.parse(XMLHttpRequest.responseText), request=( requests && typeof requests ==='object' ? requests[0] : null ), class_name="", msg="";
				// window.open='../print-xml/template/estandar/get-pdf.php/?'+query_string;
			
			window.open("../print-xml/template/estandar/get-pdf.php/?"+query_string);

			}

		});

});
*/
</script>

<script>

$(document).ready(function(){ 

    $('#form_file_upload').fileUploadUI({
        uploadTable: $('#files'),
        downloadTable: $('#files'),
        buildUploadRow: function (files, index) {
// ajax
        // $("input").prop("disabled",true);
        // $("button").prop("disabled",true);
        // $("div#ajax_loading").addClass("ajax_loading");
// ...
            return $('<tr><td>' + files[index].name + '<\/td>' +
            '<td style="width:150px; height:17px;" class     ="file_upload_progress"><div><\/div><\/td>' +
            '<td class     ="file_upload_cancel">' +
            '<button class ="ui-state-default ui-corner-all" title="Cancel">' +
            '<span class   ="ui-icon ui-icon-cancel">Cancel<\/span>' +
            '<\/button><\/td><\/tr>');
        },
        buildDownloadRow: function (file) {
            var url = "<?php echo base_url(); ?>";

        	// ajax
        // $("input").prop("disabled",false);
        // $("button").prop("disabled",false);
        // $("div#ajax_loading").removeClass("ajax_loading");
// ...

		if(!file.status){

			$("#dialog > p").text("");
			$("#dialog > p").text(file.msg);
			$("#dialog > p").dialog({
			resizable: false,
			modal: true,
			    buttons: {
			        Aceptar: function() {

			        $("#dialog").append("<p></p>");
			        $(this).dialog( "close" );

		        	},
			    }
			});
		
			return false;
		}

		// console.log(file);

		var tr='<tr>'
                    +'<td>'
                    +'<span class="'+file.classSpan+'"></span>'
                    +'<\/td>' 
                    +'<td>'
                    +'<span class="name_encode"  style="display:none">'+file.name_encode+'</span>'
                    +'<span class="file_id"  style="display:none">'+file.file_id+'</span>'
                    +'<span class="process"  style="display:none">'+file.process+'</span>'
                    +'<span class="delete"></span>'
                    +'</td>' 
                    +'<td class="file_name" style="display:none">'
                    + file.name_encode
                    +'</td>' 
					+'<td class="file_see">'
                    + '<img src='+file.friendly_path+' />'
                    +'</td>' 
                    +'<\/tr>'
                    +'</table>';

            // return $(tr);
        },
        parseResponse: function (file) {console.log(file);},

    });

});
</script>
<!-- delete imagen -->
<script>
$(document).on("click","td > span.delete",function(){

var url="<?php echo base_url(); ?>",
	item=$(this),
	process=$(this).data("process"),
	file_id=$(this).data("file_id")
	;

	$("#dialog > p").text("");
	$("#dialog > p").text("Realmente desea eliminar?");
	$(item).parent().parent().addClass("deleteStyle");
	$("#dialog > p").dialog({
	resizable: false,
	modal: true,
	    buttons: {
	        Si: function() {

					$.ajax({

					type:"POST",
					url:url+"file/delete",
					dataType:"json",
					data:{
						process:$(item).parent().find("span.process").text(), //encode_id()
						id:"<?php echo encode_id($id); ?>",
						file_name:$(item).parent().find("span.name_encode").text(),
						file_id:$(item).parent().find("span.file_id").text()
					},
					beforeSend:function(response) {
			// ajax
			        $("input").prop("disabled",true);
			        $("button").prop("disabled",true);
			        $("div#ajax_loading").addClass("ajax_loading");
			// ...
					},
					complete:function(response) {

						$(item).parent().parent().remove();
				        $("input").prop("disabled",false);
				        $("button").prop("disabled",false);
				        $("div#ajax_loading").removeClass("ajax_loading");

					}

					});

	        $("#dialog").append("<p></p>");
	        $(this).dialog( "close" );

        	},
        	No: function() {

	        $("#dialog").append("<p></p>");
	        $(this).dialog( "close" );
			$(item).parent().parent().removeClass("deleteStyle");


        	},
	    }
	});

});
</script>