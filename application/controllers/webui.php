<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WebUI extends CI_Controller
{
	public function index()
	{
		$this->config->load('pve2', TRUE);
		
		$names = explode(',', $this->config->item('name', 'pve2'));
		$hosts = explode(',', $this->config->item('host', 'pve2'));
		$users = explode(',', $this->config->item('user', 'pve2'));
		$realms = explode(',', $this->config->item('realm', 'pve2'));
		
		$accounts = array();
		foreach ($names as $idx => $name)
		{
			$accounts[] = array(		//	Temporarily hard coded into the config...
				'name' => $names[$idx],
				'host' => $hosts[$idx],
				'user' => $users[$idx],
				'realm' => $realms[$idx],
			);
		}
		
		$this->load->view('webui', array(
			'accounts' => $accounts,
		));
	}
	
}