<?php
use App\Model as M;
use Carbon\Carbon;


class Helper {

	public static function base_url() 
	{
	    $appPath = (env("APP_ENV") == "local" ? "/cineting" : "");
	    $cleanUrl = env('PROTOCOL') == 'https' ? secure_url('/') : url('/');
	    $cleanUrl = str_replace($appPath, "", $cleanUrl);

	    //return (env('PROTOCOL') == 'https' ? secure_url('/').$appPath : url('/').$appPath);

	    return $cleanUrl.$appPath;
	}

	public static function getString(){
		return "STRING FROM HELPER";
	}

	public static function uri1(){
		return Request::segment(1);
	}

	public static function uri2(){
		return Request::segment(2);
	}

	public static function uri3(){
		return Request::segment(3);
	}

	public static function uri4(){
		return Request::segment(4);
	}

	public static function allUri($n, $start = 2){
		$uri = "";
		for ($i = $start; $i <= $n; $i++){
			$uri .= Request::segment($i);
			if($i != $n){
				$uri .= "/";
			}
		}
		return $uri;
	}

	public static function compose($status,$msg,$payload = null) {
		header("Content-Type: application/json");
		$reply = json_encode(array(
			"SENDER" => "Watesta Backend App",
			"STATUS" => $status,
			"MESSAGE" => $msg,
			"PAYLOAD" => $payload));

		return $reply;
	}

	public static function compose2($status,$msg,$payload = null) { //LARAVEL WAY
		$reply = json_encode(array(
			"SENDER" => "Watesta Backend App",
			"STATUS" => $status,
			"MESSAGE" => $msg,
			"PAYLOAD" => $payload));

		return Response::make($reply, '200')->header('Content-Type', 'application/json');
	}

