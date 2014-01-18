angular.module('app', ['dynform'])
  .controller('AppCtrl', ['$scope', '$q', '$http', function ($scope, $q, $http) {
    var resetTemplate = function () {
      $scope.dataTemplate = {
        'json': {
          'type': 'textarea',
          'label': 'Data (JSON): ',
          'disabled': '["post", "put"].indexOf(method) < 0'
        }
      };
    },
    localStore = new StoreLocal();
    
    $scope.method = 'get';
    $scope.uri = '';
    $scope.data = {};
    $scope.response = {};
    resetTemplate();
    
    $scope.getTemplate = function() {
      var data = null,
          uri = $scope.uri;
      
      if (uri.length < 1) {
        resetTemplate();
      }
      else {
        data = localStore.get(uri);
        if ([null,false].indexOf(data) > -1 || data.time < (Date.now() - 900000)) {
          $http.post('/api/ui', {uri: uri})
            .success(function(data) {
              if ([null,false].indexOf(data) > -1 || angular.equals(data, [])) {
                resetTemplate();
              }
              else
              {
                angular.forEach(data, function (field, id) {
                  if (!angular.isDefined(field.disabled)) {
                    data[id].disabled = "['post', 'put'].indexOf(method) < 0";
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
        else {
          if ([null,false].indexOf(data) > -1 || angular.equals(data.data, [])) {
            resetTemplate();
          }
          else
          {
            $scope.dataTemplate = data.data;
          }
        }
      }
    };
    
    $scope.send = function() {
      var payload = {
            uri: $scope.uri
          },
          method = $scope.method;
      
      if (["post", "put"].indexOf(method) > -1) {
        payload.data = $scope.data;
      }
      
      $scope.resultsOf(method, payload).then(function(data) {
        $scope.response = data;
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
    }
    
    $scope.storageList = $scope.resultsOf('get', {'uri': 'storage'});
    
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