<?php
date_default_timezone_set('Asia/Jakarta');

/*
export excel phpexcel laravel for PHP 7 :
- phpexcel/Classes/PHPExcel/Calculation/Functions.php line 574, break dihapus
- phpexcel/Classes/PHPExcel/Shared/OLE.php line 290 / 288(phpexcel 1.8) / 290 (phpexcel 1.8.1), continue diganti continue 2
*/

// $config = include "../../app/config/database.php";
// if(!is_array($config)) die("File konfigurasi database tidak valid");

$server = 1
;

if($server == 1){
  $host = '127.0.0.1';
  $database = 'u6227930_ppdb_rus';
  $username = 'u6227930_ppdb_rus';
  $password = 'smkrus12345';
}else{
  $host = '127.0.0.1';
  $database = 'ppdb_rus';
  $username = 'root';
  $password = '';
}


//macOS
//if($host == "localhost")  $host = "127.0.0.1";

$conn = mysqli_connect($host, $username, $password) or die ("Connection failed");
mysqli_select_db($conn, $database) or die ("Cannot connect databases");

function composeReply($status,$msg,$payload = null) {
    header("Content-Type: application/json");
    $reply = json_encode(array(
              "SENDER" => "Admin PPDB SMK Raden Umar Said Kudus",
              "STATUS" => $status,
              "MESSAGE" => $msg,
        "PAYLOAD" => $payload));

    return $reply;
  }

function tglIndo($tgl,$mode) {
  if($tgl != "" && $mode != "" && $tgl!= "0000-00-00" && $tgl != "0000-00-00 00:00:00") {
    $t = explode("-",$tgl);
    $bln = array();
    $bln["01"]["LONG"] = "Januari";
    $bln["01"]["SHORT"] = "Jan";
    $bln["1"]["LONG"] = "Januari";
    $bln["1"]["SHORT"] = "Jan";
    $bln["02"]["LONG"] = "Februari";
    $bln["02"]["SHORT"] = "Feb";
    $bln["2"]["LONG"] = "Februari";
    $bln["2"]["SHORT"] = "Feb";
    $bln["03"]["LONG"] = "Maret";
    $bln["03"]["SHORT"] = "Mar";
    $bln["3"]["LONG"] = "Maret";
    $bln["3"]["SHORT"] = "Mar";   
    $bln["04"]["LONG"] = "April";
    $bln["04"]["SHORT"] = "Apr";
    $bln["4"]["LONG"] = "April";
    $bln["4"]["SHORT"] = "Apr";
    $bln["05"]["LONG"] = "Mei";
    $bln["05"]["SHORT"] = "Mei";
    $bln["5"]["LONG"] = "Mei";
    $bln["5"]["SHORT"] = "Mei";
    $bln["06"]["LONG"] = "Juni";
    $bln["06"]["SHORT"] = "Jun";
    $bln["6"]["LONG"] = "Juni";
    $bln["6"]["SHORT"] = "Jun";
    $bln["07"]["LONG"] = "Juli";
    $bln["07"]["SHORT"] = "Jul";
    $bln["7"]["LONG"] = "Juli";
    $bln["7"]["SHORT"] = "Jul";
    $bln["08"]["LONG"] = "Agustus";
    $bln["08"]["SHORT"] = "Ags";
    $bln["8"]["LONG"] = "Agustus";
    $bln["8"]["SHORT"] = "Ags";
    $bln["09"]["LONG"] = "September";
    $bln["09"]["SHORT"] = "Sep";
    $bln["9"]["LONG"] = "September";
    $bln["9"]["SHORT"] = "Sep";
    $bln["10"]["LONG"] = "Oktober";
    $bln["10"]["SHORT"] = "Okt";
    $bln["11"]["LONG"] = "November";
    $bln["11"]["SHORT"] = "Nov";
    $bln["12"]["LONG"] = "Desember";
    $bln["12"]["SHORT"] = "Des";
    
    $b = $t[1];
    
    if (strpos($t[2], ":") === false) { //tdk ada format waktu
      $jam = "";
    }
    else {
      $j = explode(" ",$t[2]);
      $t[2] = $j[0];
      $jam = $j[1];
    }
    
    return $t[2]." ".$bln[$b][$mode]." ".$t[0]." ".$jam;
  }
  else {
    return "-";
  }
}


$act = "";
if(isset($_GET["act"]))   $act = trim($_GET["act"]);
if(isset($_POST["act"]))  $act = trim($_POST["act"]);

$export = "";
if(isset($_GET["export"]))    $export = trim($_GET["export"]);
if(isset($_POST["export"]))   $export = trim($_POST["export"]);

if($export == "excel") {
  ini_set('display_errors', TRUE);
  ini_set('display_startup_errors', TRUE);
  ini_set('memory_limit', '2048M');
  require_once("phpexcel/Classes/PHPExcel.php");
  error_reporting(E_ALL);
}



// ======================================================================================================================= //
// ======================================================================================================================= //
// ======================================================================================================================= //


