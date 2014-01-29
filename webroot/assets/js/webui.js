angular.module('app', ['dynform', 'treeControl', 'ui.bootstrap'])
  .controller('AppCtrl', ['$scope', '$q', '$http', '$filter', '$timeout',
    function ($scope, $q, $http, $filter, $timeout) {
      var localStore = new StoreLocal(),
          treeTimer,
          sortArray = function (sortMe, sortBy, reverse) {
            return $filter('orderBy')(sortMe, sortBy, reverse);
          };
      
      // Imported values
      $scope.accounts = accounts;
      
      // Simple values
      $scope.working = false;
      $scope.navTree = [];
      $scope.dataTree = {
        '-': {},
      };
      $scope.nodeDisplay = {};
      
      // Complex values
      $scope.navOptions = {
        injectClasses: {
          iExpanded: "fa fa-fw fa-minus-square-o",
          iCollapsed: "fa fa-fw fa-plus-square-o",
          iLeaf: "fa fa-fw"
        },
        equality: function (a, b) {
          if (a === undefined || b === undefined) {
            return false;
          }
          return angular.equals(a.ref, b.ref);
        }
      };
      
      // Functions
      $scope.resultsOf = function (host, method, payload) {
        var defer = $q.defer();
        
        payload.host = host;
        
        $http.post('/api/' + method, payload)
          .success(function(data) {
            defer.resolve(data);
          })
          .error(function(error) {
            defer.resolve(error);
          });
        
        return defer.promise;
      };
      
      $scope.$root.faType = function (nodeType, nodeStatus) {
        var nodeClass = 'fa-';
        
        switch (nodeType) {
          case 'cluster':
            nodeClass += 'cloud';
            break;
          case 'node':
            nodeClass += 'times';
            break;
          case 'openvz':
            nodeClass += 'linux';
            break;
          case 'qemu':
            nodeClass += 'desktop';
            break;
          case 'storage':
            nodeClass += 'hdd-o';
            break;
          case 'pool':
            nodeClass += 'sitemap';
            break;
          default:
            nodeClass += 'folder';
            break;
        }
        
        switch (nodeStatus) {
          case 'up':
            nodeClass += ' up';
            break;
          case 'down':
            nodeClass += ' down';
            break;
          default:
            nodeClass += ' muted';
            break;
        }
        
        return nodeClass;
      }
      
      $scope.refreshTree = function () {
        var workingTree = [],
            workingPromises = [];
        
        angular.forEach($scope.accounts, function (account) {
          var clusterTree = {
                type: 'cluster',
                status: '',
                display: account.name,
                ref: account.host,
                children: []
              },
              clusterDefer = $q.defer(),
              nodesDefer = $q.defer(),
              poolsDefer = $q.defer();
          
          workingPromises.push(clusterDefer.promise);
          
          $scope.resultsOf(account.host, 'get', {'uri': 'nodes'}).then(function (data) {
            var nodePromises = [];
            
            angular.forEach(data, function (node) {
              var nodeTree = {
                    type: 'node',
                    status: 'up',
                    display: node.node,
                    ref: account.host + '/nodes/' + node.node,
                    children: []
                  },
                  nodeDefer = $q.defer(),
                  ctDefer, vmDefer, storageDefer;
              
              nodePromises.push(nodeDefer.promise);
              
              ctDefer = $scope.resultsOf(account.host, 'get', {'uri': 'nodes/' + node.node + '/openvz'});
              ctDefer.then(function (data) {
                angular.forEach(data, function (ct) {
                  var ctTree = {
                        type: 'openvz',
                        status: '',
                        display: ct.vmid + ' (' + ct.name + ')',
                        ref: account.host + '/nodes/' + node.node + '/openvz/' + ct.vmid
                      };
                  
                  switch (ct.status) {
                    case 'running':
                      ctTree.status = 'up';
                      break;
                    case 'stopped':
                    default:
                      ctTree.status = 'down';
                      break;
                  }
                  
                  $scope.dataTree[ctTree.ref] = ct;
                  nodeTree.children.push(ctTree);
                });
                
                nodeTree.children = sortArray(nodeTree.children, '+display');
              });
              
              vmDefer = $scope.resultsOf(account.host, 'get', {'uri': 'nodes/' + node.node + '/qemu'});
              vmDefer.then(function (data) {
                angular.forEach(data, function (vm) {
                  var vmTree = {
                        type: 'qemu',
                        status: '',
                        display: vm.vmid + ' (' + vm.name + ')',
                        ref: account.host + '/nodes/' + node.node + '/qemu/' + vm.vmid
                      };
                  
                  switch (vm.status) {
                    case 'running':
                      vmTree.status = 'up';
                      break;
                    case 'stopped':
                    default:
                      vmTree.status = 'down';
                      break;
                  }
                  
                  $scope.dataTree[vmTree.ref] = vm;
                  nodeTree.children.push(vmTree);
                });
                
                nodeTree.children = sortArray(nodeTree.children, '+display');
              });
              
              storageDefer = $scope.resultsOf(account.host, 'get', {'uri': 'nodes/' + node.node + '/storage'});
              storageDefer.then(function (data) {
                angular.forEach(data, function (storage) {
                  var storageTree = {
                        type: 'storage',
                        status: '',
                        display: storage.storage,
                        ref: account.host + '/nodes/' + node.node + '/storage/' + storage.storage
                      };
                  
                  $scope.dataTree[storageTree.ref] = storage;
                  nodeTree.children.push(storageTree);
                });
                
                nodeTree.children = sortArray(nodeTree.children, '+display');
              });
              
              $q.all([ctDefer, vmDefer, storageDefer]).then(function() {
                nodeDefer.resolve();
              });
              $scope.dataTree[nodeTree.ref] = node;
              clusterTree.children.push(nodeTree);
            });
            
            $q.all(nodePromises).then(function() {
              nodesDefer.resolve();
            });
            clusterTree.children = sortArray(clusterTree.children, ['+type', '+display']);
          });
          
          $q.all([nodesDefer.promise]).then(function() {
            $scope.resultsOf(account.host, 'get', {'uri': 'pools'}).then(function (data) {
              var poolPromises = [];
              
              angular.forEach(data, function (pool) {
                var poolTree = {
                      type: 'pool',
                      status: 'up',
                      display: pool.poolid,
                      ref: account.host + '/pools/' + pool.poolid,
                      children: []
                    },
                    poolDefer = $q.defer(),
                    dataDefer;
                
                poolPromises.push(poolDefer.promise);
                
                dataDefer = $scope.resultsOf(account.host, 'get', {'uri': 'pools/' + pool.poolid});
                dataDefer.then(function (data) {
                  angular.forEach(data.members, function (member) {
                    var memberTree = {
                      type: member.type,
                      status: '',
                      display: '',
                      ref: account.host + ':pool/nodes/' + member.node
                    };
                    
                    switch (member.type) {
                      case 'openvz':
                      case 'qemu':
                        memberTree.display = member.vmid + ' (' + member.name + ')';
                        memberTree.ref += '/' + member.type + '/' + member.vmid
                        switch ($scope.dataTree[memberTree.ref.replace(':pool', '')].status) {
                          case 'running':
                            memberTree.status = 'up';
                            break;
                          case 'stopped':
                          default:
                            memberTree.status = 'down';
                            break;
                        }
                        break;
                      case 'storage':
                        memberTree.display = member.storage,
                        memberTree.ref += '/storage/' + member.storage
                        break;
                      default:
                        break;
                    }
                    
                    poolTree.children.push(memberTree);
                  });
                  
                  $scope.dataTree[poolTree.ref] = angular.extend({}, pool, data);
                  poolTree.children = sortArray(poolTree.children, '+display');
                });
                
                $q.all([dataDefer]).then(function() {
                  poolDefer.resolve();
                });
                clusterTree.children.push(poolTree);
              });
              
              $q.all(poolPromises).then(function() {
                poolsDefer.resolve();
              });
              clusterTree.children = sortArray(clusterTree.children, ['+type', '+display']);
            });
          });
          
          $q.all([poolsDefer.promise]).then(function() {
            clusterDefer.resolve();
          });
          $scope.dataTree[clusterTree.ref] = account;
          workingTree.push(clusterTree);
        });
        
        $q.all(workingPromises).then(function() {
            $scope.navTree = [{
              type: 'navroot',
              status: '',
              display: 'Datacenter',
              ref: '-',
              children: sortArray(workingTree, '+display')
            }];
            treeTimer = $timeout($scope.refreshTree, 1000);
        });
      }
      
      $scope.navChange = function (node) {
        $scope.nodeDisplay = node;
        $scope.nodeDisplay.ref = node.ref.replace(':pool', '');
      }
      
      // Other initialization
      $scope.refreshTree();
    }
  ])
  .filter('uptime', function() {
    return function (input) {
      var output = '',
          working = input,
          scratch = {
            current: 0,
            leftover: 0,
          },
          renderMinutes = false,
          renderSeconds = false,
          lengths = {
            year: 31536000,
            day: 86400,
            hour: 3600,
            minute: 60,
          };
      
      if (!angular.isNumber(input)) {
        return input;
      }
      
      if (working >= lengths.year) {  // 1 year
        scratch.leftover = working % lengths.year;
        scratch.current = (working - scratch.leftover) / lengths.year;
        output += scratch.current + 'y ';
        working = scratch.leftover;
      }
      if (working >= lengths.day) {  // 1 day
        scratch.leftover = working % lengths.day;
        scratch.current = (working - scratch.leftover) / lengths.day;
        output += scratch.current + 'd ';
        working = scratch.leftover;
      }
      if (working >= lengths.hour) {  // 1 hour
        scratch.leftover = working % lengths.hour;
        scratch.current = (working - scratch.leftover) / lengths.hour;
        output += scratch.current + ':';
        working = scratch.leftover;
        renderMinutes = true;
      }
      if (working >= lengths.minute || renderMinutes) {  // 1 minute
        scratch.leftover = working % lengths.minute;
        scratch.current = (working - scratch.leftover) / lengths.minute;
        if (renderMinutes && scratch.current < 10) {
          output += '0';
        }
        output += scratch.current + ':';
        working = scratch.leftover;
        renderSeconds = true;
      }
      if (renderSeconds && working < 10) {
        output += '0';
      }
      output += working + 's';
      
      return output;
    };
  });

String.prototype.trim = function (chr) {
  if (!chr) {
    chr = "\\s";
  }
  
  return RegExp('^['+chr+']*([^]*?)['+chr+']*$').exec(this)[1];
};

