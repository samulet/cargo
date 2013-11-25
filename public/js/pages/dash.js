'use strict';

angular.module('website.dash', [])

    .controller('dashController', ['$scope', '$rootScope', function ($scope, $rootScope) {
        $rootScope.pageTitle = 'dash';
    }])
;