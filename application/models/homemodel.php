<?php

class Homemodel extends Model {

    private $search;
    private $distance;
    private $keyword;

    function Homemodel() {
        parent::Model();
        $this->distance = '20';
    }

    public function set_keyword($keyword) {
        $this->keyword = $keyword;
    }

    private function get_distance_condition($search, $distance = false) {
        if($distance) 
        {
            $this->distance = $distance;
        }
        // My Condition
        if($this->input->post('radius')!=0)
        {
            $this->distance = $this->input->post('radius');
        }
        //
        $condition = " AND 1*(((acos(sin((" . $search->current_lat . "*pi()/180)) * 
            sin((c.`com_lat`*pi()/180))+cos((" . $search->current_lat . "*pi()/180)) * 
            cos((c.`com_lat`*pi()/180)) * cos(((" . $search->current_lon . "- c.`com_lng`)* 
            pi()/180))))*180/pi())*60*1.1515)<" . $this->distance;
        $this->set_type_condition($condition);
        return $condition;
    }
    
    private function get_distance_condition_contractor($search, $distance = false) {
        if ($distance) {
            $this->distance = $distance;
        }
        $condition = " AND 1*(((acos(sin((" . $search->current_lat . "*pi()/180)) * 
            sin((`user_lat`*pi()/180))+cos((" . $search->current_lat . "*pi()/180)) * 
            cos((`user_lat`*pi()/180)) * cos(((" . $search->current_lon . "- u.`user_lng`)* 
            pi()/180))))*180/pi())*60*1.1515)<" . $this->distance;
       $this->set_contractortype_condition($condition);
        return $condition;
    }

    public function set_distance($distance) {
        $this->distance = $distance;
    }

    public function set_search_criteria($search)
     {
        $this->search = $search;      
     }

    function getconfigurations() {
        $query = $this->db->get('settings');
        $result = $query->row();
        return $result;
    }

    function getSuppliers() {
        $distance = "1500";

        $search = $this->search;
       
        if ($search) 
        {
            $distance_condition = $this->get_distance_condition($search, $distance);
            $this->set_type_condition($where);
            
            $where .= " " . $distance_condition;
            
            $query = "SELECT c.*,"
                    . "(((acos(sin((" . $search->current_lat . "*pi()/180)) * 
                sin((c.`com_lat`*pi()/180))+cos((" . $search->current_lat . "*pi()/180)) * 
                cos((c.`com_lat`*pi()/180)) * cos(((" . $search->current_lon . "- c.`com_lng`)* 
                pi()/180))))*180/pi())*60*1.1515
            ) as distance "
                        . " FROM " . $this->db->dbprefix('company') . " c LEFT JOIN " . $this->db->dbprefix('companytype') . " ct
    		ON c.id=ct.companyid where 1=1 $where AND c.isdeleted = 0 AND c.address !=''   GROUP BY c.`id` ORDER BY distance ASC LIMIT 0,20";
        } else {
            $query = "SELECT c.* FROM " . $this->db->dbprefix('company') . " c LEFT JOIN " . $this->db->dbprefix('companytype') . " ct
			ON c.id=ct.companyid where 1=1 $where AND c.isdeleted = 0 AND c.address !='' GROUP BY c.id LIMIT 0,20";
        }
        //echo $query;
        $items = $this->db->query($query)->result();
        return $items;
    }

    public function get_featured_suppliers() {
        $search = $this->search;
        if ($search) {
            
            $sql = "SELECT c.*, COUNT(ai.id) awards,"
                    . "(((acos(sin((" . $search->current_lat . "*pi()/180)) * 
            sin((c.`com_lat`*pi()/180))+cos((" . $search->current_lat . "*pi()/180)) * 
            cos((c.`com_lat`*pi()/180)) * cos(((" . $search->current_lon . "- c.`com_lng`)* 
            pi()/180))))*180/pi())*60*1.1515
        ) as distance 
				FROM " . $this->db->dbprefix('company') . " c, " . $this->db->dbprefix('awarditem') . " ai
				WHERE ai.company=c.id  AND c.isdeleted = 0 GROUP BY c.`id` ORDER BY distance  ASC  LIMIT 0,3";
            //. " GROUP BY c.id HAVING c.id ORDER BY awards DESC";
        } else {
            $sql = "SELECT c.*, COUNT(ai.id) awards 
				FROM " . $this->db->dbprefix('company') . " c, " . $this->db->dbprefix('awarditem') . " ai
				WHERE ai.company=c.id  AND c.isdeleted = 0 GROUP BY c.id HAVING c.id ORDER BY awards DESC LIMIT 0,3";
        }
        $items = $this->db->query($sql)->result();
        return $items;
    }

    public function get_recent_suppliers() {
        $search = $this->search;
        if ($search) {
            $where = "GROUP BY c.`id` ORDER BY distance ASC  LIMIT 0,3";
            $sql = "SELECT c.*, "
                    . "(((acos(sin((" . $search->current_lat . "*pi()/180)) * 
            sin((c.`com_lat`*pi()/180))+cos((" . $search->current_lat . "*pi()/180)) * 
            cos((c.`com_lat`*pi()/180)) * cos(((" . $search->current_lon . "- c.`com_lng`)* 
            pi()/180))))*180/pi())*60*1.1515
        ) as distance 
        FROM " . $this->db->dbprefix('company') . " c, " . $this->db->dbprefix('awarditem') . " ai  WHERE c.isdeleted = 0 
				 ";
        } else {
            $sql = "SELECT c.* FROM " . $this->db->dbprefix('company') . " c  WHERE c.isdeleted = 0 ORDER BY regdate DESC LIMIT 0,3";
        }
        $items = $this->db->query($sql)->result();
        return $items;
    }

    public function get_nearest_suppliers($ignore_location = false) 
    {
    	if($this->router->method == 'index')
    	{
        	$limit = 10000;
        	        	
    	}
    	else
    	{
    		$limit = 6;
    	}
        $ignore_distance_sorting = false;
        $return = new stdClass();

        if (!isset($_POST['pagenum']))
            $_POST['pagenum'] = 0;
        $start = $_POST['pagenum'] * $limit;

        if (!@$_POST['orderby']) {
            $_POST['orderby'] = 'title';
        } else {
            $ignore_distance_sorting = true;
        }

        if (!@$_POST['orderdir']) {
            $_POST['orderdir'] = 'asc';
        }

        $where = array();
        if (@$_POST['citystates']) {
            $citystates = explode(', ', $_POST['citystates']);
            $where[] = " city='$citystates[0]'";
            $where[] = " state='$citystates[1]'";
        }
        $distance_condition = "";
        
        // Original Condition
        if ($search = $this->search) 
        {
            $distance_condition = $this->get_distance_condition($search);
        } 
       
       // My condition       
       //$search = $this->search;
        // End
       
        $sorting_distance = ($ignore_distance_sorting) ? "" : ("distance " . $_POST['orderdir']);
        
        if (!empty($where)) 
        {
        	if ($search = $this->search) {
                $where_2 = " AND (" . implode(' OR ', $where) . ") " . "  " . $distance_condition . " GROUP BY c.id";
            } else {
                //$where_2 .= " AND (" . implode(' OR ', $where) . ")  GROUP BY c.id ORDER BY " . $_POST['orderby'] . " " . $_POST['orderdir'] . " LIMIT $start, $limit";
            }
            
        }
         else 
        {
        	$where_2 = $distance_condition . " GROUP BY c.id  " . (($sorting_distance) ? ", " . $sorting_distance : "");
            
        }
        
      
        if ($this->input->post('get_by') && $this->input->post('get_by') == "in_network" && $this->session->userdata('site_loggedin')) 
        {
            $user_id = $this->session->userdata('site_loggedin')->id;
            $query = "SELECT c.*, ct.*,  "
                    . "(((acos(sin((" . $search->current_lat . "*pi()/180)) * 
                            sin((`com_lat`*pi()/180))+cos((" . $search->current_lat . "*pi()/180)) * 
                            cos((`com_lat`*pi()/180)) * cos(((" . $search->current_lon . "- `com_lng`)* 
                            pi()/180))))*180/pi())*60*1.1515
                        ) as distance "
                                    . " FROM " . $this->db->dbprefix('company') . " c LEFT JOIN " . $this->db->dbprefix('companytype') . " ct ON c.id=ct.companyid, " . $this->db->dbprefix('network') . " n
                            
                		WHERE
                            n.company=c.id AND c.isdeleted=0 AND n.status='Active' AND n.purchasingadmin='" . $user_id . "' AND 
                            c.id=ct.companyid AND address !='' $where_2" . " ORDER BY  "
                    . (($sorting_distance) ? $sorting_distance . ", " : "")
                    . ' distance ' . " " . $_POST['orderdir'] . "  LIMIT $start, $limit";
        } 
        else 
        {
            $query = "SELECT c.*, ct.*,  "
                    . "(((acos(sin((" . $search->current_lat . "*pi()/180)) * 
                        sin((`com_lat`*pi()/180))+cos((" . $search->current_lat . "*pi()/180)) * 
                        cos((`com_lat`*pi()/180)) * cos(((" . $search->current_lon . "- `com_lng`)* 
                        pi()/180))))*180/pi())*60*1.1515
                    ) as distance "
                                . " FROM " . $this->db->dbprefix('company') . " c LEFT JOIN " . $this->db->dbprefix('companytype') . " ct ON c.id=ct.companyid
            		WHERE 1=1  AND c.isdeleted=0 AND c.address !='' $where_2" . " ORDER BY  "
                    . (($sorting_distance) ? $sorting_distance . ", " : "")
                    . ' distance ' . " " . $_POST['orderdir'] . "  LIMIT $start, $limit";
            ;
        }
       
        
        $return->suppliers = $this->db->query($query)->result();
        // Original Code commented
        /*$totalquery = str_replace("LIMIT $start, $limit","",$query);
        $totalsuppliers = $this->db->query($totalquery)->result(); */
        
        // My Code
        $totalsuppliers = $this->db->query($query)->result();  
        // End      
        $return->totalresult = ($totalsuppliers) ? count($totalsuppliers) : 0;
        return $return;
    }

    private function set_type_condition(&$where) {
        $typem = $this->input->post('typem');
        $typei = $this->input->post('typei');
        $category_conditions = false;
        if ($typei) {
            $category_conditions[] = $typei;
        }
        if ($typem) {
            $category_conditions[] = $typem;
        }
        if ($category_conditions) {
            $term_condition = "ct.typeid IN (" . implode(",", $category_conditions) . ")";

            if (trim($where)) {
                $where .= " AND " . $term_condition;
            } else {
                $where .= " AND  " . $term_condition;
            }
        }
    }
    
    private function set_contractortype_condition(&$where) {
        $typec = $this->input->post('typec');
        $category_conditions = false;
       
        if ($typec) {
            $category_conditions[] = $typec;
        }
        if ($category_conditions) 
        {
            $term_condition = "cot.id IN (" . implode(",", $category_conditions) . ")";

            if (trim($where)) {
                $where .= " AND " . $term_condition;
            } else {
                $where .= " WHERE  " . $term_condition;
            }
        }
    }

    public function find_item() 
    {
        $limit = 10;
        $return = new stdClass();

        if (!isset($_POST['pagenum']))
            $_POST['pagenum'] = 0;
        $start = $_POST['pagenum'] * $limit;

        $where = "";
        if ($this->keyword) {
            $lookup = "  (`contact` like '%$this->keyword%' OR `address` like '%$this->keyword%' or `title` like '%$this->keyword%')";
            $where .= " AND  " . $lookup;
        }

        $this->set_type_condition($where);


        $query = "SELECT * FROM " . $this->db->dbprefix('company') . " WHERE  isdeleted=0 ". $where;

        $return->totalresult = $this->db->query($query)->num_rows();


        $search = $this->search;

        $query = "SELECT c.*,"
                . "(((acos(sin((" . $search->current_lat . "*pi()/180)) * 
            sin((`com_lat`*pi()/180))+cos((" . $search->current_lat . "*pi()/180)) * 
            cos((`com_lat`*pi()/180)) * cos(((" . $search->current_lon . "- `com_lng`)* 
            pi()/180))))*180/pi())*60*1.1515
        ) as distance   FROM " . $this->db->dbprefix('company') . " c LEFT JOIN " . $this->db->dbprefix('companytype') . " ct
            
		ON c.id=ct.companyid  $where  WHERE  isdeleted=0  GROUP BY c.id ORDER BY distance ASC  LIMIT $start, $limit";
        //echo $query;
        $return->suppliers = $this->db->query($query)->result();
        return $return;
    }
    
    public function FindContractor() 
    {
        $limit = 10;
        $return = new stdClass();
        if (!isset($_POST['pagenum']))
            $_POST['pagenum'] = 0;
        $start = $_POST['pagenum'] * $limit;
        $where = "";
        if ($this->keyword) {
            $lookup = "  (`fullname` like '%$this->keyword%' OR `address` like '%$this->keyword%' or `companyname` like '%$this->keyword%')";
            $where .= " AND  " . $lookup;
        }
        
        $this->set_contractortype_condition($where);
        
        $query = "SELECT u.* FROM " . $this->db->dbprefix('users') . " u  LEFT JOIN " . $this->db->dbprefix('contractcategory') . " 
        cot ON u.category=cot.id WHERE  u.isdeleted=0 AND u.profile=1 ". $where;
        $return->totalresult = $this->db->query($query)->num_rows();
        $search = $this->search;

        $query = "SELECT u.*,". "(((acos(sin((" . $search->current_lat . "*pi()/180)) * sin((`user_lat`*pi()/180))+cos((" . $search->current_lat . "*pi()/180)) * cos((`user_lat`*pi()/180)) * cos(((" . $search->current_lon . "- `user_lng`)* pi()/180))))*180/pi())*60*1.1515) as distance   FROM " . $this->db->dbprefix('users') . " u LEFT JOIN " . $this->db->dbprefix('contractcategory') . " 
        cot ON u.category=cot.id WHERE  u.isdeleted=0 AND u.profile=1 $where GROUP BY id ORDER BY distance ASC  LIMIT $start, $limit";
        $return->contractors = $this->db->query($query)->result();
        return $return;
    }
    
    public function get_nearest_contractors($ignore_location = false) 
    {
    	if($this->router->method == 'index')
        	$limit = 10000;
    	else
    		$limit = 6;
        $ignore_distance_sorting = false;
        $return = new stdClass();

        if (!isset($_POST['pagenum']))
            $_POST['pagenum'] = 0;
        $start = $_POST['pagenum'] * $limit;

        if (!@$_POST['orderby']) {
            $_POST['orderby'] = 'companyname';
        } else {
            $ignore_distance_sorting = true;
        }

        if (!@$_POST['orderdir']) {
            $_POST['orderdir'] = 'asc';
        }

        $where = array();
        if (@$_POST['citystates']) {
            $citystates = explode(', ', $_POST['citystates']);
            $where[] = " city='$citystates[0]'";
            $where[] = " state='$citystates[1]'";
        }
        $distance_condition = "";

        if ($search = $this->search) {
            $distance_condition = $this->get_distance_condition_contractor($search);
        }

        $sorting_distance = ($ignore_distance_sorting) ? "" : ("distance " . $_POST['orderdir']);

        if (!empty($where)) 
        {
            if ($search = $this->search) 
            {
                $where_2 = " AND (" . implode(' OR ', $where) . ") " . "  " . $distance_condition . " GROUP BY id";
            } 
           
        }
         else 
         {
            $where_2 = $distance_condition . " GROUP BY id  " . (($sorting_distance) ? ", " . $sorting_distance : "");
        }
       
        if ($this->input->post('get_by') && $this->session->userdata('site_loggedin')) 
        {
            $user_id = $this->session->userdata('site_loggedin')->id;
            $query = "SELECT u.* ". "(((acos(sin((" . $search->current_lat . "*pi()/180)) * 
                            sin((`user_lat`*pi()/180))+cos((" . $search->current_lat . "*pi()/180)) * 
                            cos((`user_lat`*pi()/180)) * cos(((" . $search->current_lon . "- u. `user_lng`)* 
                            pi()/180))))*180/pi())*60*1.1515
                        ) as distance " . " FROM " . $this->db->dbprefix('users') . " u
                        LEFT JOIN " . $this->db->dbprefix('contractcategory') . " 
        cot ON u.category=cot.id
                        ORDER BY  ". (($sorting_distance) ? $sorting_distance . ", " : "")
                    . ' distance ' . " " . $_POST['orderdir'] . "  LIMIT $start, $limit";
        } 
        else 
        {
            $query = "SELECT u.* ,". "(((acos(sin((" . $search->current_lat . "*pi()/180)) * sin((`user_lat`*pi()/180))+cos((" . $search->current_lat . "*pi()/180)) * cos((`user_lat`*pi()/180)) * cos(((" . $search->current_lon . "- `user_lng`)* pi()/180))))*180/pi())*60*1.1515) as distance "
                                . " FROM " . $this->db->dbprefix('users') . " u
                                
                                LEFT JOIN " . $this->db->dbprefix('contractcategory') . " 
        cot ON u.category=cot.id
                                WHERE 1=1  AND u.isdeleted=0 AND u.profile=1 AND u.status=1 AND u.address !='' $where_2" . " ORDER BY  "
                    . (($sorting_distance) ? $sorting_distance . ", " : "")
                    . ' distance ' . " " . $_POST['orderdir'] . "  LIMIT $start, $limit";
            ;
        }
        
        $return->contractors = $this->db->query($query)->result();      
        $totalquery = str_replace("LIMIT $start, $limit","",$query);      
        $totalcontractors = $this->db->query($totalquery)->result();       
        $return->totalresult = ($totalcontractors) ? count($totalcontractors) : 0;
        return $return;
    }

}

?>