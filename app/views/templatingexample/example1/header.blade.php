<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Welcome to Back Office TimeTracker! BPO TimeTracker!</title>

    <!-- Bootstrap core CSS -->
    <!--link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css"-->    

    <link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ URL::asset('assets/css/jquery-ui.css') }}">     

    <!-- Google Font -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:700,300,400|Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>    

    <!-- Font Awesome -->
    <!--link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css"-->   
    <link rel="stylesheet" href="{{ URL::asset('assets/css/font-awesome.min.css') }}">   

    <!-- Custom styles for this template -->
    <link href="{{ URL::asset('assets/css/style.css') }}" rel="stylesheet">   

    <link href="{{ URL::asset('assets/css/metisMenu.min.css') }}" rel="stylesheet">    

    <link href="{{ URL::asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet"> 

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src={{ URL::asset('assets/js/ie8-responsive-file-warning.js') }}"></script><![endif]-->
    <script src="{{ URL::asset('assets/js/ie-emulation-modes-warning.js') }}"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->


  </head>

  <body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#"><span>Welcome to</span> <strong><span>BackOffice</span> TimeTracker!</strong> BPO TimeTracker!</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse pull-right">
          <ul class="nav navbar-nav">            

            <li class="hide hidden"><a href="{{ URL::to('users/logout') }}">Log Out <i class="fa fa-sign-out"></i></a></li>
            <li style="color:white;"><a href="#">Welcome <?php //echo $employeeInfo[0]->firstname. ', ' .$employeeInfo[0]->lastname; ?></a></li>
            <li id="timesheet-link" class="hide hidden"><a href="{{ url('/employee/clocking') }}"><i class="fa fa-clock-o"></i> Timesheet</a></li>
            <li class="active"><a href="{{ url('/admin/dashboard') }}"><i class="fa fa-tachometer"></i> Dashboard</a></li>
            <li><a href="{{ URL::to('users/logout') }}">Log Out <i class="fa fa-sign-out"></i></a></li>            

          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">    
      @yield('content')
    </div> <!-- /container -->

