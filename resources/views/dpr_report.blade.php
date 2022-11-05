<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title></title>	

</head>
<style>

.pinkcls{
	background:#fbe5dc;
	font-weight:bold;
	border:1px solid #000;
}
.blueheading{
	background:#0070c0;
	color:#fff;
	font-weight:bold;
}
.inner-table td{
	border:1px solid #000;	
	margin:0px;
}

.inner-table th{
	border:1px solid #000;	
	margin:0px;
	text-align:left;
}
.top_center{
	
	font-weight:bold;
	font-size:16px;
	background:#fff;
	border:0px !important;
}
.table_address{
	background:#fff;
}
button{
	background: #0070c0;
    width: 107px;
    height: 37px;
    border-radius: 4px;
    cursor: pointer;
    color: #fff;
    font-weight: bold;
    border: none;    
    margin: 5px;
    vertical-align: top;
}
</style>
<body>


<table class="table-responsive table table-striped" width="100%">
	<tr>
		<td width="33%" valign="top">
			<table>
				<tr>
					<td><img src="{{url('/')}}/images/trafikLogo.png" style="max-width:120px"></td>
				</tr>
				<tr>
					<td class="">Daily Site Progress report.</td>
				</tr>
				<tr>
					<td>Report No: TSITSPL-DPR-{{$site_data->alias_name}}-{{date('Ymd')}}</td>
				</tr>
								
				<tr>
					<td class="" style="float:left">Report Prepared By: {{auth()->user()->name}}</td>
				</tr>
				<tr>
					<td class="" style="float:left">Report Reviewed By: {{auth()->user()->name}}</td>
				</tr>
								
			</table>
		</td>
		<td width="33%" style="text-align:center" valign="top">
			<table align="center">
				<tr>
					<td class="top_center" colspan="2">TrafikSol ITS Technologies Pvt. Ltd.</td>
				</tr>
				<tr>
					<td class="top_center" colspan="2">C66, 2nd Floor, Sec 63, NOIDA.</td>
				</tr>
			</table>
		</td>
		<td width="33%" valign="top">
			<table width="100%" class="table-responsive">
				<tr>
					<td style="text-align:right"><img src="{{$site_data->client_image}}" alt="{{$site_data->client_name}}" style="max-width:120px;"></td>
				</tr>
				<tr>
					<td  style="float:right" class="">Client: {{$site_data->client_name}}.</td>
				</tr>
				<tr>
					<td  style="float:right" class="">Site: {{$site_data->name}}.</td>
				</tr>
				<tr>
					<td style="float:right">Form No: TSITSPL-DPR-{{$site_data->alias_name}}-{{date('Ymd')}}</td>
				</tr>
				<tr>
					<td  style="float:right" class="">Date: {{date('D d M, Y')}}</td>
				</tr>
								
				<tr>
					<td class="">&nbsp;</td>
				</tr>				
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<table class="table-responsive inner-table" width="100%">
				<tr class="pinkcls">
					<td colspan="7">Todays Activity</td>
				</tr>
				<tr>
					<td class="blueheading">SI</td>
					<td class="blueheading">Work</td>
					<td class="blueheading">Location</td>
					<td class="blueheading">Images</td>
					<td class="blueheading">UoM</td>
					<td class="blueheading">Qty</td>
					<td class="blueheading">RFI No</td>
				</tr>

				@if(count($today_activities)>0)
					@foreach($today_activities as $k=>$v)
						<tr>
							<td>{{$k+1}}</td>
							<td>{{$v->activity_name}}</td>
							<td>{{$v->activity_name}}</td>
							<td>
								@if ($v->images != "")
								@foreach(explode(',', $v->images) as $k => $image) 
									<a href="{{$image}}">Image {{$k+1}}</a>,
								@endforeach
								@endif								
							</td>
							<td>{{$v->uom_name}}</td>
							<td>{{$v->quantity}}</td>	
							<td>{{$v->rfi_no}}</td>	
						</tr>
					@endforeach
				@else
						<tr><td colspan="7"><i> No activities </i></td></tr>	
				@endif
				<tr>
					<td colspan="7" style="border:0px">&nbsp;</td>
					
				</tr>
				<tr class="pinkcls">
					<td colspan="7">Tomorrow's Activity</td>
				</tr>
				<tr>
					<td class="blueheading">SI</td>
					<td class="blueheading" colspan="3">Work</td>
					<td class="blueheading">Location</td>
					<td class="blueheading">UoM</td>
					<td class="blueheading">Qty</td>					
				</tr>
				@if(count($tomorrows_activities)>0)
					@foreach($tomorrows_activities as $k=>$v)
						<tr>
							<td>{{$k+1}}</td>
							<td colspan="3">{{$v->activity_name}}</td>
							<td>{{$v->activity_name}}</td>
							<td>{{$v->activity_name}}</td>
							<td>{{$v->activity_name}}</td>										
						</tr>
					@endforeach
				@else
						<tr><td colspan="7" style="text-align:center"><i>No record found</i></td></tr>	
				@endif
				<tr>
					<td colspan="7" style="border:0px">&nbsp;</td>
					
				</tr>

				<tr class="pinkcls">
					<td colspan="7">Expense Details</td>
				</tr>
				<tr>
					<td class="blueheading">SI</td>
					<td class="blueheading">Expense Details</td>
					<td class="blueheading">Rate</td>
					<td class="blueheading">Image</td>
					<td class="blueheading">Qty</td>
					<td class="blueheading">Amount</td>					
					<td class="blueheading">Remarks</td>	
				</tr>
				@if(count($todays_expences)>0)
					@foreach($todays_expences as $k=>$v)
						<tr>
							<td>{{$k+1}}</td>
							<td>{{$v->expence_category_name}}</td>
							<td>{{$v->rate}}</td>
							<td>
								@if ($v->images != "")
								@foreach(explode(',', $v->images) as $k => $image) 
									<a href="{{$image}}">Image {{$k+1}}</a>,
								@endforeach
								@endif
							</td>
							<td>{{$v->quantity}}</td>
							<td>{{$v->amount}}</td>										
							<td>{{$v->remarks}}</td>										
						</tr>
					@endforeach
				@else
						<tr><td colspan="7" style="text-align:center"><i>No record found</i></td></tr>	
				@endif		
				<tr>
					<td colspan="7">&nbsp;</td>					
				</tr>
				<tr>
					<td colspan="5"><b>Total Expense for the Day</b></td>					
					<td>{{$total_expence_day}}</td>					
					<td>&nbsp;</td>					
				</tr>
				<tr>
					<td colspan="5"><b>Total Expense for the Month</b></td>					
					<td>{{$total_expence_for_month}}</td>					
					<td>&nbsp;</td>					
				</tr>
				<tr>
					<td colspan="5"><b>Advance Taken for the Month ({{$month_for}} )</b></td>					
					<td>{{$advance_for_month}}</td>					
					<td>&nbsp;</td>					
				</tr>
				<tr>
					<td colspan="7" style="border:0px">&nbsp;</td>
					
				</tr>
				<tr class="pinkcls">
					<td colspan="7"><b>Vehicle Running Details</b></td>																			
				</tr>
				<tr>
					<td colspan="5" class="blueheading"><b>Description</b></td>														
					<td class="blueheading"><b>KM or LTr.</b></td>					
					<td class="blueheading"><b>Remarks</b></td>					
				</tr>

				
				@if(count($vehicle_dpr)>0)
					@foreach($vehicle_dpr as $k=>$v)
					<tr>
						<td colspan="5">{{$v->description}}</td>
						<th>{{$v->distance}} {{$v->uom_name}}</th>					
						<th>&nbsp;</th>
						
					</tr>
					@endforeach
				@else
						<tr><td colspan="7" style="text-align:center"><i>No record found</i></td></tr>	
				@endif	
				
				<tr>
					<td colspan="7">&nbsp;</td>
				</tr>													
				<tr>
					<th colspan="5" style="text-align:left">Total Running KM for the Day</th>
					<th style="text-align:left">{{$total_run_for_day}} KM</th>					
					<th>&nbsp;</th>
					
				</tr>	
				<tr>
					<th colspan="5" style="text-align:left">Total Running KM for the Month</th>
					<th style="text-align:left">{{$total_run_for_month}} KM</th>					
					<th>&nbsp;</th>
					
				</tr>	
				<tr>
					<th colspan="5" style="text-align:left">Diesel Filling (if any) for the day in Ltr</th>
					<th style="text-align:left">{{$total_diesel_day}} Ltr.</th>					
					<th>&nbsp;</th>
					
				</tr>	
				<tr>
					<th colspan="5" style="text-align:left">Diesel Filling for the Month in Ltr</th>
					<th style="text-align:left">{{$total_diesel_month}} Ltr.</th>					
					<th>&nbsp;</th>
					
				</tr>	
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<table class="table-responsive inner-table" width="100%">
							
				<tr>
					<td colspan="7" style="border:0px">&nbsp;</td>
					
				</tr>
				<tr class="pinkcls">
					<td colspan="7"><b>TrafikSol Manpower Details</b></td>																		
				</tr>
				<tr>
					<th class="blueheading">SI</th>
					<th class="blueheading">Name</th>
					<th class="blueheading" colspan="3">Designation</th>
					<th class="blueheading">Presence</th>
					<th class="blueheading">Remarks</th>					
					
				</tr>	
					
				@if(count($attendance_trafiksol)>0)
					@foreach($attendance_trafiksol as $k=>$v)
						<tr>
							<td>{{$k+1}}</td>
							<td>{{$v->username}}</td>
							<td colspan="3">{{$v->role_name}}</td>
							<td>{{$v->attendance}}</td>
							<td>&nbsp;</td>										
						</tr>
					@endforeach
				@else
						<tr><td colspan="7" style="text-align:center"><i>No record found</i></td></tr>	
				@endif		
				<tr>
					<td colspan="7" style="border:0px">&nbsp;</td>					
				</tr>
				<tr class="pinkcls">
					<td colspan="7"><b>Vendor Manpower Details</b></td>																							
				</tr>
				<tr>
					<th class="blueheading">SI</th>
					<th class="blueheading">Name</th>
					<th class="blueheading" colspan="3">Designation</th>
					<th class="blueheading">Presence</th>
					<th class="blueheading">Remarks</th>					
					
				</tr>	
				@if(count($attendance_vendor)>0)
					@foreach($attendance_vendor as $k=>$v)
						<tr>
							<td>{{$k+1}}</td>
							<td>{{$v->username}}</td>
							<td colspan="3">{{$v->role_name}}</td>
							<td>{{$v->attendance}}</td>
							<td>&nbsp;</td>										
						</tr>
					@endforeach
				@else
						<tr><td colspan="7" style="text-align:center"><i>No record found</i></td></tr>	
				@endif				
				
				
				
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<table class="table-responsive inner-table" width="100%">
				<tr>
					<td colspan="5" style="border:0px">&nbsp;</td>
					
				</tr>		
				<tr class="pinkcls">
					<td colspan="5">Total Activity</td>
				</tr>
				<tr>
					<td class="blueheading">SI</td>
					<td class="blueheading">Equipment</td>
					<td class="blueheading">UoM</td>
					<td class="blueheading">Qty</td>
					<td class="blueheading">finished</td>										
				</tr>
				@if(count($total_activities)>0)
					@foreach($total_activities as $k=>$v)
						<tr>
							<td>{{$k+1}}</td>
							<td>{{$v->activity_name}}</td>
							<td>{{$v->activity_name}}</td>
							<td>{{$v->activity_name}}</td>
							<td>{{$v->activity_name}}</td>	
						</tr>
					@endforeach
				@else
						<tr><td colspan="5"><i>No activities</i></td></tr>	
				@endif
				
			</table>
		</td>
	</tr>
</table>	
				
		
</body>

</html>
