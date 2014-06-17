<?php

class type_model extends Model {

    function type_model() {
        parent::Model();
    }

    function count_all() {
        return $this->db->count_all('type');
    }

    function get_items($limit = 10, $offset = 0) {
        if ($offset == 0) {
            $newoffset = 0;
        } else {
            $newoffset = $offset;
        }
        $sql = "SELECT * FROM " . $this->db->dbprefix('type') . "  ORDER BY title ASC LIMIT $newoffset, $limit";

        $query = $this->db->query($sql);
        if ($query->result()) {
            $result = $query->result();
            $ret = array();
            foreach ($result as $item) {
                $ret[] = $item;
            }
            //print_r($ret);die;
            return $ret;
        } else {
            return null;
        }
    }

    // counting total items
    function total_item() {
        $query = $this->db->count_all_results('type');
        return $query;
    }

    function getfields() {
        $sql = "SHOW FIELDS FROM " . $this->db->dbprefix('type');

        $query = $this->db->query($sql);
        if ($query->result()) {
            return $query->result();
        } else {
            return null;
        }
    }

    function add() {
        $options = $this->input->post();
        //print_r($options);die;
        $this->db->insert('type', $options);
        $pid = $this->db->insert_id();
        return $pid;
    }

    function update() {
        $options = $this->input->post();

        $typeid = $this->input->post('id');
        //print_r($options);die;
        $this->db->where('id', $typeid);
        $this->db->update('type', $options);
    }

    function remove($id) {
        $this->db->where('id', $id);
        $this->db->delete('type');
    }

    function get_item($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('type');
        if ($query->num_rows > 0) {
            $ret = $query->row();
            return $ret;
        }
        return NULL;
    }

}

?>