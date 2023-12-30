@extends('master')

@section('style')
<style type="text/css">
</style>
@stop

@php
    $monthNow = date("Y-m");
    $runningMonth = date("Y-m");
    $runningYear = date("Y");
@endphp

@section('breadcrumb')
@stop

@section('content')
<input type="hidden" name="_token" class="_token" value="{{ csrf_token() }}" />

<div class="panel panel-flat">
    <div class="panel-heading">
        <h4 class="panel-titletext-bold"><b>DATA DETENI</b></h4>
        <br>
    </div>

    <div class="panel-body">
        <div class="row">
            <div class="col-lg-4">
                <div class="form-group has-feedback">
                    <img src="{{ url('main/strg') . '/' . $ctl_data->DTN_FOTO }}" id="fotoPreview" width="100%">
                    <br><br>
                    <input class="form-control file-styled-primary toggle-disable-input" id="foto" placeholder="" type="file" accept="image/*"onchange="preview(event, 'fotoPreview')">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <div class="form-group has-feedback">
                    <label class="col-lg-8 control-label text-semibold">Nama Deteni</label>
                    <input type="text" class="form-control first-focus disable-deportasi toggle-disable-input" value="{{ $ctl_data->DTN_NAMA }}" id="nama">
                    <div class="form-control-feedback">
                        <i class="icon-user text-muted"></i>
                    </div>
                    <!-- <small class="display-block text-muted"><span style="color:red;">* wajib diisi</span></small> -->
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group has-feedback">
                    <label class="col-lg-8 control-label text-semibold">Jenis Kelamin</label>
                    <select class="select disable-deportasi  toggle-disable-input" id="jenisKelamin">
                        @if(isset($ctl_refJenisKelamin) && count($ctl_refJenisKelamin) > 0)
                            @foreach($ctl_refJenisKelamin as $aData)
                                <option value="{{ $aData->R_ID }}" {{ ($aData->R_ID == $ctl_data->DTN_JENIS_KELAMIN) ? "selected" : "" }} >{{ $aData->R_INFO }}</option>
                            @endforeach
                        @endif
                    </select>
                    <div class="form-control-feedback">
                        <i class="icon-people text-muted"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group has-feedback">
                    <label class="col-lg-8 control-label text-semibold">Tempat Lahir</label>
                    <input type="text" class="form-control first-focus disable-deportasi toggle-disable-input" value="{{ $ctl_data->DTN_LAHIR_TEMPAT }}" id="tempatLahir">
                    <div class="form-control-feedback">
                        <i class="icon-home8 text-muted"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="form-group has-feedback">
                    <label class="col-lg-8 control-label text-semibold">Tanggal Lahir</label>
                    <input type="text" class="form-control datepicker-menus disable-deportasi toggle-disable-input" data-date-format="dd-mm-yyyy" value="{{ $ctl_data->DTN_LAHIR_TANGGAL }}" id="tanggalLahir">
                    <div class="form-control-feedback">
                        <i class="icon-calendar52 text-muted"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group has-feedback">
                    <label class="col-lg-8 control-label text-semibold">Kewarganegaraan</label>
                    <select class="select disable-deportasi toggle-disable-input" id="kewarganegaraan">
                        @if(isset($ctl_refNegara) && count($ctl_refNegara) > 0)
                            @foreach($ctl_refNegara as $aData)
                                <option value="{{ $aData->NGR_KODE }}" {{ ($aData->NGR_KODE == $ctl_data->DTN_KEWARGANEGARAAN) ? "selected" : "" }}>{{ $aData->NGR_NAMA }}</option>
                            @endforeach
                        @endif
                    </select>
                    <div class="form-control-feedback">
                        <i class="icon-vcard text-muted"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group has-feedback">
                    <label class="col-lg-8 control-label text-semibold">Status</label>
                    <select class="select disable-deportasi toggle-disable-input" id="status">
                        @if(isset($ctl_refStatus) && count($ctl_refStatus) > 0)
                            @foreach($ctl_refStatus as $aData)
                                <option value="{{ $aData->R_ID }}" {{ ($aData->R_ID == $ctl_data->DTN_STATUS) ? "selected" : "" }}>{{ $aData->R_INFO }}</option>
                            @endforeach
                        @endif
                    </select>
                    <div class="form-control-feedback">
                        <i class="icon-reading text-muted"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <div class="form-group has-feedback">
                    <label class="col-lg-8 control-label text-semibold">Dokumen Perjalanan</label>
                    <input type="text" class="form-control first-focus disable-deportasi" value="{{ $ctl_data->DTN_DOKJAL }}" id="dokjal">
                    <div class="form-control-feedback toggle-disable-input">
                        <i class="icon-profile text-muted"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group has-feedback">
                    <label class="col-lg-8 control-label text-semibold">Tanggal Masuk</label>
                    <input type="text" class="form-control datepicker-menus disable-deportasi toggle-disable-input" data-date-format="yyyy-mm-dd" value="{{ $ctl_data->DTN_TANGGAL_MASUK }}" id="tanggalMasuk">
                    <div class="form-control-feedback">
                        <i class="icon-calendar52 text-muted"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-right toggle-deportasi toggle-show">
            <button type="button" class="btn bg-green-700 btn-labeled btn-labeled-right ml-10" id="btnSubmit" onClick="updateDeteni('')"><b><i class="icon-checkmark2"></i></b>Update</button>
            <button type="button" class="btn bg-blue-700 btn-labeled btn-labeled-right ml-10" id="btnSubmit" onClick="updateDeteni('Y')"><b><i class="icon-exit2"></i></b>Deportasi</button>
            <button type="button" class="btn bg-orange-700 btn-labeled btn-labeled-right ml-10" id="btnSubmit" onClick="hapusDeteni()"><b><i class="icon-trash"></i></b>Hapus</button>
        </div>

        <div class="text-right toggle-deteni">
            <button type="button" class="btn bg-blue-700 btn-labeled btn-labeled-right ml-10" id="btnSubmit" onClick="updateDeteni('N')"><b><i class="icon-exit2"></i></b>Kembalikan ke Non Deportasi</button>
        </div>

        <br>
        <hr>

        <h4>Jurnal dan Histori Deteni</h4>

        <div class="text-right toggle-deportasi toggle-show">
            <button type="button" class="btn bg-blue-700 btn-labeled btn-labeled-right ml-10" id="btnSubmit" onClick="tambahLog()"><b><i class="icon-add"></i></b>Tambah</button>
        </div>
        <p>keterangan warna :</p>
        <span style="background-color: #cfffd9;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> = Registrasi, Administrasi, dan Pelaporan
        <br>
        <span style="background-color: #fcffcc;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> = Perawatan dan Kesehatan
        <br>
        <span style="background-color: #dbf3ff;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> = Keamanan dan Ketertiban

        <script type="text/javascript">
            function setBackground(id, jenis){
                if(jenis == "JBERKAS_RAP"){
                    $("#tr_" + id).css("background-color", "#cfffd9");
                }else if(jenis == "JBERKAS_PERKES"){
                    $("#tr_" + id).css("background-color", "#fcffcc");
                }else if(jenis == "JBERKAS_KAMTIB"){
                    $("#tr_" + id).css("background-color", "#dbf3ff");
                }
            }
        </script>
        <table class="table datatable-basic" style="width: 100%;">
            <thead>
                <tr>
                    <th style="text-align:left;" class="text-bold">NO</th>
                    <th style="text-align:left;" class="text-bold">TANGGAL</th>
                    <th style="text-align:left;" class="text-bold">JENIS</th>
                    <th style="text-align:left;" class="text-bold">KETERANGAN</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @if(count($ctl_dataDetail) > 0)
                    @php
                        $i = 1;
                    @endphp
                    @foreach($ctl_dataDetail as $aData)
                        <tr id="tr_{{ $aData->DLOG_ID }}">
                            <td>{{ $i }}</td>
                            <td>{{ Helper::tglIndo($aData->DLOG_TANGGAL, "LONG") }}</td>
                            <td>{{ Helper::getReferenceInfo("JENIS_BERKAS", $aData->DLOG_JENIS) }}</td>
                            <td>{{ $aData->DLOG_KETERANGAN }}</td>
                            <td>
                                <a href="javascript:detailLog('{{ $aData->DLOG_ID }}')"><span class="label" style="padding: 7px; background-color: #0288D1;">Detail</span></a>
                                <a href="javascript:hapusLog('{{ $aData->DLOG_ID }}')" class="toggle-deportasi toggle-show"><span class="label label-danger" style="padding: 7px;" onclick="hapus()">Hapus</span></a>
                            </td>
                        </tr>
                        <script type="text/javascript">
                            setBackground('{{ $aData->DLOG_ID }}', '{{ $aData->DLOG_JENIS }}');
                        </script>
                        @php
                            $i++;
                        @endphp
                    @endforeach
                @else
                @endif
            </tbody>
        </table>
    </div>

