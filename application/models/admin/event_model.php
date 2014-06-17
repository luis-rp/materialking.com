<?php

class event_model extends Model {

    function event_model() {
        parent::Model();
    }

    function count_all() {
        return $this->db->count_all('event');
    }

    function get_items($limit = 10, $offset = 0) {
        if ($offset == 0) {
            $newoffset = 0;
        } else {
            $newoffset = $offset;
        }
        $sql = "SELECT * FROM " . $this->db->dbprefix('event') . "
        	WHERE purchasingadmin='".$this->session->userdata('purchasingadmin')."'
        	";

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
        $query = $this->db->count_all_results('event');
        return $query;
    }

    function getfields() {
        $sql = "SHOW FIELDS FROM " . $this->db->dbprefix('event');

        $query = $this->db->query($sql);
        if ($query->result()) {
            return $query->result();
        } else {
            return null;
        }
    }

    function add() 
    {
        $options = $this->input->post();
        $options['purchasingadmin'] = $this->session->userdata('purchasingadmin');
        if (isset($options['users'])) 
        {
            $users = $options['users'];
            unset($options['users']);
        }
        else
        {
            $users = array();
        }
        //print_r($options);die;
        $this->db->insert('event', $options);
        $eventid = $this->db->insert_id();
        
        $this->db->where('event', $eventid);
        $this->db->delete('eventuser');
        
        foreach ($users as $user) 
        {
            $this->db->insert('eventuser', array('user' => $user, 'event' => $eventid));
        }
        
        return $eventid;
    }

    function update() 
    {
        $options = $this->input->post();
        if (isset($options['users'])) 
        {
            $users = $options['users'];
            unset($options['users']);
        }
        else
        {
            $users = array();
        }

        $eventid = $this->input->post('id');
        //print_r($options);die;
        $this->db->where('id', $eventid);
        $this->db->update('event', $options);
        
        $this->db->where('event', $eventid);
        $this->db->delete('eventuser');
        
        foreach ($users as $user) 
        {
            $this->db->insert('eventuser', array('user' => $user, 'event' => $eventid));
        }
        
    }

    function remove($id) {
        $this->db->where('id', $id);
        $this->db->delete('event');
    }

    function get_item($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('event');
        if ($query->num_rows > 0) {
            $ret = $query->row();
            return $ret;
        }
        return NULL;
    }

}

?>