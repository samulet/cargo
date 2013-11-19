'use strict';

angular.module('website.user.profile', [])

    .controller('userProfileController', ['$scope', '$rootScope', '$http', 'storageFactory', 'errorFactory', 'redirectFactory', '$timeout', function ($scope, $rootScope, $http, storageFactory, errorFactory, redirectFactory, $timeout) {
        $rootScope.pageTitle = 'Профиль';
        $rootScope.bodyColor = 'filled_bg';

        $scope.editMode = false;
        $scope.showAddPhoneForm = false;
        $scope.showAddAddressForm = false;
        $scope.showAddEmailForm = false;

        var tempData = {
            phone: {},
            address: {},
            site: {},
            email: {}
        };

        $scope.tempData = tempData;

        $scope.profileData = {
            socials: [],
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

        $scope.cancelEdit = function () {
            $scope.editMode = false;
            $scope.showAddPhoneForm = false;
            $scope.showAddAddressForm = false;
            $scope.showAddEmailForm = false;
            $scope.tempData = tempData;
        };

        $scope.saveEdit = function () {
            $http.post('', $scope.profileData)
                .success(function (data) {
                    //storageFactory.setUser(data.user);
                }).error(errorFactory.resolve);
        };

        $scope.openDatePopup = function(isOpen) {
            $timeout(function () {
                $scope[isOpen] = true;
            });
        };

        $scope.today = new Date();

        function getTimestamp(aDate, callback) {
            if (aDate) {
                if (new Date(aDate) !== 'Invalid Date') {
                    aDate = aDate.split("-");
                    var newDate = aDate[2] + "/" + aDate[1] + "/" + aDate[0];
                    return callback(new Date(newDate).getTime());
                } else {
                    var day = aDate.slice(0, 2);
                    var month = aDate.slice(3, 5);
                    var year = aDate.slice(6);
                    var birthDate = new Date(+year, (+month) - 1, +day);
                    if (birthDate !== 'Invalid Date') {
                        return callback((birthDate).getTime());
                    } else {
                        return errorFactory.resolve({error: 'Неверный формат даты'});
                    }
                }
            }
            return callback(null);
        }

    }])
;