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

                function openSelectCompanyModal() {
                    $scope.selectCompanyModal = $modal.open({
                        templateUrl: 'selectCompanyModalContent.html',
                        scope: $scope,
                        controller: 'selectCompanyModalController'
                    });
                }

                function openSelectAccountModal() {
                    $scope.selectAccountModal = $modal.open({
                        templateUrl: 'selectAccountModalContent.html',
                        scope: $scope,
                        controller: 'selectAccountModalController'
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

                $scope.closeSelectCompanyModal = function () {
                    closeModal($scope.selectCompanyModal);
                };

                $scope.closeSelectAccountModal = function () {
                    closeModal($scope.selectAccountModal);
                };

                $scope.showSelectAccountPopup = function () {
                    openSelectCompanyModal();
                };

                $scope.showSelectCompanyPopup = function () {
                    openSelectAccountModal();
                };

                $scope.logout = function () {
                    redirectFactory.logout();
                }
            }
        };
    })

    .controller('selectAccountModalController', ['$scope', '$http', 'REST_CONFIG', 'errorFactory', function ($scope, $http, REST_CONFIG, errorFactory) {
        $http.get(REST_CONFIG.BASE_URL + '/accounts')
            .success(function (data) {
                $scope.accounts = data['_embedded'].accounts;
            }).error(errorFactory.resolve);
    }])

    .controller('selectCompanyModalController', ['$scope', '$http', 'REST_CONFIG', 'errorFactory', 'storageFactory', function ($scope, $http, REST_CONFIG, errorFactory, storageFactory) {
        var selectedAccount = storageFactory.getSelectedAccount();
        if (selectedAccount) {
            $http.get(REST_CONFIG.BASE_URL + '/accounts/' + selectedAccount['account_uuid'] + '/companies')
                .success(function (data) {
                    $scope.companies = data['_embedded'].companies;
                }).error(errorFactory.resolve);
        }
    }])
;