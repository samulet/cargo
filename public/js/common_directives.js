'use strict';

angular.module('common.directives', [])

    .directive('alert', function () {
        return {
            restrict: 'EA',
            templateUrl: 'html/templates/alert.html',
            transclude: true,
            replace: true,
            scope: {
                type: '=',
                close: '&'
            },
            link: function (scope, iElement, iAttrs) {
                scope.closeable = "close" in iAttrs;
            }
        };
    })

    .directive('uploader', [function () {
        return {
            restrict: 'E',
            scope: {

            },
            link: function (scope, elem, attrs, ctrl) {

            },
            replace: false,
            templateUrl: 'html/templates/uploader.html'
        };
    }])
;