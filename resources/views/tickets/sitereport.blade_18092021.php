@extends('layouts.app')

@section('content')

<style>
.tred{
	background-color:#ff0000;
}
.tgreen{
	background-color:#92d050;
}
.torange{
	background-color:#f4b183;
}


</style>


	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


      <div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">Site Report</h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>  

      

      @include('layouts.flash')
      {{-- onchange="sitereport(this.value)" --}}
      <div class="col-lg-3">
        <select id="siteReportFilter" class="form-control">
           <option value="-1" selected>Site Filter</option>
           {{-- @if(count($sites)>0) --}}
           @foreach($sites as $v)
           <option value="{{$v->id}}" sitealias="{{$v->alias_name}}">{{$v->sitename}}</option>
           @endforeach
           {{-- @endif --}}
        </select>
     </div>
     {{-- onchange='datefilter(this.value)' --}}
     <div class="col-lg-3">
        <select id="datefilter"  class="form-control">
           <option value="0">Select</option>
           <option value="Monthly">Monthly</option>
           <option value="Weekly">Weekly</option>
        </select>
     </div>
     <input type="button " id="btn" class="btn btn-primary"  value="submit">
     <br/><br><br>
     <div id="buttons"><a id="excel" class="btn btn-success">Excel</a>&nbsp;&nbsp;<a id="pdff" class="btn btn-success">PDF</a>&nbsp;&nbsp;<a id="csv" onclick="exportTableToCSV('TrafiKSol.csv')" class="btn btn-success">CSV</a>&nbsp;&nbsp;</div>
     
    
                        {{-- BAR CHART AND PIE CHART --}}
        <div style="border:1px solid #EEE; margin:5px 0px; padding:30px 0px;">
        <h3><marquee behavior="alternate">TrafikSol Report</marquee></h3>
       
        </div>
        
        <div class="canvas_div_pdf">
       <div id="capture" style="padding: 10px; background:white">    
      <div class="container" style="magin-bottom:150px">
        <div class="row">
            
            <div class="col-sm-4" id="trafik_logo"><img src="{{url('/')}}/images/trafikLogo.png"></div>
            <div class="col-sm-4" id="middle_data"></div>
            <div class="col-sm-4" id="client_logo">&nbsp;</div>
            <div class="col-sm-12"><hr/></div>
            
            
            <div class="col-sm-7 columnchart_3d">
                <div  id="columnchart_values" ></div>
            </div>
            <div class="col-sm-5 piechart">
                <div  id="pieechart_3d"></div>
             </div>
        </div>
     </div>
    </div>
         

     <div id="only_table_data">
     <table id="tableData" class="table-responsive table table-striped  bb table-bordered" style="font-weight:25%; font-size:8px;width:100% !important">
               
        <colgroup>
            <col width="8%">
            <col width="15%">
            <col width="12%">
            <col width="15%">
            <col width="25%">
            <col width="15%">
            <col width="40%">
            <col width="10%">
            <col width="20%">
        </colgroup>

        <thead   >
            {{-- <tr>
                <th class="text-center"><h1>TrafiKSol</h1></th>
            </tr>  --}}
           <tr id="heading">
			  <th>Sr No</th>
              <th>Tict.Id</th>
              <th>Date Created</th>
              <th>Created By</th>
              <th>Subject</th>
              <th>Issue Type</th>
              <th>Category</th>
              <th>Equipment</th>
              <th>Close Time</th>
              <th>Status</th>
           </tr>
        </thead>
        <tbody id="ins"  >

        </tbody>
     </table>
    </div>
     </div>
     </div>
     
