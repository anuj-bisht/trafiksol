<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="navbar-header">
		<a class="navbar-brand" href="index.html">TrafikSol</a>
	</div>

	<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	</button>

	<ul class="nav navbar-right navbar-top-links">
		<li class="dropdown navbar-inverse "   onclick="notificationData()">
			<a class="dropdown-toggle"  data-toggle="dropdown" href="#" style="color:red">
			<span id="notificationid" ></span><i class="fa fa-bell fa-fw"></i> <b class="caret"></b>
			</a>
			<ul class="dropdown-menu dropdown-alerts"  style="max-height:500px; overflow-y:scroll;" id="notification_ul"  >
							
			</ul>
		</li>
		<li class="dropdown">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#">
				<i class="fa fa-user fa-fw"></i> {{ Auth::user()->name }} <b class="caret"></b>
			</a>
			<ul class="dropdown-menu dropdown-user">
				<li><a href="#"><i class="fa fa-user fa-fw"></i> change password</a>
				</li>				
				<li class="divider"></li>
				<li>
				<a class="dropdown-item" href="{{ route('logout') }}"
					onclick="event.preventDefault();
									document.getElementById('logout-form').submit();">
					{{ __('Logout') }}
				</a>
				<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
					@csrf
				</form>				
				</li>
			</ul>
		</li>
	</ul>
	<!-- /.navbar-top-links -->

	<div class="navbar-default sidebar" role="navigation" style="overflow:auto">
		<div class="sidebar-nav navbar-collapse">
			<ul class="nav" id="side-menu">
				
				<li>
					<a href="{{ route('home') }}" class="active"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
				</li>

				@can('role-list')
				
				@endcan 

				<li>
					<a href="#"><i class="fa fa-files-o fa-fw"></i> User Management<span class="fa arrow"></span></a>
					<ul class="nav nav-second-level">
						<li>
							<a href="{{ route('type_users.index') }}">User Type Mangement</a>
						</li>		
						<li>
							<a href="{{ route('users.index') }}">Manage Users</a>
						</li>				
						@can('role-list')
						<li>
							<a href="{{ route('roles.index') }}"> Manage Role</a>
						</li>
						@endcan 
						
					</ul>
					<!-- /.nav-second-level -->
				</li>								
				
				<li>
					<a href="#"><i class="fa fa-files-o fa-fw"></i> Total Management<span class="fa arrow"></span></a>
					<ul class="nav nav-second-level">
						<li>
							<a href="{{ route('clients.index') }}">Client Details</a>
						</li>
						<li>
							<a href="{{ route('projects.index') }}">Project Details</a>
						</li>												
						<li>
							<a href="{{ route('sites.index') }}">Site Details</a>
						</li>
					</ul>
					<!-- /.nav-second-level -->
				</li>
				<li>
					<a class="nav-link" href="{{ route('equipment_slas.index') }}"><i class="fa fa-shield" aria-hidden="true"></i> SLA Management</a>
				</li>
				<li>
					<a href="#">Equipment Management<span class="fa arrow"></span></a>
					<ul class="nav nav-second-level">
												
						<li>
							<a class="nav-link" href="{{ route('equipments.index') }}"><i class="fa fa-shield" aria-hidden="true"></i> Equipments</a>
						</li>
						<li>
							<a class="nav-link" href="{{ route('brands.index') }}"><i class="fa fa-shield" aria-hidden="true"></i> Brand Management</a>
						</li>		
						<li>
							<a class="nav-link" href="{{ route('models.index') }}"><i class="fa fa-money" aria-hidden="true"></i> Model Management</a>
						</li>
						<li>
							<a class="nav-link" href="{{ route('stores.index') }}"><i class="fa fa-money" aria-hidden="true"></i> Store Management</a>
						</li>				
					</ul>
				</li>
				<li>
					<a class="nav-link" href="{{ route('uoms.index') }}"><i class="fa fa-microchip" aria-hidden="true"></i> UOM Management</a>
				</li>
				<li>
					<a href="#"><i class="fa fa-files-o fa-fw"></i> Activity Management<span class="fa arrow"></span></a>
					<ul class="nav nav-second-level">
						<li>
							<a href="{{ route('activity_categories.index') }}">Activity Category Mangement</a>
						</li>	
						<li>
							<a href="{{ route('activities.index') }}">Activity list</a>
						</li>						
					</ul>
					<!-- /.nav-second-level -->
				</li>
				
				<li>
					<a href="#"><i class="fa fa-industry" aria-hidden="true"></i> Vendor Management<span class="fa arrow"></span></a>
					<ul class="nav nav-second-level">
						<li>
							<a href="{{ route('category_vendors.index') }}">Category Mangement</a>
						</li>	
						<li>
							<a href="{{ route('vendors.index') }}">Vendor Mangement</a>
						</li>						
					</ul>
					<!-- /.nav-second-level -->
				</li>

				<li>
					<a href="#"><i class="fa fa-files-o fa-fw"></i> Expense Management<span class="fa arrow"></span></a>
					<ul class="nav nav-second-level">
						<li>
							<a href="{{ route('category_expences.index') }}">Category Mangement</a>
						</li>						
					</ul>
					<!-- /.nav-second-level -->
				</li>

				<li>
					<a href="#"><i class="fa fa-industry" aria-hidden="true"></i> DPR Management<span class="fa arrow"></span></a>
					<ul class="nav nav-second-level">
						
						<li>
							<a class="nav-link" href="{{url('/')}}/reports"><i class="fa fa-shield" aria-hidden="true"></i> DPR Reports</a>
						</li>
						<li>
							<a class="nav-link" href="{{url('/')}}/activities/dpractivity"><i class="fa fa-shield" aria-hidden="true"></i> DPR Activity</a>
						</li>
						<li>
							<a class="nav-link" href="{{url('/')}}/expences/dprexpence"><i class="fa fa-shield" aria-hidden="true"></i> DPR Expenses</a>
						</li>
						<li>
							<a class="nav-link" href="{{url('/')}}/vehicles/dprvehicle"><i class="fa fa-shield" aria-hidden="true"></i> DPR Vehicles</a>
						</li>												
						<li>
							<a class="nav-link" href="{{url('/')}}/activities/activitytomorrow"><i class="fa fa-shield" aria-hidden="true"></i> Tomorrow DPR Activity</a>
						</li>	
						<li>
							<a class="nav-link" href="{{url('/')}}/attendances/manpower"><i class="fa fa-shield" aria-hidden="true"></i> Manpower Attendance</a>
						</li>											
					</ul>
				</li>

				<li>
					<a href="#"><i class="fa fa-industry" aria-hidden="true"></i> Attendance Management<span class="fa arrow"></span></a>
					<ul class="nav nav-second-level">
						
						<li>
							<a class="nav-link" href="{{ route('attendances.index') }}"><i class="fa fa-money" aria-hidden="true"></i> Attendance Management</a>
						</li>												
					</ul>
				</li>

				<li>
					<a class="nav-link" href="#"><i class="fa fa-microchip" aria-hidden="true"></i>Report Management<span class="fa arrow"></span></a>
					<ul class="nav nav-second-level">
						<li>
							<a href="{{url('/tickets/sitereport')}}">Report</a>
						</li>											
					</ul>
				</li>

				<li>
					<a class="nav-link" href="#"><i class="fa fa-microchip" aria-hidden="true"></i> Notification Management<span class="fa arrow"></span></a>
					<ul class="nav nav-second-level">
						<li>
							<a href="{{ route('notifications.index') }}">Notification Lists</a>
						</li>											
					</ul>
				</li>

				<li>
					<a href="#"><i class="fa fa-files-o fa-fw"></i> Vehicle Management<span class="fa arrow"></span></a>
					<ul class="nav nav-second-level">
						<li>
							<a href="{{ route('type_vehicles.index') }}">Vechicle Type Mangement</a>
						</li>						
						<li>
							<a href="{{ route('vehicles.index') }}">Vehicle List</a>
						</li>						
					</ul>
					<!-- /.nav-second-level -->
				</li>

				<li>
					<a class="nav-link" href="#"><i class="fa fa-microchip" aria-hidden="true"></i> Ticket Management<span class="fa arrow"></span></a>
					<ul class="nav nav-second-level">
						<li>
						<a href="{{url('/')}}/tickets/pause">Pause Request({{$pause}})</a>
						</li>
						<li>
							<a href="{{ route('ticket_issue_types.index') }}">Ticket Issue Types</a>
						</li>	
						<li>
							<a href="{{ route('ticket_categories.index') }}">Ticket Categories</a>
						</li>					
						<li>
							<a href="{{ route('tickets.index') }}">Ticket list</a>
						</li>
						<li>
							<a href="{{url('/')}}/tickets/hwrequest">Hardware Request</a>
						</li>					
					</ul>
				</li>

			</ul>
		</div>
	</div>
</nav>

<script>

</script>