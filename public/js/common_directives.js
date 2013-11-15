'use strict';

angular.module('common.directives', [])

    .directive('alert', function () {
        return {
            restrict: 'EA',
            templateUrl: "",
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
                action: '@'
            },
            controller: ['$scope', function ($scope) {

                // controller:
                // here you should define properties and methods
                // used in the directive's scope

            }],
            link: function (scope, elem, attrs, ctrl) {
                elem.find('.fake-uploader').click(function () {
                    elem.find('input[type="file"]').click();
                });
            },
            replace: false,
            templateUrl: 'uploader.html'
        };

    }]);