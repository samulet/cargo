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
        var token = 'ef32b5d575b528582924588093da0d491b85a5ac507330bcc0a5b11feaf6ed4f';
        var selectedAccount = 'aa38c4511de0409787baf55a83f03452';
        var selectedCompany = 'eaf4befff8aa4a0090e86cb9821026a0';

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
                        url: '/api/accounts/' + uuid,
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    },
                    {
                        method: 'DELETE',
                        url: '/api/accounts/' + uuid,
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    },
                    {
                        method: 'PATCH',
                        url: '/api/accounts/' + uuid,
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    }
                ]
            },
            {
                title: 'Account Company',
                routes: [
                    {
                        method: 'DELETE',
                        url: '/api/accounts/' + accountUuid + '/companies/' + companyUuid,
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    }
                ]
            },
            {
                title: 'Account Companies',
                routes: [
                    {
                        method: 'POST',
                        url: '/api/accounts/' + accountUuid + '/companies',
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    }
                ]
            },
            {
                title: 'Profile',
                routes: [
                    {
                        method: 'GET',
                        url: '/api/profiles/' + uuid,
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    },
                    {
                        method: 'DELETE',
                        url: '/api/profiles/' + uuid,
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    },
                    {
                        method: 'PATCH',
                        url: '/api/profiles/' + uuid,
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    }
                ]
            },
            {
                title: 'Company',
                routes: [
                    {
                        method: 'GET',
                        url: '/api/companies/' + uuid,
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    },
                    {
                        method: 'DELETE',
                        url: '/api/companies/' + uuid,
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    },
                    {
                        method: 'PATCH',
                        url: '/api/companies/' + uuid,
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    }
                ]
            },
            {
                title: 'Product Group Reference',
                routes: [
                    {
                        method: 'GET',
                        url: '/api/ref/product-group/' + code,
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    },
                    {
                        method: 'DELETE',
                        url: '/api/ref/product-group/' + code,
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    }
                ]
            },
            {
                title: 'Product Group Reference Collection',
                routes: [
                    {
                        method: 'GET',
                        url: '/api/ref/product-group',
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    },
                    {
                        method: 'POST',
                        url: '/api/ref/product-group',
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    },
                    {
                        method: 'PUT',
                        url: '/api/ref/product-group',
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    }

                ]
            },
            {
                title: 'Company import service',
                routes: [
                    {
                        method: 'GET',
                        url: '/api/service/import/company',
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    }
                ]
            },
            {
                title: 'Link companies with external records',
                routes: [
                    {
                        method: 'GET',
                        url: '/api/service/import/company-intersect',
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    },
                    {
                        method: 'POST',
                        url: '/api/service/import/company-intersect',
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    },
                    {
                        method: 'DELETE',
                        url: '/api/service/import/company-intersect',
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    }
                ]
            }
        ];

        for (var i in $scope.api) {
            if ($scope.api.hasOwnProperty(i)) {
                var routes = $scope.api[i].routes;
                for (var j in routes) {
                    if (routes.hasOwnProperty(j)) {
                        var params = {
                            method: routes[j].method,
                            url: serverUrl + routes[j].url
                        };

                        if (routes[j].headers) {
                            params.headers = {};
                            var header = routes[j].headers;
                            for (var k in header) {
                                if (header.hasOwnProperty(k)) {
                                    params.headers[k] = header[k];
                                }
                            }
                        }


                        (function (routes, i, j) {
                            $http(params).success(function (data, status) {
                                routes[j].status = 'ok';
                                routes[j].code = status;
                            }).error(function (data, status) {
                                    routes[j].status = 'failed';
                                    routes[j].code = status;
                                });
                        })(routes, i, j);
                    }
                }
            }
        }

    }])
;