	public static function tanggal($tgl,$mode = "LONG") {
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

	public static function tglIndo($tgl,$mode, $timeSegment = "SECOND") {
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
				if($timeSegment == "HOUR"){
					$jam = substr($jam, 0, 2);
				}else if($timeSegment == "MINUTE"){
					$jam = substr($jam, 0, 5);
				}
		  	}

		  	return $t[2]." ".$bln[$b][$mode]." ".$t[0]." ".$jam;
		}
		else {
		  	return "-";
		}
	}

	public static function bulan($tgl,$mode = "LONG") {
		if($tgl == "" || $mode == "" || $tgl == "0000-00"){
			return "-";
		}
		$t = explode("-", $tgl);
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

		return $bln[$t[1]][$mode] . " " . $t[0];
	}

	public static function randomDigits($length){
		$digits = "";
		$numbers = range(0,9);
		shuffle($numbers);
		for($i = 0;$i < $length;$i++) {
		  $digits .= $numbers[$i];
		}
		return $digits;
	}

	public static function createCode($codeLength) {
		$kode = strtoupper(substr(md5(Helper::randomDigits($codeLength)), 0,($codeLength-1) ));

		return $kode;
	}

	public static function getSetting($setId)  {
		$setValue = "";
		$setting = DB::select("SELECT S_VALUE FROM _setting WHERE S_ID = ? LIMIT 0,1",array($setId));
		if(count($setting) > 0) {
		  $rs_setting = $setting[0];
		  $setValue = $rs_setting->{"S_VALUE"};
		}

		return $setValue;
	}

	public static function getReferenceInfo($refKtgId, $refValueId) {
		$ref = DB::table("_reference")
			->where("R_CATEGORY",$refKtgId)
			->where("R_ID",$refValueId)
			->first();

		if(isset($ref) > 0) {
			return $ref->{"R_INFO"};
		}
	  	else {
	  		return "";
	  	}
	}

	public static function getReferenceInfoByOrder($refKtgId, $refOrder) {
		$ref = DB::table("_reference")
			->where("R_CATEGORY",$refKtgId)
			->where("R_ORDER",$refOrder)
			->first();

		if(isset($ref) > 0) {
			return $ref->{"R_INFO"};
		}
	  	else {
	  		return "";
	  	}
	}

	public static function getReferenceOrderById($refKtgId, $refId) {
		$ref = DB::table("_reference")
			->where("R_CATEGORY",$refKtgId)
			->where("R_ID",$refId)
			->first();

		if(isset($ref) > 0) {
			return $ref->{"R_ORDER"};
		}
	  	else {
	  		return "";
	  	}
	}

	public static function getReferenceIdByOrder($refKtgId, $order) {
		$ref = DB::table("_reference")
			->where("R_CATEGORY",$refKtgId)
			->where("R_ORDER",$order)
			->first();

		if(isset($ref)) {
			return $ref->{"R_ID"};
		}
	  	else {
	  		return $order;
	  	}
	}

	public static function getReference($refKtgId) {
		$ref = DB::table("_reference")
			->where("R_CATEGORY",$refKtgId)
			->orderBy("R_ORDER", "ASC")
			->get();

		if(isset($ref) || $ref > 0) {
			return $ref;
		}
	  	else {
	  		return [];
	  	}
	}


	public static function logCURL($name, $url, $method, $data, $response){
		$save = DB::table("request")->insertGetId([
			"REQUEST_DATETIME" => date("Y-m-d H:i:s"),
			"REQUEST_URL" => $url,
			"REQUEST_NAME" => $name,
			"REQUEST_METHOD" => $method,
			"REQUEST_DATA" => json_encode($data),
			"REQUEST_RESPONSE" => $response,
		]);
		return $save;
	}


	public static function curl(){
		$curl = curl_init();
		$token = "AX0KwzOkATFQ0GkcDKmkjffiFVrzSUZM0LMRPQu58KgHIlTpCmvX71iK74pTLGYi";
		$phone = "6281215992673";
		$message = Helper::createCode(10);
		curl_setopt($curl, CURLOPT_URL, "https://kudus.wablas.com/api/send-message?phone=$phone&message=$message&token=$token");
		$result = curl_exec($curl);
		curl_close($curl);
		return($result);
	}


	public static function curl2(){
		$token = Helper::getSetting("WABOT_TOKEN");
		$phone = Helper::getSetting("WABOT_DEFAULT_DESTINATION");
		$message = Helper::createCode(10);
		$options = array(
			CURLOPT_RETURNTRANSFER => true,   // return web page
			CURLOPT_HEADER         => false,  // don't return headers
			CURLOPT_FOLLOWLOCATION => true,   // follow redirects
			CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
			CURLOPT_ENCODING       => "",     // handle compressed
			CURLOPT_USERAGENT      => "test", // name of client
			CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
			CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
			CURLOPT_TIMEOUT        => 120,    // time-out on response
		);
        $curl = curl_init("https://kudus.wablas.com/api/send-message?phone=$phone&message=$message&token=$token");
		curl_setopt_array($curl, $options);
		$result = curl_exec($curl);
		curl_close($curl);
		return $result;
	}


	public static function calculateZScore($gender, $usia, $tb){
		$usia = intval($usia);
		$tb = floatval($tb);
		if($gender == "1"){
			$gender = "GENDER_MALE";
		}elseif($gender == "2"){
			$gender = "GENDER_FEMALE";
		}
		$formula = DB::select("
			SELECT * FROM zscore WHERE ZSCORE_GENDER = ? AND ZSCORE_USIA = ?
		", [$gender, $usia]);
		if(count($formula) == 0){
			return 0;
		}
		$formula = $formula[0];
		$pembilang = (floatval($tb) - $formula->{"ZSCORE_MEDIAN"});
		$penyebut = $pembilang > 0 ? ($formula->{"ZSCORE_PLUS1SD"} - $formula->{"ZSCORE_MEDIAN"}) : ($formula->{"ZSCORE_MEDIAN"} - $formula->{"ZSCORE_MIN1SD"});
		$zscore = $pembilang / $penyebut;
		
		return number_format((float)$zscore, 2, '.', '');
	}


	public static function zscoreInfo($zscore){
		if($zscore < -3){
			$stunting = "Sangat Pendek";
		}else if($zscore >= -3 && $zscore <= -2){
			$stunting = "Pendek";
		}else if($zscore > -2 && $zscore <= 2){
			$stunting = "Normal";
		}else if($zscore > 2){
			$stunting = "Tinggi";
		}
		return $stunting;
	}


	public static function getRekomendasi($zscore){
		$stunting = Helper::zscoreInfo($zscore);
		if($stunting == "Sangat Pendek"){
			$suggest = "Segera laporkan ke kader/bidan untuk segera ditindaklanjuti";
		}else if($stunting == "Pendek"){
			$suggest = "Laporkan ke kader/bidan dan berikan anak makanan tinggi protein (ikan, daging) serta suplemen seng";
		}else if($stunting == "Normal"){
			$suggest = "Pantau tumbuh kembang anak setiap bulan dengan datang ke posyandu";
		}else if($stunting == "Tinggi"){
			$suggest = "Pantau tumbuh kembang anak setiap bulan dengan datang ke posyandu";
		}
		return $suggest;
	}


	public static function calculateAge($birthdate, $type = "MONTH"){
		$date1 = $birthdate;
		$date2 = date("Y-m-d");

		$ts1 = strtotime($date1);
		$ts2 = strtotime($date2);

		$year1 = date('Y', $ts1);
		$year2 = date('Y', $ts2);

		$month1 = date('m', $ts1);
		$month2 = date('m', $ts2);

		$diff = (($year2 - $year1) * 12) + ($month2 - $month1);
		return $diff;
	}

}
