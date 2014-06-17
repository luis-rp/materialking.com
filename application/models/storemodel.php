<?php

class Storemodel extends Model 
{

    function Storemodel() 
    {
        parent::Model();
    }
    
    public function get_items($company, $manufacturer) 
    {
        $limit = 10;
        $return = new stdClass();

        if (!isset($_POST['pagenum']))
            $_POST['pagenum'] = 0;
        $start = $_POST['pagenum'] * $limit;

        $where = array();
        $where []= "ci.company=c.id";
        $where []= "c.username='$company'";
        $where []= "type='Supplier'";
        $where []= "ci.instore='1'";
        if (@$_POST['category'])
        {
            $where []= "category='".$_POST['category']."'";
        }
        if ($manufacturer)
        {
            $where []= "ci.manufacturer='".$manufacturer."'";
        }
        
        if ($where)
            $where = " WHERE ci.itemid=i.id  AND " . implode(' AND ', $where) . " ";
        else
            $where = ' WHERE ci.itemid=i.id ';

        $query = "SELECT ci.*, i.url FROM " . $this->db->dbprefix('companyitem') .' ci, 
        							'.$this->db->dbprefix('item').' i, 
        							'.$this->db->dbprefix('company').' c '. $where;
        $return->totalresult = $this->db->query($query)->num_rows();
        $query = $query." LIMIT $start, $limit";
        //echo $query;//die;
        $return->items = $this->db->query($query)->result();
        return $return;
    }
}

?>