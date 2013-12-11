'use strict';

angular.module('website.account', [])

    .controller('accountController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', 'storageFactory', '$modal', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS, storageFactory, $modal) {
        $rootScope.pageTitle = 'Аккаунт';
        $rootScope.bodyColor = 'filled_bg';
        $scope.companyModal = null;
        $scope.selectedAccount = null;
        $scope.showJuridicWizard = false;

        $scope.addCompany = function (account) {
            $scope.selectedAccount = account;
            $scope.showJuridicWizard = true;
            openCompanyModal();
        };

        function openCompanyModal() {
            $scope.companyModal = $modal.open({
                templateUrl: 'addCompanyModalContent.html',
                backdrop: 'static',
                scope: $scope,
                controller: 'addCompanyModalController'
            });
        }

        function closeCompanyModal() {
            $scope.companyModal.close();
        }

        $scope.closeCompanyModal = function () {
            closeCompanyModal();
        };

        getAccounts();

        $scope.getAccounts = function () {
            getAccounts();
        };

        function getAccounts() {
            $http.get(REST_CONFIG.BASE_URL + '/accounts')
                .success(function (data) {
                    var accounts = data._embedded.accounts;
                    $scope.accounts = accounts;
                    if (accounts.length === 1) {
                        $scope.firstAccount = data._embedded.accounts[0];
                    }
                }).error(errorFactory.resolve);
        }

        $scope.removeAccount = function (account) {
            $http.delete(REST_CONFIG.BASE_URL + '/accounts/' + account['account_uuid'])
                .success(function () {
                    getAccounts();
                }).error(errorFactory.resolve);
        };
    }])

    .controller('addCompanyModalController', ['$scope', '$http', 'REST_CONFIG', 'errorFactory', '$timeout', function ($scope, $http, REST_CONFIG, errorFactory, $timeout) {

        $scope.openCatalog = function () {
            //placeholder
        };

        $scope.openDatePopup = function (isOpen) {
            $timeout(function () {
                $scope[isOpen] = true;
            });
        };

        /*
         $scope.saveAccountData = function () {
         $http.post(REST_CONFIG.BASE_URL + '/accounts', {title: $scope.accountData.title})
         .success(function () {
         $scope.getAccounts();
         $scope.showAccountRegistration = false;
         $scope.showJuridicWizard = true;
         }).error(errorFactory.resolve);
         };*/
    }])
;