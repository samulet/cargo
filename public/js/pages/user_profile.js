'use strict';

angular.module('website.user.profile', [])

    .controller('userProfileController', ['$scope', '$rootScope', '$http', 'storageFactory', 'errorFactory', 'redirectFactory', function ($scope, $rootScope, $http, storageFactory, errorFactory, redirectFactory) {
        $rootScope.pageTitle = 'Профиль';
        $rootScope.bodyColor = 'filled_bg';

        $scope.editMode = true; //TODO false

        $scope.tempData = {
            phone: {},
            address: {},
            email: {}
        };

        $scope.profileData = {
            social: {},
            personal: {},
            passport: {},
            phones: [],
            addresses: [],
            other: {}
        };

        $scope.startEdit = function () {
            $scope.editMode = true;
        };

        $scope.addPhone = function () {
            $scope.profileData.phones.push($scope.tempData.phone);
            $scope.tempData.phone = {};
        };

        $scope.addAddress = function () {
            $scope.profileData.addresses.push($scope.tempData.address);
            $scope.tempData.address = {};
        };

        $scope.removePhone = function (phone) {
            var index = $scope.profileData.phones.indexOf(phone);
            if (index !== -1) $scope.profileData.phones.splice(index, 1);
        };

        $scope.removeAddress = function (address) {
            var index = $scope.profileData.addresses.indexOf(address);
            if (index !== -1) $scope.profileData.addresses.splice(index, 1);
        };

    }])
;