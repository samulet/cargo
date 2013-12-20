'use strict';

angular.module('test', [
        'env.config'
    ])

    .directive('tile', function () {
        return {
            restrict: 'E',
            templateUrl: 'tile_template.html',
            scope: {
                name: '=name',
                routes: '=routes'
            },
            link: function (scope, elem, attrs) {
                scope.isShowResponse = false;

                scope.showResponse = function () {
                    scope.isShowResponse = true;
                };

                scope.hideResponse = function () {
                    scope.isShowResponse = false;
                };

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
        var sourceId = '123';

        $scope.passedTestsPercent = 0;
        $scope.inProgressTestsPercent = 100;
        $scope.failedTestsPercent = 0;
        $scope.api = [];

        var passedTests = 0;
        var failedTests = 0;
        var totalTests = null;

        function getTotalTestsCount() {
            if (totalTests) return totalTests;

            var count = 0;
            var api = $scope.api;
            for (i in api) {
                if (api.hasOwnProperty(i)) {
                    for (j in api[i].routes) {
                        if (api[i].routes.hasOwnProperty(j)) count++;
                    }
                }
            }
            totalTests = count;
            return count;
        }

        function setTestPassed(isTestPassed) {
            var totalTests = getTotalTestsCount();

            if (isTestPassed) {
                passedTests++;
            } else {
                failedTests++;
            }
            $scope.passedTestsPercent = ((passedTests * 100) / totalTests).toFixed(0);
            $scope.failedTestsPercent = ((failedTests * 100) / totalTests).toFixed(0);
            var inProgressTests = (totalTests - (passedTests + failedTests));
            $scope.inProgressTestsPercent = ((inProgressTests * 100) / totalTests).toFixed(0);
        }

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
                        method: 'PATCH',
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
                    }
                ]
            },
            {
                title: 'Accounts Collection',
                routes: [
                    {
                        method: 'GET',
                        url: '/api/accounts',
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    },
                    {
                        method: 'POST',
                        url: '/api/accounts',
                        data: JSON.stringify({title: 'demo'}),
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
                        data: JSON.stringify({short: 'demo', inn: '1111111111'}),
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
                        method: 'PATCH',
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
                        method: 'PATCH',
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
                        data: JSON.stringify({"code": "milk", "title": "Молочные продукты"}),
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
                        url: '/api/service/import/company-intersect/' + sourceId,
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    },
                    {
                        method: 'POST',
                        url: '/api/service/import/company-intersect/' + sourceId,
                        data: JSON.stringify({
                            "source": "vesta",
                            "id": 123,
                            "company": companyUuid
                        }),
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    },
                    {
                        method: 'DELETE',
                        url: '/api/service/import/company-intersect/' + sourceId,
                        headers: {
                            'X-Auth-UserToken': token,
                            'X-App-Account': selectedAccount,
                            'X-App-Company': selectedCompany
                        }
                    }
                ]
            }
        ];

        function setHeaders(headers) {
            if (headers) {
                params.headers = {};
                for (var k in headers) {
                    if (headers.hasOwnProperty(k)) {
                        params.headers[k] = headers[k];
                    }
                }
            }
        }

        function setDataParams(data) {
            if (data) {
                params.data = data
            }
        }

        for (var i in $scope.api) {
            if ($scope.api.hasOwnProperty(i)) {
                var routes = $scope.api[i].routes;
                for (var j in routes) {
                    if (routes.hasOwnProperty(j)) {
                        var params = {
                            method: routes[j].method,
                            url: serverUrl + routes[j].url
                        };

                        setHeaders(routes[j].headers);
                        setDataParams(routes[j].data);

                        (function (routes, i, j) {
                            $http(params).success(function (data, status) {
                                routes[j].status = 'ok';
                                routes[j].code = status;
                                routes[j].response = data;
                                setTestPassed(true);
                            }).error(function (data, status) {
                                    routes[j].status = 'failed';
                                    routes[j].code = status;
                                    routes[j].response = data;
                                    setTestPassed(false);
                                });
                        })(routes, i, j);
                    }
                }
            }
        }

    }])
;
