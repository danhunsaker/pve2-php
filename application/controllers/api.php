<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class API extends CI_Controller
{
	protected $payload = null;
	
	public function index()
	{
		$this->config->load('pve2', TRUE);
		$uris = $this->config->item('uris', 'pve2');
		
		if (isset($this->payload['uri']))
		{
			$ret = array();
			$uri = trim($this->payload['uri'], "/ \t\n\r\0\x0b");
			if (isset($uris[$uri]))
			{
				$ret = $uris[$uri];
			}
			else
			{
				foreach ($uris as $pat => $form)
				{
					$pat = preg_replace('/:[^\/:]+:/', '[^/]+', $pat);
					if (preg_match("#^{$pat}$#", $uri) === 1)
					{
						$ret = $form;
						break;
					}
				}
			}
			
			$this->output->set_header('Content-Type: application/json');
			$this->output->set_output(json_encode($ret));
		}
		else
		{
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
			
			$this->load->view('api_explorer', array(
				'uris' => array_keys($uris),
				'accounts' => $accounts,
			));
		}
	}
	
	public function _remap($method, $params = array())
	{
		$this->payload = json_decode(file_get_contents('php://input'), true);
		
		if ($method === 'ui' || $method === 'index')
		{
			return call_user_func_array(array($this, 'index'), $params);
		}
		
		if (isset($this->payload['host']) && isset($this->payload['uri']))
		{
			$this->load->model('pve2');
			if (method_exists($this->pve2, $method))
			{
				if (isset($this->payload['data']))
				{
					if (count($this->payload['data']) == 1 && isset($this->payload['data']['json']))
					{
						array_unshift($params, json_decode($this->payload['data']['json'], true));
					}
					else
					{
						array_unshift($params, $this->payload['data']);
					}
				}
				array_unshift($params, $this->payload['uri']);
				array_unshift($params, $this->payload['host']);
				$ret = call_user_func_array(array($this->pve2, $method), $params);
			}
			else
			{
				$ret = "Bad method...  ({$method})";
			}
		}
		else
		{
			$ret = "Need a host and URI...  (/api/{$method})";
		}
		
		$this->output->set_header('Content-Type: application/json');
		$this->output->set_output(json_encode($ret));
	}
	
}