if(strtolower($act) == "data-pendaftar") {
  $all_jurusan = '';
  $all_kota = '';
  if(isset($_GET["jurusan"])) $jurusan = trim($_GET["jurusan"]);
  if(!isset($jurusan)) $all_jurusan = 'Y';
  if($jurusan == '_ALL_') $all_jurusan = 'Y';

  if(isset($_GET["kota"])) $kota = trim($_GET["kota"]);
  if($kota != null && $kota != '_ALL_' && $kota != 'kudus') $kota = 'kudus';
  if(!isset($kota)) $all_kota = 'Y';
  if($kota == '_ALL_') $all_kota = 'Y';

  $tahun_ajaran = $_GET['tahun_ajaran'];
  $gelombang = $_GET['gelombang'];

  if($export == "excel") {
    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

    // Set document properties
    $objPHPExcel->getProperties()->setCreator("PPDB SMKRUS")
                   ->setLastModifiedBy("PPDB SMKRUS")
                   ->setTitle("PPDB SMKRUS")
                   ->setSubject("PPDB SMKRUS")
                   ->setDescription("PPDB SMKRUS")
                   ->setKeywords("PPDB SMKRUS")
                   ->setCategory("PPDB SMKRUS");
    // template
    $objPHPExcel = PHPExcel_IOFactory::load("reports/template-data_pendaftar.xlsx");
    $sheet = $objPHPExcel->setActiveSheetIndex(0);
    $sheet->setCellValue('B3', tglIndo(date("Y-m-d H:i:s"), "SHORT"));

    $pw = mysqli_query($conn,  "SELECT * FROM info WHERE inf_subject = 'tahun_ajaran' ");
    $rs_pw = mysqli_fetch_assoc($pw);

    if($all_jurusan == 'Y') $sheet->setCellValue('B4', 'Semua jurusan');
    else $sheet->setCellValue('B4', $jurusan);

    if($all_kota == 'Y') $sheet->setCellValue('B5', 'Semua kota');
    else $sheet->setCellValue('B5', $kota);

    $startRow = 9;
    $xlsRow = $startRow;

    $arrItems = [];
    $arrPO = [];

    //jurusan all //kota all
    if($all_jurusan == 'Y' && $all_kota == 'Y'){
      $po = mysqli_query($conn,  "SELECT * FROM siswa WHERE sw_status >= 7 AND sw_tahun_ajaran = '".$tahun_ajaran."' AND sw_gelombang = '".$gelombang."' ");
    //jurusan all //kota spc  
    }elseif($all_jurusan == 'Y' && $all_kota != 'Y'){
      $po = mysqli_query($conn,  "SELECT * FROM siswa WHERE sw_status >= 7 AND sw_alamat_kota = '".$kota."' AND sw_tahun_ajaran = '".$tahun_ajaran."' AND sw_gelombang = '".$gelombang."' ");
    //jurusan spc //kota all  
    }elseif($all_jurusan != 'Y' && $all_kota == 'Y'){
      $po = mysqli_query($conn,  "SELECT * FROM siswa WHERE sw_status >= 7 AND sw_jurusan = '".$jurusan."' AND sw_tahun_ajaran = '".$tahun_ajaran."' AND sw_gelombang = '".$gelombang."' ");
    //jurusan spc //kota spc  
    }elseif($all_jurusan != 'Y' && $all_kota != 'Y'){
      $po = mysqli_query($conn,  "SELECT * FROM siswa WHERE sw_status >= 7 AND sw_jurusan = '".$jurusan."' AND sw_alamat_kota = '".$kota."' AND sw_tahun_ajaran = '".$tahun_ajaran."' AND sw_gelombang = '".$gelombang."' ");
    }

    $count = mysqli_num_rows($po);
      $style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        )
      );
      $style2 = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
        )
      );

    $i = 1;
    while($rs_po = mysqli_fetch_assoc($po)) {
      $arrPO[] = $rs_po["sw_id"];

      $sheet->setCellValue('A'.$xlsRow, $i);
      $sheet->getStyle('A'.$xlsRow)->applyFromArray($style);

      $sheet->setCellValue('B'.$xlsRow, $rs_po["sw_nama_lengkap"]);
      $sheet->getStyle('B'.$xlsRow)->getFont()->setSize(13);
      $sheet->getStyle('C'.$xlsRow)->getFont()->setBold(true);

      $sheet->setCellValue('C'.$xlsRow, $rs_po["sw_no_tes"]);
      $sheet->getStyle('C'.$xlsRow)->getFont()->setSize(13);
      $sheet->getStyle('C'.$xlsRow)->getFont()->setBold(true);

      $sheet->setCellValue('D'.$xlsRow, $rs_po["sw_ttl"]);
      $sheet->getStyle('D'.$xlsRow)->getFont()->setSize(13);

      $sheet->setCellValue('E'.$xlsRow, $rs_po["sw_alamat"]);
      $sheet->getStyle('E'.$xlsRow)->getFont()->setSize(13);

      if($rs_po["sw_gender"] == 'L'){
        $sheet->setCellValue('F'.$xlsRow, 'Laki-laki');
        $sheet->getStyle('F'.$xlsRow)->getFont()->setSize(13);
      }  
      else{
        $sheet->setCellValue('F'.$xlsRow, 'Perempuan');
        $sheet->getStyle('F'.$xlsRow)->getFont()->setSize(13); 
      } 

      $sheet->setCellValue('G'.$xlsRow, $rs_po["sw_agama"]);
      $sheet->getStyle('G'.$xlsRow)->getFont()->setSize(13);

      $sheet->setCellValue('H'.$xlsRow, $rs_po["sw_jurusan"]);
      $sheet->getStyle('H'.$xlsRow)->getFont()->setSize(13);

      $sheet->setCellValue('I'.$xlsRow, $rs_po["sw_sekolah_asal"]);
      $sheet->getStyle('I'.$xlsRow)->getFont()->setSize(13);

      $sheet->setCellValue('J'.$xlsRow, $rs_po["sw_sekolah_asal_alamat"]);
      $sheet->getStyle('J'.$xlsRow)->getFont()->setSize(13);

      $sheet->setCellValue('K'.$xlsRow, $rs_po["sw_tahun_ajaran"]);
      $sheet->getStyle('K'.$xlsRow)->getFont()->setSize(13);

      $sheet->setCellValue('L'.$xlsRow, $rs_po["sw_created_at"]);
      $sheet->getStyle('L'.$xlsRow)->getFont()->setSize(13);

      $type1 = 'AYAH';
      $poPby1 = mysqli_query($conn, "SELECT * FROM ortu WHERE sw_id = '".$rs_po["sw_id"]."' AND ot_type = '".$type1."' "); 
      $pivotRow1 = $xlsRow;
      if(mysqli_num_rows($poPby1)) {
        while ($rs_pby1 = mysqli_fetch_assoc($poPby1)) {
          $sheet->setCellValue('M'.$pivotRow1, $rs_pby1["ot_nama"]);
          $sheet->getStyle('M'.$pivotRow1)->getFont()->setSize(13);
          $format = substr($rs_pby1["ot_no_hp"], 0, 2);
          if($format == '08') $rs_pby1["ot_no_hp"] = '+628'.substr($rs_pby1["ot_no_hp"], 2);
          $sheet->setCellValue('Q'.$pivotRow1, $rs_pby1["ot_no_hp"]);
          $sheet->getStyle('Q'.$pivotRow1)->getFont()->setSize(13);

          $sheet->setCellValue('T'.$pivotRow1, $rs_pby1["ot_pekerjaan"]);
          $sheet->getStyle('T'.$pivotRow1)->getFont()->setSize(13);
          
        }   
      }else{
        $sheet->setCellValue('M'.$pivotRow1, '-');
        $sheet->getStyle('M'.$pivotRow1)->getFont()->setSize(13);
        $sheet->setCellValue('Q'.$pivotRow1, '-');
        $sheet->getStyle('Q'.$pivotRow1)->getFont()->setSize(13);
      }

      $type2 = 'IBU';
      $poPby2 = mysqli_query($conn, "SELECT * FROM ortu WHERE sw_id = '".$rs_po["sw_id"]."' AND ot_type = '".$type2."' ");
      $pivotRow2 = $xlsRow;
      if(mysqli_num_rows($poPby2)) {
        while ($rs_pby2 = mysqli_fetch_assoc($poPby2)) {
          $sheet->setCellValue('N'.$pivotRow2, $rs_pby2["ot_nama"]);
          $sheet->getStyle('N'.$pivotRow2)->getFont()->setSize(13);
          $format = substr($rs_pby2["ot_no_hp"], 0, 2);
          if($format == '08') $rs_pby2["ot_no_hp"] = '+628'.substr($rs_pby2["ot_no_hp"], 2);
          $sheet->setCellValue('R'.$pivotRow2, $rs_pby2["ot_no_hp"]);
          $sheet->getStyle('R'.$pivotRow2)->getFont()->setSize(13);

          $sheet->setCellValue('U'.$pivotRow2, $rs_pby2["ot_pekerjaan"]);
          $sheet->getStyle('U'.$pivotRow2)->getFont()->setSize(13);
        }
      }else{
        $sheet->setCellValue('N'.$pivotRow2, '-');
        $sheet->getStyle('N'.$pivotRow2)->getFont()->setSize(13);
        $sheet->setCellValue('R'.$pivotRow2, '-');
        $sheet->getStyle('R'.$pivotRow2)->getFont()->setSize(13);
      }

      $type3 = 'WALI';
      $poPby3 = mysqli_query($conn, "SELECT * FROM ortu WHERE sw_id = '".$rs_po["sw_id"]."' AND ot_type = '".$type3."' ");
      $pivotRow3 = $xlsRow;
      if(mysqli_num_rows($poPby3)) {
        while ($rs_pby3 = mysqli_fetch_assoc($poPby3)) {
          $sheet->setCellValue('O'.$pivotRow3, $rs_pby3["ot_nama"]);
          $sheet->getStyle('O'.$pivotRow3)->getFont()->setSize(13);
          $format = substr($rs_pby3["ot_no_hp"], 0, 2);
          if($format == '08') $rs_pby3["ot_no_hp"] = '+628'.substr($rs_pby3["ot_no_hp"], 2);
          $sheet->setCellValue('S'.$pivotRow1, $rs_pby3["ot_no_hp"]);
          $sheet->getStyle('S'.$pivotRow1)->getFont()->setSize(13);

          $sheet->setCellValue('V'.$pivotRow3, $rs_pby3["ot_pekerjaan"]);
          $sheet->getStyle('V'.$pivotRow3)->getFont()->setSize(13);
        }
      }else{
        $sheet->setCellValue('O'.$pivotRow3, '-');
        $sheet->getStyle('O'.$pivotRow3)->getFont()->setSize(13);
        $sheet->setCellValue('S'.$pivotRow3, '-');
        $sheet->getStyle('S'.$pivotRow3)->getFont()->setSize(13);
      }


      if($rs_po["sw_status"] == 8){
        $sheet->setCellValue('P'.$xlsRow, '-');
        $sheet->getStyle('P'.$xlsRow)->getFont()->setSize(13);
        $sheet->getStyle('P'.$xlsRow)->getFont()->setBold(true);
      }elseif($rs_po["sw_status"] == 9){
        $sheet->setCellValue('P'.$xlsRow, '-');
        $sheet->getStyle('P'.$xlsRow)->getFont()->setSize(13);
        $sheet->getStyle('P'.$xlsRow)->getFont()->setBold(true);
      }else{
        if(!isset($_GET['is_final'])){
          $sheet->setCellValue('P'.$xlsRow, '-');
          $sheet->getStyle('P'.$xlsRow)->getFont()->setSize(13);
          $sheet->getStyle('P'.$xlsRow)->getFont()->setBold(true);
        }else{
          $sheet->setCellValue('P'.$xlsRow, '-');
          $sheet->getStyle('P'.$xlsRow)->getFont()->setSize(13);
          $sheet->getStyle('P'.$xlsRow)->getFont()->setBold(true);
        }
      }

      $xlsRow++;
      $i++;
    }
    $xlsRow += 1;
    $sheet->setCellValue('B'.$xlsRow, ' T O T A L   P E N D A F T A R  : ');
    $sheet->getStyle('B'.$xlsRow)->applyFromArray($style2);
    $sheet->setCellValue('C'.$xlsRow, $count);
    
    $sheet->getStyle('B'.$xlsRow.':C'.$xlsRow)->getFont()->setSize(13);
    $sheet->getStyle('B'.$xlsRow.':C'.$xlsRow)->getFont()->setBold(true);

    $xlsRow += 3;
    $sheet->getStyle('B'.$xlsRow)->getFont()->setBold(true);
    $sheet->getStyle('C'.$xlsRow)->getFont()->setBold(true);
    $sheet->setCellValue('B'.$xlsRow, "Pembuat");
    $sheet->getStyle('B'.$xlsRow)->applyFromArray($style2);
    $sheet->setCellValue('C'.$xlsRow, "Panitia");
    $sheet->getStyle('C'.$xlsRow)->applyFromArray($style2);
    
    $xlsRow += 3;
    $sheet->setCellValue('B'.$xlsRow, "Badrul Akbar A M");
    $sheet->getStyle('B'.$xlsRow)->applyFromArray($style2);

    //set default sheet on opening file
    $objPHPExcel->setActiveSheetIndex(0);

    //OUTPUT section
    header('Content-Type: application/vnd.ms-excel');
    if($all_jurusan == 'Y' && $all_kota == 'Y') header('Content-Disposition: attachment;filename="Data pendaftaran SMK RUS tahun ajaran '.$tahun_ajaran.' gelombang '.$gelombang.' semua jurusan dan semua kota ('.date("YmdHis").').xls"');
    elseif($all_jurusan == 'Y' && $all_kota != 'Y') header('Content-Disposition: attachment;filename="Data pendaftaran SMK RUS tahun ajaran '.$tahun_ajaran.' gelombang '.$gelombang.' semua jurusan dan kota kudus ('.date("YmdHis").').xls"');
    elseif($all_jurusan != 'Y' && $all_kota == 'Y') header('Content-Disposition: attachment;filename="Data pendaftaran SMK RUS tahun ajaran '.$tahun_ajaran.' gelombang '.$gelombang.' jurusan '.$jurusan.' dan semua kota ('.date("YmdHis").').xls"');
    elseif($all_jurusan != 'Y' && $all_kota != 'Y') header('Content-Disposition: attachment;filename="Data pendaftaran SMK RUS tahun ajaran '.$tahun_ajaran.' gelombang '.$gelombang.' jurusan '.$jurusan.' dan kota kudus ('.date("YmdHis").').xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
  }
}


