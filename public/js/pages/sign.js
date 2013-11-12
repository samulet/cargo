'use strict';

angular.module('website.sign', [])

    .controller('signUpController', ['$scope', '$http', 'storageFactory', function ($scope, $http, storageFactory) {
        //
    }])
    .controller('signInController', ['$scope', '$rootScope', '$http', 'storageFactory', function ($scope, $rootScope, $http, storageFactory) {
        $rootScope.pageTitle = 'Вход';

        $scope.alerts = [
            { type: 'danger', msg: 'Oh snap! Change a few things up and try submitting again.' },
            { type: 'success', msg: 'Well done! You successfully read this important alert message.' }
        ];

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