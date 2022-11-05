@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


      <div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">{{__('Project Phase List')}}</h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>  

      <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Project Phase Management</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ route('phases.create') }}"> Create New Phase</a>
            </div>
        </div>
      </div>

      @include('layouts.flash')

      <table class="table table-bordered">
        <tr>
          <th>No</th>
          <th>Project</th>
          <th>Name</th>               
          <th>Alias name</th>    
          <th width="280px">Action</th>
        </tr>
        @foreach ($phases as $key => $phase)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $phase->project->name }}</td>
            <td>{{ $phase->name }}</td>           
            <td>{{ $phase->alias_name }}</td>         
            <td>
              <a class="btn btn-info" href="{{ route('phases.show',$phase->id) }}">Show</a>
              <a class="btn btn-primary" href="{{ route('phases.edit',$phase->id) }}">Edit</a>
                {!! Form::open(['method' => 'DELETE','route' => ['phases.destroy', $phase->id],'style'=>'display:inline']) !!}
                    {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                {!! Form::close() !!}
            </td>
        </tr>
        @endforeach
      </table>
      {!! $phases->render() !!}


		</div>		
	</div>
	<!-- /#page-wrapper -->
            
@endsection
