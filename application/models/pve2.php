<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PVE2 extends CI_Model
{
	protected $_pve2 = null;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->config->load('pve2', TRUE);
		require_once(APPPATH . 'third_party/pve2_api.class.php');
		
		$this->_pve2 = new PVE2_API($this->config->item('host', 'pve2'), $this->config->item('user', 'pve2'), $this->config->item('realm', 'pve2'), $this->config->item('pass', 'pve2'));
		$this->_pve2->login();
	}
	
	public function get($uri)
	{
		return $this->_pve2->get($uri);
	}
	
	public function post($uri, $data)
	{
		return $this->_pve2->post($uri, $data);
	}
	
	public function put($uri, $data)
	{
		return $this->_pve2->put($uri, $data);
	}
	
	public function delete($uri)
	{
		return $this->_pve2->delete($uri);
	}
	
}