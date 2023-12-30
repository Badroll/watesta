<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller as mCtl;
use Illuminate\Http\Request;
use App\Model as M;
use Helper, DB;
use \stdClass;
use Carbon\Carbon;

class MainController extends mCtl
{

    protected $request;

    public function __construct(Request $req) {
    }

    public function home(Request $req){
        $data["ctl_periode"] = $req->periode;
        $data["ctl_deteni"] = DB::table("deteni")->where("DTN_TANGGAL_MASUK", "LIKE", $req->periode."%")->get();
        $data["ctl_log"] = DB::table("deteni_log as A")
                        ->join("deteni as B", "B.DTN_ID", "=", "A.DTN_ID")
                        ->where("B.DTN_TANGGAL_MASUK", "LIKE", $req->periode."%")
                        ->get();

    	return view("main.home", $data);
    }


    // deteni
    public function deteni(Request $req){
        $periode = $req->periode;

        //
        $deteni1 = DB::table("deteni as A")
                    ->join("negara as B", "A.DTN_KEWARGANEGARAAN", "=", "B.NGR_KODE")
                    //->where("A.DTN_TANGGAL_MASUK", "LIKE", $periode . "%")
                    ->orderBy("A.DTN_ID", "ASC")
                    ->where("A.DTN_DEPORTASI", "N")->get();
        $deteni2 = DB::table("deteni as A")
                    ->join("negara as B", "A.DTN_KEWARGANEGARAAN", "=", "B.NGR_KODE")
                    //->where("A.DTN_TANGGAL_MASUK", "LIKE", $periode . "%")
                    ->orderBy("A.DTN_ID", "ASC")
                    ->where("A.DTN_DEPORTASI", "Y")->get();

        $data["ctl_periode"] = $periode;
        $data["ctl_refJenisKelamin"] = Helper::getReference("JENIS_KELAMIN");
        $data["ctl_refStatus"] = Helper::getReference("STATUS_DETENI");
        $data["ctl_refKewarganegaraan"] = DB::table("negara")->get();
        $data["ctl_data1"] = $deteni1;
        $data["ctl_data2"] = $deteni2;
        //dd($deteni1);

        return view("main.deteni", $data);
    }


    public function deteniSave(Request $req){
        $nama = $req->nama;
        $jenisKelamin = $req->jenisKelamin;
        $tempatLahir = $req->tempatLahir;
        $tanggalLahir = $req->tanggalLahir;
        $kewarganegaraan = $req->kewarganegaraan;
        $status = $req->status;
        $dokjal = $req->dokjal;
        $tanggalMasuk = $req->tanggalMasuk;
        $file = $req->file;
        if( 
            !isset($nama)||
            !isset($jenisKelamin)||
            !isset($tempatLahir)||
            !isset($tanggalLahir)||
            !isset($kewarganegaraan)||
            !isset($status)||
            !isset($dokjal)||
            !isset($tanggalMasuk)
        )   return Helper::composeReply2("ERROR", "Parameter tidak lengkap");

        $fileName = $file->getClientOriginalName();
        $fileSize = $file->getClientSize();
        if($fileSize > 4096000) return Helper::composeReply2("ERROR", "Batas ukuran file maksimal adalah 4MB");
        $fileExt = strtolower($file->getClientOriginalExtension());
        if(in_array($fileExt, ["jpg", "jpeg", "png"])=== false) return Helper::composeReply2("ERROR", 'Format file tidak diizinkan');
        $uploadFile = "foto-profil-" . substr(md5(date("YmdHis"). $file),0,10) . "." . $fileExt;
        $file->move("storage/", $uploadFile);

        $save = DB::table("deteni")->insertGetId([
            "DTN_NAMA" => $nama,
            "DTN_JENIS_KELAMIN" => $jenisKelamin,
            "DTN_LAHIR_TEMPAT" => $tempatLahir,
            "DTN_LAHIR_TANGGAL" => $tanggalLahir,
            "DTN_KEWARGANEGARAAN" => $kewarganegaraan,
            "DTN_STATUS" => $status,
            "DTN_DOKJAL" => $dokjal,
            "DTN_TANGGAL_MASUK" => $tanggalMasuk,
            "DTN_FOTO" => $uploadFile
        ]);

        return Helper::composeReply2("SUCCESS", "Berhasil menyimpan data", $save);
    }


