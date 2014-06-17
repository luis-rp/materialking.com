<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @description	Allows you to use templates in CodeIgniter
 * 				Written and tested in CI 1.7.0
 * 				Usage (in your controller):
 * 					$this->load->template('template_path/template_filename_without_php');
 * 						(just like you load a view)
 * 					then just do: 
 * 					$this->load->view('view_path/view_filename_without_php');
 * 					for your own conveniece I recommend loading the template in the constructor
 * 					of your controller
 * 
 * 				Limitations:
 * 				i) You can't load multiple views from a method in your controller unless
 * 					your template displays only the top part of a template.
 * 					In plain english, if you load 2 views in a single method, the second view 
 * 					will be loaded after the </body></html> part of your template.
 * 					To overcome this, load any extra views from within the first view so that
 * 					there is only one view loaded from the controller's method
 * 
 * @author		Vangelis Bibakis, bibakisv@gmail.com
 * @license		You are free to use the code in your projects, commercial or not.
 * 				You are free to modify and redistribute the code as long as you 
 * 					don't remove the author information and these lines about the license.
 * 				You are free to charge for installing or supporting this library.
 * 				You are not allowed to redistribute the code under a different license or 
 *	 				sell the code.
 * 				You are not allowed to remove the orginal author info even if you make 
 * 					changes or additions to the code.
 * @version		0.1
 * @date		26-dec-2008
**/


class My_Loader extends CI_Loader {

	var $template = '';
	var $data = array();
	var $return = FALSE;
	var $template_loaded;
	
	/**
	 * Allows the loading of templates. Normaly you want pages with the same layout across your site.
	 * If you decide to load a template, then any of the views you load afterwards will be placed inside
	 * the template's code in the position with the $content variable
	 * 
	 * @param $template		The filename of the template to use, in the style of loading a view
	 * @param $data			Any data you wish to pass to the template, in a data array just like the views
	 * @param $return		If you want to just get the template contents set to true
	 * @param $files		Any css/js files you want to be included in the template's <head> section
	 * 						example: 
	 * 						$files = array('js' => 'http://somesite.com/somefile.js', 'css' => 'www/css/styles.css');
	 * 						all local files' path is relative to base_url as defined in your application/config.php file
	 * @return unknown_type
	 */
	function template($template = '', $data = array(), $return = FALSE, $files = array()){
		if ($template == ''){
			return FALSE;
		}
		
		$this->template = $template;
		$this->data = $this->_ci_object_to_array($data);
		$this->return = $return;
		
		if (count($files) > 0){
			$includes = '';
			foreach ($files as $file_array){
				foreach ($file_array as $type => $file){
					if (!((substr($file,0,7) == 'http://') || (substr($file,0,7) == 'https:/'))){
						$file = base_url().$file;
					}
					
					if ($type == 'css'){
						$includes .= '<link rel="stylesheet" type="text/css" href="'.$file.'" media="screen" />';
					}
					elseif ($type == 'js'){
						$includes .= '<script type="text/javascript" src="'.$file.'"></script>';
					}
				}
			}
			$this->data['includes'] = $includes;
		}
	}
	

	/**
	 * Checks if a template has already been loaded and then either loads the view directly or puts it inside 
	 * the template and loads it with the view.
	 * 
	 * @see /system/libraries/CI_Loader#view($view, $vars, $return)
	 */
	function view($view, $vars = array(), $return = FALSE){
		// this part loads a view
		if (($this->template == '') || ($this->template_loaded == TRUE)){
			$this->template_loaded = TRUE;
			return $this->_ci_load(array(
				'_ci_view' => $view, 
				'_ci_vars' => $this->_ci_object_to_array($vars), 
				'_ci_return' => $return)
			);
		}
		// this part loads the template
		else {
			$this->template_loaded = TRUE;
			$data = $this->_ci_object_to_array($vars);
			
			// adds the template $data 
			if (count($this->data) > 0){
				foreach ($this->data as $key => $value){
					$data[$key] = $value;
				}
			}
			
			$data['content'] = $this->_ci_load(array(
				'_ci_view' => $view, 
				'_ci_vars' => $this->_ci_object_to_array($vars),
				'_ci_return' => TRUE)
			);
			
			return $this->_ci_load(array(
				'_ci_view' => $this->template, 
				'_ci_vars' => $data, 
				'_ci_return' => $return)
			);
		}
	}

	
}
