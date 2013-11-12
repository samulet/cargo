'use strict';

angular.module('website.sign', [])

    .controller('signUpController', ['$scope', '$http', 'storageFactory', function ($scope, $http, storageFactory) {
        //
    }])
    .controller('signInController', ['$scope', '$rootScope', '$http', 'storageFactory', 'errorFactory', function ($scope, $rootScope, $http, storageFactory, errorFactory) {
        $rootScope.pageTitle = 'Вход';

        /*$scope.messages = []; //TODO remove this, when done
        $scope.messages.push(errorFactory.resolve({error: 'CustomError'}, 400));*/

        function onError(data, status) {
            errorFactory.resolve(data, status, true);
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