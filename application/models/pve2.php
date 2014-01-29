<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PVE2 extends CI_Model
{
	protected $_pve2 = array();
	
	public function __construct()
	{
		parent::__construct();
		
		$this->config->load('pve2', TRUE);
		require_once(APPPATH . 'third_party/pve2_api.class.php');
		
		$hosts = explode(',', $this->config->item('host', 'pve2'));
		$users = explode(',', $this->config->item('user', 'pve2'));
		$realms = explode(',', $this->config->item('realm', 'pve2'));
		$passes = explode(',', $this->config->item('pass', 'pve2'));
		
		foreach ($hosts as $idx => $host)
		{
			$this->_pve2[$host] = new PVE2_API($hosts[$idx], $users[$idx], $realms[$idx], $passes[$idx]);
			$this->_pve2[$host]->login();
		}
	}
	
	public function get($host, $uri)
	{
		return $this->_pve2[$host]->get($uri);
	}
	
	public function post($host, $uri, $data)
	{
		return $this->_pve2[$host]->post($uri, $data);
	}
	
	public function put($host, $uri, $data)
	{
		return $this->_pve2[$host]->put($uri, $data);
	}
	
	public function delete($host, $uri)
	{
		return $this->_pve2[$host]->delete($uri);
	}
	
}