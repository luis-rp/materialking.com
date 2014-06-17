<?php
class statmodel extends Model
{
	function statmodel()
	{
		parent::Model();
	}
	
	function getProjects()
	{
		if($this->session->userdata('usertype_id')>1)
			$this->db->where('purchasingadmin',$this->session->userdata('purchasingadmin'));
		$query = $this->db->get('project');
		$ret = $query->result();
		return $ret;
	}
	
	function getfolderSize()
	{
		$SIZE_LIMIT = 5368709120;
		$disk_used = $this->foldersize("uploads");
		return $this->format_size($disk_used);
	}
	
	function foldersize($path) 
	{
		$total_size = 0;
		$files = scandir($path);
		$cleanPath = rtrim($path, '/'). '/';
	
		foreach($files as $t) 
		{
			if ($t<>"." && $t<>"..") 
			{
				$currentFile = $cleanPath . $t;
				if (is_dir($currentFile)) 
				{
					$size = $this->foldersize($currentFile);
					$total_size += $size;
				}
				else 
				{
					$size = filesize($currentFile);
					$total_size += $size;
				}
			}
		}
		return $total_size;
	}
	
	
	function format_size($size) 
	{
		$units = explode(' ', 'B KB MB GB TB PB');
		$mod = 1024;
		for ($i = 0; $size > $mod; $i++) {
			$size /= $mod;
		}
		$endIndex = strpos($size, ".")+3;
		return substr( $size, 0, $endIndex).' '.$units[$i];
	}
}
?>