</div>     
<script>

          $('#pieechart_3d').hide(); 
        $('#columnchart_values').hide();
        $('#tableData').hide();
       $('#buttons').hide();
    $("#btn").click(function(){
//   event.preventDefault();
  var datefilter=$('#datefilter').val();
  var sitefilter=$('#siteReportFilter').val();

            $('#a').empty();    
            $('#b').empty();
            $('#c').empty();
            $('#d').empty();
            $('#e').empty();
            $('#f').empty();
            $('#g').empty();
            $('#h').empty();
            $('#i').empty();
            $('#j').empty();

            if(sitefilter=='-1'){  
            $('#tableData').hide();
            $('#buttons').hide();
            $('#pieechart_3d').hide();
            $('#columnchart_values').hide();
           }


           else{ 
            $('#pieechart_3d').show();
            $('#columnchart_values').show();
            $('#tableData').show();
            $('#buttons').show();
            $('#ins').empty();
            $.ajax({
                url:'{{URL::to('/tickets/sitereports')}}',
                type:"get",
                data: {'id' : sitefilter, 'datefilter' : datefilter},
                datatype:"json",
                success: function(data) { 
                  
                    console.log(data[0]);
                    
                    //alert(data[0].client_image);
                    
					$('#middle_data').html(data[0].client_name);
					$('#client_logo').html("<img src='"+data[0].client_image+"' style='max-width:150px'>");
					//var siteurl = "{{url('/')}}";
					//$('#client_logo').html('<img src="'+siteurl+'/images/trafikLogo.png">');
					
					
                    if(data.length!='0'){  
					 
                     for(let i=0; i<data.length; i++){
                    
                        
						 var ap = i+1;	
                         var a = data[i].id;
                         var b = data[i].created_at;
                         var c = data[i].created;
                         var d = data[i].subject;
                         var e = data[i].issue;
                         var f= data[i].ticket_categories;
                         var g = data[i].equipment;
                         var h = data[i].close_time;
                         var z = data[i].status;
                  

                         added_row = '<tr>'
                        + '<td>'+'<b>' + ap +'</b>'+'</td>'
                        + '<td>'+'<b>' + a +'</b>'+'</td>'
                        + '<td>'+'<b>' + b + '</b>'+ '</td>'
                        + '<td>'+'<b>' + c + '</b>'+ '</td>'
                        + '<td>'+'<b>' + d +'</b>'+  '</td>'
                        + '<td>'+'<b>' + e + '</b>'+ '</td>'
                        + '<td>'+'<b>' + f + '</b>'+ '</td>'
                        + '<td>'+'<b>' + g + '</b>'+ '</td>'
                        + '<td>'+'<b>' + h + '</b>'+ '</td>';
                        
                        if(z=='open'){
							added_row += '<td class="tgreen" style="backgroundColor:#92d050"><b>' + z + '</b></td>';
						}else{
							added_row += '<td class="tred"><b>' + z + '</b></td>';
						}
                        
                        
                        added_row += '</tr>';
                        
                        $('#ins').append(added_row);
                       }
                      } 
                       else{  
                    $('#tableData').hide();
                    $('#pieechart_3d').hide();
                    $('#columnchart_values').hide();
                 }
                } 
                // }    
            
    });


    $.ajax({
                url:'{{URL::to('/tickets/ticketsdata')}}',
                type:"get",
                data: {'id' : sitefilter, 'datefilter' : datefilter},
                datatype:"json",
                success: function(ticketdata) { 
                  
                    console.log(ticketdata.length);
                    // 
                    google.charts.load("current", {packages:["corechart"]});
                    google.charts.setOnLoadCallback(drawChart);
                    function drawChart(){
                    if(ticketdata.length==1){
                    var data = google.visualization.arrayToDataTable([
                        ['Ticket', 'Tickets Data'],
                        [ticketdata[0].status,     ticketdata[0].total],
                        // [ticketdata[1].status,     ticketdata[1].total],
                        ]);
                    }
                    else{
                        var data = google.visualization.arrayToDataTable([
                        ['Ticket', 'Tickets Data'],
                        [ticketdata[0].status,     ticketdata[0].total],
                        [ticketdata[1].status,     ticketdata[1].total],
                        ]);
                    }

     
    
                        var options = {
                        title: 'Tickets Data According to Tickets Close And Open',
                        colors: ['#92d050', '#ff0000'],
                        is3D: true,
                        
                        };

                        var chart = new google.visualization.PieChart(document.getElementById('pieechart_3d'));
                        chart.draw(data, options);
                        }
                        }
                    // in console.log(ticketdata[0].total)  give 2. now i want to pass this value to pie chart
              
                        });


                        

                        $.ajax({
                url:'{{URL::to('/tickets/ticketsdata')}}',
                type:"get",
                data: {'id' : sitefilter, 'datefilter' : datefilter},
                datatype:"json",
                success: function(ticketdata) { 
                  
                    console.log("ticketdata=====>",ticketdata[0]);
                    
                    // 
                    google.charts.load("current", {packages:["corechart"]});
                    google.charts.setOnLoadCallback(drawChart);
                    function drawChart(){
                    if(ticketdata.length==1){

                        if(ticketdata[0].status=='close'){

                                    var data = google.visualization.arrayToDataTable([
                                    ['Ticket', 'Tickets Data'],
                                    [ticketdata[0].status,     ticketdata[0].total],
                                    // [ticketdata[1].status,     ticketdata[1].total],
                                    ]);
                                    var options = {

                                            title: 'Tickets Data According to Tickets Close And Open',
                                            colors: ['#92d050'],
                                            is3D: true,
                                            responsive:false,
                        
                                        };
                        }
                        
                        else if(ticketdata[0].status=='open'){

                                    var data = google.visualization.arrayToDataTable([
                                    ['Ticket', 'Tickets Data'],
                                    [ticketdata[0].status,     ticketdata[0].total],
                                    // [ticketdata[1].status,     ticketdata[1].total],
                                    ]);
                                    var options = {
                                    title: 'Tickets Data According to Tickets Close And Open',
                                    colors: ['#ff0000'],
                                    is3D: true,
                                    responsive:false,
                        
                                                };
                        }
                    }
                    
                        else{

                                var data = google.visualization.arrayToDataTable([
                                ['Ticket', 'Tickets Data'],
                                [ticketdata[0].status,     ticketdata[0].total],
                                [ticketdata[1].status,     ticketdata[1].total],
                                ]);
                                var options = {
                                                    title: 'Tickets Data According to Tickets Close And Open',
                                                    colors: ['#92d050', '#ff0000'],
                                                    is3D: true,
                                                    responsive:false,
                        
                                            };
                            }

     
    
                       

                        var chart = new google.visualization.PieChart(document.getElementById('pieechart_3d'));
                        chart.draw(data, options);
                        }
                        }
                    // in console.log(ticketdata[0].total)  give 2. now i want to pass this value to pie chart
              
                        });


                                //    HISTOGRAM

                                $.ajax({
                url:'{{URL::to('/tickets/ticketsdata')}}',
                type:"get",
                data: {'id' : sitefilter, 'datefilter' : datefilter},
                datatype:"json",
                success: function(ticketdata) { 
                  
                console.log(ticketdata.length);
                    
                google.charts.load("current", {packages:['corechart']});
                google.charts.setOnLoadCallback(drawChart);
                function drawChart() {
                if(ticketdata.length==1){
                    
                var data = google.visualization.arrayToDataTable([
                ['Tickets', 'Tickets Data', { role: 'style' } ],
                [ticketdata[0].status,     ticketdata[0].total, '#92d050'],
                        
       
        
                ]);
            }
                else{
                    var data = google.visualization.arrayToDataTable([
					['Tickets', 'Tickets Data', { role: 'style' } ],
					[ticketdata[0].status,     ticketdata[0].total, '#92d050'],
					[ticketdata[1].status,     ticketdata[1].total, '#ff0000']
				]);
                }
                var view = new google.visualization.DataView(data);                              
                view.setColumns([0, 1,
                       { calc: "stringify",
                         sourceColumn: 1,
                         type: "string",
                         role: "annotation" },
                       2]);
                var options = {title: "Tickets Data According to Tickets Close And Open",
                width: 600,
                height: 250,
                bar: {groupWidth: "95%"},
                legend: { position: "none" },                
                };
     

                var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values"));
                chart.draw(data, options);
            }
        }
    });
    } 
});  




