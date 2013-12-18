'use strict';

angular.module('test', [])

    .constant("WEB_CONFIG", {
        "PROTOCOL": "http",
        "HOST": "cargo",
        "HOST_CONTEXT": "",
        "PORT": "8000",
        "DOMAIN": "cargo.dev",
        "BASE_URL": "http://cargo.dev:8000"
    })

    .constant("REST_CONFIG", {
        "PROTOCOL": "http",
        "HOST": "cargo",
        "HOST_CONTEXT": "/api",
        "PORT": "8000",
        "DOMAIN": "cargo.dev",
        "BASE_URL": "http://cargo.dev:8000/api"
    })

    .directive('tile', function () {
        return {
            restrict: 'E',
            templateUrl: 'tile_template.html',
            scope: {
                title: '=title',
                routes: '=routes'
            },
            link: function (scope, elem, attrs) {

            }
        };
    })

    .controller('apiTestController', ['$scope', '$http', 'REST_CONFIG', function ($scope, $http, REST_CONFIG) {

        $scope.api = [
            {
                title: 'Cargo API Root',
                routes: [
                    {
                        method: 'GET',
                        url: '/meta',
                        status: null
                    }
                ]
            },
            {
                title: 'Account',
                routes: [
                    {
                        method: 'GET',
                        url: '/accounts{/uuid}',
                        status: null
                    },
                    {
                        method: 'DELETE',
                        url: '/accounts{/uuid}',
                        status: null
                    },
                    {
                        method: 'PATCH',
                        url: '/accounts{/uuid}',
                        status: null
                    }
                ]
            }
        ];

        for (var k in $scope.api) {
            if ($scope.api.hasOwnProperty(k)) {
                for (var j in $scope.api[k].routes) {
                    if ($scope.api[k].routes.hasOwnProperty(j)) {
                        var params = {
                            method: $scope.api[k].routes[j].method,
                            url: REST_CONFIG.BASE_URL + $scope.api[k].routes[j].url
                        };

                        (function (k, j) {
                            $http(params).success(function (data, status, headers, config) {
                                $scope.api[k].routes[j].status = 'ok';
                                $scope.api[k].routes[j].code = status;
                            }).error(function (data, status, headers, config) {
                                    $scope.api[k].routes[j].status = 'failed';
                                    $scope.api[k].routes[j].code = status;
                                });
                        })(k, j);
                    }
                }
            }
        }

    }])
;
