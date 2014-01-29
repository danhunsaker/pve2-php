<!DOCTYPE html>
<html data-ng-app="app">
  <head>
    <title>PVE2 API Explorer</title>
<!---->
	<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/bootswatch/latest/cyborg/bootstrap.min.css" />
<!---->
<!--- ->
	<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/bootstrap/latest/css/bootstrap.min.css" />
<!---->
	<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" />
	<link rel="stylesheet" href="/assets/css/app.css" />
  </head>
  <body data-ng-controller="AppCtrl" data-ng-cloak="">
    <p>Exploratory interface, for now.</p>
	<form data-ng-submit="send()">
		<legend>PVE2 API Explorer</legend>
		<label>Host:
			<select data-ng-model="host" data-ng-options="a.host as a.name+' ('+a.host+' as '+a.user+'@'+a.realm+')' for a in accounts" class="form-control">
			</select>
		</label>
		<label>HTTP Method: 
			<select data-ng-model="method" class="form-control">
				<option value="get">GET</option>
				<option value="post">POST</option>
				<option value="put">PUT</option>
				<option value="delete">DELETE</option>
			</select>
		</label>
		<label>URI: 
			<input type="text" class="form-control" data-ng-model="uri" autofocus="autofocus" data-typeahead="uri for uri in uriTypeahead($viewValue)" data-typeahead-loading="typeaheadWorking" data-typeahead-wait-ms="750" data-typeahead-on-select="getTemplate()" />
		</label>
		<dynamic-form template="dataTemplate" data-ng-model="data"></dynamic-form>
		<button class="btn btn-primary">Send</button>
	</form>
	<pre>Response: {{response | pretty}}</pre>
	<div data-ng-show="working || typeaheadWorking">
		<div class="modal-backdrop in"></div>
		<div style="text-align: center; margin: 25% 0; background: none;" class="modal-backdrop">
			<span class="btn btn-info" style="width: initial;"><i class="fa fa-cog fa-spin"></i> Working...</span>
		</div>
	</div>
	<!-- Load scripts at the end for performance. -->
	<script>
		accounts = <?= json_encode($accounts); ?>;
		knownURIs = <?= json_encode($uris); ?>;
	</script>
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.8/angular.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-bootstrap/0.10.0/ui-bootstrap-tpls.min.js"></script>
	<script src="/assets/js/dynamic-forms.js"></script>
	<script src="/assets/js/local-storage-class.js"></script>
	<script src="/assets/js/apix.js"></script>
  </body>
</html>
