<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- CSRF Token -->
		<meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TrafikSol') }}</title>
	<script src="{{ asset('js/jquery-3.5.1.js') }}"></script>
        
        <!-- Bootstrap Core CSS -->
        
        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

        <link href="{{ asset('css/dataTables/dataTables.bootstrap.css') }}" rel="stylesheet">
        <link href="{{ asset('css/dataTables/buttons.dataTables.min.css') }}" rel="stylesheet">        
        <link href="{{ asset('css/dataTables/dataTables.responsive.css') }}" rel="stylesheet">
        
        
        <!-- MetisMenu CSS -->
        <link href="{{ asset('css/metisMenu.min.css') }}" rel="stylesheet">

        <!-- Timeline CSS -->
        
        <!-- Custom CSS -->
        <link href="{{ asset('css/startmin.css') }}" rel="stylesheet">

        <!-- Morris Charts CSS -->
        <link href="{{ asset('css/morris.css') }}" rel="stylesheet">
        
        <!-- Custom Fonts -->
        <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">

        <link href="{{ asset('css/jquery-confirm.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/jquery.datePicker.css') }}" rel="stylesheet">
        <link href="{{ asset('css/jquery.fancybox.min.css') }}" rel="stylesheet">
                

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>


        <![endif]-->
    </head>
    <body>

        <div id="wrapper">
			@yield('content')
        </div>
        <!-- /#wrapper -->

        <!-- jQuery -->
        
                        
        <!-- Bootstrap Core JavaScript -->
        

        
	 
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>

        <script src="{{ asset('js/dataTables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('js/dataTables/dataTables.bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/dataTables/dataTables.buttons.min.js') }}"></script>    
        <script src="{{ asset('js/dataTables/pdfmake.min.js') }}"></script>  
        <script src="{{ asset('js/dataTables/vfs_fonts.js') }}"></script>      
        <script src="{{ asset('js/dataTables/buttons.html5.min.js') }}"></script>  
	   
           

        <!-- Metis Menu Plugin JavaScript -->
        <script src="{{ asset('js/metisMenu.min.js') }}" defer></script>

        <!-- Morris Charts JavaScript -->
        <script src="{{ asset('js/raphael.min.js') }}" defer></script>
        
        <script src="{{ asset('js/jquery-confirm.min.js') }}"></script>       

        <!-- Custom Theme JavaScript -->
        <script src="{{ asset('js/startmin.js') }}" defer></script>
        <script src="{{ asset('js/multiselect.js') }}"></script>
        
        <script src="{{ asset('js/moment-with-locales.min.js') }}"></script>
        <script src="{{ asset('js/jquery.datePicker.js') }}"></script>
        <script src="{{ asset('js/jquery.fancybox.min.js') }}"></script>
        {{-- <script src="{{asset('js/jspdf.js')}}"></script> --}}
        

      
        <script src="{{ asset('js/custom.js') }}"></script>
      
	  <script>
        $(document).ready(function(){
            var site_url = "{{ url('/') }}";
            var csrf_token = "{{ csrf_token() }}";
            setSiteURL(site_url,csrf_token);
        });
        </script>
        
    </body>
</html>
