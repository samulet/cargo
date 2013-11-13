'use strict';

angular.module('website.user.profile', [])

    .controller('userProfileController', ['$scope', '$rootScope', '$http', 'storageFactory', 'errorFactory', 'redirectFactory', function ($scope, $rootScope, $http, storageFactory, errorFactory, redirectFactory) {
        $rootScope.pageTitle = 'Профиль';
        $scope.editMode = false;

        $scope.profileData = {
            fio: "-",
            email: "-",
            passportData: "-",
            phones: "-"
        }


    }])
;