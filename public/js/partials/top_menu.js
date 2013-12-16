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

        $http.get(REST_CONFIG.BASE_URL + '/accounts')
            .success(function (data) {
                $scope.accounts = data['_embedded'].accounts;
            }).error(errorFactory.resolve);

        $scope.getCompanies = function (selectedAccount) {
            if (selectedAccount) {
                $http.get(REST_CONFIG.BASE_URL + '/accounts/' + selectedAccount['account_uuid'] + '/companies')
                    .success(function (data) {
                        $scope.companies = data['_embedded'].companies;
                    }).error(errorFactory.resolve);
            }
        }

    }])
;