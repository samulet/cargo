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

        $scope.progress = 0;
        $scope.avatar = '';

        $scope.sendFile = function (element) {

            var $form = $(element).parents('form');

            if ($(element).val() == '') {
                return false;
            }

            $form.attr('action', $scope.action);

            $scope.$apply(function () {
                $scope.progress = 0;
            });

            $form.ajaxSubmit({
                type: 'POST',
                uploadProgress: function (event, position, total, percentComplete) {

                    $scope.$apply(function () {
                        // upload the progress bar during the upload
                        $scope.progress = percentComplete;
                    });

                },
                error: function (event, statusText, responseText, form) {

                    // remove the action attribute from the form
                    $form.removeAttr('action');

                    /*
                     handle the error ...
                     */

                },
                success: function (responseText, statusText, xhr, form) {

                    var ar = $(element).val().split('\\'),
                        filename = ar[ar.length - 1];

                    // remove the action attribute from the form
                    $form.removeAttr('action');

                    $scope.$apply(function () {
                        $scope.avatar = filename;
                    });

                }
            });

        }

    }])
;