if(strtolower($act) == "data-lolos-seleksi") {

  $tahun_ajaran = $_GET['tahun_ajaran'];
  $gelombang = $_GET['gelombang'];

  if($export == "excel") {
    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

    // Set document properties
    $objPHPExcel->getProperties()->setCreator("PPDB SMKRUS")
                   ->setLastModifiedBy("PPDB SMKRUS")
                   ->setTitle("PPDB SMKRUS")
                   ->setSubject("PPDB SMKRUS")
                   ->setDescription("PPDB SMKRUS")
                   ->setKeywords("PPDB SMKRUS")
                   ->setCategory("PPDB SMKRUS");
    // template
    $objPHPExcel = PHPExcel_IOFactory::load("reports/template-data_lolos-seleksi.xlsx");
    $sheet = $objPHPExcel->setActiveSheetIndex(0);
    $sheet->setCellValue('B3', tglIndo(date("Y-m-d H:i:s"), "SHORT"));

    // $pw = mysqli_query($conn,  "SELECT * FROM info WHERE inf_subject = 'tahun_ajaran' ");
    // $rs_pw = mysqli_fetch_assoc($pw);

    $startRow = 7;
    $xlsRow = $startRow;

    $arrItems = [];
    $arrPO = [];

    $po = mysqli_query($conn,  "SELECT * FROM siswa WHERE sw_status = 9 AND sw_gelombang = '".$gelombang."' AND sw_tahun_ajaran = '".$tahun_ajaran."' ");
    $count = mysqli_num_rows($po);

    $i = 1;
    $gelombang = '';
    $tahun_ajaran = '';
    while($rs_po = mysqli_fetch_assoc($po)) {
      $gelombang = $rs_po["sw_gelombang"];
      $tahun_ajaran = $rs_po["sw_tahun_ajaran"];

      $sheet->setCellValue('A'.$xlsRow, $i);
      $style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        )
      );
      $style2 = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
        )
      );
      $sheet->getStyle('A'.$xlsRow)->applyFromArray($style);

      $sheet->setCellValue('B'.$xlsRow, $rs_po["sw_nama_lengkap"]);
      $sheet->getStyle('B'.$xlsRow)->getFont()->setSize(13);
      $sheet->getStyle('B'.$xlsRow)->getFont()->setBold(true);

      $type1 = 'AYAH';
      $poPby1 = mysqli_query($conn, "SELECT * FROM ortu WHERE sw_id = '".$rs_po["sw_id"]."' AND ot_type = '".$type1."' "); 
      $pivotRow1 = $xlsRow;
      if(mysqli_num_rows($poPby1)) {
        while ($rs_pby1 = mysqli_fetch_assoc($poPby1)) {
          $sheet->setCellValue('C'.$pivotRow1, $rs_pby1["ot_no_hp"]);
          $sheet->getStyle('C'.$pivotRow1)->getFont()->setSize(13);
        }   
      }else{
        $sheet->setCellValue('C'.$pivotRow1, '-');
        $sheet->getStyle('C'.$pivotRow1)->getFont()->setSize(13);
      }

      $type2 = 'IBU';
      $poPby2 = mysqli_query($conn, "SELECT * FROM ortu WHERE sw_id = '".$rs_po["sw_id"]."' AND ot_type = '".$type2."' ");
      $pivotRow2 = $xlsRow;
      if(mysqli_num_rows($poPby2)) {
        while ($rs_pby2 = mysqli_fetch_assoc($poPby2)) {
          $sheet->setCellValue('D'.$pivotRow2, $rs_pby2["ot_no_hp"]);
          $sheet->getStyle('D'.$pivotRow2)->getFont()->setSize(13);
        }
      }else{
        $sheet->setCellValue('D'.$pivotRow1, '-');
        $sheet->getStyle('D'.$pivotRow1)->getFont()->setSize(13);
      }

      $type3 = 'WALI';
      $poPby3 = mysqli_query($conn, "SELECT * FROM ortu WHERE sw_id = '".$rs_po["sw_id"]."' AND ot_type = '".$type3."' ");
      $pivotRow3 = $xlsRow;
      if(mysqli_num_rows($poPby3)) {
        while ($rs_pby3 = mysqli_fetch_assoc($poPby3)) {
          $sheet->setCellValue('E'.$pivotRow3, $rs_pby3["ot_no_hp"]);
          $sheet->getStyle('E'.$pivotRow3)->getFont()->setSize(13);
        }
      }else{
        $sheet->setCellValue('E'.$pivotRow3, '-');
        $sheet->getStyle('E'.$pivotRow3)->getFont()->setSize(13);
      }
      
      $sheet->setCellValue('F'.$xlsRow, $rs_po["sw_jurusan"]);
      $sheet->getStyle('F'.$xlsRow)->getFont()->setSize(13);
      
      $sheet->setCellValue('G'.$xlsRow, $rs_po["sw_alamat_kota"]);
      $sheet->getStyle('G'.$xlsRow)->getFont()->setSize(13);
      
      $xlsRow++;
      $i++;
    }
    $xlsRow += 1;
    $sheet->setCellValue('B'.$xlsRow, ' T O T A L  : ');
    $sheet->getStyle('B'.$xlsRow)->applyFromArray($style2);
    $sheet->setCellValue('C'.$xlsRow, $count);
    
    $sheet->getStyle('B'.$xlsRow.':C'.$xlsRow)->getFont()->setSize(13);
    $sheet->getStyle('B'.$xlsRow.':C'.$xlsRow)->getFont()->setBold(true);
    
    $xlsRow += 3;
    $sheet->getStyle('B'.$xlsRow)->getFont()->setBold(true);
    $sheet->getStyle('C'.$xlsRow)->getFont()->setBold(true);
    $sheet->setCellValue('B'.$xlsRow, "Pembuat");
    $sheet->getStyle('B'.$xlsRow)->applyFromArray($style2);
    $sheet->setCellValue('C'.$xlsRow, "Panitia");
    $sheet->getStyle('C'.$xlsRow)->applyFromArray($style2);
    
    $xlsRow += 3;
    $sheet->setCellValue('B'.$xlsRow, "Badrul Akbar A M");
    $sheet->getStyle('B'.$xlsRow)->applyFromArray($style2);
    
    //set default sheet on opening file
    $objPHPExcel->setActiveSheetIndex(0);

    //OUTPUT section
    header('Content-Type: application/vnd.ms-excel');header('Content-Disposition: attachment;filename="Data pendaftar lolos seleksi SMK RUS tahun ajaran '.$tahun_ajaran.' gelombang '.$gelombang.' ('.date("YmdHis").').xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
  }
}


