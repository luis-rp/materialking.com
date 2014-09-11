<?php
class event extends CI_Controller 
{
	private $limit = 10;
	private $pageid = 6;
	
	function event() 
	{
		parent::__construct ();
		$this->load->library('session');
	    if(!$this->session->userdata('id'))
		{
			redirect('admin/login/index', 'refresh'); 
		}
		if($this->session->userdata('userevent_id')==3)
		{
			redirect('admin/dashboard', 'refresh'); 
		}
		$this->load->dbforge();
		$this->load->library ('form_validation');
		$this->load->library ( array ('table', 'validation', 'session'));
		$this->load->helper ( 'form', 'url');
		$this->load->model('admin/quote_model');
		$this->load->model('admin/adminmodel');
		$this->load->model('admin/event_model');
		$this->load->model('admin/settings_model');
		$id = $this->session->userdata('id');
		$setting=$this->settings_model->getalldata($id);
		if(empty($setting)){
		$data['settingtour']=$setting;
		$data['timezone']='America/Los_Angeles';
		}else{
		$data['timezone']=$setting[0]->tour;
		$data['timezone']=$setting[0]->timezone;
		}
		$data['pendingbids'] = $this->quote_model->getpendingbids();
		$this->form_validation->set_error_delimiters ('<div class="red">', '</div>');
		$data ['title'] = "Administrator";
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/admin/template', $data);
	}
	
	function index($offset = 0) 
	{
		$data = array();
		
		$this->load->view ('admin/event/calendar', $data);
	}

    function jsonlist() 
    {
        $events = $this->event_model->get_items();
        $eventlist = array();
        $added_date = array();
        foreach ($events as $event) 
        {
            $obj = array();
            $obj['title'] = $event->title;
            $obj['start'] = $event->evtdate;
            $obj['end'] = $event->evtdate;
            if ($this->session->userdata('usertype_id') == 3)
            {
                $obj['url'] = site_url('admin/event/comments/'.$event->id);
                $checkauth = array('event' => $event->id, 'user' => $this->session->userdata('id'));
                $this->db->where($checkauth);
                $checkauth = $this->db->get('eventuser')->num_rows;
                if ($checkauth)
                    $eventlist[] = $obj;
            }
            else 
            {
                $obj['url'] = site_url('admin/event/comments/'.$event->id);
                $eventlist[] = $obj;
            }
        }
        //print_r($eventlist);
        //fwrite(fopen('test.txt',"w+"), print_r($eventlist,true));
        echo json_encode($eventlist);
    }

	function add()
	{
	    if ($this->session->userdata('usertype_id') == 3)
	        redirect('admin/event');
		$this->_set_fields ();
		$data ['heading'] = 'Add New event';
		$data ['message'] = '';
		$data ['action'] = site_url ('admin/event/additem');
		$users = $this->db->where('purchasingadmin',$this->session->userdata('purchasingadmin'))
		            ->where('usertype_id',3)
		            ->get('users')->result();  
		$data ['users'] = array(); 
        foreach ($users as $u) 
        {
            $u->checked = '';
            $data['users'][] = $u;
        }
		$this->load->view ('admin/event/form', $data);
	}
	
	function additem() 
	{
		$_POST['evtdate'] = date('Y-m-d', strtotime($_POST['evtdate']));
		
	    if ($this->session->userdata('usertype_id') == 3)
	        redirect('admin/event');
		$data ['heading'] = 'Add New event';
		$data ['action'] = site_url ('admin/event/additem');
		
		$this->_set_fields ();
		$this->_set_rules ();
		
		if ($this->validation->run () == FALSE) 
		{
		    $data ['users'] = $this->db->where('purchasingadmin',$this->session->userdata('purchasingadmin'))
		                    ->where('usertype_id',3)
		                    ->get('users')->result();
			$this->load->view ('admin/event/form', $data);
		} 
		else 
		{
			$itemid = $this->event_model->add ();
			$this->sendEventEmail($itemid);
			$this->session->set_flashdata('message', '<div class="alert alert-success fade in"><button event="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>event Added Successfully.</div>');
			redirect('admin/event'); 
		}
	}
	
