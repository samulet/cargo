'use strict';

angular.module('common.directives', [])

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