if(strtolower($act) == "template-seleksi") {

  $tahun_ajaran = $_GET['tahun_ajaran'];
  $gelombang = $_GET['gelombang'];
  $jurusan = $_GET['jurusan'];

  if($export == "excel") {
    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

    // Set document properties
    $objPHPExcel->getProperties()->setCreator("PPDB SMKRUS")
                   ->setLastModifiedBy("PPDB SMKRUS")
                   ->setTitle("PPDB SMKRUS")
                   ->setSubject("PPDB SMKRUS")
                   ->setDescription("PPDB SMKRUS")
                   ->setKeywords("PPDB SMKRUS")
                   ->setCategory("PPDB SMKRUS");
    // template
    $objPHPExcel = PHPExcel_IOFactory::load("reports/data-seleksi.xlsx");
    $sheet = $objPHPExcel->setActiveSheetIndex(0);

    $style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        )
      );
      $style2 = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
        )
      );
      $style3 = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
        )
      );

    $sheet->setCellValue('B3', $tahun_ajaran);
    $sheet->setCellValue('B4', $gelombang);
    $sheet->getStyle('B4')->applyFromArray($style3);
    $sheet->setCellValue('B5', $jurusan);
    $sheet->getStyle('B5')->getFont()->setBold(true);

    // $pw = mysqli_query($conn,  "SELECT * FROM info WHERE inf_subject = 'tahun_ajaran' ");
    // $rs_pw = mysqli_fetch_assoc($pw);

    $startRow = 9;
    $xlsRow = $startRow;

    $arrItems = [];
    $arrPO = [];

    $po = mysqli_query($conn,  "SELECT * FROM siswa WHERE sw_status = 7  AND sw_gelombang = '".$gelombang."' AND sw_tahun_ajaran = '".$tahun_ajaran."' AND sw_jurusan = '".$jurusan."' ");
    $count = mysqli_num_rows($po);

    $i = 1;
    $gelombang = '';
    $tahun_ajaran = '';
    while($rs_po = mysqli_fetch_assoc($po)) {
      $tahun_ajaran = $rs_po["sw_tahun_ajaran"];
      $gelombang = $rs_po["sw_gelombang"];

      $sheet->setCellValue('A'.$xlsRow, $i);
      $style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        )
      );
      $style2 = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
        )
      );
      $sheet->getStyle('A'.$xlsRow)->applyFromArray($style);

      $sheet->setCellValue('B'.$xlsRow, $rs_po["sw_no_tes"]);
      $sheet->getStyle('B'.$xlsRow)->getFont()->setSize(13);
      $sheet->getStyle('B'.$xlsRow)->getFont()->setBold(true);
      
      $sheet->setCellValue('C'.$xlsRow, $rs_po["sw_nama_lengkap"]);
      $sheet->getStyle('C'.$xlsRow)->getFont()->setSize(13);
      //$sheet->getStyle('C'.$xlsRow)->getFont()->setBold(true);
      

      $sheet->setCellValue('D'.$xlsRow, "_ PILIH _");

      $configs = "DITERIMA, TIDAK DITERIMA, CADANGAN, LAINNYA";

      $objValidation = $objPHPExcel->getActiveSheet()->getCell('D'.$xlsRow)->getDataValidation();
      $objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
      $objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
      $objValidation->setAllowBlank(false);
      $objValidation->setShowInputMessage(true);
      $objValidation->setShowErrorMessage(true);
      $objValidation->setShowDropDown(true);
      $objValidation->setErrorTitle('input error');
      $objValidation->setError('tidak ada pilihan tersedia.');
      $objValidation->setPromptTitle('pilih status penerimaan siswa!');
      $objValidation->setPrompt('klik icon segitiga disamping untuk memilih!');
      $objValidation->setFormula1('"'.$configs.'"');

      $xlsRow++;
      $i++;
    }
    $xlsRow += 1;
    $sheet->setCellValue('B'.$xlsRow, ' T O T A L  : ');
    $sheet->getStyle('B'.$xlsRow)->applyFromArray($style2);
    $sheet->setCellValue('C'.$xlsRow, $count);
    
    $sheet->getStyle('B'.$xlsRow.':C'.$xlsRow)->getFont()->setSize(13);
    $sheet->getStyle('B'.$xlsRow.':C'.$xlsRow)->getFont()->setBold(true);
    
    $xlsRow += 3;
    $sheet->getStyle('B'.$xlsRow)->getFont()->setBold(true);
    $sheet->getStyle('C'.$xlsRow)->getFont()->setBold(true);
    $sheet->setCellValue('B'.$xlsRow, "Pembuat");
    $sheet->getStyle('B'.$xlsRow)->applyFromArray($style2);
    $sheet->setCellValue('C'.$xlsRow, "Panitia");
    $sheet->getStyle('C'.$xlsRow)->applyFromArray($style2);
    
    $xlsRow += 3;
    $sheet->setCellValue('B'.$xlsRow, "Badrul Akbar A M");
    $sheet->getStyle('B'.$xlsRow)->applyFromArray($style2);

    $sheet->getProtection()->setSheet(true);
    $sheet->getStyle('D'.$startRow.':D'.$xlsRow)
    ->getProtection()
    ->setLocked(
        PHPExcel_Style_Protection::PROTECTION_UNPROTECTED
    );

    //set default sheet on opening file
    $objPHPExcel->setActiveSheetIndex(0);

    //OUTPUT section
    header('Content-Type: application/vnd.ms-excel');header('Content-Disposition: attachment;filename="Data seleksi SMK RUS tahun ajaran '.$tahun_ajaran.' gelombang '.$gelombang.' jurusan '.$jurusan.' ('.date("YmdHis").').xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');

  }
}