	function update($id)
	{
		
	    if ($this->session->userdata('usertype_id') == 3)
	        redirect('admin/event');
		$this->_set_fields ();
		$item = $this->event_model->get_item($id);
		$columns = (array)$this->event_model->getfields();
		
		foreach($columns as $column)
		{
			$column = (array)$column;
			$this->validation->$column['Field'] = $item->$column['Field'];
		}
		//print_r($item);die;
		$data ['heading'] = 'Update event';
		$data ['message'] = '';
		$users = $this->db->where('purchasingadmin',$this->session->userdata('purchasingadmin'))
		            ->where('usertype_id',3)
		            ->get('users')->result();  
		$data ['users'] = array(); 
        foreach ($users as $u) 
        {
            $this->db->where(array('event' => $id, 'user' => $u->id));
            
            if ($this->db->get('eventuser')->num_rows > 0)
                $u->checked = 'checked="CHECKED"';
            else
                $u->checked = '';
            $data['users'][] = $u;
        }
		$data ['action'] = site_url ('admin/event/updateitem');
		$this->load->view ('admin/event/form', $data);
	}
	
	function updateitem()
	{
		$_POST['evtdate'] = date('Y-m-d', strtotime($_POST['evtdate']));
	    if ($this->session->userdata('usertype_id') == 3)
	        redirect('admin/event');
		$data ['heading'] = 'Update event';
		$data ['action'] = site_url ('admin/event/updateitem');
		$this->_set_fields ();
		$this->_set_rules ();
		
		if ($this->validation->run () == FALSE) 
		{
		    $data ['users'] = $this->db->where('purchasingadmin',$this->session->userdata('purchasingadmin'))
		                    ->where('usertype_id',3)
		                    ->get('users')->result();
		    $data ['action'] = site_url ('admin/event/updateitem');
			$this->load->view ('admin/event/form', $data);
		} 
		else 
		{
			$this->event_model->update ();
			$this->session->set_flashdata('message', '<div class="alert alert-success fade in"><button event="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>event has been Updated.</div>');
			redirect('admin/event'); 
		}
	}
	
	function delete($id) 
	{
	    if ($this->session->userdata('usertype_id') == 3)
	        redirect('admin/event');
		$this->event_model->remove($id);
		$this->session->set_flashdata('message', '<div class="alert alert-success fade in"><button event="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>Event Deleted.</div>');
		redirect ('admin/event', 'refresh');
	}
	
	function _set_fields() 
	{
		$columns = (array)$this->event_model->getfields();
		$fields = array();
		foreach($columns as $column)
		{
			$column = (array)$column;
			$fields [$column['Field']] = $column['Field'];
		}
		$fileds['labels'] = 'Lables';
		$fileds['events'] = 'Events';
		$this->validation->set_fields ($fields);
	}
	
	function _set_rules() 
	{
		$rules = array();
		$rules ['title'] = 'trim|required';
		$this->validation->set_rules ($rules);
		$this->validation->set_message ('required', '* Field Required');
		$this->validation->set_error_delimiters ( '<div class="frmerror">', '</div>');
	}
	
	function _createThumbnail($fileName, $foldername="", $width=170, $height=150)
	{
		ini_set("memory_limit","512M");
		ini_set("max_execution_time", 1500);
		ini_set("set_time_limit", 200);
		
		$config['image_library'] = 'gd2';
		$config['source_image'] = 'uploads/'.($foldername?$foldername.'/':'') . $fileName;
		$config['new_image'] = 'uploads/'.($foldername?$foldername.'/':'').'thumbs/' . $fileName;
		$config['create_thumb'] = FALSE;
		$config['maintain_ratio'] = TRUE;
		$image_config['x_axis'] = '0';
		$image_config['y_axis'] = '0';
		$config['width'] = $width;
		$config['height'] = $height;
	
		$this->load->library('image_lib', $config);
		if(!$this->image_lib->resize()) echo $this->image_lib->display_errors();
	}
	
