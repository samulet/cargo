'use strict';

angular.module('website.user.profile', [])

    .controller('userProfileController', ['$scope', '$rootScope', '$http', 'storageFactory', 'errorFactory', 'redirectFactory', function ($scope, $rootScope, $http, storageFactory, errorFactory, redirectFactory) {
        $rootScope.pageTitle = 'Профиль';
        $rootScope.bodyColor = 'filled_bg';

        $scope.editMode = true; //TODO false

        $scope.tempData = {
            phone: {},
            address: {},
            site: {},
            email: {}
        };

        $scope.profileData = {
            social: {},
            personal: {},
            passport: {},
            phones: [],
            addresses: [],
            sites: {},
            emails: {},
            photo: {},
            eSignature: {},
            other: {}
        };

        $scope.startEdit = function () {
            $scope.editMode = true;
        };

        $scope.moveToProfileData = function (item) {
            $scope.profileData[item].push($scope.tempData[item]);
            $scope.tempData[item] = {};
        };

        $scope.remove = function (from, element) {
            var index = $scope.profileData[from].indexOf(element);
            if (index !== -1) $scope.profileData[from].splice(index, 1);
        };

    }])
;