</script>
<script src="https://cdn.jsdelivr.net/gh/linways/table-to-excel@v1.0.4/dist/tableToExcel.js"></script>
<script>
    
$(document).ready(function(){
    $("#excel").click(function() {
        let table = document.getElementById('tableData');
        let piechart = document.getElementById('pieechart_3d');
       
        TableToExcel.convert(table, piechart, { // html code may contain multiple tables so here we are refering to 1st table tag
           name: `TraficSol.xlsx`, // fileName you could use any name
           sheet: {
              name: 'Sheet 1' // sheetName
           }
        });
    });
    });

  
</script>

<script> 

    //user-defined function to download CSV file  
    function downloadCSV(csv, filename) {  
        var csvFile;  
        var downloadLink;  
         
        //define the file type to text/csv  
        csvFile = new Blob([csv], {type: 'text/csv'});  
        downloadLink = document.createElement("a");  
        downloadLink.download = filename;  
        downloadLink.href = window.URL.createObjectURL(csvFile);  
        downloadLink.style.display = "none";  
      
        document.body.appendChild(downloadLink);  
        downloadLink.click();  
    }  
      
    //user-defined function to export the data to CSV file format  
    function exportTableToCSV(filename) {  
       //declare a JavaScript variable of array type  
       var csv = [];  
       var rows = document.querySelectorAll("#tableData tr");  
       
       //merge the whole data in tabular form   
       for(var i=0; i<rows.length; i++) {  
        var row = [], cols = rows[i].querySelectorAll("td, th");  
        for( var j=0; j<cols.length; j++)  
           row.push(cols[j].innerText);  
        csv.push(row.join(","));  
       }   
       //call the function to download the CSV file  
       downloadCSV(csv.join("\n"), filename);  
    }  

 </script>
 <script>   
  

