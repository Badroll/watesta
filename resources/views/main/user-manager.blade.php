@extends('master')

@section('style')
<style type="text/css">
        
</style>
@stop

@section('breadcrumb')
@stop

@section('content')
<input type="hidden" name="_token" class="_token" value="{{ csrf_token() }}" />

<div class="panel panel-flat">
    <div class="panel-heading">
        <h4 class="panel-titletext-bold"><b>USER MANAGER</b></h4>
        <!-- <br>
        <div class="form-group">
            <label class="control-label col-lg-1">Periode</label>
            <div class="col-lg-10">
                <div class="row">
                    <div class="col-md-4">
                        <input class="form-control month-picker" id="periode" />
                    </div>
                </div>
            </div>
        </div> -->

        <div class="text-right">
            <button type="button" class="btn bg-blue-700 btn-labeled btn-labeled-right ml-10" onclick="tambah()"><b><i class="icon-plus3"></i></b> Tambah</button>
        </div>
    </div>

    <div class="panel-body">
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th style="text-align:left;" class="text-bold">USERNAME</th>
                    <th style="text-align:left;" class="text-bold">NAMA LENGKAP</th>
                    <th style="text-align:left;" class="text-bold">ROLE</th>
                    <th style="text-align:left;" class="text-bold">STATUS AKTIF</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @if(count($ctl_data) > 0)
                    @foreach($ctl_data as $aData)
                    <tr>
                        <td>{{ $aData->U_USERNAME }}</td>
                        <td>{{ $aData->U_FULLNAME }}</td>
                        <td>{{ Helper::getReferenceInfo("GROUP_ROLE", $aData->U_ROLE) }}</td>
                        <td>{{ Helper::getReferenceInfo("USER_STATUS", $aData->U_STATUS) }}</td>
                        <td>
                            @if(session("admin_session")->U_ROLE == "ROLE_ADMIN")
                                @if($aData->U_ROLE != "ROLE_SUPERADMIN")
        	                        <a href="javascript:detail('{{ $aData->U_USERNAME }}')"><span class="label label-info" style="padding: 7px;">Edit</span></a>
                                    @if(session("admin_session")->U_USERNAME != $aData->U_USERNAME)
        	                           <a href="javascript:hapus('{{ $aData->U_USERNAME }}')"><span class="label label-danger" style="padding: 7px;" onclick="hapus()">Hapus</span></a>
                                    @else
                                    	&nbsp;
                                    @endif
                                @else
                                    &nbsp;
                                @endif
                            @else
                                <a href="javascript:detail('{{ $aData->U_USERNAME }}')"><span class="label label-info" style="padding: 7px;">Edit</span></a>
                                @if(session("admin_session")->U_USERNAME != $aData->U_USERNAME)
                                    <a href="javascript:hapus('{{ $aData->U_USERNAME }}')"><span class="label label-danger" style="padding: 7px;" onclick="hapus()">Hapus</span></a>
                                @else
                                    &nbsp;
                                @endif
                            @endif
                        </td>
                    </tr>
                    @endforeach
                @else
                @endif
            </tbody>
        </table>
    </div>

</div>

