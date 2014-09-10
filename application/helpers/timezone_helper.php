<?php function bd_time() {

	  $CI =& get_instance();
      //$CI->load->model('admin/settings_model');
      $settings = $CI->settings_model->get_current_settings();
	  $id = $CI->session->userdata('id');
	  $setting=$CI->settings_model->getalldata($id);
	  if(isset($setting[0]->timezone))
	  return $setting[0]->timezone;
	  else 
	  return "UTC";
} 
?>      