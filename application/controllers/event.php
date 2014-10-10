<?php
class event extends CI_Controller 
{
	private $limit = 10;
	private $pageid = 6;
	
	function event() 
	{
		parent::__construct ();
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		$data ['title'] = 'Dashboard';
		$this->load->dbforge();
		$this->load->library('form_validation');

        $this->load->library(array('table', 'validation', 'session'));
        $this->load->helper('form', 'url');
		$this->load->model ('homemodel', '', TRUE);
		$this->load->model ('messagemodel', '', TRUE);
		$this->load->model ('quotemodel', '', TRUE);
		$this->load->model ('companymodel', '', TRUE);
		$this->load->model('event_model');
		if($this->session->userdata('company')) $data['newquotes'] = $this->quotemodel->getnewinvitations($this->session->userdata('company')->id);
		$data['newnotifications'] = $this->messagemodel->getnewnotifications();
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/front/template', $data);
	}
	
	function index($offset = 0) 
	{
		$data = array();
		
		$this->load->view ('event/calendar', $data);
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
                $obj['url'] = site_url('event/comments/'.$event->id);
                $checkauth = array('event' => $event->id, 'user' => $this->session->userdata('id'));
                $this->db->where($checkauth);
                $checkauth = $this->db->get('eventuser')->num_rows;
                if ($checkauth)
                    $eventlist[] = $obj;
            }
            else 
            {
                $obj['url'] = site_url('event/comments/'.$event->id);
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
	        redirect('event');
		$this->_set_fields ();
		$data ['heading'] = 'Add New event';
		$data ['message'] = '';
		$data ['action'] = site_url ('event/additem');
		/*$users = $this->db->where('purchasingadmin',$this->session->userdata('purchasingadmin'))
		            ->where('usertype_id',3)
		            ->get('users')->result();  
		$data ['users'] = array(); 
        foreach ($users as $u) 
        {
            $u->checked = '';
            $data['users'][] = $u;
        }*/
		$this->load->view ('event/form', $data);
	}
	
	function additem() 
	{
		$_POST['evtdate'] = date('Y-m-d', strtotime($_POST['evtdate']));
		
	    if ($this->session->userdata('usertype_id') == 3)
	        redirect('event');
		$data ['heading'] = 'Add New event';
		$data ['action'] = site_url ('event/additem');
		
		$this->_set_fields ();
		$this->_set_rules ();
		
		if ($this->validation->run () == FALSE) 
		{
		    $data ['users'] = $this->db->where('purchasingadmin',$this->session->userdata('purchasingadmin'))
		                    ->where('usertype_id',3)
		                    ->get('users')->result();
			$this->load->view ('event/form', $data);
		} 
		else 
		{
			$itemid = $this->event_model->add ();
			$this->sendEventEmail($itemid);
			$this->session->set_flashdata('message', '<div class="alert alert-success fade in"><button event="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>event Added Successfully.</div>');
			redirect('event'); 
		}
	}
	
	function update($id)
	{
		
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
		/*$users = $this->db->where('purchasingadmin',$this->session->userdata('purchasingadmin'))
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
        }*/
		$data ['action'] = site_url ('event/updateitem');
		$this->load->view ('event/form', $data);
	}
	
	function updateitem()
	{
		$_POST['evtdate'] = date('Y-m-d', strtotime($_POST['evtdate']));
	   
		$data ['heading'] = 'Update event';
		$data ['action'] = site_url ('event/updateitem');
		$this->_set_fields ();
		$this->_set_rules ();
		
		if ($this->validation->run () == FALSE) 
		{
		    $data ['users'] = $this->db->where('purchasingadmin',$this->session->userdata('purchasingadmin'))
		                    ->where('usertype_id',3)
		                    ->get('users')->result();
		    $data ['action'] = site_url ('admin/event/updateitem');
			$this->load->view ('event/form', $data);
		} 
		else 
		{
			$this->event_model->update ();
			$this->session->set_flashdata('message', '<div class="alert alert-success fade in"><button event="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>event has been Updated.</div>');
			redirect('event'); 
		}
	}
	
	function delete($id) 
	{
	    if ($this->session->userdata('usertype_id') == 3)
	        redirect('event');
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
	    $event = $this->db->where('id',$id)->get('event_company')->row();
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
	     
            $data['email_body_title'] = "Dear User";
		    		 
		  	$data['email_body_content'] = "You have been assigned to the event " . $event->title . " :  <br><br>	
		    Please find the details below:<br/><br/>
  	        date: ".$event->evtdate."<br/>
  	        starttime: ".$event->eventstart."<br/>
  	        end time: ".$event->eventend."<br/>
  	        location: ".$event->location."<br/>
  	        contact name: ".$event->contactname."<br/>
  	        contact phone: ".$event->contactphone."<br/>
  	        notes: ".$event->notes."<br/>
		    ";
		  	$loaderEmail = new My_Loader();
            $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
            //$this->load->model('admin/settings_model');
            $settings = (array) $this->settings_model->get_current_settings();
            $this->load->library('email');

            $config['charset'] = 'utf-8';
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            $this->email->from($settings['adminemail'], "Administrator");

            $this->email->to($to);
            $this->email->subject('New Event assigned: ' . $event->title);
            $this->email->message($send_body);
            $this->email->set_mailtype("html");
            $this->email->send();
	}
	
	function comments($id)
	{
	    $event = $this->db->where('id',$id)->get('event_company')->row();
	    $comments = $this->db->select('eventcomment.*, users.fullname `from`')
	                ->from('eventcomment')
	                ->join('users','eventcomment.user = users.id')
	                ->where('event',$id)
	                ->order_by('commentdate','ASC')
	                ->get()
	                ->result();
	    
	    $data['event'] = $event;
	    $data['comments'] = $comments;
	    $this->load->view ('event/comments', $data);
	}
	
	function sendcomment($id)
	{
	    $this->db->insert('eventcomment',$_POST);
	    $this->session->set_flashdata('message', '<div class="alert alert-success fade in"><button event="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>Comment posted.</div>');
	    
		// echo "<pre>",print_r($_POST); die;		
		$event = $this->db->where('id',$_POST['event'])->get('event_company')->row();
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
			     
            $data['email_body_title'] = "Dear User";
		    		 
		  	$data['email_body_content'] = "You have got a new comment from company ".$userdata[0]->companyname." for event " . $event->title . " :  <br><br>	
		    Please find the details below:<br/><br/>
  	        ".$_POST['comment']."<br/><br/>
  	        Comment Posted on:".$_POST['commentdate']."<br/>
		    ";
		  	$loaderEmail = new My_Loader();
            $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
            //$this->load->model('admin/settings_model');
            $settings = (array) $this->settings_model->get_current_settings();
            $this->load->library('email');
            $config['charset'] = 'utf-8';
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            $this->email->from($settings['adminemail'], "Administrator");

            $this->email->to($to);
            $this->email->subject('New Comment');
            $this->email->message($send_body);
            $this->email->set_mailtype("html");
            $this->email->send();
            //echo "<pre>",print_r($this->email); die;
            
            redirect ('event/comments/'.$id, 'refresh');
		
	}
}
?>