<!-- MODAL -->
<div id="mdlAdd" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content --modal-lg">
            <div class="modal-header bg-teal-400" style="background: #0d004c">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h6 class="modal-title"> Tambah User</h6>
            </div>

            <div class="modal-body">
                <form class="form-horizontal">
                <div class="col-lg-12">

                    <div class="form-group">
                        <label class="col-lg-4 control-label text-semibold">Username</label>
                        <div class="col-lg-8"><input class="form-control" id="username"></div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-4 control-label text-semibold">Nama Lengkap</label>
                        <div class="col-lg-8"><input class="form-control" id="fullname"></div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-4 control-label text-semibold">Role</label>
                        <div class="col-lg-8">
                        	<select class="select" id="role">
                                @if(isset($ctl_refRole) && count($ctl_refRole) > 0)
                                    @foreach($ctl_refRole as $aData)
                                        <option value="{{ $aData->R_ID }}">{{ $aData->R_INFO }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-4 control-label text-semibold">Status Aktif</label>
                        <div class="col-lg-8">
                        	<select class="select" id="status">
                                @if(isset($ctl_refStatus) && count($ctl_refStatus) > 0)
                                    @foreach($ctl_refStatus as $aData)
                                        <option value="{{ $aData->R_ID }}">{{ $aData->R_INFO }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-4 control-label text-semibold">Password</label>
                        <div class="col-lg-8"><input class="form-control" type="text" id="password"></div>
                    </div>

                  <!-- -->
                </div>
                </form>
            </div>

            <div class="modal-footer">
                
                <button type="button" class="btn btn-success btn-labeled btn-xs" onclick="simpan()"><b><i class="icon-checkmark2"></i></b> Simpan </button>
            </div>
        </div>
    </div>
</div>

<div id="mdlEdit" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content --modal-lg">
            <div class="modal-header bg-teal-400" style="background: #0d004c">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h6 class="modal-title"> Edit User</h6>
            </div>

            <div class="modal-body">
                <form class="form-horizontal">
                <div class="col-lg-12">

                    <input type="hidden" id="id_">

                    <div class="form-group">
                        <label class="col-lg-4 control-label text-semibold">Username</label>
                        <div class="col-lg-8"><input class="form-control" type="text" id="username_"></div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-4 control-label text-semibold">Nama Lengkap</label>
                        <div class="col-lg-8"><input class="form-control" type="text" id="fullname_"></div>
                    </div>

                    <!-- <div class="form-group">
                        <label class="col-lg-4 control-label text-semibold">Role</label>
                        <div class="col-lg-8">
                        	<select class="select" id="role_">
                                @if(isset($ctl_refRole) && count($ctl_refRole) > 0)
                                    @foreach($ctl_refRole as $aData)
                                        <option value="{{ $aData->R_ID }}">{{ $aData->R_INFO }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div> -->

                    <div class="form-group">
                        <label class="col-lg-4 control-label text-semibold">Status Aktif</label>
                        <div class="col-lg-8">
                        	<select class="select" id="status_">
                                @if(isset($ctl_refStatus) && count($ctl_refStatus) > 0)
                                    @foreach($ctl_refStatus as $aData)
                                        <option value="{{ $aData->R_ID }}">{{ $aData->R_INFO }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-4 control-label text-semibold">Password</label>
                        <div class="col-lg-8"><input class="form-control" type="text" id="password_"></div>
                    </div>

                  <!-- -->
                </div>
                </form>
            </div>

            <div class="modal-footer">
                
                <button type="button" class="btn btn-success btn-labeled btn-xs" onclick="update()"><b><i class="icon-checkmark2"></i></b> Update </button>
            </div>
        </div>
    </div>
</div>

@stop

@section('script')
<script type="text/javascript">
    $(document).ready(function(){
        
    });

    function setHidden(elm){
        //$(elm).attr("type", "password");
    }


    function tambah(){
        // $("#username").val("");
        // $("#fullname").val("");
        // $("#password").val("");
        $("#mdlAdd").modal("show");
    }


    function simpan(){
        var username = $("#username").val();
        var password = $("#password").val();
        var fullname = $("#fullname").val();
        var status = $("#status").val();
        var role = $("#role").val();

        if(username != "" && password != "" && fullname != ""){
            $("#mdlAdd").modal("toggle");
            var formData = new FormData();
            formData.append("username", username);
            formData.append("password", password);
            formData.append("fullname", fullname);
            formData.append("status", status);
            formData.append("role", role);
            formData.append("_token", "{{ csrf_token() }}");
            createOverlay("Proses...");
            $.ajax({
                type  : "POST",
                url   : "{{ url('main/'.Helper::uri2().'/save') }}",
                cache: false,
                contentType: false,
                processData: false,
                data  : formData,
                success : function(data) {
                    gOverlay.hide();
                    if(data["STATUS"] == "SUCCESS") {
                        notify("s", data["MESSAGE"]);
                        delayReload();
                    }
                    else{
                        notify("e", data["MESSAGE"]);
                    }
                },
                error : function(error) {
                    gOverlay.hide();
                    notify("e", "Network/server error\r\n" + error);
                }
            });
        }else{
            notify("e", "Mohon lenkgapi isian");
        }
    }


    function detail(id){
        createOverlay("Proses...");
        $.ajax({
            type  : "GET",
            url   : "{{ url('main/'.Helper::uri2().'/detail') }}",
            data  :  {
                    "id" : id,
                    "_token" : "{{ csrf_token() }}"
            },
            success : function(data) {
                gOverlay.hide();
                if(data["STATUS"] == "SUCCESS") {
                    var payload = data["PAYLOAD"];
                        $('#mdlEdit').on('shown.bs.modal', function() {
                            $("#id_").val(data["PAYLOAD"]["U_USERNAME"]);
                            $("#username_").val(data["PAYLOAD"]["U_USERNAME"]);
                            $("#fullname_").val(data["PAYLOAD"]["U_FULLNAME"]);
                            // $("#role_").select2("val", data["PAYLOAD"]["U_ROLE"]);
                            $("#status_").select2("val", data["PAYLOAD"]["U_STATUS"]);
                            if(data["PAYLOAD"]["U_ROLE"] == "ROLE_SUPERADMIN"){
                                $("#status_").attr("disabled", "true");
                            }
                        })
                        $("#mdlEdit").modal("show");
                }
                else{
                    notify("e", data["MESSAGE"]);
                }
            },
            error : function(error) {
                gOverlay.hide();
                notify("e", "Network/server error\r\n" + error);
            }
        });
    }


    function update(){
        var id = $("#id_").val();
        var username = $("#username_").val();
        var password = $("#password_").val();
        var fullname = $("#fullname_").val();
        // var role = $("#role_").val();
        var status = $("#status_").val();

        if(username != "" && password != "" && fullname != ""){
            $("#mdlEdit").modal("toggle");
            var formData = new FormData();
            formData.append("id", id);
            formData.append("username", username);
            formData.append("password", password);
            formData.append("fullname", fullname);
            // formData.append("role", role);
            formData.append("status", status);
            formData.append("_token", "{{ csrf_token() }}");
            createOverlay("Proses...");
            $.ajax({
                type  : "POST",
                url   : "{{ url('main/'.Helper::uri2().'/update') }}",
                cache: false,
                contentType: false,
                processData: false,
                data  : formData,
                success : function(data) {
                    gOverlay.hide();
                    if(data["STATUS"] == "SUCCESS") {
                        notify("s", data["MESSAGE"]);
                        delayReload();
                    }
                    else{
                        notify("e", data["MESSAGE"]);
                    }
                },
                error : function(error) {
                    gOverlay.hide();
                    notify("e", "Network/server error\r\n" + error);
                }
            });
        }else{
            notify("e", "Mohon lenkgapi isian");
        }
    }


    function hapus(id){
        swal({
            title: "Konfirmasi",
            text: "Hapus data ini ?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Ya",
            cancelButtonText: "Batal",
            closeOnConfirm: true,
            html: true
        },
        function(){
            var formData = new FormData();
            formData.append("id", id);
            formData.append("_token", "{{ csrf_token() }}");
            createOverlay("Proses...");
            $.ajax({
                type  : "POST",
                url   : "{{ url('main/'.Helper::uri2().'/delete') }}",
                cache: false,
                contentType: false,
                processData: false,
                data  : formData,
                success : function(data) {
                    gOverlay.hide();
                    if(data["STATUS"] == "SUCCESS") {
                        notify("s", data["MESSAGE"]);
                        delayReload();
                    }
                    else{
                        notify("e", data["MESSAGE"]);
                    }
                },
                error : function(error) {
                    gOverlay.hide();
                    notify("e", "Network/server error\r\n" + error);
                }
            });
        });
    }

</script>
@stop