</div>

<!-- MODAL -->
<div id="mdlAdd" class="modal fade">
    <div class="modal-dialog modal-full">
        <div class="modal-content --modal-lg">
            <div class="modal-header bg-teal-400" style="background: #0d004c">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h6 class="modal-title"> Tambah Jurnal dan Histori</h6>
            </div>

            <div class="modal-body">
                <form class="form-horizontal" method="post" action="">
                <div class="col-lg-12">

                    <div class="form-group">
                        <label class="col-lg-3 control-label text-semibold">Tanggal Dokumen</label>
                        <div class="col-lg-8"><input class="form-control datepicker-menus" data-date-format="yyyy-mm-dd" type="text" id="tanggal"></div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label text-semibold">Asal Dokumen</label>
                        <div class="col-lg-8">
                            <select class="select" id="jenis">
                                @if(isset($ctl_refJenis) && count($ctl_refJenis) > 0)
                                    @foreach($ctl_refJenis as $aData)
                                        <option value="{{ $aData->R_ID }}">{{ $aData->R_INFO }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label text-semibold">File Berkas</label>
                        <div class="col-lg-8">
                            <!-- <img src="" id="filePreview" width="250" height="150"> -->
                            <iframe src="" id="filePreview" style="width: 100%; height: 800px;"></iframe>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-lg-3 text-semibold">Upload File</label>
                        <div class="col-lg-8">
                            <input class="form-control file-styled-primary" id="file" placeholder="" type="file" accept="image/*"onchange="preview(event, 'filePreview')">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label text-semibold">Keterangan</label>
                        <div class="col-lg-8"><input class="form-control" type="text" value="" id="keterangan"></div>
                    </div>

                  <!-- -->
                </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-warning btn-labeled btn-xs" data-dismiss="modal"><b><i class="icon-cross3"></i></b> Tutup </button>
                <button type="button" class="btn btn-primary btn-labeled btn-xs" onclick="simpanLog()"><b><i class="icon-checkmark2"></i></b> Simpan </button>
            </div>
        </div>
    </div>
</div>

<div id="mdlDetail" class="modal fade">
    <div class="modal-dialog modal-full">
        <div class="modal-content --modal-lg">
            <div class="modal-header bg-teal-400" style="background: #0d004c">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h6 class="modal-title"> Detail Aktivitas</h6>
            </div>

            <div class="modal-body">
                <form class="form-horizontal" method="post" action="">
                <div class="col-lg-12">

                    <div class="form-group">
                        <label class="col-lg-3 control-label text-semibold">Tanggal Dokumen</label>
                        <div class="col-lg-8"><input class="form-control datepicker-menus" data-date-format="yyyy-mm-dd" type="text" disabled id="tanggal_"></div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label text-semibold">Asal Dokumen</label>
                        <div class="col-lg-8">
                            <select class="select" disabled id="jenis_">
                                @if(isset($ctl_refJenis) && count($ctl_refJenis) > 0)
                                    @foreach($ctl_refJenis as $aData)
                                        <option value="{{ $aData->R_ID }}">{{ $aData->R_INFO }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label text-semibold">File Berkas</label>
                        <div class="col-lg-8">
                            <!-- <img src="" id="filePreview" width="250" height="150"> -->
                            <iframe src="" id="filePreview_" style="width: 100%; height: 600px;"></iframe>
                        </div>
                        <label class="col-lg-3 control-label text-semibold">&nbsp;</label>
                        <!-- <div class="text-right"> -->
                            <button type="button" class="btn bg-blue-700 btn-labeled btn-labeled-right ml-10" data-img-path="" onClick="" id="btnDownload"><b><i class="icon-download"></i></b>Download</button>
                        <!-- </div> -->
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label text-semibold">Keterangan</label>
                        <div class="col-lg-8"><input class="form-control" type="text" value="" disabled id="keterangan_"></div>
                    </div>

                  <!-- -->
                </div>
                </form>
            </div>

            <div class="modal-footer">
                
            </div>
        </div>
    </div>
</div>

@stop

@section('script')
<script type="text/javascript">

    var dtnId;
    var dtnDeportasi;

    $(document).ready(function(){;
        dtnId = "{{ $ctl_data->DTN_ID }}";

        if("{{ $ctl_data->DTN_DEPORTASI }}" == "Y"){
            $(".toggle-deportasi").css("display", "none");
            $(".disable-deportasi").attr("disabled", "true");
        }else{
            $(".toggle-deteni").css("display", "none");
        }
    });


    function filter(){
        var periode = $("#periode").val();
        window.location = "{{ url('main/'.Helper::uri2()) }}?periode="+periode
    }


    function updateDeteni(type){
        var val = ["Perbarui data deteni?", "#689F38"];
        if(type == "Y"){
            val = ["Deportasi deteni?", "#0288D1"];
        }else if(type == "N"){
            val = ["Kembalikan ke Non Deportasi?", "#0288D1"];
        }
        swal({
            title: "Konfirmasi",
            text: val[0],
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: val[1],
            confirmButtonText: "Ya",
            cancelButtonText: "Batal",
            closeOnConfirm: true,
            html: true
        },
        function(){
            var nama = $("#nama").val();
            var jenisKelamin = $("#jenisKelamin").val();
            var tempatLahir = $("#tempatLahir").val();
            var tanggalLahir = $("#tanggalLahir").val();
            var kewarganegaraan = $("#kewarganegaraan").val();
            var status = $("#status").val();
            var dokjal = $("#dokjal").val();
            var tanggalMasuk = $("#tanggalMasuk").val();
            var foto = $("#foto").prop('files')[0];

            if(nama != "" && tempatLahir != "" && tanggalLahir != "" && dokjal != "" && tanggalMasuk != "" && kewarganegaraan != "_PILIH_"){
                var formData = new FormData();
                formData.append("id", dtnId);
                formData.append("type", type);
                formData.append("nama", nama);
                formData.append("jenisKelamin", jenisKelamin);
                formData.append("tempatLahir", tempatLahir);
                formData.append("tanggalLahir", tanggalLahir);
                formData.append("kewarganegaraan", kewarganegaraan);
                formData.append("status", status);
                formData.append("dokjal", dokjal);
                formData.append("tanggalMasuk", tanggalMasuk);
                formData.append("foto", foto);
                formData.append("_token", "{{ csrf_token() }}");
                createOverlay("Proses...");
                $.ajax({
                    type  : "POST",
                    url   : "{{ url(Helper::allUri(2, 1).'/update') }}",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data  : formData,
                    success : function(data) {
                        gOverlay.hide();
                        if(data["STATUS"] == "SUCCESS") {
                            notify("s",data["MESSAGE"]);
                            delayReload();
                        }
                        else{
                            notify("e",data["MESSAGE"]);
                        }
                    },
                    error : function(error) {
                        gOverlay.hide();
                        notify("e","Network/server error\r\n" + error);
                    }
                });
            }else{
                notify("e","Mohon lenkgapi isian");
            }
        });
    }


    function hapusDeteni(){
        swal({
            title: "Konfirmasi",
            text: "Hapus deteni ?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#F57C00",
            confirmButtonText: "Ya",
            cancelButtonText: "Batal",
            closeOnConfirm: true,
            html: true
        },
        function(){
            var formData = new FormData();
            formData.append("id", dtnId);
            formData.append("_token", "{{ csrf_token() }}");
            createOverlay("Proses...");
            $.ajax({
                type  : "POST",
                url   : "{{ url(Helper::allUri(2, 1).'/delete') }}",
                cache: false,
                contentType: false,
                processData: false,
                data  : formData,
                success : function(data) {
                    gOverlay.hide();
                    if(data["STATUS"] == "SUCCESS") {
                        notify("s",data["MESSAGE"]);
                        window.location.href = "{{ url(Helper::allUri(2, 1)) }}?periode={{ $runningMonth }}";
                    }
                    else{
                        notify("e",data["MESSAGE"]);
                    }
                },
                error : function(error) {
                    gOverlay.hide();
                    notify("e","Network/server error\r\n" + error);
                }
            });
        });
    }


    function tambahLog(){
        $("#mdlAdd").modal("show");
    }


    function simpanLog(){
        var tanggal = $("#tanggal").val();
        var jenis = $("#jenis").val();
        var file = $("#file").prop('files')[0];
        var keterangan = $("#keterangan").val();

        if(tanggal != "" && file != null){
            $("#mdlAdd").modal("toggle");
            var formData = new FormData();
            formData.append("dtnId", dtnId);
            formData.append("tanggal", tanggal);
            formData.append("jenis", jenis);
            formData.append("file", file);
            formData.append("keterangan", keterangan);
            formData.append("_token", "{{ csrf_token() }}");
            createOverlay("Proses...");
            $.ajax({
                type  : "POST",
                url   : "{{ url(Helper::allUri(3, 1).'/save') }}",
                cache: false,
                contentType: false,
                processData: false,
                data  : formData,
                success : function(data) {
                    gOverlay.hide();
                    if(data["STATUS"] == "SUCCESS") {
                        notify("s",data["MESSAGE"]);
                        delayReload();
                    }
                    else{
                        notify("e",data["MESSAGE"]);
                    }
                },
                error : function(error) {
                    gOverlay.hide();
                    notify("e","Network/server error\r\n" + error);
                }
            });
        }else{
            notify("e","Mohon lenkgapi isian");
        }
    }


    function detailLog(id){
        createOverlay("Proses...");
        $.ajax({
            type  : "GET",
            url   : "{{ url(Helper::allUri(3, 1).'/detail') }}",
            data  :  {
                    "id" : id,
                    "_token" : "{{ csrf_token() }}"
            },
            success : function(data) {
                gOverlay.hide();
                if(data["STATUS"] == "SUCCESS") {
                    var payload = data["PAYLOAD"];
                    $('#mdlDetail').on('shown.bs.modal', function() {
                        $("#id_").val(data["PAYLOAD"]["DLOG_ID"]);
                        $("#tanggal_").val(data["PAYLOAD"]["DLOG_TANGGAL"]);
                        $("#jenis_").select2("val", data["PAYLOAD"]["DLOG_JENIS"]);
                        $("#filePreview_").attr({
                            "src" : "{{ url('main/strg') }}" + "/" + data["PAYLOAD"]["DLOG_FILE"]
                        });
                        $("#btnDownload").attr("onClick", "download('" + data["PAYLOAD"]["DLOG_FILE"] + "');");
                        $("#keterangan_").val(data["PAYLOAD"]["DLOG_KETERANGAN"]);
                    })
                    $("#mdlDetail").modal("show");
                }
                else{
                    notify("e",data["MESSAGE"]);
                }
            },
            error : function(error) {
                gOverlay.hide();
                notify("e","Network/server error\r\n" + error);
            }
        });
    }


    function download(path){
        window.location = '{{ url(Helper::uri1(1, 1)."/download/pdf") }}' + "/" + path;
    }


    function updateLog(){

    }


    function hapusLog(id){
        swal({
            title: "Konfirmasi",
            text: "Hapus data aktifitas ?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#F44336",
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
                url   : "{{ url(Helper::allUri(3, 1).'/delete') }}",
                cache: false,
                contentType: false,
                processData: false,
                data  : formData,
                success : function(data) {
                    gOverlay.hide();
                    if(data["STATUS"] == "SUCCESS") {
                        notify("s",data["MESSAGE"]);
                        delayReload();
                    }
                    else{
                        notify("e",data["MESSAGE"]);
                    }
                },
                error : function(error) {
                    gOverlay.hide();
                    notify("e","Network/server error\r\n" + error);
                }
            });
        });
    }

</script>
@stop