// if(strtolower($act) == "hasil-seleksi") {

//   if($export == "excel") {

//     if(isset($_FILES['result-exam'])){
//       $fileName = $_FILES['result-exam']['name'];
//       $fileSize = $_FILES['result-exam']['size'];
//       $fileTmp = $_FILES['result-exam']['tmp_name'];
//       $fileType = $_FILES['result-exam']['type'];
//       $a = explode(".", $_FILES["result-exam"]["name"]);
//       $fileExt = strtolower(end($a));

//       $uploadFile = "reports/result-".date("YmdHis").".".$fileExt;
//       move_uploaded_file($fileTmp, $uploadFile);

//       // Create new PHPExcel object
//       $objPHPExcel = new PHPExcel();

//       // Set document properties
//       $objPHPExcel->getProperties()->setCreator("PPDB SMKRUS")
//                      ->setLastModifiedBy("PPDB SMKRUS")
//                      ->setTitle("PPDB SMKRUS")
//                      ->setSubject("PPDB SMKRUS")
//                      ->setDescription("PPDB SMKRUS")
//                      ->setKeywords("PPDB SMKRUS")
//                      ->setCategory("PPDB SMKRUS");
//       // template
//       $objPHPExcel = PHPExcel_IOFactory::load($uploadFile);
//       $sheet = $objPHPExcel->setActiveSheetIndex(0);

