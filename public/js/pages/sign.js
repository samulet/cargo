'use strict';

angular.module('website.sign', [])

    .controller('signUpController', ['$scope', '$http', 'storageFactory', function ($scope, $http, storageFactory) {
        function onError(data, error) {
            //
        }

        $scope.signUp = function () {
            $http.post('', {
                email: this.signUpData.email,
                password: this.signUpData.password
            }).success(function (data) {
                    alert('success sign up');
                    storageFactory.setUser(data.user);
                }).error(onError);
        };
    }])
;