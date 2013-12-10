'use strict';

angular.module('website.dashboard', [])

    .controller('dashboardController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', 'storageFactory', '$modal', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS, storageFactory, $modal) {
        $rootScope.pageTitle = 'dashboard';
        $rootScope.bodyColor = 'filled_bg';
        var accountModal;
        $scope.accountData = [];
        $scope.firstAccount = null;
        $scope.showAccountRegistration = false;
        $scope.showJuridicWizard = false;
        checkForAccounts();

        function checkForAccounts() {
            $scope.showAccountRegistration = true;
            getAccounts();
        }

        function openAccountModal() {
            accountModal = $modal.open({
                templateUrl: 'registrationModalContent.html',
                backdrop: 'static',
                scope: $scope,
                controller: 'registrationModalController'
            });
        }

        function closeAccountModal() {
            accountModal.close();
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
                    }
                }).error(onError);
        }

        $scope.removeAccount = function (account) {
            $http.delete(REST_CONFIG.BASE_URL + '/accounts/' + account.account_uuid)
                .success(function () {
                    getAccounts();
                }).error(onError);
        };
    }])

    .controller('registrationModalController', ['$scope', '$http', 'REST_CONFIG', 'errorFactory', '$timeout', function ($scope, $http, REST_CONFIG, errorFactory, $timeout) {

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
                    $scope.showJuridicWizard = true;
                }).error(errorFactory.resolve);
        };
    }])
;