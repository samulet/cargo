'use strict';

angular.module('test', [])

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

    .controller('apiTestController', ['$scope', '$http', function ($scope, $http) {

        var serverUrl = 'http://cargo.dev:8000';
        var accountUuid = '12345';
        var companyUuid = '56789';
        var uuid = '012345';
        var code = '678901';

        $scope.api = [
            {
                title: 'Cargo API Root',
                routes: [
                    {
                        method: 'GET',
                        url: '/api/meta'
                    }
                ]
            },
            {
                title: 'Account',
                routes: [
                    {
                        method: 'GET',
                        url: '/api/accounts/' + uuid
                    },
                    {
                        method: 'DELETE',
                        url: '/api/accounts/' + uuid
                    },
                    {
                        method: 'PATCH',
                        url: '/api/accounts/' + uuid
                    }
                ]
            },
            {
                title: 'Account Company',
                routes: [
                    {
                        method: 'DELETE',
                        url: '/api/accounts/' + accountUuid + '/companies/' + companyUuid
                    }
                ]
            },
            {
                title: 'Account Companies',
                routes: [
                    {
                        method: 'POST',
                        url: '/api/accounts/' + accountUuid + '/companies'
                    }
                ]
            },
            {
                title: 'Profile',
                routes: [
                    {
                        method: 'GET',
                        url: '/api/profiles/' + uuid
                    },
                    {
                        method: 'DELETE',
                        url: '/api/profiles/' + uuid
                    },
                    {
                        method: 'PATCH',
                        url: '/api/profiles/' + uuid
                    }
                ]
            },
            {
                title: 'Company',
                routes: [
                    {
                        method: 'GET',
                        url: '/api/companies/' + uuid
                    },
                    {
                        method: 'DELETE',
                        url: '/api/companies/' + uuid
                    },
                    {
                        method: 'PATCH',
                        url: '/api/companies/' + uuid
                    }
                ]
            },
            {
                title: 'Product Group Reference',
                routes: [
                    {
                        method: 'GET',
                        url: '/api/ref/product-group/' + code
                    },
                    {
                        method: 'DELETE',
                        url: '/api/ref/product-group/' + code
                    }
                ]
            },
            {
                title: 'Product Group Reference Collection',
                routes: [
                    {
                        method: 'GET',
                        url: '/api/ref/product-group'
                    },
                    {
                        method: 'POST',
                        url: '/api/ref/product-group'
                    },
                    {
                        method: 'PUT',
                        url: '/api/ref/product-group'
                    }

                ]
            },
            {
                title: 'Company import service',
                routes: [
                    {
                        method: 'GET',
                        url: '/api/service/import/company'
                    }
                ]
            },
            {
                title: 'Link companies with external records',
                routes: [
                    {
                        method: 'GET',
                        url: '/api/service/import/company-intersect'
                    },
                    {
                        method: 'POST',
                        url: '/api/service/import/company-intersect'
                    },
                    {
                        method: 'DELETE',
                        url: '/api/service/import/company-intersect'
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
                            url: serverUrl + $scope.api[k].routes[j].url
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
