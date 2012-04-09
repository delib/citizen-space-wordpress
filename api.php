<?php

/****************************************************
 * API
 ****************************************************/ 
function citizenspace_api_get($url, $just_status_please=false) {
  $root = get_option('citizenspace_url', '');
  $ch = curl_init($root . '/api/1.0/' . $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, True);
  $result = curl_exec($ch);
  if($just_status_please) {
    return curl_getinfo($ch, CURLINFO_HTTP_CODE);
  }
  else {
    return $result;
  }
}

function citizenspace_api_is_valid_url($url) {
  $status = citizenspace_api_get('citizen_space_version', true);
  return(substr($status, 0, 1) == '2'); # success code, ie not a 404 or 5XX
}

function citizenspace_api_advanced_search_fields($query) {
  $ret = citizenspace_api_get('advanced_search_fields?'.$query);
  if(!$ret) return "Citizen Space not available.";
  return $ret;
}

function citizenspace_api_search_fields($query) {
  $ret = citizenspace_api_get('search_fields?'.$query);
  if(!$ret) return "Citizen Space not available.";
  return $ret;
}

function citizenspace_api_search_results($query, $offsite_consultations=false) {
  $ret = citizenspace_api_get('search_results?'.$query);
  if(!$ret) return "Citizen Space not available.";
  
  // use the original URLs
  if($offsite_consultations)
    return $ret;
  
  // rewrite the consultation URLs to use the wordpress page
	$dom = new DOMDocument;
  $dom->loadHTML($ret);
  $items = $dom->getElementsByTagName('a');

  for ($i = 0; $i < $items->length; $i++) {
      $href = $items->item($i)->getAttribute('href');
      $items->item($i)->setAttribute('href', get_bloginfo('url').'?cs_consultation&url='.$href);
  }
  return $dom->saveHTML();
}

function citizenspace_api_consultation_body($url) {
  $root = get_option('citizenspace_url', '');
  if(strpos($url, $root) !== 0) {
    return 'This page is not part of the Citizen Space instance.';
  }
  $path = str_replace($root, '', $url);
  $ret = citizenspace_api_get('consult_body?path='.$path);
  if(!$ret) return "Citizen Space not available.";
  return $ret;
}

function citizenspace_api_consultation_sidebar($url) {
  $root = get_option('citizenspace_url', '');
  if(strpos($url, $root) !== 0) {
    return 'This page is not part of the Citizen Space instance.';
  }
  $path = str_replace($root, '', $url);
  $ret = citizenspace_api_get('consult_sidebar?path='.$path);
  if(!$ret) return "Citizen Space not available.";
  return $ret;
}

?>
