'use strict';

angular.module('website.top.menu', [])

    .directive('topPublicMenu', function () {
        return {
            restrict: 'A',
            /*scope: {
             current: '=current'
             },*/
            templateUrl: 'public/partials/public/top_menu.html',
            controller: function ($scope) {
                //
            }
        };
    })

    .directive('topPrivateMenu', function () {
        return {
            restrict: 'A',
            /*scope: {
             current: '=current'
             },*/
            templateUrl: 'public/partials/private/top_menu.html',
            controller: function ($scope) {
                //
            }
        };
    })
;