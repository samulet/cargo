'use strict';

angular.module('website.dashboard', [])

    .controller('dashboardController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', 'storageFactory', '$modal', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS, storageFactory, $modal) {
        $rootScope.pageTitle = 'dashboard';
        $rootScope.bodyColor = 'filled_bg';
        var accountModal;
        $scope.accountData = [];
        $scope.today = new Date();
        $scope.firstAccount = null;
        checkForAccounts();

        function checkForAccounts() {
            $scope.registrationStep = 5;//TODO should be 0
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

        $scope.nextStep = function () {
            $scope.registrationStep++;
        };

        $scope.prevStep = function () {
            $scope.registrationStep--;
        };

        function onError(data, status) {
            // if (status === RESPONSE_STATUS.NOT_FOUND) { //TODO uncomment
            openAccountModal();
            // } else {
            //     errorFactory.resolve(data, status);
            // }
        }

        function getAccounts() {
            $http.get(REST_CONFIG.BASE_URL + '/accounts')
                .success(function (data) {
                    var accounts = data._embedded.accounts;
                    if (accounts.length === 1) {
                        $scope.firstAccount = data._embedded.accounts[0];
                    }
                }).error(onError);
        }
    }])

    .controller('registrationModalController', ['$scope', '$http', 'REST_CONFIG', 'errorFactory', '$timeout', function ($scope, $http, REST_CONFIG, errorFactory, $timeout) {
        $scope.juridicData = {
            contacts: {
                phones: [],
                emails: [],
                sites: [],
                addresses: []
            },
            founders: [],
            authorized_persons: [],
            pfr: [],
            fms: [],
            licenses: [],
            applicants: [],
            tax: {}
        };

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
                    $scope.nextStep();
                }).error(errorFactory.resolve);
        };

        $scope.saveData = function (isLastStep) {
            if (isLastStep) {
                console.log($scope.juridicData.inn);
                $http.post(REST_CONFIG.BASE_URL + '/accounts/' + $scope.firstAccount['account_uuid'] + '/companies', $scope.juridicData)
                    .success(function () {
                        $scope.closeAccountModal();
                        $scope.registrationStep = 0;
                    }).error(errorFactory.resolve);
            } else {
                $scope.nextStep();
            }
        };
    }])
;