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
        <h4 class="panel-titletext-bold"><b>DETENI</b></h4>
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

        <div class="text-right toggle-show">
            <button type="button" class="btn bg-blue-700 btn-labeled btn-labeled-right ml-10" onclick="tambah()"><b><i class="icon-plus3"></i></b> Tambah</button>
        </div>
    </div>

    <div class="panel-body">
        <h4>Data Deteni</h4>
        <table class="table datatable-basic" style="width: 100%;">
            <thead>
                <tr>
                    <th style="text-align:left;" class="text-bold">NAMA DETENI</th>
                    <th style="text-align:left;" class="text-bold">JENIS KELAMIN</th>
                    <th style="text-align:left;" class="text-bold">TEMPAT<br>TANGGAL LAHIR</th>
                    <th style="text-align:left;" class="text-bold">KEWARGANEGARAAN</th>
                    <th style="text-align:left;" class="text-bold">STATUS</th>
                    <th style="text-align:left;" class="text-bold">DOKUMEN PERJALANAN</th>
                    <th style="text-align:left;" class="text-bold">TANGGAL MASUK</th>
                </tr>
            </thead>
            <tbody>
                @if(count($ctl_data1) > 0)
                    @foreach($ctl_data1 as $aData)
                    <tr>
                        <td><b><a href="javascript:detil('{{ $aData->DTN_ID }}')">{{ $aData->DTN_NAMA }}</a></b></td>
                        <td>{{ Helper::getReferenceInfo("JENIS_KELAMIN", $aData->DTN_JENIS_KELAMIN) }}</td>
                        <td>{{ $aData->DTN_LAHIR_TEMPAT }}<br>{{ Helper::tglIndo($aData->DTN_LAHIR_TANGGAL, "LONG") }}</td>
                        <td>{{ $aData->NGR_NAMA }}</td>
                        <td>{{ Helper::getReferenceInfo("STATUS_DETENI", $aData->DTN_STATUS) }}</td>
                        <td>{{ $aData->DTN_DOKJAL }}</td>
                        <td>{{ Helper::tglIndo($aData->DTN_TANGGAL_MASUK, "LONG") }}</td>
                    </tr>
                    @endforeach
                @else
                @endif
            </tbody>
        </table>
        
        <br><br>
        <h4>Data Deteni Terdeportasi</h4>
        <table class="table datatable-basic" style="width: 100%;">
            <thead>
                <tr>
                    <th style="text-align:left;" class="text-bold">NAMA DETENI</th>
                    <th style="text-align:left;" class="text-bold">JENIS KELAMIN</th>
                    <th style="text-align:left;" class="text-bold">TEMPAT<br>TANGGAL LAHIR</th>
                    <th style="text-align:left;" class="text-bold">KEWARGANEGARAAN</th>
                    <th style="text-align:left;" class="text-bold">STATUS</th>
                    <th style="text-align:left;" class="text-bold">DOKUMEN PERJALANAN</th>
                    <th style="text-align:left;" class="text-bold">TANGGAL MASUK</th>
                </tr>
            </thead>
            <tbody>
                @if(count($ctl_data2) > 0)
                    @foreach($ctl_data2 as $aData)
                    <tr>
                        <td><b><a href="javascript:detil('{{ $aData->DTN_ID }}')">{{ $aData->DTN_NAMA }}</a></b></td>
                        <td>{{ Helper::getReferenceInfo("JENIS_KELAMIN", $aData->DTN_JENIS_KELAMIN) }}</td>
                        <td>{{ $aData->DTN_LAHIR_TEMPAT }}<br>{{ Helper::tglIndo($aData->DTN_LAHIR_TANGGAL, "LONG") }}</td>
                        <td>{{ $aData->NGR_NAMA }}</td>
                        <td>{{ Helper::getReferenceInfo("STATUS_DETENI", $aData->DTN_STATUS) }}</td>
                        <td>{{ $aData->DTN_DOKJAL }}</td>
                        <td>{{ Helper::tglIndo($aData->DTN_TANGGAL_MASUK, "LONG") }}</td>
                    </tr>
                    @endforeach
                @else
                @endif
            </tbody>
        </table>

        <br><br>

    </div>

</div>

