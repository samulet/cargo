'use strict';

angular.module('website.dashboard', [])

    .controller('dashboardController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', 'storageFactory', '$modal', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS, storageFactory, $modal) {
        $rootScope.pageTitle = 'dashboard';
        $rootScope.bodyColor = 'filled_bg';
        var accountModal;

        checkForAccounts();

        function checkForAccounts() {
           // if (!storageFactory.getAccounts()) {//TODO
                getAccounts();
           // }
        }

        /* $scope.messages = [  //TODO Check styles of the alert
         { type: 'danger', msg: 'Oh snap! Change a few things up and try submitting again.' },
         { type: 'success', msg: 'Well done! You successfully read this important alert message.' }
         ];*/

        function openAccountModal() {
            accountModal = $modal.open({
                templateUrl: 'accountModalContent.html',
                backdrop: 'static',
                scope: $scope,
                controller: 'accountModalController',
                resolve: {
                    accountName: function () {
                        return $scope.accountName;
                    }
                }
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
            $http.get(REST_CONFIG.BASE_URL + '/accounts')//TODO should be /accounts
                .success(function (accounts) {
                    //storageFactory.setAccounts(accounts);//TODO
                    console.log(accounts);
                    onError(null, RESPONSE_STATUS.NOT_FOUND);
                }).error(onError);
        }


    }])

    .controller('accountModalController', ['$scope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', 'storageFactory', function ($scope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS, storageFactory) {
        $scope.save = function () {
            $http.post(REST_CONFIG.BASE_URL + '/accounts', {name: $scope.accountName})
                .success(function () {
                    $scope.closeAccountModal();
                    $scope.getAccounts();
                }).error(errorFactory.resolve);
        };
    }])
;