<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class API extends CI_Controller
{
	protected $payload = null;
	
	public function ui()
	{
		if ( ! isset($this->payload['uri']))
		{
			$this->load->view('angular');
		}
		else
		{
			$ret = array();
			$uri = trim($this->payload['uri'], '/');
			$this->config->load('pve2', TRUE);
			$uris = $this->config->item('uris', 'pve2');
			if (isset($uris[$uri]))
			{
				$ret = $uris[$uri];
			}
			else
			{
				foreach ($uris as $pat => $form)
				{
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
	}
	
	public function _remap($method, $params = array())
	{
		$this->payload = json_decode(file_get_contents('php://input'), true);
		
		if ($method === 'ui' || $method === 'index')
		{
			return call_user_func_array(array($this, 'ui'), $params);
		}
		
		if (isset($this->payload['uri']))
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
				$ret = call_user_func_array(array($this->pve2, $method), $params);
			}
			else
			{
				$ret = "Bad method...  ({$method})";
			}
		}
		else
		{
			$ret = "Need a URI...  (/api/{$method})";
		}
		
		$this->output->set_header('Content-Type: application/json');
		$this->output->set_output(json_encode($ret));
	}
	
}