angular.module('app', ['dynform', 'ui.bootstrap'])
  .controller('AppCtrl', ['$scope', '$q', '$http', function ($scope, $q, $http) {
    var resetTemplate = function () {
      $scope.dataTemplate = {
        'json': {
          'type': 'textarea',
          'label': 'Data (JSON): ',
          'disabled': '["get", "post", "put"].indexOf(method) < 0',
          'class': '"form-control"'
        }
      };
    },
    localStore = new StoreLocal();
    
    $scope.working = false;
    $scope.typeaheadWorking = false;
    $scope.method = 'get';
    $scope.uri = '';
    $scope.accounts = accounts;
    $scope.host = accounts[0].host;
    $scope.knownURIs = knownURIs;
    $scope.data = {};
    $scope.response = {};
    $scope.storageList = [];
    resetTemplate();
    
    $scope.uriTypeahead = function(uri) {
      var matching = [];
      uri = uri.trim('/\\s').split('/');
      angular.forEach($scope.knownURIs, function(match) {
        var found = true,
            parts = match.split('/');
        
        if(parts.length >= uri.length) {
          angular.forEach(uri, function(segment, idx) {
            if (parts[idx].match(/^:[^/:]+:$/)) {
              parts[idx] = segment;
              found = found && true;
            }
            else if (RegExp('^' + segment).exec(parts[idx])) {
              found = found && true;
            }
            else
            {
              found = false;
              return false;
            }
          });
          
          if (found) {
            matching.push(parts.join('/'));
          }
        }
      });
      $scope.typeaheadWorking = false;
      return matching;
    };
    
    $scope.getTemplate = function() {
      var data = null,
          uri = $scope.uri.trim('/\\s');
      
      if (uri.length < 1) {
        resetTemplate();
      }
      else {
        data = localStore.get(uri);
        if ([null,false].indexOf(data) > -1 || data.time < (Date.now() - 900000)) {
          $http.post('/api/ui', {host: $scope.host, uri: uri})
            .success(function(data) {
              if ([null,false].indexOf(data) > -1 || angular.equals(data, [])) {
                resetTemplate();
              }
              else
              {
                angular.forEach(data, function (field, id) {
                  if (!angular.isDefined(field.disabled)) {
                    data[id].disabled = "['get', 'post', 'put'].indexOf(method) < 0";
                  }
                  if (!angular.isDefined(field.class)) {
                    data[id].class = "'form-control'";
                  }
                });
                $scope.dataTemplate = data;
              }
              localStore.save(uri, {time: Date.now(), data: data});
            })
            .error(function(error) {
              resetTemplate();
            });
        }
        else if (angular.equals(data.data, [])) {
          resetTemplate();
        }
        else
        {
          $scope.dataTemplate = data.data;
        }
      }
    };
    
    $scope.send = function() {
      var payload = {
            host: $scope.host,
            uri: $scope.uri.trim("/\\s")
          },
          method = $scope.method;
      
      if (["get", "post", "put"].indexOf(method) > -1) {
        angular.forEach($scope.data, function(value, key) {
          if (angular.isDefined(value) && value !== null && value !== '') {
            this[key] = value;
          }
        }, (payload.data = {}));
      }
      
      $scope.working = true;
      $scope.resultsOf(method, payload).then(function(data) {
        $scope.response = data;
        $scope.working = false;
      });
    };
    
    $scope.resultsOf = function(method, payload) {
      var defer = $q.defer();
      
      $http.post('/api/' + method, payload)
        .success(function(data) {
          defer.resolve(data);
        })
        .error(function(error) {
          defer.resolve(error);
        });
      
      return defer.promise;
    };
    
    angular.forEach($scope.accounts, function (account) {
      $scope.resultsOf('get', {host: account.host, uri: 'storage'})
        .then(function(data) { $scope.storageList[account.host] = data; });
    });
    
  }])
  .filter('pretty', function() {
    return function (input) {
      var temp;
      try {
        temp = angular.fromJson(input);
      }
      catch (e) {
        temp = input;
      }
      
      return angular.toJson(temp, true);
    };
  });

String.prototype.trim = function(chr) {
  if (!chr) {
    chr = "\\s";
  }
  
  return RegExp('^['+chr+']*([^]*?)['+chr+']*$').exec(this)[1];
};

