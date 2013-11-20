'use strict';

angular.module('website.top.menu', [])

    .directive('topPublicMenu', function () {
        return {
            restrict: 'E',
            /*scope: {
             current: '=current'
             },*/
            templateUrl: 'html/partials/public/top_menu.html',
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
            templateUrl: 'html/partials/private/top_menu.html',
            controller: function ($scope) {
                //
            }
        };
    })
;