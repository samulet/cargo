'use strict';

angular.module('website.dashboard', [])

    .controller('dashboardController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', 'storageFactory', '$modal', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS, storageFactory, $modal) {
        $rootScope.pageTitle = 'dashboard';
        $scope.accountModal = null;
        $scope.accountData = [];
        $scope.firstAccount = null;
        $scope.showAccountRegistration = false;
        $scope.showCompanyWizard = false;
        checkForAccounts();

        //TODO remove (just demo for a catalogs tests)
        $scope.catalogModel = null;

        $scope.getData = function () {
            return [
                {value: 1, description: 'Петров В.', firstname: 'Василий', lastName: 'Петров', age: '21' },
                {value: 1, description: 'Антонов К.', firstname: 'Константин', lastName: 'Антонов', age: '37' },
                {value: 1, description: 'Яковлев Б.', firstname: 'Борис', lastName: 'Яковлев', age: '17' },
                {value: 1, description: 'Туполев М.', firstname: 'Туполев', lastName: 'Марат', age: '33' },
                {value: 1, description: 'Лавочкин С.', firstname: 'Серафим', lastName: 'Лавочкин', age: '24' }
            ];
        };
        //TODO END remove

        function checkForAccounts() {
            $scope.showAccountRegistration = true;
            getAccounts();
        }

        function openAccountModal() {
            $scope.accountModal = $modal.open({
                templateUrl: 'registrationModalContent.html',
                backdrop: 'static',
                scope: $scope,
                controller: 'registrationModalController'
            });
        }

        function closeAccountModal() {
            $scope.accountModal.close();
        }

        $scope.closeAccountModal = function () {
            closeAccountModal();
        };

        $scope.getAccounts = function () {
            getAccounts();
        };

        function onError(data, status) {
            if (status === RESPONSE_STATUS.NOT_FOUND) {
                openAccountModal();
            } else {
                errorFactory.resolve(data, status);
            }
        }

        function getAccounts() {
            $http.get(REST_CONFIG.BASE_URL + '/accounts')
                .success(function (data) {
                    var accounts = data._embedded.accounts;
                    $scope.accounts = accounts;
                    if (accounts.length === 1) {
                        $scope.firstAccount = data._embedded.accounts[0];
                        storageFactory.setSelectedAccount($scope.firstAccount);
                    } else if (accounts.length === 0) {
                        storageFactory.removeSelectedAccount();
                        storageFactory.removeSelectedCompany();
                    }
                }).error(onError);
        }

        $scope.addAccount = function () {
            $scope.showAccountRegistration = true;
            $scope.showCompanyWizard = false;
            openAccountModal();
        };

        $scope.removeAccount = function (account) {
            $http.delete(REST_CONFIG.BASE_URL + '/accounts/' + account.account_uuid)
                .success(function () {
                    getAccounts();
                }).error(onError);
        };
    }])

    .controller('registrationModalController', ['$scope', '$http', 'REST_CONFIG', 'errorFactory', '$timeout', function ($scope, $http, REST_CONFIG, errorFactory, $timeout) {
        $scope.registrationModalMessages = [];

        $scope.openCatalog = function () {
            //placeholder
        };

        $scope.openDatePopup = function (isOpen) {
            $timeout(function () {
                $scope[isOpen] = true;
            });
        };

        $scope.saveAccountData = function () {
            $http.post(REST_CONFIG.BASE_URL + '/accounts', {title: $scope.accountData.title})
                .success(function () {
                    $scope.getAccounts();
                    $scope.showAccountRegistration = false;
                    $scope.showCompanyWizard = true;
                }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.registrationModalMessages);
                }
            );
        };
    }])
;