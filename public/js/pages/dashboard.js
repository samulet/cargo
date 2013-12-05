'use strict';

angular.module('website.dashboard', [])

    .controller('dashboardController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', 'storageFactory', '$modal', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS, storageFactory, $modal) {
        $rootScope.pageTitle = 'dashboard';
        $rootScope.bodyColor = 'filled_bg';
        var accountModal;
        $scope.accountData = [];
        $scope.today = new Date();
        checkForAccounts();

        function checkForAccounts() {
            $scope.registrationStep = 0;
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

        function onError(data, status) {
            // if (status === RESPONSE_STATUS.NOT_FOUND) { //TODO uncomment
            openAccountModal();
            // } else {
            //     errorFactory.resolve(data, status);
            // }
        }

        function getAccounts(callback) {
            $http.get(REST_CONFIG.BASE_URL + '/accounts')
                .success(function (accounts) {
                    if (callback) callback(accounts);
                }).error(onError);
        }
    }])

    .controller('registrationModalController', ['$scope', '$http', 'REST_CONFIG', 'errorFactory', '$timeout', function ($scope, $http, REST_CONFIG, errorFactory, $timeout) {
        $scope.firstAccount = null;

        $scope.juridicData = {
            common: {},
            contacts: {
                phones: [],
                emails: [],
                sites: [],
                addresses: []
            },
            details: {},
            persons: {
                founders: [],
                authorizedPersons: []
            },
            pfrAndFms: {
                pfr: {},
                fms: {}
            },
            licensesAndRequesters: {
                licenses: [],
                registrationRequesters: []
            },
            misc: {
                check: {},
                other: {},
                tax: {}
            }
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
                    $scope.getAccounts(function (accounts) {
                        $scope.firstAccount = accounts[0];
                    });
                    $scope.nextStep();
                }).error(errorFactory.resolve);
        };

        $scope.saveData = function (isLastStep) {
            if (isLastStep) {
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