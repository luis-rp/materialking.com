<?php

class company_model extends Model {

    function company_model() {
        parent::Model();
    }

    function get_companys($limit = 0, $offset = 0) {
        if ($offset == 0) {
            $newoffset = 0;
        } else {
            $newoffset = $offset;
        }

        $sql = "SELECT *
		FROM
		" . $this->db->dbprefix('company') . " ORDER BY title";

        $query = $this->db->query($sql);
        if ($query->result()) {
            $result = $query->result();
            $ret = array();
            foreach ($result as $item) {
                $item->poitems = $this->getpoitems($item->id);
                $ret[] = $item;
            }
            //print_r($ret);die;
            return $ret;
        } else {
            return null;
        }
    }

    // counting total companys
    function total_company() {
        $query = $this->db->count_all_results('company');
        return $query;
    }

    function getpoitems($id) {
        $sql = "SELECT a.id aid, q.id quote, q.ponum, sum(ai.totalprice) totalamount, a.awardedon
		FROM
		" . $this->db->dbprefix('awarditem') . " ai, " . $this->db->dbprefix('award') . " a,
		" . $this->db->dbprefix('quote') . " q
		WHERE a.quote=q.id AND ai.award=a.id AND a.quote=q.id AND ai.company='$id'
		GROUP BY q.ponum
		ORDER BY totalamount DESC
		";
        //echo $sql.'<br>';
        $query = $this->db->query($sql);
        if ($query->result()) {
            return $query->result();
        } else {
            return null;
        }
        /*
          $this->db->where('costcode',$costcode);
          $query = $this->db->get('quoteitem');
          if($query->num_rows>0)
          {
          $ret = $query->result();
          return $ret;
          }
          return NULL;
         */
    }

    function SaveCompany() 
    {
    	$completeaddress="";
    	if($_POST['street'])
    	{
    		$completeaddress.=$_POST['street'].",";
    	}
    	if($_POST['city'])
    	{
    		$completeaddress.=$_POST['city'].",";
    	}
    	if($_POST['state'])
    	{
    		$completeaddress.=$_POST['state'].",";
    	}
    	if($_POST['zip'])
    	{
    		$completeaddress.=$_POST['zip'];
    	}

    	$_POST['address'] = $completeaddress;
    	
        $address = ($this->input->post('address'));
        if($address)
            $geoloc = $this->getLatLong($address);
        $_POST['pwd1'] = $_POST['password'];
        $_POST['password'] = md5($_POST['password']);
        $_POST['pwd'] = $_POST['password'];   
            
        $options = $this->input->post();
        $address = ($this->input->post('address'));
        if($address)
        {
            $geoloc = $this->getLatLong($address);
            $options['com_lat'] = @$geoloc['lat'];
            $options['com_lng'] = @$geoloc['lng'];
        }
        if (isset($options['types']))
            unset($options['types']);
        //print_r($options);die;
        $this->db->insert('company', $options);
        $cid = $this->db->insert_id();
        if (isset($_POST['types']))
            foreach ($_POST['types'] as $type) {
                $insert = array();
                $insert['companyid'] = $cid;
                $insert['typeid'] = $type;
                $this->db->insert('companytype', $insert);
            }
        return $cid;
    }

    // updating pricing column
    function updateCompany($id) 
    {
        //
        //$address = ($this->input->post('address')) ? $this->input->post('address') : $this->input->post('contact');
        $completeaddress="";
        if($_POST['street'])
        {
        	$completeaddress.=$_POST['street'].",";
        }
        if($_POST['city'])
        {
        	$completeaddress.=$_POST['city'].",";
        }
        if($_POST['state'])
        {
        	$completeaddress.=$_POST['state'].",";
        }
        if($_POST['zip'])
        {
        	$completeaddress.=$_POST['zip'];
        }

        $_POST['address'] = $completeaddress;
        
        $options = $this->input->post();
        $address = ($this->input->post('address'));
        if($address)
        {
            $geoloc = $this->getLatLong($address);
            $options['com_lat'] = @$geoloc['lat'];
            $options['com_lng'] = @$geoloc['lng'];
        }
        
        if($options['password'])
        {
            $options['password'] = md5($options['password']);
            $options['pwd'] = $options['password'];
        }
        else
        {
            unset($options['password']);
            unset($options['pwd']);
        }
        //print_r($options);die;
        if (isset($options['types']))
            unset($options['types']);
        $cid = $this->input->post('id');
        $this->db->where('id', $cid);
        $this->db->update('company', $options);
        
        $this->db->where('companyid', $cid);
        $this->db->delete('companytype');
        if (isset($_POST['types']))
            foreach ($_POST['types'] as $type) {
                $insert = array();
                $insert['companyid'] = $cid;
                $insert['typeid'] = $type;
                $this->db->insert('companytype', $insert);
            }
    }

    // removing product
    function remove_company($id) {
        $this->db->where('id', $id);
        $this->db->delete('company');

        $this->db->where('companyid', $id);
        $this->db->delete('companytype');

        $this->db->where('company', $id);
        $this->db->delete('awarditem');

        $this->db->where('company', $id);
        $this->db->delete('bid');

        $query = "delete from pms_biditem where bid not in (select id from pms_bid)";
        $this->db->query($query);

        $this->db->where('company', $id);
        $this->db->delete('backtrack');

        $this->db->where('company', $id);
        $this->db->delete('companyemail');

        $this->db->where('company', $id);
        $this->db->delete('companyitem');

        $this->db->where('company', $id);
        $this->db->delete('invitation');

        $this->db->where('fromtype', 'company');
        $this->db->where('fromid', $id);
        $this->db->delete('joinrequest');

        $this->db->where('totype', 'company');
        $this->db->where('toid', $id);
        $this->db->delete('joinrequest');

        $this->db->where('company', $id);
        $this->db->delete('message');

        $this->db->where('company', $id);
        $this->db->delete('minprice');

        $this->db->where('company', $id);
        $this->db->delete('network');

        $this->db->where('company', $id);
        $this->db->delete('notification');

        $this->db->where('company', $id);
        $this->db->delete('orderdetails');

        $this->db->where('company', $id);
        $this->db->delete('purchasingtier');

        $this->db->where('company', $id);
        $this->db->delete('quoteitem');

        $this->db->where('company', $id);
        $this->db->delete('tierpricing');
    }

    // retrieve product by their id
    function get_companys_by_id($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('company');
        if ($query->num_rows > 0) {
            $ret = $query->row();
            return $ret;
        } else {
            return NULL;
        }
    }

    function getLatLong($address) {

        $fields['sensor'] = 'true';
        $fields['address'] = $address;

        $datas = array();
        $geo = get_geo_from_address($address);
        if (!$geo) {
            $this->load->library('curl');
            $this->curl = new Curl();
            $result = $this->curl->simple_get('http://maps.googleapis.com/maps/api/geocode/json', $fields);
            $jsondata = json_decode($result, true);
            if (is_array($jsondata) && $jsondata ['status'] == 'OK') {
                $lat = $jsondata['results'][0]['geometry']['location']['lat'];
                $lng = $jsondata['results'][0]['geometry']['location']['lng'];
                $datas['lat'] = $lat;
                $datas['lng'] = $lng;
            }
        } else {
            $datas['lat'] = $geo->lat;
            $datas['lng'] = $geo->long;
        }
        return $datas;
    }

}

?>