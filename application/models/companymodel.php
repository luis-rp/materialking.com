<?php

class Companymodel extends Model {

    function Companymodel() {
        parent::Model();
    }

    function getconfigurations() {
        $query = $this->db->get('settings');
        $result = $query->result();
        return $result [0];
    }

    function getcompanybyid($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('company');
        if ($query->num_rows > 0)
            return $query->row();
        else
            return NULL;
    }

    function getStates() {
        $query = $this->db->get('state');
        if ($query->num_rows > 0)
            return $query->row();
        else
            return NULL;
    }

}

?>