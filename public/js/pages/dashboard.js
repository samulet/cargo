'use strict';

angular.module('website.dashboard', [])

    .controller('dashboardController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', 'storageFactory', '$modal', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS, storageFactory, $modal) {
        $rootScope.pageTitle = 'dashboard';
        $rootScope.bodyColor = 'filled_bg';
        var accountModal;
        $scope.account = [];

        checkForAccounts();

        function checkForAccounts() {
            if (!storageFactory.getAccounts()) {
                getAccounts();
            }
        }

        function openAccountModal() {
            accountModal = $modal.open({
                templateUrl: 'accountModalContent.html',
                backdrop: 'static',
                scope: $scope,
                controller: 'accountModalController'
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
                    storageFactory.setAccounts(accounts);
                }).error(onError);
        }
    }])

    .controller('accountModalController', ['$scope', '$http', 'REST_CONFIG', 'errorFactory', function ($scope, $http, REST_CONFIG, errorFactory) {
        $scope.save = function () {
            $http.post(REST_CONFIG.BASE_URL + '/accounts', {name: $scope.account.name})
                .success(function () {
                    $scope.closeAccountModal();
                    $scope.getAccounts();
                }).error(errorFactory.resolve);
        };
    }])
;