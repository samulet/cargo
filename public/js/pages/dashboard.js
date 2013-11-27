'use strict';

angular.module('website.dashboard', [])

    .controller('dashboardController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', 'storageFactory', '$modal', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS, storageFactory, $modal) {
        $rootScope.pageTitle = 'dashboard';
        $rootScope.bodyColor = 'filled_bg';
        var accountModal;

        getAccounts();

        /* $scope.messages = [  //TODO Check styles of the alert
         { type: 'danger', msg: 'Oh snap! Change a few things up and try submitting again.' },
         { type: 'success', msg: 'Well done! You successfully read this important alert message.' }
         ];*/

        function openAccountModal() {
            accountModal = $modal.open({
                templateUrl: 'accountModalContent.html',
                backdrop: 'static'
            });
        }

        function closeAccountModal() {
            accountModal.close();
        }

        function onError(data, status) {
            if (status === RESPONSE_STATUS.NOT_FOUND) {
                openAccountModal();
            } else {
                errorFactory.resolve(data, status, true);
            }
        }

        function getAccounts() {
            if (!storageFactory.getAccounts()) {
                $http.get(REST_CONFIG.BASE_URL + '/accountszzz')//TODO should be /accounts
                    .success(function (accounts) {
                        storageFactory.setAccounts(accounts);
                    }).error(onError);
            }
        }

        $scope.save = function () { //TODO cannot reach from html
            closeAccountModal();
            $http.post(REST_CONFIG.BASE_URL + '/accounts', {name: $scope.accountName})
             .success(function () {
             closeAccountModal();
             getAccounts();
             }).error(errorFactory.resolve);
        };

    }])
;