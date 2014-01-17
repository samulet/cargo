'use strict';

angular.module('website.user.param', [])
    .factory('userParamsFactory', ['$http', 'storageFactory', 'errorFactory', 'REST_CONFIG', function ($http, storageFactory, errorFactory, REST_CONFIG) {
        function onError(data, status) {
            if (!errorFactory.isUnauthorized(status)) {
                errorFactory.resolve(data, status);
            }
        }

        function getAccounts() {
            $http.get(REST_CONFIG.BASE_URL + '/accounts')
                .success(function (data) {
                    var accounts = data._embedded.accounts;
                    if (accounts.length === 1) {
                        storageFactory.setSelectedAccount(accounts[0]);
                        getCompanies(accounts[0], true);
                    } else if (accounts.length === 0) {
                        storageFactory.removeSelectedAccount();
                        storageFactory.removeSelectedCompany();
                    }
                }).error(function (data, status) {
                    onError(data, status);
                }
            );
        }

        function getCompanies(account, isSetSelected) {
            $http.get(REST_CONFIG.BASE_URL + '/accounts/' + account.account_uuid + '/companies')
                .success(function (data) {
                    var companies = data._embedded.companies;
                    if (companies.length === 1 && isSetSelected === true) {
                        storageFactory.setSelectedCompany(companies[0]);
                    }
                    getUser();
                }).error(function (data, status) {
                    onError(data, status);
                }
            );
        }

        function getApiRoutes() {
            $http.get(REST_CONFIG.BASE_URL + '/meta').success(function (data) {
                storageFactory.setApiRoutes(data._embedded.resource_meta);
            }).error(function (data, status) {
                    errorFactory.resolve(data, status);
                }
            );
        }

        function getUser() {//TODO api didn't work yet
            // $http.get(REST_CONFIG.BASE_URL + '/profile').success(function (data) {
            // storageFactory.setUser(data._embedded.user);
            //}).error(function (data, status) {
            // errorFactory.resolve(data, status)
            //     }
            // );
        }

        return {
            getApiRoutes: function (isForce) {
                if (isForce !== true) {
                    var routes = storageFactory.getApiRoutes();
                    if (!routes) {
                        getApiRoutes();
                    }
                } else {
                    getApiRoutes();
                }
            },
            prepareUser: function () {
                $http.defaults.headers.common['X-Auth-UserToken'] = storageFactory.getToken();
                var selectedAccount = storageFactory.getSelectedAccount();
                var selectedCompany = storageFactory.getSelectedCompany();
                if (!selectedAccount || !selectedCompany) {
                    getAccounts();
                }

                if (!storageFactory.getUser()) {
                    getUser();
                }
            }
        };
    }])
;