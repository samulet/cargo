'use strict';

angular.module('website.user.profile', [])

    .controller('userProfileController', ['$scope', '$rootScope', '$http', 'storageFactory', 'errorFactory', 'redirectFactory', function ($scope, $rootScope, $http, storageFactory, errorFactory, redirectFactory) {
        $rootScope.pageTitle = 'Профиль';
        $rootScope.bodyColor = 'filled_bg';

        $scope.editMode = true; //TODO false
        $scope.showAddPhoneForm = false;
        $scope.showAddAddressForm = false;
        $scope.showAddEmailForm = false;

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
            sites: [],
            emails: [],
            photo: {},
            eSignature: {},
            other: {}
        };

        $scope.startEdit = function () {
            $scope.editMode = true;
        };

        function moveToProfileData(itemFrom, itemTo) {
            $scope.profileData[itemTo].push($scope.tempData[itemFrom]);
            $scope.tempData[itemFrom] = {};
        }

        $scope.addPhone = function () {
            moveToProfileData('phone', 'phones');
        };

        $scope.addEmail = function () {
            moveToProfileData('email', 'emails');
        };

        $scope.addAddress = function () {
            moveToProfileData('address', 'addresses');
        };

        $scope.addSite = function () {
            moveToProfileData('site', 'sites');
        };

        $scope.remove = function (from, element) {
            var index = $scope.profileData[from].indexOf(element);
            if (index !== -1) $scope.profileData[from].splice(index, 1);
        };

    }])
;