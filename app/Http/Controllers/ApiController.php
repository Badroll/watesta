<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Request, File, Response, Helper, DB;

class ApiController extends Controller
{
    public $wabotToken;
    public $wabotDefaultDestination;
    public $code;
    public $wabotFooterMessage;
    public $errorMessage = "";

    public function __construct(){
        $this->wabotToken = Helper::getSetting("WABOT_TOKEN");
        $this->wabotDefaultDestination = Helper::getSetting("WABOT_DEFAULT_DESTINATION");
        $this->code = date("Y-m-d H:i:s");
    }


    public function hooks(){
        $jsonRequest = Request::all();

        $ymdhis = date("Y-m-d H:i:s");
        DB::table("hooks")->insertGetId([
            "HOOKS_ID" => $ymdhis . "-" . md5($ymdhis),
            "HOOKS_RESPONSE" => json_encode($jsonRequest),
            "HOOKS_timestamp" => $ymdhis
        ]);
        // $params = [
        //     "HOOKS_ID" => date("Y-m-d H:i:s") . "-" . md5(date("Y-m-d H:i:s")),
        //     "HOOKS_RESPONSE" => json_encode($jsonRequest)
        // ];
        // foreach($jsonRequest as $key => $value){
        //     if($key != "id") continue;
        //     if(gettype($value) == "array"){
        //         $value = json_encode($value);
        //     }
        //     if($key == "timestamp"){
        //         $value = date("Y-m-d H:i:s");
        //     }
        //     $params["HOOKS_" . $key] = $value;
        // }
        // $save = DB::table("hooks")->insertGetId($params);

        $user = DB::select("
            SELECT * FROM user WHERE USER_PHONE = ?
        ", [$jsonRequest["phone"]]);
        if(count($user) == 0){
            $userId = DB::table("user")->insertGetId([
                "USER_FULLNAME" => $jsonRequest["pushName"],
                "USER_PHONE" => $jsonRequest["phone"]
            ]);
            $user = DB::select("
                SELECT * FROM user WHERE USER_ID = ?
            ", [$userId]);
        }
        $user = $user[0];

        $msg = $jsonRequest["message"];
        if($msg == "ML"){
            $this->ML($jsonRequest, $user);
        }
        else if($msg == "MLL"){
            $this->MLL($jsonRequest, $user);
        }
        else if($msg == "MLC"){
            $this->MLC($jsonRequest, $user);
        }
        else if(str_contains($msg, "#MLCP")){
            $this->MLCP($jsonRequest, $user, $msg);
        }
        else if($msg == "MD"){
            $this->MD($jsonRequest, $user);
        }
        else if($msg == "ME"){
            $this->ME($jsonRequest);
        }
        else if(substr($msg, 0, 2) == "ME" && substr($msg, 0, 3) != "MED" && substr($msg, 0, 3) != "MEL"){
            $this->MEX($jsonRequest, $msg);
        }
        else if(substr($msg, 0, 3) == "MED"){
            $this->MED($jsonRequest, $msg);
        }
        else if($msg == "MEL"){
            $this->MEL($jsonRequest, $user);
        }
        else if($msg == "MP"){
            $this->MP($jsonRequest, $user);
        }
        else if(str_contains($msg, "#MPP")){
            $this->MPP($jsonRequest, $user, $msg);
        }
        else if($msg == "menu"){
            $this->menu($jsonRequest);
        }
        else if($msg == "0"){
            $this->menu($jsonRequest);
        }
        else{
            // $replyHeader = "MOHON MAAF";
            // $replyContent = "\n";
            // $replyContent .= "\nsepertinya anda mengiputkan perintah tidak valid";
            // $replyContent .= "\n_kembali ke menu awal..._";

            // $finalReply = "*" . $replyHeader . "*" . $replyContent;
            // $this->multipleSendtext($jsonRequest["phone"], $finalReply, false);

            // $this->menu($jsonRequest);
        }
        
        return Helper::compose2("SUCCESS", "message processed", $jsonRequest);
    }

    
    private function menu($jsonRequest){//OK
        error_log(__FUNCTION__ . " called");
        $replyHeader = "Halo, Selamat Datang di ". Helper::getSetting("APP_NAME_LONG") . " 😊";
        $replyContent = "\n";
        $replyContent .= "\nSilahkan pilih menu dengan ketik:";
        $replyContent .= "\n*MP* untuk PROFIL ANDA";
        $replyContent .= "\n*ML* untuk INPUT DATA DAN LAPORAN";
        $replyContent .= "\n*MD* untuk DIAGNOSA & REKOMENDASI";
        $replyContent .= "\n*ME* untuk EDUKASI";
        
        $finalReply = "*" . $replyHeader . "*" . $replyContent;
        $this->multipleSendtext($jsonRequest["phone"], $finalReply, false);
    }


    private function ML($jsonRequest, $user){//OK
        error_log(__FUNCTION__ . " called");
        $replyHeader = "MENU LAPORAN";
        $replyContent = "\n";

        if($this->validateProfil($user) ==  false){
            $replyContent .= "\n*Maaf, profil anda belum lengkap untuk mengakses menu ini. Perbarui profil anda untuk menggunakan layanan ini*";
            
            $finalReply = "*" . $replyHeader . "*" . $replyContent;
            $this->multipleSendtext($jsonRequest["phone"], $finalReply);

            $this->MP($jsonRequest, $user);
        }else{
            $replyContent .= "\nBuat, lihat, dan monitor dari laporan pemeriksaan buah hati anda, ketik:";
            $replyContent .= "\n*MLL* untuk MELIHAT DAFTAR LAPORAN ANDA";
            $replyContent .= "\n*MLC* untuk INPUT DATA";

            $finalReply = "*" . $replyHeader . "*" . $replyContent;
            $this->multipleSendtext($jsonRequest["phone"], $finalReply);
        }
    }


    private function MLL($jsonRequest, $user){//OK
        $replyHeader = "DAFTAR LAPORAN";
        $replyContent = "\n";
        $replyContent .= "\nBerikut data laporan anda:";
        $replyContent .= "\n";

        $laporan = DB::select("
            SELECT * FROM laporan as A JOIN user as B ON A.LAPORAN_USER = B.USER_ID WHERE B.USER_PHONE = ?
        ", [$jsonRequest["phone"]]);
        foreach($laporan as $key => $value){
            $zscore = Helper::calculateZscore($value->{"LAPORAN_GENDER"}, $value->{"LAPORAN_USIA"}, $value->{"LAPORAN_TB"});
            $stunting = Helper::zscoreInfo($zscore);
            $replyContent .= "\n".$key+1 . ". " . Helper::tglIndo($value->{"LAPORAN_DATETIME"}, "SHORT", "MINUTE") . " => hasil : *" . strtoupper($stunting) . "*";
            $replyContent .= "\n";
        }
        if(count($laporan) == 0){
            $replyContent .= "\nLaporan kosong, yuk laporkan perkembangan buah hati anda";
        
            $finalReply = "*" . $replyHeader . "*" . $replyContent;
            $this->multipleSendtext($jsonRequest["phone"], $finalReply);

            $this->MLC($jsonRequest, $user);
        }else{
            $finalReply = "*" . $replyHeader . "*" . $replyContent;
            $this->multipleSendtext($jsonRequest["phone"], $finalReply);
        }
    }


    private function MLC($jsonRequest, $user){//OK
        $replyHeader = "BUAT LAPORAN";
        $replyContent = "\n";
        $replyContent .= "\n* _salin dan isi dengan mengganti titik tiga pesan ini, lalu kirimkan_";
        $replyContent .= "\n";
        $replyContent .= "\nNama";
        $replyContent .= "\n*". $user->{"USER_CHILDREN_FULLNAME"} ."*";
        $replyContent .= "\n";
        $replyContent .= "\nJenis Kelamin";
        $replyContent .= "\n*". Helper::getReferenceInfo("GENDER", $user->{"USER_CHILDREN_GENDER"}) ."*";
        $replyContent .= "\n";
        $replyContent .= "\nUsia (bulan)";
        $replyContent .= "\n*" . Helper::calculateAge($user->{"USER_CHILDREN_BIRTHDATE"}) . "*";
        $replyContent .= "\n";
        $replyContent .= "\nTinggi Badan (cm)";
        $replyContent .= "\n...";
        $replyContent .= "\n";
        $replyContent .= "\nBerat Badan (kg)";
        $replyContent .= "\n...";
        $replyContent .= "\n";
        $replyContent .= "\nPola Makan (pilih berdasarkan angka)";
        $replyContent .= "\n*1* => Sehari 1 kali makan tanpa camilan";
        $replyContent .= "\n*2* => Sehari 2 kali makan dengan camilan";
        $replyContent .= "\n*3* => Sehari 3 kali makan dengan camilan";
        $replyContent .= "\n..."; 
        $replyContent .= "\n";
        $replyContent .= "\nNafsu Makan (pilih berdasarkan angka)";
        $replyContent .= "\n*1* => Kuat";
        $replyContent .= "\n*2* => Sedang";
        $replyContent .= "\n*3* => Kurang";
        $replyContent .= "\n..."; 
        $replyContent .= "\n";
        $replyContent .= "\nKetercukupan Lauk Pauk (pilih berdasarkan angka)";
        $replyContent .= "\n*1* => Baik";
        $replyContent .= "\n*2* => Sedang";
        $replyContent .= "\n*3* => Kurang";
        $replyContent .= "\n..."; 
        $replyContent .= "\n";
        $replyContent .= "\nKetercukupan Nasi/Makanan Pokok (pilih berdasarkan angka)";
        $replyContent .= "\n*1* => Baik";
        $replyContent .= "\n*2* => Sedang";
        $replyContent .= "\n*3* => Kurang";
        $replyContent .= "\n..."; 
        $replyContent .= "\n";
        $replyContent .= "\nFrekuensi BAB (pilih berdasarkan angka)";
        $replyContent .= "\n*1* => 1 kali sehari";
        $replyContent .= "\n*2* => lebih dari 1 kali sehari";
        $replyContent .= "\n*3* => 1-2 kali seminggu";
        $replyContent .= "\n...";
        $replyContent .= "\n";
        $replyContent .= "\nAktivitas Anak (pilih berdasarkan angka)";
        $replyContent .= "\n*1* => Aktif";
        $replyContent .= "\n*2* => Sedang";
        $replyContent .= "\n*3* => Aktif Sekali";
        $replyContent .= "\n...";
        $replyContent .= "\n";
        $replyContent .= "\n*_pastikan semua titik tiga sudah terisi, lalu kirim pesan ini_*";
        $replyContent .= "\nâ€¢";
        $replyContent .= "\n_(process code #MLCP)_";
        
        $finalReply = "*" . $replyHeader . "*" . $replyContent;
        $this->multipleSendtext($jsonRequest["phone"], $finalReply, false);
    }


    private function MLCP($jsonRequest, $user, $msg){//OK
        try{
            $extract = "";
            $extractLine = 0;
            $params = [
                "LAPORAN_DATETIME" => date("Y-m-d H:i:s"),
                "LAPORAN_USER" => $user->{"USER_ID"},
            ];
            foreach(preg_split("/((\r?\n)|(\r\n?))/", $msg) as $key => $line){
                error_log($line);
                if(str_contains($line, "Tinggi Badan (cm)")){
                    $extract = "TB";
                    $extractLine = $key + 1;
                }
                else if(str_contains($line, "Berat Badan (kg)")){
                    $extract = "BB";
                    $extractLine = $key + 1;
                }
                else if(str_contains($line, "Pola Makan (pilih berdasarkan angka)")){
                    $extract = "POLA_MAKAN";
                    $extractLine = $key + 4;
                }
                else if(str_contains($line, "Nafsu Makan (pilih berdasarkan angka)")){
                    $extract = "NAFSU_MAKAN";
                    $extractLine = $key + 4;
                }
                else if(str_contains($line, "Ketercukupan Lauk Pauk (pilih berdasarkan angka)")){
                    $extract = "KETERCUKUPAN_PROTEIN";
                    $extractLine = $key + 4;
                }
                else if(str_contains($line, "Ketercukupan Nasi/Makanan Pokok (pilih berdasarkan angka)")){
                    $extract = "KETERCUKUPAN_KARBO";
                    $extractLine = $key + 4;
                }
                else if(str_contains($line, "Frekuensi BAB (pilih berdasarkan angka)")){
                    $extract = "BAB";
                    $extractLine = $key + 4;
                }
                else if(str_contains($line, "Aktivitas Anak (pilih berdasarkan angka)")){
                    $extract = "AKTIVITAS";
                    $extractLine = $key + 4;
                }

                if($key == $extractLine){
                    if($extract == "BB"){
                        $params["LAPORAN_BB"] = str_replace(",", ".", trim($line));
                    }else if($extract == "TB"){
                        $params["LAPORAN_TB"] = str_replace(",", ".", trim($line));
                    }else if($extract == "POLA_MAKAN"){
                        $params["LAPORAN_POLA_MAKAN"] = Helper::getReferenceIdByOrder("POLA_MAKAN", trim($line));
                    }else if($extract == "NAFSU_MAKAN"){
                        $params["LAPORAN_NAFSU_MAKAN"] = Helper::getReferenceIdByOrder("NAFSU_MAKAN", trim($line));
                    }else if($extract == "KETERCUKUPAN_PROTEIN"){
                        $params["LAPORAN_KETERCUKUPAN_PROTEIN"] = Helper::getReferenceIdByOrder("KETERCUKUPAN_PROTEIN", trim($line));
                    }else if($extract == "KETERCUKUPAN_KARBO"){
                        $params["LAPORAN_KETERCUKUPAN_KARBO"] = Helper::getReferenceIdByOrder("KETERCUKUPAN_KARBO", trim($line));
                    }else if($extract == "BAB"){
                        $params["LAPORAN_BAB"] = Helper::getReferenceIdByOrder("BAB", trim($line));
                    }else if($extract == "AKTIVITAS"){
                        $params["LAPORAN_AKTIVITAS"] = Helper::getReferenceIdByOrder("AKTIVITAS", trim($line));
                    }
                }
            }
            $params["LAPORAN_GENDER"] = $user->{"USER_CHILDREN_GENDER"};
            $params["LAPORAN_USIA"] = Helper::calculateAge($user->{"USER_CHILDREN_BIRTHDATE"});
            $saveLaporan = DB::table("laporan")->insertGetId($params);
            
            $zscore = Helper::calculateZScore($params["LAPORAN_GENDER"], $params["LAPORAN_USIA"], $params["LAPORAN_TB"]);
            $stunting = Helper::zscoreInfo($zscore);
            $replyHeader = "HASIL LAPORAN";
            $replyContent = "\n";
            $replyContent .= "\n*Z-Score = " . $zscore . " SD*";
            $replyContent .= "\nBuah hati anda berada pada kondisi *_" . strtoupper($stunting) . "_*";
            $finalReply = "*" . $replyHeader . "*" . $replyContent;
            
            $finalReply = "*" . $replyHeader . "*" . $replyContent;
            $this->multipleSendtext($jsonRequest["phone"], $finalReply, false);

        }catch(Exception $e){
            error_log($e);
            $this->invalidInput($jsonRequest);
        }
    }


    private function MD($jsonRequest, $user){//OK
        $replyHeader = "MENU DIAGNOSA DAN REKOMENDASI";
        $replyContent = "\n";

        if($this->validateProfil($user) ==  false){
            $replyContent .= "\n*Maaf, profil anda belum lengkap untuk mengakses menu ini. Perbarui profil anda untuk menggunakan layanan ini*";
            
            $finalReply = "*" . $replyHeader . "*" . $replyContent;
            $this->multipleSendtext($jsonRequest["phone"], $finalReply);

            $this->MP($jsonRequest, $user);
        }
        else{
            $laporan = DB::select("
                SELECT * FROM laporan WHERE LAPORAN_USER = ? ORDER BY LAPORAN_DATETIME DESC
            ", [$user->{"USER_ID"}]);

            if(count($laporan) > 0){
                $laporan = $laporan[0];
                $replyContent .= "\nRekap laporan terbaru buah hati anda";
                $replyContent .= "\n=====================================";
                $replyContent .= "\nTanggal\t\t\t: *" . Helper::tglIndo($laporan->{"LAPORAN_DATETIME"}, "LONG") . "*";
                $replyContent .= "\nJenis Kelamin\t\t: *" . Helper::getReferenceInfo("GENDER", $laporan->{"LAPORAN_GENDER"}) . "*";
                $replyContent .= "\nUsia\t\t\t: *" . $laporan->{"LAPORAN_USIA"}. " bulan*";
                $replyContent .= "\nBerat Badan\t\t: *" . $laporan->{"LAPORAN_BB"}. " kg*";
                $replyContent .= "\nTinggi Badan\t\t: *" . $laporan->{"LAPORAN_TB"}. " cm*";
                $replyContent .= "\nPola Makan\t\t: *" . Helper::getReferenceInfo("POLA_MAKAN", $laporan->{"LAPORAN_POLA_MAKAN"}) . "*";
                $replyContent .= "\nNafsu Makan\t\t: *" . Helper::getReferenceInfo("NAFSU_MAKAN", $laporan->{"LAPORAN_NAFSU_MAKAN"}) . "*";
                $replyContent .= "\nKetercukupan Lauk Pauk\t: *" . Helper::getReferenceInfo("KETERCUKUPAN_PROTEIN", $laporan->{"LAPORAN_KETERCUKUPAN_PROTEIN"}) . "*";
                $replyContent .= "\nKetercukupan Nasi/Makanan Pokok\t: *" . Helper::getReferenceInfo("KETERCUKUPAN_KARBO", $laporan->{"LAPORAN_KETERCUKUPAN_KARBO"}) . "*";
                $replyContent .= "\nFrekuensi BAB\t\t: *" . Helper::getReferenceInfo("BAB", $laporan->{"LAPORAN_BAB"}) . "*";
                $replyContent .= "\nAktivitas\t\t\t: *" . Helper::getReferenceInfo("AKTIVITAS", $laporan->{"LAPORAN_AKTIVITAS"}) . "*";
                $zscore = Helper::calculateZScore($laporan->{"LAPORAN_GENDER"}, $laporan->{"LAPORAN_USIA"}, $laporan->{"LAPORAN_TB"});
                $stunting = Helper::zscoreInfo($zscore);
                $replyContent .= "\nâ€¢";
                $replyContent .= "\nHasil Perhitungan : *" . $zscore . " SD*";
                $replyContent .= "\nBuah hati anda berada pada kondisi : *" . strtoupper($stunting). "*";
                $replyContent .= "\nRekomendasi untuk buah hati anda :";
                $replyContent .= "\n".Helper::getRekomendasi($zscore);

            }else{
                $replyContent .= "\nAnda tidak memiliki laporan apapun, yuk laporkan perkembangan buah hati anda, ketik:";
                $replyContent .= "\n*MLC* untuk MEMBUAT LAPORAN";
            }
                
            $finalReply = "*" . $replyHeader . "*" . $replyContent;
            $this->multipleSendtext($jsonRequest["phone"], $finalReply);
        }
    }


    private function ME($jsonRequest){//OK
        $replyHeader = "MENU KONSULATSI";
        $replyContent = "\n";
        $replyContent .= "\Edukasi untuk kesehatan buah hati anda melalui layanan berikut, ketik:";
        $replyContent .= "\n*ME1* untuk EDUKASI TERKAIT *EDUKASI STUNTING*";
        $replyContent .= "\n*ME2* untuk EDUKASI TERKAIT *MP ASI*";
        $replyContent .= "\n*ME3* untuk EDUKASI TERKAIT *ASI EKSLUSIF*";
        $replyContent .= "\n*ME4* untuk EDUKASI TERKAIT *PENCEGAHAN INFEKSI*";
        $replyContent .= "\n*ME5* untuk EDUKASI TERKAIT *PERAWATAN KESEHATAN*";
        //$replyContent .= "\n*MEL* untuk EDUKASI *LANGSUNG DENGAN BIDAN*";
        
        $finalReply = "*" . $replyHeader . "*" . $replyContent;
        $this->multipleSendtext($jsonRequest["phone"], $finalReply);
    }


    private function MEX($jsonRequest, $msg){//OK
        error_log(__FUNCTION__ . " called");
        if($msg == "ME1"){
            $kategori = "KATEGORI_ARTIKEL_EDUKASI_STUNTING";
        }else if($msg == "ME2"){
            $kategori = "KATEGORI_ARTIKEL_MP_ASI";
        }else if($msg == "ME3"){
            $kategori = "KATEGORI_ARTIKEL_ASI_EKSLUSIF";
        }else if($msg == "ME4"){
            $kategori = "KATEGORI_ARTIKEL_PENCEGAHAN_INFEKSI";
        }else if($msg == "ME5"){
            $kategori = "KATEGORI_ARTIKEL_PERAWATAN_KESEHATAN";
        }
        error_log($kategori);
        $info = Helper::getReferenceInfo("KATEGORI_ARTIKEL", $kategori);
        $artikel = DB::select("
            SELECT * FROM artikel WHERE ARTIKEL_KATEGORI = ?
        ", [$kategori]);

        $replyHeader = "MENU EDUKASI ". strtoupper($info);
        $replyContent = "\n";
        $replyContent .= "\n";
        foreach($artikel as $key => $value){
            $replyContent .= "\n• *" . substr($value->{"ARTIKEL_JUDUL"}, 0, 50) . "*";
            $replyContent .= "\n   selengkapnya di https://wa.me/62882008074530?text=MED" . $value->{"ARTIKEL_ID"} . "";
            $replyContent .= "\n";
        }
        if(count($artikel) == 0){
            $replyContent .= "\n_Mohon maaf, edukasi tidak tersedia_";
        }
        
        $finalReply = "*" . $replyHeader . "*" . $replyContent;
        $this->multipleSendtext($jsonRequest["phone"], $finalReply);
    }

 
    private function MED($jsonRequest, $msg){//OK
        $artikelId = str_replace("MED", "", $msg);
        $artikel = DB::select("
            SELECT * FROM artikel WHERE ARTIKEL_ID = ?
        ", [$artikelId]);
        if(count($artikel) == 0){
            $replyHeader = "Mohon Maaf, Edukasi ini tidak ditemukan";
            $replyContent = "\n";
            $replyContent .= "\n" . "_kembali ke menu sebelumnya..._";

            $finalReply = "*" . $replyHeader . "*" . $replyContent;
            $this->multipleSendtext($jsonRequest["phone"], $finalReply, false);
            $this->ME($jsonRequest);

        }else{
            $artikel = $artikel[0];
            $replyHeader = $artikel->{"ARTIKEL_JUDUL"};
            $replyContent = "\n";
            $replyContent .= "\n" . $artikel->{"ARTIKEL_DESKRIPSI"};
            
            $finalReply = "*" . $replyHeader . "*" . $replyContent;
            $this->multipleSendtext($jsonRequest["phone"], $finalReply);
        }
    }


    private function MEL($jsonRequest, $user){//OK
        $dokter = DB::select("
            SELECT * FROM dokter WHERE DOKTER_DAERAH = ?
        ", [$user->{"USER_DAERAH"}]);

        $replyHeader = "MENU EDUKASI LANGSUNG DENGAN BIDAN";
        $replyContent = "\n";
        foreach($dokter as $key => $value){
            $text = "Halo"  . $value->{"DOKTER_NAMA"} . "";
            $text = str_replace(" ", "%20", $text);
            $replyContent .= "\n " . ($key + 1) . ". " . $value->{"DOKTER_NAMA"};
            $replyContent .= "\n  hubungi di https://wa.me/" . $value->{"DOKTER_PHONE"} ."?text=" . $text . "";
            $replyContent .= "\n";
        }
        if(count($dokter) == 0){
            $replyContent .= "\n*Mohon maaf, saat ini tidak ada bidan tersedia*";
        }
        
        $finalReply = "*" . $replyHeader . "*" . $replyContent;
        $this->multipleSendtext($jsonRequest["phone"], $finalReply);
    }


    private function MP($jsonRequest, $user){//OK
        $daerah = DB::select("
            SELECT * FROM daerah
        ", []);

        $replyHeader = "MENU PROFIL ANDA";
        $replyContent = "\n";
        $replyContent .= "\n* _untuk MEMPERBARUI, salin dan ganti informasi pesan ini, lalu kirimkan_";
        $replyContent .= "\n";
        $replyContent .= "\nNama Lengkap";
        $replyContent .= "\n" . $user->{"USER_FULLNAME"};
        $replyContent .= "\n";
        $replyContent .= "\nDaerah (pilih berdasarkan angka)";
        foreach($daerah as $key => $value){
            $replyContent .= "\n*". $value->{"DAERAH_ID"} ."* => " . $value->{"DAERAH_NAMA"};
        }
        $replyContent .= "\n" . $user->{"USER_DAERAH"};
        $replyContent .= "\n";
        $replyContent .= "\nEmail";
        $replyContent .= "\n" . $user->{"USER_EMAIL"};
        $replyContent .= "\n";
        $replyContent .= "\nTanggal Lahir Orang Tua (contoh 2022-01-23)";
        $display = $user->{"USER_BIRTHDATE"} == null ? "0000-00-00" : $user->{"USER_BIRTHDATE"};
        $replyContent .= "\n" . $display;
        $replyContent .= "\n";
        $replyContent .= "\nAlamat";
        $replyContent .= "\n" . $user->{"USER_ADDRESS"};
        $replyContent .= "\n";
        $replyContent .= "\nJenis Kelamin (pilih berdasarkan angka)";
        $replyContent .= "\n*1* => Laki-laki";
        $replyContent .= "\n*2* => Perempuan";
        $display = $user->{"USER_GENDER"} == "GENDER_UNDEFINED" ? "..." : Helper::getReferenceOrderById("GENDER", $user->{"USER_GENDER"});
        $replyContent .= "\n" . $display; 
        $replyContent .= "\n";
        $replyContent .= "\nNama Anak";
        $replyContent .= "\n" . $user->{"USER_CHILDREN_FULLNAME"};
        $replyContent .= "\n";
        $replyContent .= "\nTanggal Lahir Anak (contoh 2022-01-23)";
        $display = $user->{"USER_CHILDREN_BIRTHDATE"} == null ? "0000-00-00" : $user->{"USER_CHILDREN_BIRTHDATE"};
        $replyContent .= "\n" . $display;
        $replyContent .= "\n";
        $replyContent .= "\nJenis Kelamin Anak";
        $replyContent .= "\n*1* => Laki-laki";
        $replyContent .= "\n*2* => Perempuan";
        $display = $user->{"USER_CHILDREN_GENDER"} == "GENDER_UNDEFINED" ? "..." : Helper::getReferenceOrderById("GENDER", $user->{"USER_CHILDREN_GENDER"});
        $replyContent .= "\n" . $display; 
        $replyContent .= "\n";

        $replyContent .= "\n*_pastikan semua isian sudah terisi dan tidak ada titik 3 tersisa, lalu kirim pesan ini untuk MEMPERBARUI_*";
        $replyContent .= "\nâ€¢";
        $replyContent .= "\n_(process code #MPP)_";
        
        $finalReply = "*" . $replyHeader . "*" . $replyContent;
        $this->multipleSendtext($jsonRequest["phone"], $finalReply);

        error_log($finalReply);
    }

    
    private function MPP($jsonRequest, $user, $msg){//OK
        $extract = "";
        $extractLine = 0;
        $params = [
            "USER_ID" => $user->{"USER_ID"},
        ];
        $daerah = DB::select("
            SELECT * FROM daerah
        ", []);
        try{
            foreach(preg_split("/((\r?\n)|(\r\n?))/", $msg) as $key => $line){
                error_log($line);
                if(str_contains($line, "Nama Lengkap")){
                    $extract = "FULLNAME";
                    $extractLine = $key + 1;
                }
                else if(str_contains($line, "Daerah (pilih berdasarkan angka)")){
                    $extract = "DAERAH";
                    $extractLine = $key + 1 + count($daerah);
                }
                else if(str_contains($line, "Email")){
                    $extract = "EMAIL";
                    $extractLine = $key + 1;
                }
                else if(str_contains($line, "Tanggal Lahir Orang Tua (contoh 2022-01-23)")){
                    $extract = "BIRTHDATE";
                    $extractLine = $key + 1;
                }
                else if(str_contains($line, "Alamat")){
                    $extract = "ADDRESS";
                    $extractLine = $key + 1;
                }
                else if(str_contains($line, "Jenis Kelamin (pilih berdasarkan angka)")){
                    $extract = "GENDER";
                    $extractLine = $key + 1 + 2;
                }
                else if(str_contains($line, "Nama Anak")){
                    $extract = "CHILDREN_FULLNAME";
                    $extractLine = $key + 1;
                }
                else if(str_contains($line, "Tanggal Lahir Anak (contoh 2022-01-23)")){
                    $extract = "CHILDREN_BIRTHDATE";
                    $extractLine = $key + 1;
                }
                else if(str_contains($line, "Jenis Kelamin Anak")){
                    $extract = "CHILDREN_GENDER";
                    $extractLine = $key + 1 + 2;
                }

                if($key == $extractLine){
                    if($extract == "FULLNAME"){
                        $params["USER_FULLNAME"] = trim($line);
                    }else if($extract == "DAERAH"){
                        $params["USER_DAERAH"] = trim($line);
                    }else if($extract == "EMAIL"){
                        $params["USER_EMAIL"] = trim($line);
                    }else if($extract == "BIRTHDATE"){
                        $params["USER_BIRTHDATE"] = trim($line);
                    }else if($extract == "ADDRESS"){
                        $params["USER_ADDRESS"] = trim($line);
                    }else if($extract == "GENDER"){
                        $params["USER_GENDER"] = Helper::getReferenceIdByOrder("GENDER", trim($line));
                    }else if($extract == "CHILDREN_FULLNAME"){
                        $params["USER_CHILDREN_FULLNAME"] = trim($line);
                    }else if($extract == "CHILDREN_BIRTHDATE"){
                        $params["USER_CHILDREN_BIRTHDATE"] = trim($line);
                    }else if($extract == "CHILDREN_GENDER"){
                        $params["USER_CHILDREN_GENDER"] = Helper::getReferenceIdByOrder("GENDER", trim($line));
                    }
                }
            }

            if($this->validateProfil($params) == false){
                $this->invalidInput($jsonRequest);
            }
            else{
                $updateProfil = DB::table("user")->where("USER_ID", $user->{"USER_ID"})->update($params);

                $replyHeader = "PROFIL ANDA BERHASIL DIPERBARUI";
                $replyContent = "\n";
                $replyContent .= "\n_kembali ke menu awal..._";

                $finalReply = "*" . $replyHeader . "*" . $replyContent;
                $this->multipleSendtext($jsonRequest["phone"], $finalReply, false);

                $this->menu($jsonRequest);
            }

        }catch(Exception $e){
            error_log($e);
            $this->invalidInput($jsonRequest);
        }
    }


    private function validateProfil($user){
        if(gettype($user) == "array"){
            $user = json_decode(json_encode($user));
        }
        $valid = true;
        if($user->{"USER_GENDER"} == "GENDER_UNDEFINED" || $user->{"USER_GENDER"} == ""){
            $this->errorMessage = "jenis kelamin tidak valid";
            $valid = false;
        }
        if($user->{"USER_DAERAH"} == 0){
            $this->errorMessage = "daerah tidak valid";
            $valid = false;
        }
        if($user->{"USER_BIRTHDATE"} == null){
            $this->errorMessage = "tanggal lahir orang tua tidak valid";
            $valid = false;
        }
        if($user->{"USER_CHILDREN_BIRTHDATE"} == null){
            $this->errorMessage = "tanggal lahir anak tidak valid";
            $valid = false;
        }
        if($user->{"USER_CHILDREN_GENDER"} == "GENDER_UNDEFINED" || $user->{"USER_CHILDREN_GENDER"} == ""){
            $this->errorMessage = "jenis kelamin anak tidak valid";
            $valid = false;
        }
        //
        if($user->{"USER_BIRTHDATE"} == "..." || $user->{"USER_BIRTHDATE"} == "000-00-00"){
            $this->errorMessage = "tanggal lahir orang tua tidak valid";
            $valid = false;
        }
        if($user->{"USER_CHILDREN_BIRTHDATE"} == "..." || $user->{"USER_CHILDREN_BIRTHDATE"} == "000-00-00"){
            $this->errorMessage = "tanggal lahir anak tidak valid";
            $valid = false;
        }

        return $valid;
    }


    private function invalidInput($jsonRequest){
        error_log(__FUNCTION__ . " called");
        error_log($this->errorMessage);
        $replyHeader = "MOHON MAAF.. TERJADI KESALAHAN SAAT MEMROSES";
        $replyContent = "\n";
        $replyContent .= "\nPastikan inputan anda sudah sesuai";
        if($this->errorMessage != ""){
            $replyContent .= "\n*".$this->errorMessage;
        }
        
        $finalReply = "*" . $replyHeader . "*" . $replyContent;
        $this->multipleSendtext($jsonRequest["phone"], $finalReply, false);
    }


    private function redirectProfil(){

    }


    // ==================================================================================

    public function broadcast(){
        $start = microtime(true);
        $replyHeader = "BROADCAST TESTING #1";
        $replyContent = "\n";
        $replyContent .= "\n_Hello, this is broadcast testing_";
        $replyContent .= "\n*just archive this chat*";
        $replyContent .= "\n_*lorem ipsum dolor sit amet*_";
        $finalReply = "*" . $replyHeader . "*" . $replyContent;
        $this->broadcastSend($finalReply);

        $replyHeader = "BROADCAST TESTING #2";
        $replyContent = "\n";
        $replyContent .= "\n_Hello, this is broadcast testing_";
        $replyContent .= "\n*just archive this chat*";
        $replyContent .= "\n_*lorem ipsum dolor sit amet*_";
        $finalReply = "*" . $replyHeader . "*" . $replyContent;
        $this->broadcastSend($finalReply);

        $time_elapsed_secs = microtime(true) - $start;
        return Helper::compose2("SUCCESS", "broadcast sent", $time_elapsed_secs);
    }


    public function broadcastSend($reply){
        $phones = [
            "6281215992673",
            "6281215992673",
            "6281215992673",
            "6281215992673",
            "6281215992673",

            // "6281215992673",
            // "6281215992673",
            // "6281215992673",
            // "6281215992673",
            // "6281215992673",
        ];

        // $phones = [
        //     "6281215992673", //badrul
        //     "6281904948412", //alaik
        //     "6282256652144", //nanda
        //     "6281806437710", //bu evina
        //     "6285290014400", //bu ika
        
        //     // "6281215992673", //badrul
        //     // "6281904948412", //alaik
        //     // "6282256652144", //nanda
        //     // "6281806437710", //bu evina
        //     // "6285290014400", //bu ika
        // ];

        foreach($phones as $key => $value){
            $finalReply = $reply;
            $finalReply .= "\n" . date("Y-m-d H:i:s");
            //sleep(2);
            $this->multipleSendtext($value, $finalReply, false);
        }
    }


    public function singleSendText(){
        $curl = curl_init();
        $token = $this->wabotToken;
        $data = [
            'phone' => $this->wabotDefaultDestination,
            'message' => $this->code,
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array(
                "Authorization: $token",
            )
        );
        $url = "https://kudus.wablas.com/api/send-message";
        $method = "POST";
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_URL,  $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($curl);
        curl_close($curl);

        $save = Helper::logCURL(__FUNCTION__, $url, $method, $data, $result);
        return Helper::compose2("SUCCESS", "curl requested " . __FUNCTION__, $result);
    }


    public function multipleSendtext($phone = false, $message = false, $withFooter = true){
        sleep(1);
        $curl = curl_init();
        $token = $this->wabotToken;
        $random = true;
        if($phone == false){
            $phone = $this->wabotDefaultDestination;
        }
        if($message == false){
            $message = '_selamat_ *pagi* _*pengguna*_ http://badrulam.com ' . $this->code;
        }
        $message .= "\n\n-----------------------------------------";
        if($withFooter){
            $message .= "\n_ketik *0* untuk kembali ke menu awal_";
        }
        $payload = [
            "data" => [
                [
                    'phone' => $phone,
                    'message' => $message,
                ]
            ]
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array(
                "Authorization: $token",
                "Content-Type: application/json"
            )
        );
        $url = "https://kudus.wablas.com/api/v2/send-message";
        $method = "POST";
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($curl, CURLOPT_URL,  $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($curl);
        curl_close($curl);

        $save = Helper::logCURL(__FUNCTION__, $url, $method, $payload, $result);
        return Helper::compose2("SUCCESS", "curl requested " . __FUNCTION__, $result);
    }

/*
MENU PROFIL ANDA

* untuk MEMPERBARUI, salin dan ganti informasi pesan ini, lalu kirimkan

Nama Lengkap
Mei Afianti

Daerah (pilih berdasarkan angka)
1 => Sokawera
2 => Ketanda
3 => Karanglewas
4 => Sawangan
5 => Kranji
6 => Pandak
7 => Randegan
8 => Banjarsari
9 => Banjarsari Kidul
10 => Rancamaya
11 => Mandirancan
11

Email
afianti0405@gmail.com

Tanggal Lahir Orang Tua (contoh 2022-01-23)
1990-05-04

Alamat
Mandirancan RT 03 RW 03 

Jenis Kelamin (pilih berdasarkan angka)
1 => Laki-laki
2 => Perempuan
2

Nama Anak
Khalid Zakaria Abdurrahman

Tanggal Lahir Anak (contoh 2022-01-23)
2021-12-04

Jenis Kelamin Anak
1 => Laki-laki
2 => Perempuan
1

pastikan semua isian sudah terisi dan tidak ada titik 3 tersisa, lalu kirim pesan ini untuk MEMPERBARUI

(process code #MPP)

#MPP
ketik 0 untuk kembali ke menu awal
*/


    //!! di laptop terkirim, tapi di HP tidak
    public function multipleSendFooter(){
        $curl = curl_init();
        $token = $this->wabotToken;
        $payload = [
            "data" => [
                [
                    'phone' => $this->wabotDefaultDestination,
                    'message'=> [
                        'title' => [
                            'type' => 'text',
                            'content' => 'salam',
                        ],
                        'content' => 'selamat pagi *pengguna* http://badrulam.com ' . $this->code,
                        'footer' => 'support by wablas',
                    ],
                ]
            ]
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array(
                "Authorization: $token",
                "Content-Type: application/json"
            )
        );
        $url = "https://kudus.wablas.com/api/v2/send-template";
        $method = "POST";
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload) );
        curl_setopt($curl, CURLOPT_URL,  $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($curl);
        curl_close($curl);

        $save = Helper::logCURL(__FUNCTION__, $url, $method, $payload, $result);
        return Helper::compose2("SUCCESS", "ok", $result);
    }


    //!! TIDAK BISA, HARUS PAKE OFFICIAL API. WABLAS GAK JELAS
    public function multipleSendButton(){
        $curl = curl_init();
        $token = $this->wabotToken;
        $payload = [
            "data" => [
                [
                    'phone' => $this->wabotDefaultDestination,
                    'message' => [
                        'buttons' => ["button 1","button 2","button 3"],
                        'content' => 'Default Message Button',
                        // 'footer' => 'cineting 2023',
                    ],
                ]
            ]
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array(
                "Authorization: $token",
                "Content-Type: application/json"
            )
        );
        $method = "POST";
        $url = "https://kudus.wablas.com/api/v2/send-button";
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($curl);
        curl_close($curl);
        
        $save = Helper::logCURL(__FUNCTION__, $url, $method, $payload, $result);
        return Helper::compose2("SUCCESS", "curl requested " . __FUNCTION__, $result);
    }


    // di laptop bisa, sedangkan di HP banyak yg tidak bisa
    // ANEHNYA, di sini button message BISA
    public function multipleSendTemplate(){
        //$this->wabotDefaultDestination = "6285290014400"; // bu ika
        //$this->wabotDefaultDestination = "62895395343692"; // farauq
        //$this->wabotDefaultDestination = "6281904948412"; //  alek
        $curl = curl_init();
        $token = $this->wabotToken;
        $payload = [
            "data" => [
                [
                    'phone' => $this->wabotDefaultDestination,
                    'message'=> [
                        // 'title' => [
                        //     'type' => 'text',
                        //     'content' => 'template text',
                        // ],
                        'buttons' => [
                            // 'url' => [
                            //     'display' => 'badrulam.com',
                            //     'link' => 'http://badrulam.com',
                            // ],
                            // 'call' => [
                            //     'display' => 'contact us',
                            //     'phone' => '081215992673',
                            // ],
                            'quickReply' => ["reply 1","reply 2"],
                        ],
                        'content' => 'sending template message...',
                        // 'footer' => 'footer template here',
                        'isGroup' => false,
                    ],
                    'isGroup' => false,
                ]
            ]
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array(
                "Authorization: $token",
                "Content-Type: application/json"
            )
        );
        $url = "https://kudus.wablas.com/api/v2/send-template";
        $method = "POST";
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload) );
        curl_setopt($curl, CURLOPT_URL,  $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($curl);
        curl_close($curl);

        $save = Helper::logCURL(__FUNCTION__, $url, $method, $payload, $result);
        return Helper::compose2("SUCCESS", "curl requested " . __FUNCTION__, $result);
    }


    // =============================================================================

    public function fileRead(){
        $filename = Request::get("filename");
        if(!isset($filename)) return Helper::compose2("ERROR", "Parameter incomplete (filename)");
        $path = public_path('storage/' . $filename);
        if (!File::exists($path)) {
            return Helper::compose2("ERROR", "File tidak ditemukan");
        }
        $file = File::get($path);
        $type = File::mimeType($path);
        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);
        return $response;
    }


    public function fileDownload(){
        $filename = Request::get("filename");
        if(!isset($filename)) return Helper::compose2("ERROR", "Parameter incomplete (filename)");
        $path = public_path('storage/' . $filename);
        if (!File::exists($path)) {
            return Helper::compose2("ERROR", "File tidak ditemukan");
        }
        $type = File::mimeType($path);
        $headers = array(
            "Content-disposition: attachment; filename=" . $filename,
            "Content-type: " . $type
            );
        return Response::download($path, $filename, $headers);
    }

}
