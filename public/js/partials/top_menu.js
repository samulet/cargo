'use strict';

angular.module('website.top.menu', [])

    .directive('topPublicMenu', function () {
        return {
            restrict: 'E',
            /*scope: {
             current: '=current'
             },*/
            templateUrl: 'partials/public/top_menu.html',
            controller: function ($scope) {
                //
            }
        };
    })

    .directive('topPrivateMenu', function () {
        return {
            restrict: 'E',
            /*scope: {
             current: '=current'
             },*/
            templateUrl: 'partials/private/top_menu.html',
            controller: function ($scope) {
                //
            }
        };
    })
;