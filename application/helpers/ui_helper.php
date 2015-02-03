<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function uri_tree($uris)
{
	if ( ! is_array($uris)) {
		return array();
	}
	
	return uri_tree_recurse(array_map(function ($uri) { return explode('/', $uri); }, $uris));
}

function uri_tree_recurse($uris, $tree = array())
{
	if (is_array($uris[0]))
	{
		foreach ($uris as $uri)
		{
			if (count($uri) > 1)
			{
				$tree[$uri[0]] = uri_tree_recurse(array_slice($uri, 1), isset($tree[$uri[0]]) ? $tree[$uri[0]] : array());
			}
			else
			{
				$tree[$uri[0]] = array();
			}
		}
		return $tree;
	}
	else
	{
		if (count($uris) > 1)
		{
			$tree[$uris[0]] = uri_tree_recurse(array_slice($uris, 1), isset($tree[$uris[0]]) ? $tree[$uris[0]] : array());
		}
		else
		{
			$tree[$uris[0]] = array();
		}
		return $tree;
	}
}