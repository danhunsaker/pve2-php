<!DOCTYPE html>
<html data-ng-app="app">
  <head>
    <title>PVE2 PHP Client</title>
<!---->
	<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/bootswatch/latest/cyborg/bootstrap.min.css" />
<!---->
<!--- ->
	<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/bootstrap/latest/css/bootstrap.min.css" />
<!---->
	<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" />
	<link rel="stylesheet" href="/assets/css/tree-control.css" />
	<link rel="stylesheet" href="/assets/css/app.css" />
  </head>
  <body data-ng-controller="AppCtrl" data-ng-cloak="">
	<ul class="advanced">
		<li><a href="api">API Explorer</a></li>
	</ul>
	<section id="top">
		<nav>
			<treecontrol data-tree-model="navTree" data-options="navOptions" data-on-selection="navChange(node)">
				<span class="fa fa-fw type-icon" data-ng-class="$root.faType(node.type, node.status)"></span> {{node.display}}
			</treecontrol>
		</nav>
		<div class="item-detail">
			<div data-ng-if="nodeDisplay.type == 'navroot'">
				<h2><span class="fa fa-fw type-icon" data-ng-class="$root.faType(nodeDisplay.type, nodeDisplay.status)"></span> Datacenter Cluster List</h2>
				<table>
					<thead>
						<tr>
							<th>Cluster</th>
							<th>Host</th>
							<th>User</tH>
							<th>Realm</th>
						</tr>
					</thead>
					<tbody>
						<tr data-ng-repeat="cluster in nodeDisplay.children" data-ng-dblclick="navChange(cluster)">
							<td><span class="fa fa-fw type-icon" data-ng-class="$root.faType(cluster.type, cluster.status)"></span> {{ dataTree[cluster.ref].name }}</td>
							<td>{{ dataTree[cluster.ref].host }}</td>
							<td>{{ dataTree[cluster.ref].user }}</td>
							<td>{{ dataTree[cluster.ref].realm }}</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div data-ng-if="nodeDisplay.type == 'cluster'">
				<h2><span class="fa fa-fw type-icon" data-ng-class="$root.faType(nodeDisplay.type, nodeDisplay.status)"></span> {{ dataTree[nodeDisplay.ref].name }} ({{ dataTree[nodeDisplay.ref].host }} as {{ dataTree[nodeDisplay.ref].user }}@{{ dataTree[nodeDisplay.ref].realm }})</h2>
				<table>
					<thead>
						<tr>
							<th>Type</th>
							<th>Name</th>
							<th>Status</th>
							<th>Components</th>
						</tr>
					</thead>
					<tbody>
						<tr data-ng-repeat="node in nodeDisplay.children" data-ng-dblclick="navChange(node)">
							<td>{{ node.type }}</td>
							<td><span class="fa fa-fw type-icon" data-ng-class="$root.faType(node.type, node.status)"></span> {{ node.display }}</td>
							<td>{{ node.status }}<span data-ng-if="node.type == 'node' && node.status == 'up'"> for {{ dataTree[node.ref].uptime | uptime }}</span></td>
							<td>{{ node.children.length }}</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div data-ng-if="nodeDisplay.type == 'node'">
				<h2><span class="fa fa-fw type-icon" data-ng-class="$root.faType(nodeDisplay.type, nodeDisplay.status)"></span> {{ dataTree[nodeDisplay.ref].node }}</h2>
				<table>
					<thead>
						<tr>
							<th>Type</th>
							<th>Name</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>
						<tr data-ng-repeat="item in nodeDisplay.children" data-ng-dblclick="navChange(item)">
							<td>{{ item.type }}</td>
							<td><span class="fa fa-fw type-icon" data-ng-class="$root.faType(item.type, item.status)"></span> {{ item.display }}</td>
							<td>{{ item.status }}<span data-ng-if="['openvz', 'qemu'].indexOf(item.type) > -1 && item.status == 'up'"> for {{ dataTree[item.ref].uptime | uptime }}</span></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div data-ng-if="nodeDisplay.type == 'pool'">
				<h2><span class="fa fa-fw type-icon" data-ng-class="$root.faType(nodeDisplay.type, nodeDisplay.status)"></span> {{ dataTree[nodeDisplay.ref].poolid }}</h2>
				<p>{{ dataTree[nodeDisplay.ref].comment }}</p>
				<table>
					<thead>
						<tr>
							<th>Type</th>
							<th>Name</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>
						<tr data-ng-repeat="item in nodeDisplay.children" data-ng-dblclick="navChange(item)">
							<td>{{ item.type }}</td>
							<td><span class="fa fa-fw type-icon" data-ng-class="$root.faType(item.type, item.status)"></span> {{ item.display }}</td>
							<td>{{ item.status }}<span data-ng-if="['openvz', 'qemu'].indexOf(item.type) > -1 && item.status == 'up'"> for {{ dataTree[item.ref.replace(':pool', '')].uptime | uptime }}</span></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div data-ng-if="nodeDisplay.type == 'openvz'">
				<h2><span class="fa fa-fw type-icon" data-ng-class="$root.faType(nodeDisplay.type, nodeDisplay.status)"></span> {{ nodeDisplay.display }}</h2>
			</div>
			<div data-ng-if="nodeDisplay.type == 'qemu'">
				<h2><span class="fa fa-fw type-icon" data-ng-class="$root.faType(nodeDisplay.type, nodeDisplay.status)"></span> {{ nodeDisplay.display }}</h2>
			</div>
			<div data-ng-if="nodeDisplay.type == 'storage'">
				<h2><span class="fa fa-fw type-icon" data-ng-class="$root.faType(nodeDisplay.type, nodeDisplay.status)"></span> {{ nodeDisplay.display }}</h2>
			</div>
		</div>
	</section>
	<div data-ng-if="nodeDisplay.type">
		{{ nodeDisplay.type }}<br />
		{{ dataTree[nodeDisplay.ref] }}
		<ul>
			<li data-ng-repeat="child in nodeDisplay.children">
				{{ child.type }}<br />
				{{ dataTree[child.ref.replace(':pool', '')] }}
			</li>
		</ul>
	</div>
	<div data-ng-if="working">
		<div class="modal-backdrop in"></div>
		<div style="text-align: center; margin: 25% 0; background: none;" class="modal-backdrop">
			<span class="btn btn-info" style="width: initial;"><i class="fa fa-cog fa-spin"></i> Working...</span>
		</div>
	</div>
	<!-- Load scripts at the end for performance. -->
	<script>
		accounts = <?= json_encode($accounts); ?>;
	</script>
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.8/angular.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-bootstrap/0.10.0/ui-bootstrap-tpls.min.js"></script>
	<script src="/assets/js/angular-tree-control.js"></script>
	<script src="/assets/js/dynamic-forms.js"></script>
	<script src="/assets/js/local-storage-class.js"></script>
	<script src="/assets/js/webui.js"></script>
  </body>
</html>
