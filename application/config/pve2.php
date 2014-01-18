<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* 
$config = array(
	'host'	=> 'localhost',
	'user'	=> 'root',
	'realm'	=> 'pve',
	'pass'	=> 'root',
);
 */

$config = array(
	'host'	=> $_SERVER['PVE_HOST'],
	'user'	=> $_SERVER['PVE_USER'],
	'realm'	=> $_SERVER['PVE_REALM'],
	'pass'	=> $_SERVER['PVE_PASS'],
);

$config['uris'] = array(
	'access' => (object)array(),
	'cluster' => (object)array(),
	'cluster/ha' => (object)array(),
	'cluster/ha/config' => (object)array(),
	'cluster/log' => (object)array(),
	'cluster/nextid' => (object)array(),
	'cluster/resources' => (object)array(),
	'cluster/status' => (object)array(),
	'cluster/tasks' => (object)array(),
	'nodes' => (object)array(),
	'nodes/[^/]+' => (object)array(),
	'nodes/[^/]+/aplinfo' => array(
		'storage' => array(
			'type' => 'select',
			'label' => 'Target Storage: ',
			'autoOptions' => 's.storage as s.storage for s in storageList',
		),
		'template' => array(
			'type' => 'text',
			'label' => 'Template to Download: ',
		),
	),
	'nodes/[^/]+/bootlog' => (object)array(),
	'nodes/[^/]+/netstat' => (object)array(),
	'nodes/[^/]+/openvz/[^/]+/initlog' => (object)array(),
	'nodes/[^/]+/openvz/[^/]+/rrd' => (object)array(),
	'nodes/[^/]+/openvz/[^/]+/rrddata' => (object)array(),
	'nodes/[^/]+/openvz/[^/]+/status' => (object)array(),
	'nodes/[^/]+/openvz/[^/]+/status/current' => (object)array(),
	'nodes/[^/]+/openvz/[^/]+/status/ubc' => (object)array(),
	'nodes/[^/]+/qemu/[^/]+/feature' => (object)array(),
	'nodes/[^/]+/qemu/[^/]+/rrd' => (object)array(),
	'nodes/[^/]+/qemu/[^/]+/rrddata' => (object)array(),
	'nodes/[^/]+/qemu/[^/]+/status' => (object)array(),
	'nodes/[^/]+/qemu/[^/]+/status/current' => (object)array(),
	'nodes/[^/]+/rrd' => (object)array(),
	'nodes/[^/]+/rrddata' => (object)array(),
	'nodes/[^/]+/scan' => (object)array(),
	'nodes/[^/]+/scan/iscsi' => (object)array(),
	'nodes/[^/]+/scan/lvm' => (object)array(),
	'nodes/[^/]+/scan/nfs' => (object)array(),
	'nodes/[^/]+/scan/usb' => (object)array(),
	'nodes/[^/]+/services' => (object)array(),
	'nodes/[^/]+/services/[^/]+' => (object)array(),
	'nodes/[^/]+/services/[^/]+/state' => (object)array(),
	'nodes/[^/]+/storage' => (object)array(),
	'nodes/[^/]+/storage/[^/]+' => (object)array(),
	'nodes/[^/]+/storage/[^/]+/rrd' => (object)array(),
	'nodes/[^/]+/storage/[^/]+/rrddata' => (object)array(),
	'nodes/[^/]+/storage/[^/]+/status' => (object)array(),
	'nodes/[^/]+/syslog' => (object)array(),
	'nodes/[^/]+/tasks' => (object)array(),
	'nodes/[^/]+/tasks/[^/]+/log' => (object)array(),
	'nodes/[^/]+/tasks/[^/]+/status' => (object)array(),
	'nodes/[^/]+/ubcfailcnt' => (object)array(),
	'nodes/[^/]+/version' => (object)array(),
	'version' => (object)array(),
);