//       $tahun_ajaran = $sheet->getCell('B3')->getValue();
//       $gelombang = $sheet->getCell('B4')->getValue();
//       $jurusan = $sheet->getCell('B5')->getValue();

//       $startRow = 9;
//       $xlsRow = $startRow;

//       $po = mysqli_query($conn,  "SELECT * FROM siswa WHERE sw_status >= 7  AND sw_gelombang = '".$gelombang."' AND sw_tahun_ajaran = '".$tahun_ajaran."' AND sw_jurusan = '".$jurusan."' ");
//       $count = mysqli_num_rows($po);

//       // bandingkan jumlah record excel di DB dg data DB skrg
//       $shorter = false;

//       $gelombang = '';
//       $tahun_ajaran = '';
//       $i = 1;
//       $status7 = array();
//       $errors = array(); 
//       while($rs_po = mysqli_fetch_assoc($po)) {

//         //if($rs_po['sw_status'] == 7) array_push($status7, $rs_po['sw_no_tes']);
//         $no_tes = trim($sheet->getCell('B'.$xlsRow)->getValue());
//         $C_cell = strtolower(str_replace(' ', '',$sheet->getCell('D'.$xlsRow)->getValue()));
//         if($C_cell == 'diterima' || $C_cell == 'lolos' || $C_cell == 'y' || $C_cell == 'ya' || $C_cell == 'yes'){
//           mysqli_query($conn,  "UPDATE siswa SET sw_status = 9 WHERE sw_no_tes = '".$no_tes."' ");
//         }elseif($C_cell == 'tidakditerima' || $C_cell == 'gagal' || $C_cell == 'tidaklolos' || $C_cell == 'n' || $C_cell == 'no' || $C_cell == 'tidak'){
//           mysqli_query($conn,  "UPDATE siswa SET sw_status = 8 WHERE sw_no_tes = '".$no_tes."' ");
//         }elseif($C_cell == 'cadangan' || $C_cell == 'pengganti' || $C_cell == 'tidaklolos'){
//           mysqli_query($conn,  "UPDATE siswa SET sw_status = 10 WHERE sw_no_tes = '".$no_tes."' ");
//         }else{ // if data awal < data final (tambahan data)
//           array_push($errors, $rs_po['sw_no_tes']);
//           $longer = true;
//         }

