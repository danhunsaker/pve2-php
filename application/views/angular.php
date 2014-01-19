<!DOCTYPE html>
<html data-ng-app="app">
  <head>
    <title>PVE2 API Explorer</title>
	<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" />
	<link rel="stylesheet" href="/assets/css/app.css" />
  </head>
  <body data-ng-controller="AppCtrl" data-ng-cloak="">
    <p>Exploratory interface, for now.</p>
	<form data-ng-submit="send()">
		<legend>PVE2 API Explorer</legend>
		<label>HTTP Method: 
			<select data-ng-model="method">
				<option value="get">GET</option>
				<option value="post">POST</option>
				<option value="put">PUT</option>
				<option value="delete">DELETE</option>
			</select>
		</label>
		<label>URI: 
			<input type="text" data-ng-model="uri" data-ng-change="getTemplate()" autofocus="autofocus" />
		</label>
		<dynamic-form template="dataTemplate" data-ng-model="data"></dynamic-form>
		<button class="btn btn-primary">Send</button>
	</form>
	<pre>Response: {{response | pretty}}</pre>
	<!-- Load scripts at the end for performance. -->
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.0.8/angular.js"></script>
	<script src="/assets/js/dynamic-forms.js"></script>
	<script src="/assets/js/local-storage-class.js"></script>
	<script src="/assets/js/app.js"></script>
  </body>
</html>
