@extends('layouts.app')

@section('content')

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
       <option value="{{$v->id}}">{{$v->sitename}}</option>
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
 <div id="buttons"><a id="excel" class="btn btn-success">Excel</a>&nbsp;&nbsp;<a id="pdf" class="btn btn-success">PDF</a>&nbsp;&nbsp;<a id="csv" onclick="exportTableToCSV('ReportData.csv')" class="btn btn-success">CSV</a>&nbsp;&nbsp;</div>
 

                    {{-- BAR CHART AND PIE CHART --}}
  <div class="container">
    <div class="row">
       <div class="col-sm-5">
          <div  id="pieechart_3d"></div>
       </div>
       <div class="col-sm-7">
          <div  id="columnchart_values" ></div>
       </div>
    </div>
 </div>
 
 
 <table id="tableDat" class="table-responsive table table-striped  bb table-bordered" style="font-size:12px;width:100% !important">
    <thead>
       <tr>
          <th>Ticket Id</th>
          <th>Ticket Created Date</th>
          <th>Ticket Created By</th>
          <th>Subject</th>
          <th>Issue Type</th>
          <th>Category</th>
          <th>Equipment</th>
          <th>Ticket Closure Time</th>
          <th>Status</th>
       </tr>
    </thead>
    <tbody id="in">
        @foreach($pdfdata as $data)
        <tr>
            <td>{{$data->id}}</td>
            <td>{{$data->created_at}}</td>
            <td>{{$data->created}}</td>
            <td>{{$data->subject}}</td>
            <td>{{$data->issue}}</td>
            <td>{{$data->ticket_categories}}</td>
            <td>{{$data->equipment}}</td>
            <td>{{$data->close_time}}</td>
            <td>{{$data->status}}</td>
        
        </tr>

        @endforeach

    </tbody>
 </table>
 </div>
 </div>
            
@endsection