//         $xlsRow++;
//         $i++;
//       }

//       $po2 = mysqli_query($conn,  "SELECT * FROM siswa WHERE sw_status = 7  AND sw_gelombang = '".$gelombang."' AND sw_tahun_ajaran = '".$tahun_ajaran."' AND sw_jurusan = '".$jurusan."' ");

//       $sheet->getProtection()->setSheet(true);
//       $sheet->getStyle('C'.$startRow.':C'.$xlsRow)
//       ->getProtection()
//       ->setLocked(
//           PHPExcel_Style_Protection::PROTECTION_UNPROTECTED
//       );

//       //set default sheet on opening file
//       $objPHPExcel->setActiveSheetIndex(0);

//       header("Content-Type: application/json");
//       echo json_encode(array(
//                 "SENDER" => "Admin PPDB SMK Raden Umar Said Kudus",
//                 "STATUS" => 'SUCCESS',
//                 "MESSAGE" => 'success import exam result data!',
//                 "PAYLOAD" => array('not_filtered' => $errors, 'shorter' => $shorter) ));
//     }else{
//       header("Content-Type: application/json");
//       echo json_encode(array(
//               "SENDER" => "Admin PPDB SMK Raden Umar Said Kudus",
//               "STATUS" => 'ERROR',
//               "MESSAGE" => 'please input excels file!',
//               "PAYLOAD" => null ));
//     }
//   }
// }


