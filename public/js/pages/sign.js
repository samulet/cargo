'use strict';

angular.module('website.sign', [])

    .controller('signUpController', ['$scope', '$http', 'storageFactory', function ($scope, $http, storageFactory) {
        //
    }])
    .controller('signInController', ['$scope', '$rootScope', '$http', 'storageFactory', 'errorFactory', 'redirectFactory', 'REST_CONFIG', function ($scope, $rootScope, $http, storageFactory, errorFactory, redirectFactory, REST_CONFIG) {
        $rootScope.pageTitle = 'Вход';

        /*$scope.messages = []; //TODO remove this, when done
         $scope.messages.push(errorFactory.resolve({error: 'CustomError'}, 400));*/

        function onError(data, status) {
            errorFactory.resolve(data, status, true);
        }

        $scope.test = function () {
            $http.get(REST_CONFIG.BASE_URL + '/accounts').
                success(function (data) {
                    console.log(data);
                    debugger;
                }).error(onError);
        }();

        $scope.signIn = function () {
            $http.post('', { //TODO still don't know what the url to login
                email: $scope.signInData.email,
                password: $scope.signInData.password
            }).success(function (data) {
                    storageFactory.setUser(data.user); //TODO hope we should save a user
                    redirectFactory.goDashboard(); //TODO where we go now? Who knows...
                }).error(onError);
        };
    }])
;