	function sendEventEmail($id)
	{
	    $event = $this->db->where('id',$id)->get('event')->row();
	    if(!$event)
	        return false;
	    $emails = array();
	    $users = $this->db->select('users.email')
	             ->from('eventuser')
	             ->join('users','users.id=eventuser.user')
	             ->where('eventuser.event',$id)
	             ->get()
	             ->result();
	     foreach($users as $user)
	     {
	         $emails[]=$user->email;
	     }
	     if(!$emails)
	         return false;
	     $to = implode(',',$emails);
	     
            $body = "Dear User,<br><br>
		    		 
		  	You have been assigned to the event " . $event->title . " :  <br><br>	
		    Please find the details below:<br/><br/>
  	        date: ".$event->evtdate."<br/>
  	        starttime: ".$event->eventstart."<br/>
  	        end time: ".$event->eventend."<br/>
  	        location: ".$event->location."<br/>
  	        contact name: ".$event->contactname."<br/>
  	        contact phone: ".$event->contactphone."<br/>
  	        notes: ".$event->notes."<br/>
		    ";
            //$this->load->model('admin/settings_model');
            $settings = (array) $this->settings_model->get_current_settings();
            $this->load->library('email');

            $this->email->from($settings['adminemail'], "Administrator");

            $this->email->to($to);
            $this->email->subject('New Event assigned: ' . $event->title);
            $this->email->message($body);
            $this->email->set_mailtype("html");
            $this->email->send();
	}
	
	function comments($id)
	{
	    $event = $this->db->where('id',$id)->get('event')->row();
	    $comments = $this->db->select('eventcomment.*, users.fullname `from`')
	                ->from('eventcomment')
	                ->join('users','eventcomment.user = users.id')
	                ->where('event',$id)
	                ->order_by('commentdate','ASC')
	                ->get()
	                ->result();
	    
	    $data['event'] = $event;
	    $data['comments'] = $comments;
	    $this->load->view ('admin/event/comments', $data);
	}
	
	function sendcomment($id)
	{
	    $this->db->insert('eventcomment',$_POST);
	    $this->session->set_flashdata('message', '<div class="alert alert-success fade in"><button event="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>Comment posted.</div>');
	    
		// echo "<pre>",print_r($_POST); die;		
		$event = $this->db->where('id',$_POST['event'])->get('event')->row();
	    if(!$event)
	        return false;
	    $emails = array();
	    $users = $this->db->select('users.email')
	             ->from('eventuser')
	             ->join('users','users.id=eventuser.user')
	             ->where('eventuser.event',$_POST['event'])
	             ->get()
	             ->result();
	     foreach($users as $user)
	     {
	         $emails[]=$user->email;
	     }
	     if(!$emails)
	         return false;
	     $to = implode(',',$emails);
	     
	     $userdata = $this->db->select('companyname')
	     					 ->from('users')
		                     ->where('id',$_POST['user'])
		                    ->get()->result();
	     //echo "<pre>",print_r($userdata[0]->companyname); die;		
			     
            $body = "Dear User,<br><br>
		    		 
		  	You have got a new comment from company ".$userdata[0]->companyname." for event " . $event->title . " :  <br><br>	
		    Please find the details below:<br/><br/>
  	        ".$_POST['comment']."<br/><br/>
  	        Comment Posted on:".$_POST['commentdate']."<br/>
		    ";
            //$this->load->model('admin/settings_model');
            $settings = (array) $this->settings_model->get_current_settings();
            $this->load->library('email');

            $this->email->from($settings['adminemail'], "Administrator");

            $this->email->to($to);
            $this->email->subject('New Comment');
            $this->email->message($body);
            $this->email->set_mailtype("html");
            $this->email->send();
            //echo "<pre>",print_r($this->email); die;
            
            redirect ('admin/event/comments/'.$id, 'refresh');
		
	}
}
?>