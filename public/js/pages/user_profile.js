'use strict';

angular.module('website.user.profile', [])

    .controller('userProfileController', ['$scope', '$rootScope', '$http', 'storageFactory', 'errorFactory', 'redirectFactory', function ($scope, $rootScope, $http, storageFactory, errorFactory, redirectFactory) {
        $rootScope.pageTitle = 'Профиль';
        $rootScope.bodyColor = 'filled_bg';

        $scope.editMode = true; //TODO false

        $scope.profileData = {
            tempPhone: {},
            social: {},
            personal: {},
            passport: {},
            phones: [],
            other: {}
        };

        $scope.startEdit = function () {
            $scope.editMode = true;
        };

        $scope.addPhone = function () {
            $scope.profileData.phones.push($scope.profileData.tempPhone);
            $scope.profileData.tempPhone = {};
        };

        $scope.removePhone = function (phone) {
            var index = $scope.profileData.phones.indexOf(phone);
            if (index !== -1) {
                $scope.profileData.phones.splice(index, 1);
            }


        }


    }])
;