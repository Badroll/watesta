<!-- Simple login form -->
<form id="form-login" action="{{ url('auth/login')  }}" method="POST" class="form-valid" autocomplete="off">
  	<input type="hidden" name="_token" class="_token" value="{{ csrf_token() }}" />
	<div class="panel panel-body login-form" style="">
		<div class="text-center">
			<!--div class="icon-object border-slate-300 text-slate-300"><i class="icon-reading"></i></div-->
			<img src="{{ url('assets/logo-main.png') }}" height="80" width="160" />
			<h4 class="content-group"><b>LOGIN SI TENAR</b></h4>
			<h6 class="content-group" style="margin-top: -20px;"><b>SISTEM INFORMASI</b></h6>
			<h6 class="content-group" style="margin-top: -25px;"><b>RUMAH DETENSI IMIGRASI SEMARANG</b></h6>
			<p class="content-group" style="font-size: 85%;">KEMENTRIAN HUKUM DAN HAK ASASI MANUSIA RI</p>
			<p class="content-group" style="font-size: 85%; margin-top: -25px;">KANTOR WILAYAH JAWA TENGAH</p>
			<h6 class="content-group" style="margin-top: -20px;"><b>RUMAH DETENSI IMIGRASI SEMARANG</b></h6>
		</div>

		<div class="form-group has-feedback has-feedback-left">
			<div class="form-control-feedback">
				<i class="icon-user text-muted"></i>
			</div>
			<input type="text" name="username" class="form-control" placeholder="Username" required="" value="{!! old('username') !!}"  autocomplete="off"/>
		</div>

		<div class="form-group has-feedback has-feedback-left">
			<div class="form-control-feedback">
				<i class="icon-lock2 text-muted"></i>
			</div>
			<input type="password" name="password" class="form-control" placeholder="Password" required="" autocomplete="off" />
		</div>

		<div class="form-group">
			<button type="submit" class="btn btn-primary btn-block" style="background-color:#001F3F; border-color:#001F3F;">Login <i class="icon-circle-right2 position-right"></i></button>
		</div>

		<!--div class="text-center">
			<a href="{!! url('auth/forgot') !!}">Forgot password?</a>
		</div-->
	</div>
</form>
<!-- /simple login form -->


