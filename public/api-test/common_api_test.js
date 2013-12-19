'use strict';

angular.module('test', [
    'env.config'
    ])

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

        var serverUrl = REST_CONFIG.PROTOCOL + "://" + REST_CONFIG.DOMAIN + ':' + REST_CONFIG.PORT;
        var accountUuid = 'b21295c8a94c4bb0a4de07bd2d76ed38';
        var companyUuid = '1e35ef244bb044bd989e9013594699e3';
        var userUuid = '93456a97789c4538ba8d0e8d7419e658';
        var productGroupCode = 'PLACEHOLDER'; //TODO no product group yet
        var token = 'db057553f1a4989210ae84a2825982e1d04d6879a2690365e1fcecb619fb77e2';
        var selectedAccount = 'b21295c8a94c4bb0a4de07bd2d76ed38';
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
                        url: '/api/accounts/' + accountUuid,
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    },
                    {
                        method: 'DELETE',
                        url: '/api/accounts/' + accountUuid,
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    },
                    {
                        method: 'PATCH',
                        url: '/api/accounts/' + accountUuid,
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
                        url: '/api/profiles/' + userUuid,
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    },
                    {
                        method: 'DELETE',
                        url: '/api/profiles/' + userUuid,
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    },
                    {
                        method: 'PATCH',
                        url: '/api/profiles/' + userUuid,
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
                        url: '/api/companies/' + companyUuid,
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    },
                    {
                        method: 'DELETE',
                        url: '/api/companies/' + companyUuid,
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    },
                    {
                        method: 'PATCH',
                        url: '/api/companies/' + companyUuid,
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
                        url: '/api/ref/product-group/' + productGroupCode,
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    },
                    {
                        method: 'DELETE',
                        url: '/api/ref/product-group/' + productGroupCode,
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
