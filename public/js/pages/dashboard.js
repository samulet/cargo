'use strict';

angular.module('website.dashboard', [])

    .controller('dashboardController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', 'storageFactory', '$modal', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS, storageFactory, $modal) {
        $rootScope.pageTitle = 'dashboard';
        $rootScope.bodyColor = 'filled_bg';
        var accountModal;
        $scope.account = [];
        $scope.today = new Date();
        checkForAccounts();

        function checkForAccounts() {
            openAccountModal();//TODO remove
            $scope.registrationStep = 1;//TODO remove
            if (!storageFactory.getAccounts()) {
                $scope.registrationStep = 0;
                getAccounts();
            }
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

        $scope.skipStep = function () {
            $scope.registrationStep++;
        };

        function onError(data, status) {
            if (status === RESPONSE_STATUS.NOT_FOUND) {
                openAccountModal();
            } else {
                // errorFactory.resolve(data, status); //TODO uncomment
            }
        }

        function getAccounts() {
            $http.get(REST_CONFIG.BASE_URL + '/accounts')
                .success(function (accounts) {
                    storageFactory.setAccounts(accounts);
                }).error(onError);
        }
    }])

    .controller('registrationModalController', ['$scope', '$http', 'REST_CONFIG', 'errorFactory', '$timeout', function ($scope, $http, REST_CONFIG, errorFactory, $timeout) {
        $scope.juridicData = {
            phones: [],
            emails: [],
            founders: [],
            authorizedPersons: [],
            licenses: [],
            registrationRequesters: [],
            sites: [],
            addresses: []
        };

        $scope.openCatalog = function () {
            //placeholder
        };

        $scope.openDatePopup = function (isOpen) {
            $timeout(function () {
                $scope[isOpen] = true;
            });
        };

        $scope.saveData = function (data) {
            $http.post(REST_CONFIG.BASE_URL + '/accounts', data)
                .success(function () {
                    $scope.getAccounts();
                    $scope.registrationStep = 1;
                }).error(errorFactory.resolve);
        };

        $scope.finishSaveData = function (data) {
            $scope.saveData(data);
            $scope.closeAccountModal();
            $scope.registrationStep = 0;
        };

    }])
;