'use strict';

angular.module('website.dashboard', [])

    .controller('dashboardController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS) {
        $rootScope.pageTitle = 'dashboard';
        $rootScope.bodyColor = 'filled_bg';
        $scope.firstTimeVisit = true; //TODO false

        function onError(data, status) {
            if (status === RESPONSE_STATUS.NOT_FOUND) {
                $scope.firstTimeVisit = true;
            } else {
                errorFactory.resolve(data, status, true);
            }
        }

        $scope.getAccounts = function () {
            $http.get(REST_CONFIG.BASE_URL + '/accounts').
                success(function (data) {

                }).error(onError);
        };


    }])
;