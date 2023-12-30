<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" href="#" type="image/x-icon">
    <link rel="icon" href='{!! url("assets/logo-main-imigrasi.png")  !!}' type="image/x-icon">
	<title>SI TENAR</title>

	<!-- Global stylesheets -->
	<!-- <link href="{!! url('/') !!}/lib/css/css.css" rel="stylesheet" type="text/css"> -->
	<link href="{!! url('/') !!}/lib/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="{!! url('/') !!}/lib/css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="{!! url('/') !!}/lib/css/core.css" rel="stylesheet" type="text/css">
	<link href="{!! url('/') !!}/lib/css/components.css" rel="stylesheet" type="text/css">
	<link href="{!! url('/') !!}/lib/css/colors.css" rel="stylesheet" type="text/css">
	<!-- /global stylesheets -->

	<!-- Core JS files -->
	<script type="text/javascript" src="{!! url('/') !!}/lib/js/plugins/loaders/pace.min.js"></script>
	<script type="text/javascript" src="{!! url('/') !!}/lib/js/core/libraries/jquery.min.js"></script>
	<script type="text/javascript" src="{!! url('/') !!}/lib/js/core/libraries/bootstrap.min.js"></script>
	<script type="text/javascript" src="{!! url('/') !!}/lib/js/plugins/loaders/blockui.min.js"></script>
	<!-- /core JS files -->
	
    <script type="text/javascript" src="{!! url('/') !!}/lib/js/plugins/notifications/noty.min.js"></script>
    <script type="text/javascript" src="{!! url('/') !!}/lib/js/pages/components_notifications_pnotify.js"></script>

    <script type="text/javascript" src="{{ url('/') }}/lib/js/plugins/toastr/toastr.js"></script>
    <link type="text/css" rel="stylesheet" href="{{ url('/') }}/lib/js/plugins/toastr/toastr.css" />  

	<!-- Theme JS files -->
	<script type="text/javascript" src="{!! url('/') !!}/lib/js/core/app.js"></script>
	<!-- /theme JS files -->

    <!-- /JS Plugins files -->
    <script type="text/javascript">
        $(document).ready(function(){

        	toastr.options = {
                "closeButton": true,
                "debug": false,
                "positionClass": "toast-top-full-width",
                "onclick": null,
                "showDuration": "15000",
                "hideDuration": "15000",
                "timeOut": "15000",
                "extendedTimeOut": "15000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "slideDown",
                "hideMethod": "slideUp"
            }

            @if(Session::has('error'))
                notify("e","{!! Session::get('error') !!}");
                @php
                    Session::forget('error');
                @endphp
            @endif
            @if(Session::has('message'))
                notify("s","{!! Session::get('message') !!}");
                @php
                    Session::forget('message');
                @endphp
            @endif
            @if(Session::has('info'))
                notify("i","{!! Session::get('info') !!}");
                @php
                    Session::forget('info');
                @endphp
            @endif
            @if(Session::has('warning'))
                notify("w","{!! Session::get('warning') !!}");
                @php
                    Session::forget('warning');
                @endphp
            @endif
            
        });

        function notify(typ, msg){
            var t = "information";
            if(typ == "s")  t = "success";
            else if(typ == "w")  t = "warning";
            else if(typ == "e")  t = "error";

            noty({
                width: 200,
                text: msg,
                type: t,
                dismissQueue: true,
                timeout: 4000,
                layout: "topRight",
                buttons: (t != 'confirm') ? false : [
                    {
                        addClass: 'btn btn-primary btn-xs',
                        text: 'Ok',
                        onClick: function ($noty) {
                            $noty.close();
                            noty({
                                force: true,
                                text: 'You clicked "Ok" button',
                                type: 'success',
                                layout: "topRight"
                            });
                        }
                    },
                    {
                        addClass: 'btn btn-danger btn-xs',
                        text: 'Cancel',
                        onClick: function ($noty) {
                            $noty.close();
                            noty({
                                force: true,
                                text: 'You clicked "Cancel" button',
                                type: 'error',
                                layout: "topRight"
                            });
                        }
                    }
                ]
            });
        }
    </script>

</head>

<body style="background-color: #FFCD05;">

	<!-- Main navbar -->
	<!-- <div class="navbar navbar-inverse">
		<div class="navbar-header">
			<a class="navbar-brand" href="{!! url('auth') !!}">
				<strong>Rumah Detensi Imigrasi Semarang Backend System</strong>
			</a>

			<ul class="nav navbar-nav pull-right visible-xs-block">
				<li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
			</ul>
		</div>

		<div class="navbar-collapse collapse" id="navbar-mobile">
			ul class="nav navbar-nav navbar-right">
				<li>
					<a href="{!! url('') !!}">
						<i class="icon-display4"></i> <span class="visible-xs-inline-block position-right"> Go to website</span>
					</a>
				</li>

				<li>
					<a href="#">
						<i class="icon-user-tie"></i> <span class="visible-xs-inline-block position-right"> Contact admin</span>
					</a>
				</li>

				<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown">
						<i class="icon-cog3"></i>
						<span class="visible-xs-inline-block position-right"> Options</span>
					</a>
				</li>
			</ul
		</div>
	</div> -->
	<!-- /main navbar -->


	<!-- Page container -->
	<div class="page-container login-container">

		<!-- Page content -->
		<div class="page-content">

			<!-- Main content -->
			<div class="content-wrapper">

				<!-- Content area -->
				<div class="content">
					@include($page)
					<!-- Footer -->
					<div class="footer text-muted">
						&copy;{{ date("Y") . " Rumah Detensi Imigrasi Semarang" }}
						<br><p style="font-size: 85%;">Developed by <a href="https://bensae.com" target="_blank">bensae.com</a></p>
					</div>
					<!-- /footer -->

				</div>
				<!-- /content area -->

			</div>
			<!-- /main content -->

		</div>
		<!-- /page content -->

	</div>
	<!-- /page container -->

</body>
</html>
