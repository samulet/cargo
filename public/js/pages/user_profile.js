'use strict';

angular.module('website.user.profile', [])

    .controller('userProfileController', ['$scope', '$rootScope', '$http', 'storageFactory', 'errorFactory', 'redirectFactory', function ($scope, $rootScope, $http, storageFactory, errorFactory, redirectFactory) {
        $rootScope.pageTitle = 'Профиль';
        $rootScope.bodyColor = 'filled_bg';

        $scope.editMode = true; //TODO false

        $scope.profileData = {
            fio: "Petrovasiliev Alexander Fargotovitch",
            email: "sads-dfdffsd@dsdsd.ff",
            passportData: "34234r f s sf sdfsdfsd fsdf sd fsd fsd f",
            phones: "324-432, 324-545"
        };

        $scope.startEdit = function () {
            $scope.editMode = true;
        }


    }])
;