    public function log($id){
        //
        $data["ctl_refJenisKelamin"] = Helper::getReference("JENIS_KELAMIN");
        $data["ctl_refJenis"] = Helper::getReference("JENIS_BERKAS");
        $data["ctl_refStatus"] = Helper::getReference("STATUS_DETENI");
        $data["ctl_refNegara"] = DB::table("negara")->get();
        $data["ctl_data"] = DB::table("deteni")->where("DTN_ID", $id)->first();
        $data["ctl_dataDetail"] = DB::table("deteni_log")->where("DTN_ID", $id)->orderBy("DLOG_TANGGAL", "asc")->get();

        return view("main.deteni-detail", $data);
    }


    public function deteniUpdate(Request $req){
        $id = $req->id;
        $nama = $req->nama;
        $jenisKelamin = $req->jenisKelamin;
        $tempatLahir = $req->tempatLahir;
        $tanggalLahir = $req->tanggalLahir;
        $kewarganegaraan = $req->kewarganegaraan;
        $status = $req->status;
        $dokjal = $req->dokjal;
        $tanggalMasuk = $req->tanggalMasuk;
        if( 
            !isset($id)||
            !isset($nama)||
            !isset($jenisKelamin)||
            !isset($tempatLahir)||
            !isset($tanggalLahir)||
            !isset($kewarganegaraan)||
            !isset($status)||
            !isset($dokjal)||
            !isset($tanggalMasuk)
        )   return Helper::composeReply2("ERROR", "Parameter tidak lengkap");
        //dd($req->foto);
        if(isset($req->foto) && $req->foto != null && $req->foto != "undefined"){
            $file = $req->foto;
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getClientSize();
            if($fileSize > 4096000) return Helper::composeReply2("ERROR", "Batas ukuran file maksimal adalah 4MB");
            $fileExt = strtolower($file->getClientOriginalExtension());
            if(in_array($fileExt, ["jpg", "jpeg", "png"])=== false) return Helper::composeReply2("ERROR", 'Format file tidak diizinkan');
            $uploadFile = "foto-profil-" . substr(md5(date("YmdHis"). $file),0,10) . "." . $fileExt;
            $file->move("storage/", $uploadFile);
        }else{
            $uploadFile = DB::table("deteni")->where("DTN_ID", $id)->first()->DTN_FOTO;
        }

        $query = [
            "DTN_NAMA" => $nama,
            "DTN_JENIS_KELAMIN" => $jenisKelamin,
            "DTN_LAHIR_TEMPAT" => $tempatLahir,
            "DTN_LAHIR_TANGGAL" => $tanggalLahir,
            "DTN_KEWARGANEGARAAN" => $kewarganegaraan,
            "DTN_STATUS" => $status,
            "DTN_DOKJAL" => $dokjal,
            "DTN_TANGGAL_MASUK" => $tanggalMasuk,
            "DTN_FOTO" => $uploadFile
        ];
        if($req->type != ""){
            $query = [
                "DTN_NAMA" => $nama,
                "DTN_JENIS_KELAMIN" => $jenisKelamin,
                "DTN_LAHIR_TEMPAT" => $tempatLahir,
                "DTN_LAHIR_TANGGAL" => $tanggalLahir,
                "DTN_KEWARGANEGARAAN" => $kewarganegaraan,
                "DTN_STATUS" => $status,
                "DTN_DOKJAL" => $dokjal,
                "DTN_TANGGAL_MASUK" => $tanggalMasuk,
                "DTN_FOTO" => $uploadFile,
                "DTN_DEPORTASI" => $req->type
            ];
        }

        DB::table("deteni")->where("DTN_ID", $id)->update($query);

        return Helper::composeReply2("SUCCESS", "Berhasil memperbarui data");
    }


