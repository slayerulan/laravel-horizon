<?php
/**
 *  This is a common helper page, auto loaded from composer.json.
 *  This will contain several helper functions
 */


/**
 * this will print an array
 *
 * @author Anirban Saha
 * @param  array $array array that need to be print
 *
 * @return void        print array
 */
function p($array){
	echo '<pre>';
	print_r($array);
	echo '</pre>';
	die;
}
/**
 * this will print an array
 *
 * @author Anirban Saha
 * @param  array $array array that need to be print
 *
 * @return void        print array
 */
function a($array){
	echo '<pre>';
	print_r($array);
	echo '</pre>';
	die;
}
/**
 * this will return a label from a code like string
 * eg : user_name => User Name
 *
 * @author Anirban Saha
 *
 * @return string
 */
function labelCase($string){
	return $string = ucfirst(str_ireplace(['_','-'],' ',$string));
}
/**
 * to prevent sqlinjection and script scriptinjection
 *
 * @author Anirban Saha
 * @param  string $data need to be cleand
 *
 * @return string       sanitized data
 */
function cleanData($data)
{
	return addslashes(e($data));
}
/**
 * Url from where data should fetch
 *
 * @author Anirban Saha
 * @param  string $url feed url
 *
 * @return ArrayObject array from xml
 */
function getObjectFromXMl($url){
	$xml	=	callCURL($url);
	$obj 	= 	simplexml_load_string($xml, null, LIBXML_NOCDATA);
	return $obj;
}
/**
 * Url from where data should fetch
 *
 * @author Anirban Saha
 * @param  string $url feed url
 *
 *  @return ArrayObject array from xml
 */

function getObjectFromJSON($url)
{
	$json	=	callCURL($url);
	$obj 	= 	json_decode($json);
	return $obj;
}
/**
 * call cUrl
 *
 * @author Anirban Saha
 * @param  string $url calling url
 *
 * @return mixed      xml/json string
 */
function callCURL($url)
{
	$curl = curl_init($url);
	curl_setopt_array($curl, array(
		CURLOPT_ENCODING => 'gzip', // specify that we accept all supported encoding types
		CURLOPT_RETURNTRANSFER => true
	));
	$data = curl_exec($curl);
	curl_close($curl);
	if ($data === false) {
		die('Can\'t get data');
	}
	return $data;
}

/**
 * call the API of host with necessary data
 * @author Arijit Jana
 * @param  string 	$url        	api url of host
 * @param  array 	$data_array 	necessary data that need to be sent
 * @return array           necessary responce for the api
 */
function hostApiCall($url, $data_array) {
	$json	=	postCurlWithData($url, $data_array);
	$obj 	= 	json_decode($json);
	return $obj;
}

/**
 * call a api with curl
 * @author Arijit Jana
 * @param  string 	$url       		api url
 * @param  array 	$curl_data 		data need to pass with api
 * @return json          response of the api
 */
function postCurlWithData($url, $curl_data) {
	$curl = curl_init($url);
	curl_setopt_array($curl, array(
		CURLOPT_ENCODING 		=> 'gzip', // specify that we accept all supported encoding types
		CURLOPT_RETURNTRANSFER 	=> true,
		CURLOPT_POSTFIELDS     	=> $curl_data,
		CURLOPT_HTTPHEADER 		=> array('Content-Type: application/json; charset=utf-8')
	));
	$response = curl_exec($curl);
	curl_close($curl);
	if ($response === false) {
		die("Can\'t get data");
	}
	return $response;
}

/**
 *  this will return small loader. you need to show and hide by id
 *
 *  @author Anirban Saha
 * 	@param  string 	$display block|none. It is value of display style. default is none
 * 	@param  string 	$id      id of current loader. By default small_loader
 *
 *   @return  string  html element of loader
 */
function smallLoader($display = null, $id = 'small_loader')
{
	$class = $display ? "show" : "";
	return '<div id="'.$id.'" class="loder_cover '.$class.'"><img src="'.asset('frontend/images/loader.gif').'" ></div>';
}

/**
 * calculates the odds value according to the selected odds display type
 *
 * @param      integer         $odds_value  	The actual odds value in decimal.
 *
 * @return     integer|string  		Odds value to display.
 */
function oddsValue($odds_value)
{
	$odds_type = Session::get('odds_value_type');
	$oddsValue = $odds_value;
	if ($odds_type == 'American') {
		if ($odds_value >= 2) {
			$oddsValue = '+'.(($odds_value-1)*100);
		}
		else{
			if ($odds_value == 1) {
				$oddsValue = '+000';
			}
			else{
				$oddsValue = '-'.(ceil(100/($odds_value-1)));
			}
		}
	}
	return $oddsValue;
}

/**
 *  this will return market extra value by filtering
 *
 *  @param  string  $market_group        AH|OU|1x2
 *  @param  string  $odds_name           1|2|over|under
 *  @param  string  $market_extra_value  -2.5|+1.5
 *  @param  string  $market_name         Asian Handicap| Over Under(1st half) nullable
 *
 *  @return  string  filtered valur
 */
function getMarketExtraValue($market_group, $odds_name, $market_extra_value, $market_name = null) {
	return $market_group == 'AH'  && $odds_name == '2' ? (-1 * $market_extra_value) : $market_extra_value;
}

