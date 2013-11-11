'use strict';

angular.module('website.sign', [])

    .controller('signUpController', ['$scope', '$http', 'storageFactory', function ($scope, $http, storageFactory) {
        //
    }])
    .controller('signInController', ['$scope', '$rootScope', '$http', 'storageFactory', function ($scope, $rootScope, $http, storageFactory) {
        $rootScope.pageTitle = 'Вход';

        function onError(data, error) {
            //
        }

        $scope.signIn = function () {
            $http.post('', {
                email: this.signInData.email,
                password: this.signInData.password
            }).success(function (data) {
                    storageFactory.setUser(data.user);
                }).error(onError);
        };
    }])
;