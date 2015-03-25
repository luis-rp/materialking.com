<?php

class supplier_model extends Model {

    function supplier_model() {
        parent::Model();
    }

    function update_supplier($id, $data) {
    	
        foreach($data as $key=>$value){
            $this->db->set($key, $value);
        }
        $this->db->where('id', $id);
        $this->db->update('company');
    }
    
    function update_contractor($id, $data) {
    	
        foreach($data as $key=>$value){
            $this->db->set($key, $value);
        }
        $this->db->where('id', $id);
        $this->db->update('users');
    }
    
    function get_supplier($username){
        $this->db->where('username', $username);
        $this->db->where('isdeleted', 0);
        $result = $this->db->get('company');
        if($result->num_rows()){
            $row = $result->row();
//            if(!trim($row->logo)){
//                $row->logo = "big.png";
//            }
            $row->joinstatus = '';
            return $row;
        }else{
            return false;
        }
    }
    
    function get_contractor($username){
        $this->db->where('username', $username);
        $this->db->where('isdeleted', 0);
        $result = $this->db->get('users');
        if($result->num_rows()){
            $row = $result->row();
            return $row;
        }else{
            return false;
        }
    }
    function get_supplierbyid($id){
    	$this->db->where('id', $id);
    	$result = $this->db->get('company');
    	if($result->num_rows()){
    		$row = $result->row();
    		//            if(!trim($row->logo)){
    		//                $row->logo = "big.png";
    		//            }
    	
    		return $row;
    	}else{
    		return false;
    	}
    }
    
    function getrelatedsupplier($id)
    {
        $related = array();
        $me = $this->db->where('id',$id)->get('company')->row();
        $mytypes = array();
        $types = $this->db->where('companyid',$id)->get('companytype')->result();
        foreach($types as $t)
        {
            $mytypes[]=$t->typeid;
        }
        if(!$me->com_lat || !$me->com_lng)
            return $related;
        $suppliers = $this->db->where('username !=','')->where('id !=',$id)->where('isdeleted =',0)->get('company')->result();
        foreach($suppliers as $supplier)
        {
            if(count($related) == 5)
                break;
            $lat1 = $me->com_lat; $lon1 = $me->com_lng;
            $lat2 = $supplier->com_lat; $lon2 = $supplier->com_lng;
            if(!$lon1 || !$lon2)
                continue;
            
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;            
        
            $ctypes = array();
            $types = $this->db->where('companyid',$supplier->id)->get('companytype')->result();
            foreach($types as $t)
            {
                $ctypes[]=$t->typeid;
            }
            //echo '<pre>';echo "MILES: $miles";echo "MY TYPES:";print_r($mytypes); echo $supplier->title." TYPES:";print_r($ctypes); echo "---------";
            $commontypes = array_intersect($mytypes, $ctypes);
            
            if($miles <= 10 && count($commontypes) >= (count($mytypes)/2) )
                $related[]=$supplier;
        }
        //echo "Matched companies:";print_r($related);
        return $related;
    }
    
    function getrelatedcontractor($id)
    {
        $related = array();
        $me = $this->db->where('id',$id)->get('users')->row();
        if(!$me->user_lat || !$me->user_lng)
        {
            return $related;
        }
        $contractors = $this->db->where('username !=','')->where('id !=',$id)->where('isdeleted =',0)->get('users')->result();
        foreach($contractors as $contractor)
        {
            if(count($related) == 5)
                break;
            $lat1 = $me->user_lat; 
            $lon1 = $me->user_lng;
            $lat2 = $contractor->user_lat; 
            $lon2 = $contractor->user_lng;
            if(!$lon1 || !$lon2)
                continue;
            
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;                      
            if($miles <= 10)
                $related[]=$contractor;
        }
        return $related;
    }
    

}

?>