if(strtolower($act) == "hasil-seleksi2") {

  if($export == "excel") {

    if(isset($_FILES['result-exam'])){
      $fileName = $_FILES['result-exam']['name'];
      $fileSize = $_FILES['result-exam']['size'];
      $fileTmp = $_FILES['result-exam']['tmp_name'];
      $fileType = $_FILES['result-exam']['type'];
      $a = explode(".", $_FILES["result-exam"]["name"]);
      $fileExt = strtolower(end($a));

      $uploadFile = "reports/result-".date("YmdHis").".".$fileExt;
      move_uploaded_file($fileTmp, $uploadFile);

      $openFile = fopen($uploadFile, "r") or die("Unable to open file!");

      $reader = PHPExcel_IOFactory::createReaderForFile($uploadFile);
      $reader->setReadDataOnly(true);
      $objXLS = $reader->load($uploadFile);

      $objWorksheet = $objXLS->getSheet(0);

      $highestColumn = $objWorksheet->getHighestColumn();
      $highestRow = $objWorksheet->getHighestRow();

      //$headersLine = [1,2,3,4,5,6,7,8];
      $errors = [];
      for ($i=1; $i <= ($highestRow - 8) ; $i++) { 
        if($i == 1 || $i == 2 || $i == 3 || $i == 4 || $i == 5 || $i == 6 || $i == 7 || $i == 8){
          //do nothing
        }else{
          $no_tes =  $objWorksheet->getCell('B'.$i)->getValue();
          $status = strtolower(str_replace(' ', '',$objWorksheet->getCell('D'.$i)->getValue()));
          if($status == 'diterima'){
            $update = mysqli_query($conn,  "UPDATE siswa SET sw_status = 9 WHERE sw_no_tes = '".$no_tes."' ");
          }elseif($status == 'tidakditerima'){
            $update = mysqli_query($conn,  "UPDATE siswa SET sw_status = 8 WHERE sw_no_tes = '".$no_tes."' ");
          }elseif($status == 'cadangan'){
            $update = mysqli_query($conn,  "UPDATE siswa SET sw_status = 10 WHERE sw_no_tes = '".$no_tes."' ");
          }else{
            $update = mysqli_query($conn,  "UPDATE siswa SET sw_status = 7 WHERE sw_no_tes = '".$no_tes."' ");
            array_push($errors, $no_tes);
          }
          
        }
      }
      mysqli_close($conn);

      $objXLS->disconnectWorksheets();
      unset($objXLS);

      header("Content-Type: application/json");
      echo json_encode(array(
              "SENDER" => "Admin PPDB SMK Raden Umar Said Kudus",
              "STATUS" => 'SUCCESS',
              "MESSAGE" => 'success import excel',
              "PAYLOAD" => ["errors" => $errors] ));
      
    }else{
      header("Content-Type: application/json");
      echo json_encode(array(
              "SENDER" => "Admin PPDB SMK Raden Umar Said Kudus",
              "STATUS" => 'ERROR',
              "MESSAGE" => 'please input excels file!',
              "PAYLOAD" => null ));
    }
  }
}


// ======================================================================================================================= //


?>