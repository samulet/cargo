'use strict';

angular.module('website.dashboard', [])

    .controller('dashboardController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', 'storageFactory', '$modal', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS, storageFactory, $modal) {
        $rootScope.pageTitle = 'dashboard';
        $rootScope.bodyColor = 'filled_bg';
        $scope.firstTimeVisit = false;

        $scope.showPopup = function (){
            $modal.open({
                templateUrl: 'accountModalContent.html',
                controller: 'accountModalController'
            });
        };

        function onError(data, status) {
            if (status === RESPONSE_STATUS.NOT_FOUND) {
                $scope.firstTimeVisit = true;
                $modal.open({
                    templateUrl: 'accountModalContent.html',
                    controller: 'accountModalController'
                });
            } else {
                errorFactory.resolve(data, status, true);
            }
        }

        $scope.getAccounts = function () {
            if (!storageFactory.getAccounts()) {
                $http.get(REST_CONFIG.BASE_URL + '/accounts').
                    success(function (accounts) {
                        storageFactory.setAccounts(accounts);
                    }).error(onError);
            }
        };
    }])

    .controller('accountModalController', ['$scope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', 'storageFactory', function ($scope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS, storageFactory) {

    }])
;