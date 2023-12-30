<div class="navbar navbar-inverse">
    <div class="navbar-header" style=" padding: 8px !important;">
        <img src="{!! url("assets/logo-main.png")  !!}" width="60" height="30" alt="logo navbar">
        &nbsp;&nbsp;
        <strong style="font-size: 120%; vertical-align: middle;">SI TENAR</strong>

        <ul class="nav navbar-nav pull-right visible-xs-block">
            <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
            <li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
        </ul>
    </div>
    

    <div class="navbar-collapse collapse" id="navbar-mobile">

        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown dropdown-user">
                <a class="dropdown-toggle" data-toggle="dropdown">
                    <img src="{!! url("assets/logo-user-1.png")  !!}">
                    <span>{{ session("admin_session")->U_FULLNAME }}</span>
                    <i class="caret"></i>
                </a>

                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a href="{{ url('auth/logout') }}"><i class="icon-switch2"></i> Logout</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>

<div class="navbar navbar-default" id="navbar-second">
    <ul class="nav navbar-nav no-border visible-xs-block">
        <li><a class="text-center collapsed" data-toggle="collapse" data-target="#navbar-second-toggle"><i class="icon-menu7"></i></a></li>
    </ul>

    <div class="navbar-collapse collapse" id="navbar-second-toggle">
        <ul class="nav navbar-nav"> 
            <li class="{{ (Helper::uri2() == 'home')?'active':'' }}">
                <a href="javascript:goTo('{{ url('main/home') }}?periode=')"><i class="icon-more position-left"></i> <b>HOME</b></a>
            </li>      

            <li class="{{ (Helper::uri2() == 'deteni')?'active':'' }}">
                <a href="javascript:goTo('{{ url('main/deteni') }}?periode=')"><i class="icon-more position-left"></i> <b>DETENI</b></a>
            </li> 

            @if(Session("admin_session")->U_ROLE != "ROLE_USER")
                <li class="{{ (Helper::uri2() == 'user')?'active':'' }}">
                    <a href="javascript:window.location = '{{ url('main/user') }}'"><i class="icon-more position-left"></i> <b>USER MANAGER</b></a>
                </li>
            @endif
        </ul>
    </div>
    <script type="text/javascript">
        function goTo(url){
            // var item = localStorage.getItem("selectedPeriode");
            // localStorage.removeItem("selectedPeriode");
            window.location = url+"{{ $runningMonth }}";
        }
    </script>
</div>
