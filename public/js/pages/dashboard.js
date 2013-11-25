'use strict';

angular.module('website.dashboard', [])

    .controller('dashboardController', ['$scope', '$rootScope', function ($scope, $rootScope) {
        $rootScope.pageTitle = 'dash';
    }])
;