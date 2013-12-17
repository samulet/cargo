'use strict';

angular.module('website.top.menu', [])

    .directive('topPrivateMenu', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/partials/private/top_menu.html',
            controller: function ($scope, $location, storageFactory) {
                var user = storageFactory.getUser();
                var selectedAccount = storageFactory.getSelectedAccount();
                var selectedCompany = storageFactory.getSelectedCompany();

                $scope.displayedName = (user) ? user.name : 'Пользователь';
                $scope.accountName = (selectedAccount) ? selectedAccount.title : '(Нет аккаунта)';
                $scope.companyShortName = (selectedCompany) ? selectedCompany.short : '(Юр. Лицо не выбрано)';

                $scope.getClass = function (path) {
                    if ('/' + $location.path().substr(1, path.length) == path) {
                        return "active"
                    } else {
                        return ""
                    }
                }
            }
        };
    })

    .directive('userMenu', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/partials/private/user_menu.html',
            scope: {
                displayedName: '=displayedName',
                accountName: '=accountName',
                companyShortName: '=companyShortName'
            },
            controller: function ($scope, redirectFactory, storageFactory, $modal) {

                function openSelectAccountAndCompanyModal() {
                    $scope.selectAccountAndCompanyModal = $modal.open({
                        templateUrl: 'selectAccountAndCompanyModalContent.html',
                        scope: $scope,
                        controller: 'selectAccountAndCompanyModalController'
                    });
                }

                function closeModal(modal) {
                    modal.close();
                }

                $scope.selectAccount = function () {
                    //TODO
                };

                $scope.selectCompany = function () {
                    //TODO
                };

                $scope.closeSelectAccountAndCompanyModal = function () {
                    closeModal($scope.selectAccountAndCompanyModal);
                };

                $scope.showSelectAccountAndCompanyModal = function () {
                    openSelectAccountAndCompanyModal();
                };

                $scope.logout = function () {
                    redirectFactory.logout();
                }
            }
        };
    })

    .controller('selectAccountAndCompanyModalController', ['$scope', '$http', 'REST_CONFIG', 'errorFactory', 'storageFactory', function ($scope, $http, REST_CONFIG, errorFactory, storageFactory) {
        $scope.tempSelectedAccount = [];
        $scope.options = [];

        getCompaniesForAccounts();

        function getAccounts(callback) {
            $http.get(REST_CONFIG.BASE_URL + '/accounts')
                .success(function (data) {
                    $scope.accounts = data['_embedded'].accounts;
                    callback($scope.accounts);
                }).error(errorFactory.resolve);
        }

        function getCompanies(account, callback) {
            if (account) {
                $http.get(REST_CONFIG.BASE_URL + '/accounts/' + account['account_uuid'] + '/companies')
                    .success(function (data) {
                        $scope.companies = data['_embedded'].companies;
                        callback($scope.companies);
                    }).error(errorFactory.resolve);
            }
        }

        function getCompaniesForAccounts() {
            getAccounts(function (accounts) {
                for (var k in accounts) {
                    getCompanies(accounts[k], function (companies) {
                        for (var j in companies) {
                            $scope.options.push(
                                {
                                    accountUuid: accounts[k]['account_uuid'],
                                    accountTitle: accounts[k]['title'],
                                    companyUuid: companies[j]['company_uuid'],
                                    short: companies[j]['short']
                                }
                            );
                        }
                    });

                }
            });
        }

    }])
;