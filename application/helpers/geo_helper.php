<?php
    function get_geo_from_address ($address)
    {
        /*
        $geocode = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=" . urlencode(str_replace("\n", ", ", $address)) . "&sensor=false");
        $output = json_decode($geocode);
        print_r($output);die;
        $return = new stdClass();
        $return->lat = $response_a->results[0]->geometry->location->lat;
        $return->long = $response_a->results[0]->geometry->location->lng;
        return $return;
        */
        $address = urlencode($address);
        $url = "http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response);
        if ($response_a && ! empty($response_a->results))
        {
            $return = new stdClass();
            $return->lat = $response_a->results[0]->geometry->location->lat;
            $return->long = $response_a->results[0]->geometry->location->lng;
            return $return;
        }
        else
        {
            return false;
        }
    }
    function get_my_address ()
    {
        $CI = &get_instance();
        if ($CI->session->userdata('navigator_lat') &&
         $CI->session->userdata('navigator_lng'))
        {
            $geo_coords[0] = $CI->session->userdata('navigator_lat');
            $geo_coords[1] = $CI->session->userdata('navigator_lng');
            $city_name = get_nearest_city($geo_coords[0], $geo_coords[1]);
            $details = new stdClass();
            $details->loc = $geo_coords[0] . "," . $geo_coords[1];
            $details->city = get_nearest_city($geo_coords[0], $geo_coords[1]);
            $details->country = get_nearest_city($geo_coords[0], $geo_coords[1], 
            $return = "country");
        }
        else
        {
            $ip = $_SERVER['REMOTE_ADDR'];
            if ($ip == "192.168.1.1")
            {
                $ip = '178.254.161.68';
            }
            if ($ip == "::1")
            {
                $ip = '178.254.161.68';
            }
            $details = json_decode(file_get_contents("http://ipinfo.io/" . $ip . "/json"));
            
            if(@$details->loc=="")
            $details->loc = "33.956419 , -118.442232";
        }
        return $details;
    }
    function get_my_location ($details)
    {
        $CI = &get_instance();
        if ($CI->session->userdata('navigator_lat') &&
         $CI->session->userdata('navigator_lng'))
        {
            $geo_coords[0] = $CI->session->userdata('navigator_lat');
            $geo_coords[1] = $CI->session->userdata('navigator_lng');
            $city_name = get_nearest_city($geo_coords[0], $geo_coords[1]);
            $my_location = $city_name;
        }
        else
        {
            $center = $details->loc;
            $geo_coords = explode(",", $center);
            if(isset($details->city)){
            	if (trim($details->city))
            	{
            		$my_location = $details->city . ", " . $details->country;
            	}
            }
            else
            {
                $city_name = get_nearest_city($geo_coords[0], $geo_coords[1]);
                $my_location = $city_name;
            }
        }
        return $my_location;
    }
    function get_nearest_city ($latitude, $longitude, $return = "city")
    {
    	$latitude = trim($latitude);
    	$longitude = trim($longitude);
        
    	$url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=".$latitude.",".$longitude."&sensor=false";
         
        $d = file_get_contents($url);
        $data = json_decode($d, true);
        $city = false;
        $contry_name = '';
        if (strtolower($data['status']) == "ok")
        {
            $info = $data['results']['0']['address_components'];
            if (is_array($info))
            {
                foreach ($info as $gps_item)
                {
                    if (in_array($gps_item['types'][0], 
                    array('postal_code', 'postal_code_prefix')))
                    {
                        $update['postal_code'] = $gps_item['long_name'];
                    }
                    if (in_array("country", $gps_item['types']))
                    {
                        $contry_name = $gps_item['long_name'];
                    }
                    if (in_array("administrative_area_level_2", $gps_item['types']))
                    {
                        $city = $gps_item['long_name'];
                    }
                    if (in_array("locality", $gps_item['types']))
                    {
                        $locality = $gps_item['long_name'];
                    }
                }
                if (! $city)
                {
                    $city = $locality;
                }
            }
        }
        if ($return == "country")
        {
            return $contry_name;
        }
        return $city;
    }