</script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js" integrity="sha512-s/XK4vYVXTGeUSv4bRPOuxSDmDlTedEpMEcAQk0t/FMd9V6ft8iXdwSBxV0eD60c6w/tjotSlKu9J2AAW1ckTA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.3/jspdf.min.js"></script>
<script src="https://rawgit.com/someatoms/jsPDF-AutoTable/master/dist/jspdf.plugin.autotable.js"></script>
<script type="text/javascript" src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
<script type="text/javascript" src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

<script>
    
    function demo(){

		html2canvas(document.querySelector("#capture")).then(canvas =>{
			
		   var img = canvas.toDataURL('image/png');
			var pdf = new jsPDF('p', 'mm', 'a3');
			pdf.autoTable({

				theme:'grid',
				//columnStyles: { 0: { halign: 'center',  fillColor: [203, 179, 225] }  },
				margin: { top: 120 },
				html:'#tableData',
			})
		   
			pdf.setFont('TimesNewRoman');
			pdf.setFontSize(60);
			pdf.addImage(img, 'png', 0, 0, 0, 0);
			
			var alias = $('#siteReportFilter option:selected').attr('sitealias');
			var mtype = $('#datefilter').val();
			
			
			pdf.save(alias+'_'+mtype+'.pdf')
		});
    }
    
    function getPDF(){

		var HTML_Width = $(".canvas_div_pdf").width();
		var HTML_Height = $(".canvas_div_pdf").height();
		var top_left_margin = 15;
		var PDF_Width = HTML_Width+(top_left_margin*2);
		var PDF_Height = (PDF_Width*1.5)+(top_left_margin*2);
		var canvas_image_width = HTML_Width;
		var canvas_image_height = HTML_Height;
		
		var totalPDFPages = Math.ceil(HTML_Height/PDF_Height)-1;
		

		html2canvas($(".canvas_div_pdf")[0],{allowTaint:true}).then(function(canvas) {
			canvas.getContext('2d');
			
			console.log(canvas.height+"  "+canvas.width);
			
			
			var imgData = canvas.toDataURL("image/jpeg", 1.0);
			var pdf = new jsPDF('p', 'pt',  [PDF_Width, PDF_Height]);
		    pdf.addImage(imgData, 'JPG', top_left_margin, top_left_margin,canvas_image_width,canvas_image_height);
			
			
			for (var i = 1; i <= totalPDFPages; i++) { 
				pdf.addPage(PDF_Width, PDF_Height);
				pdf.addImage(imgData, 'JPG', top_left_margin, -(PDF_Height*i)+(top_left_margin*4),canvas_image_width,canvas_image_height);
			}
			
			var alias = $('#siteReportFilter option:selected').attr('sitealias');
			var mtype = $('#datefilter').val();
		    pdf.save(alias+'_'+mtype+'.pdf');
        });
	};


let da=document.getElementById('pdff');
da.addEventListener('click', getPDF);
//

</script>




                                        {{-- Google Pie chart And Hostogram --}}


<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">


</script>




@endsection