<!-- MODAL -->
<div id="mdlAdd" class="modal fade">
    <div class="modal-dialog modal-full">
        <div class="modal-content --modal-lg">
            <div class="modal-header bg-teal-400" style="background: #0d004c">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h6 class="modal-title"> Tambah Deteni</h6>
            </div>

            <div class="modal-body">
                <form class="form-horizontal" method="post" action="">
                <div class="col-lg-12">

                    <div class="form-group">
                        <label class="col-lg-4 control-label text-semibold">Nama</label>
                        <div class="col-lg-8"><input class="form-control" type="text" id="nama"></div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-4 control-label text-semibold">Jenis Kelamin</label>
                        <div class="col-lg-8">
                            <select class="select" id="jenisKelamin">
                                @if(isset($ctl_refJenisKelamin) && count($ctl_refJenisKelamin) > 0)
                                    @foreach($ctl_refJenisKelamin as $aData)
                                        <option value="{{ $aData->R_ID }}">{{ $aData->R_INFO }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-4 control-label text-semibold">Tempat Lahir</label>
                        <div class="col-lg-8"><input class="form-control" type="text" id="tempatLahir"></div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-4 control-label text-semibold">Tanggal Lahir</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control datepicker-menus" id="tanggalLahir" data-date-format="yyyy-mm-dd">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-4 control-label text-semibold">Kewarganegaraan</label>
                        <div class="col-lg-8">
                            <select class="select" id="kewarganegaraan">
                                <option value="_PILIH_">-- Pilih --</option>
                                @if(isset($ctl_refKewarganegaraan) && count($ctl_refKewarganegaraan) > 0)
                                    @foreach($ctl_refKewarganegaraan as $aData)
                                        <option value="{{ $aData->NGR_KODE }}">{{ $aData->NGR_NAMA }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-4 control-label text-semibold">Status</label>
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
                        <label class="col-lg-4 control-label text-semibold">Dokumen Perjalanan</label>
                        <div class="col-lg-8"><input class="form-control" type="text" id="dokjal"></div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-4 control-label text-semibold">Tanggal Masuk</label>
                        <div class="col-lg-8"><input class="form-control  datepicker-menus" data-date-format="yyyy-mm-dd" type="text" onchange="validatePeriode()" id="tanggalMasuk"></div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-4 control-label text-semibold">&nbsp;</label>
                        <div class="col-lg-8">
                            <img src="" id="filePreview" width="100%">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-lg-4 text-semibold">Foto</label>
                        <div class="col-lg-8">
                            <input class="form-control file-styled-primary" id="file" placeholder="" type="file" accept="image/*"onchange="preview(event, 'filePreview')">
                        </div>
                    </div>

                  <!-- -->
                </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-warning btn-labeled btn-xs" data-dismiss="modal"><b><i class="icon-cross3"></i></b> Tutup </button>
                <button type="button" class="btn btn-primary btn-labeled btn-xs" onclick="simpan()"><b><i class="icon-checkmark2"></i></b> Simpan </button>
            </div>
        </div>
    </div>
</div>

@stop

@section('script')
<script type="text/javascript">

    $(document).ready(function(){
        console.log(getCurrentDate());

        // $("#periode").datepicker('update', "{{ $ctl_periode }}");
        // $("#periode").on('changeDate', function(selected){
        //     filter();
        // });
    });

    function validatePeriode(){
        var tgl = $("#tanggalMasuk").pickadate("picker").get("select", "yyyy-mm-dd");
        if(tgl > getCurrentDate()){
            notify("w","Batas maksimal tanggal adalah : " + getCurrentDate());
            $("#tanggalMasuk").val("");
        }else{
            return;
        }
    }


    // function filter(){
    //     var periode = $("#periode").val();
    //     window.location = "{{ url('main/'.Helper::uri2()) }}?periode="+periode
    // }


    function detil(id){
        //localStorage.setItem("selectedPeriode", $("#periode").val());
        window.location = "{{ url('main/'.Helper::uri2()) }}/"+id;
    }


    function tambah(){
        $("#mdlAdd").modal("show");
    }


    function simpan(){
        var nama = $("#nama").val();
        var jenisKelamin = $("#jenisKelamin").val();
        var tempatLahir = $("#tempatLahir").val();
        var tanggalLahir = $("#tanggalLahir").val();
        var kewarganegaraan = $("#kewarganegaraan").val();
        var status = $("#status").val();
        var dokjal = $("#dokjal").val();
        var file = $("#file").prop('files')[0];
        var tanggalMasuk = $("#tanggalMasuk").val();
        //.pickadate("picker").get("select", "yyyy-mm-dd");

        if(nama != "" && tempatLahir != "" && tanggalLahir != "" && dokjal != "" && tanggalMasuk != "" && file != null && kewarganegaraan != "_PILIH_"){
            $("#mdlAdd").modal("toggle");
            var formData = new FormData();
            formData.append("nama", nama);
            formData.append("jenisKelamin", jenisKelamin);
            formData.append("tempatLahir", tempatLahir);
            formData.append("tanggalLahir", tanggalLahir);
            formData.append("kewarganegaraan", kewarganegaraan);
            formData.append("status", status);
            formData.append("dokjal", dokjal);
            formData.append("tanggalMasuk", tanggalMasuk);
            formData.append("file", file);
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
                        notify("s",data["MESSAGE"]);
                        window.location.href = "{{ url(Helper::allUri(2, 1)) }}/"+data["PAYLOAD"];
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

</script>
@stop
