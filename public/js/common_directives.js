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

    .directive('ajaxDisabler', function () {
        return {
            restrict: 'A',
            link: function (scope, elem, attrs) {
                scope.$watch(attrs.ajaxDisabler, function (value) {
                    var isAlreadyDisabled = elem[0].getAttribute('disabled');
                    var ngDisabled = elem[0].getAttribute('data-ng-disabled');
                    if (value) {
                        if (!isAlreadyDisabled) elem.attr('disabled', !value);
                        elem.addClass('btn-loading');
                    } else {
                        if (!isAlreadyDisabled && !ngDisabled) elem[0].removeAttribute('disabled');
                        if (ngDisabled) {
                            var isNgDisabled = scope.$eval(ngDisabled);
                            if (isNgDisabled) {
                                elem.attr('disabled', !value);
                            } else {
                                elem[0].removeAttribute('disabled');
                            }
                        }
                        elem.removeClass('btn-loading');
                    }
                });
            }
        };
    })
;