    public function deteniDelete(Request $req){
        $id = $req->id;
        if(!isset($id)) return Helper::composeReply2("ERROR", "Parameter tidak lengkap");

        DB::table("deteni")->where("DTN_ID", $id)->delete();
        return Helper::composeReply2("SUCCESS", "Berhasil menghapus data");
    }


    public function logSave(Request $req){
        $dtnId = $req->dtnId;
        $tanggal = $req->tanggal;
        $jenis = $req->jenis;
        $file = $req->file;
        $keterangan = ($req->keterangan != null) ? $req->keterangan : "-";
        if( 
            !isset($dtnId)||
            !isset($tanggal)||
            !isset($jenis)||
            !isset($file)
        )   return Helper::composeReply2("ERROR", "Parameter tidak lengkap");

        $fileName = $file->getClientOriginalName();
        $fileSize = $file->getClientSize();
        if($fileSize > 4096000) return Helper::composeReply2("ERROR", "Batas ukuran file maksimal adalah 4MB");
        $fileExt = strtolower($file->getClientOriginalExtension());
        if(in_array($fileExt, ["pdf", "doc", "docx"])=== false) return Helper::composeReply2("ERROR", 'Format file tidak diizinkan');
        $uploadFile = "berkas-" . substr(md5(date("YmdHis"). $file),0,10) . "." . $fileExt;
        try{
            $file->move("storage/", $uploadFile);
        }catch(Exception $e){
            return Helper::composeReply2("ERROR", "Terjadi Kesalahan Internal". $e);
        }

        DB::table("deteni_log")->insertGetId([
            "DTN_ID" => $dtnId,
            "DLOG_TANGGAL" => $tanggal,
            "DLOG_JENIS" => $jenis,
            "DLOG_FILE" => $uploadFile,
            "DLOG_KETERANGAN" => $keterangan
        ]);

        return Helper::composeReply2("SUCCESS", "Berhasil menyimpan data");
    }


    public function logDetail(Request $req){
        $id = $req->id;
        if(!isset($id)) return Helper::composeReply2("ERROR", "Parameter tidak lengkap");

        $data = DB::table("deteni_log")->where("DLOG_ID", $id)->first();
        return Helper::composeReply2("SUCCESS", "Data", $data);
    }


    public function logUpdate(Request $req){
        $id = $req->id;
        $tanggal = $req->tanggal;
        $jenis = $req->jenis;
        $file = $req->file;
        $keterangan = ($req->keterangan != null) ? $req->keterangan : "-";
        if( 
            !isset($id)||
            !isset($tanggal)||
            !isset($jenis)||
            !isset($file)
        )   return Helper::composeReply2("ERROR", "Parameter tidak lengkap");

        $fileName = $file->getClientOriginalName();
        $fileSize = $file->getClientSize();
        if($fileSize > 4096000) return Helper::composeReply2("ERROR", "Batas ukuran file maksimal adalah 4MB");
        $fileExt = strtolower($file->getClientOriginalExtension());
        if(in_array($fileExt, ["pdf", "doc", "docx"])=== false) return Helper::composeReply2("ERROR", 'Format file tidak diizinkan');
        
        $uploadFile = substr(md5(date("YmdHis"). $file),0,15) . "." . $fileExt;
        $file->move("storage/", $uploadFile);
        DB::table("deteni_log")->where("DLOG_ID", $id)->update([
            "DLOG_TANGGAL" => $tanggal,
            "DLOG_JENIS" => $jenis,
            "DLOG_FILE" => $uploadFile,
            "DLOG_KETERANGAN" => $keterangan
        ]);

        return Helper::composeReply2("SUCCESS", "Berhasil menyimpan data");
    }


    public function logDelete(Request $req){
        $id = $req->id;
        if(!isset($id)) return Helper::composeReply2("ERROR", "Parameter tidak lengkap");

        DB::table("deteni_log")->where("DLOG_ID", $id)->delete();
        return Helper::composeReply2("SUCCESS", "Berhasil menghapus data");
    }


}