/**
 *  this will return bet rule of current user
 *
 *  @return  array  rule object
 */
function getBetRule()
{
	if(session('user_details')) {
		//logged in user
		return session('user_details.bet_rule')[0];
	} else {
		//not loggedin default rule
		$bet_rules	= \App\BetRule::where('is_default', 'yes')->first();
		return json_decode($bet_rules->rule);
	}
}

function checkParentIds($id, $data = array())
{
    $parent_query = \App\User::where('id', $id)->get();
    if ($parent_query[0]->agent_id > 0) {
        $data[] = $parent_query[0]->agent_id;
        $parent_result = checkParentIds($parent_query[0]->agent_id, $data);
    } else {
        $parent_result = $data;
				$parent_result = \App\User::whereIn('id', $parent_result)->get();
    }
    return $parent_result;
}
/**
 * parses score according to sport
 *
 * @author Atul Verma
 * @param  string score,string sport name
 *
 * @return parsed array
 */
function SCOREPARSER($score,$sportName)
{
	$score_pasred = array();
	if($sportName == "Football"){
		if( $score == null || $score == ""){
			$score_pasred[] = "[?-?]";
		}else{
			if($score != "Not started"){
				$score_part = explode(" ",$score);

				if(count($score_part) == 1){
					$score_pasred[] = $score_part[0];
				}else if(count($score_part) > 1){
					foreach ($score_part as $key => $value) {
						if (strpos($value, 'E') !== false){
							//$score_pasred[] = str_replace(array( '(', ')',".",":","E" ), array( '[', ']',"","-","Extra Time: " ), $value);
						}else if(strpos($value, 'P') !== false){
							//$score_pasred[] = str_replace(array( '(', ')',".",":","P" ), array( '[', ']',"","-","Penalty: " ), $value);
						}else{
							if($key == 0)
								$score_pasred[] = "Ft - ".$value;
							else if($key == 1)
								$score_pasred[] = "Ht - ".$value;
							else
								$score_pasred[] = $value;
						}
					}
				}else{
					$score_pasred[] = $score;
				}
			}else{
				$score_pasred[] = $score;
			}
		}

	}
	else if($sportName == "Ice Hockey" || $sportName == "Basketball" || $sportName == "Handball"){
		if( $score == null || $score == ""){
			$score_pasred[] = "[?-?]";
		}else{
			if($score != "Not started"){
				$score_part = explode(" ",$score);

				if(count($score_part) == 1){
					$score_pasred[] = $score_part[0];
				}else if(count($score_part) > 1){
					foreach ($score_part as $key => $value) {
						if (strpos($value, 'E') !== false){
							//$score_pasred[] = str_replace(array( '(', ')',".",":","E" ), array( '[', ']',"","-","Extra Time: " ), $value);
						}else if(strpos($value, 'P') !== false){
							//$score_pasred[] = str_replace(array( '(', ')',".",":","P" ), array( '[', ']',"","-","Penalty: " ), $value);
						}else{
							if($key == 0){
								$score_pasred[] = "Score: [". $value ."]";
							}
							else if($key == 1){
								$separate_arr = explode(",",$value);
								if(count($separate_arr) == 1)
									$score_pasred[] = $separate_arr[0];
								else if(count($separate_arr) > 1){
									foreach($separate_arr as $index => $val){
										$score_pasred[] = "quater ".($index+1).": [".str_replace(array( '(', ')' ),"",$val)."]";
									}
								}else{
									$score_pasred[] = $value;
								}
							}
							else{
								$score_pasred[] = $value;
							}
						}
					}
				}else{
					$score_pasred[] = $score;
				}
			}else{
				$score_pasred[] = $score;
			}
		}

	}else if($sportName == "Volleyball"){
		if( $score == null || $score == ""){
			$score_pasred[] = "[?-?]";
		}else{
				$score_part = explode(",",$score);

				if(count($score_part) == 1){
					$score_pasred[] = $score_part[0];
				}else if(count($score_part) > 1){
					foreach ($score_part as $key => $value) {
						$score_pasred[] = "set ".($key+1).": [".$value."]";
					}
				}else{
					$score_pasred[] = $score;
				}

		}
	}else if($sportName == "Tennis"){
		if( $score == null || $score == ""){
			$score_pasred[] = "[?-?]";
		}else{
			if($score == "Not started" || $score == "Walkover Player2" || $score == "Walkover Player1"){
				$score_pasred[] = $score;
			}else{
				$without_space = explode(" ",$score);
				foreach($without_space as $key => $value){
					$without_comma = explode(",",$value);
					if(count($without_comma) != 1){
						foreach($without_comma as $index => $val){
							if($val != ""){
								//preg_match('#\((.*?)\)#', $val, $match);
								preg_match_all('/\(([A-Za-z0-9 ]+?)\)/', $val, $match);
								$tie_score = "";
								foreach($match[1] as $out){
									$tie_score .= $out."-";
								}
								if($tie_score != ""){
									$tie_score = "[".rtrim($tie_score,"-")."]";
								}
								$score_pasred[] = "set ".($index+1)."- " .preg_replace("/\([^)]+\)/","",$val)." ".$tie_score;
							}
						}
					}else{
						$score_pasred[] = $without_comma[0];
					}
				}
			}
		}
	}else{
		$score_pasred[] = $score;
	}

	return $score_pasred;

}
