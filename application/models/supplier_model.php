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
    
    function get_supplier($username){
        $this->db->where('username', $username);
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
        $suppliers = $this->db->where('username !=','')->where('id !=',$id)->get('company')->result();
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
    



}

?>