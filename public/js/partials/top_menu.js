;
'use strict';

angular.module('website.top.menu', [])

    .directive('topPublicMenu', function () {
        return {
            restrict: 'A',
            /*scope: {
             current: '=current'
             },*/
            templateUrl: 'partials/public/top_menu.html',
            controller: function ($scope) {
                //
            }
        };
    })
;