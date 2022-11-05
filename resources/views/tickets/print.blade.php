	
<style type = "text/css">
    table tr td,table tr th{
		text-align: center;
        border:1px solid #000;
	}
	@media only screen and (max-width: 500px) {

	 table,head,tbody,th,td,tr {
		display: block;
        border:1px solid #000;
	}
	thead tr {
	display: none;
	}
	tr {
	 border: 1px solid #ccc;
	}
	td {
	border: none;
	border-bottom: 1px solid #eee;
	position: relative;
	padding-left: 50%;
	white-space: normal;
	text-align:left;
	min-height: 30px;
	overflow: hidden;
	word-break:break-all;
	}
	td:before {
	position: absolute;
	top: 6px;
	left: 6px;
	width: 45%;
	padding-right: 10px;
	text-align:left;
	font-weight: bold;
	}
	td:before { content: attr(data-title); }
}
</style>

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">sdfgdgf</h1>
                    </div>
            </div>

            <div class="row">
                    <table cellpadding="0" style="width:100%;">
                        <tr>
                            <td style="padding-left:0px !important;text-align:left;border:0px;"><img src="sdf" style="max-width:200px;"></td>
                            <td style="float:right;text-align:right;border:0px;"><img src="{{url('/')}}/images/trafikLogo.png?kk" style="max-width:180px;"></td>
                        </tr>

                    </table>

            </div>

            <div class="row">
                <div id="piechart" style="width: 900px; height: 500px;"></div>

            </div>


               
                <table id="tableData" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">

                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>User Email</th>
                        </tr>
                    </thead>
                    <tbody>
                           
                            <tr>
                                <td>dsgf</td>
                                <td>sdfsdf</td>
                            </tr>
                           
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Username</th>
                            <th>User Email</th>
                        </tr>
                    </tfoot>
                </table>
                

		</div>
	</div>
	
	<button class="btn btn-danger" onclick="demo()">Download</button>
	
		
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js" integrity="sha512-s/XK4vYVXTGeUSv4bRPOuxSDmDlTedEpMEcAQk0t/FMd9V6ft8iXdwSBxV0eD60c6w/tjotSlKu9J2AAW1ckTA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.3/jspdf.min.js"></script>
	<script src="https://rawgit.com/someatoms/jsPDF-AutoTable/master/dist/jspdf.plugin.autotable.js"></script>
	<script type="text/javascript" src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
	<script type="text/javascript" src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
	
	<script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Task', 'Hours per Day'],
          ['Work',     11],
          ['Eat',      2],
          ['Commute',  2],
          ['Watch TV', 2],
          ['Sleep',    7]
        ]);

        var options = {
          title: 'My Daily Activities'
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
      }
    
    
    
    function demo(){
    
		html2canvas(document.querySelector("#page-wrapper")

		).then(canvas =>{
			
		   var img = canvas.toDataURL('image/png');
			var pdf = new jsPDF('p', 'mm', 'a3');
			   pdf.autoTable({

			theme:'grid',
			//columnStyles: { 0: { halign: 'center',  fillColor: [203, 179, 225] }  },
			margin: { top: 120 },
			html:'#page-wrapper',
		})
		   
			pdf.setFont('TimesNewRoman');
			pdf.setFontSize(60);
			pdf.addImage(img, 'png', 0, 0, 0, 0);
			
			pdf.save('sadf.pdf')
		});
    }
    
    
	</script>
	<!-- /#page-wrapper -->
