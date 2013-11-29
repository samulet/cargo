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
            if (!storageFactory.getAccounts())
                $scope.registrationStep = 0;//TODO should be = 0 at the end and 1 for dev for a while
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

        function onError(data, status) {
            if (status === RESPONSE_STATUS.NOT_FOUND) {
                openAccountModal();
            } else {
                errorFactory.resolve(data, status, true);
            }
        }

        function getAccounts() {
            $http.get(REST_CONFIG.BASE_URL + '/accounts')
                .success(function (accounts) {
                    //  storageFactory.setAccounts(accounts);//TODO
                    openAccountModal();//TODO
                }).error(onError);
        }
    }])

    .controller('registrationModalController', ['$scope', '$http', 'REST_CONFIG', 'errorFactory', '$timeout', function ($scope, $http, REST_CONFIG, errorFactory, $timeout) {
        $scope.juridicData = {};

        $scope.openCatalog = function () {
            //placeholder
        };

        $scope.openDatePopup = function (isOpen) {
            $timeout(function () {
                $scope[isOpen] = true;
            });
        };

        $scope.saveAccountData = function () {
            $http.post(REST_CONFIG.BASE_URL + '/accounts', {name: $scope.account.name})
                .success(function () {
                    $scope.closeAccountModal();
                    $scope.getAccounts();
                    $scope.registrationStep = 1;
                }).error(errorFactory.resolve);
        };
    }])
;