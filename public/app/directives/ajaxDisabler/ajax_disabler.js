'use strict';

angular.module('website.custom.attrs', [])

    .directive('ajaxDisabler', function () {
        return {
            restrict: 'A',
            link: function (scope, elem, attrs) {

                function getNgDisabled(elem) {
                    var ngDisabled = elem.getAttribute('ng-disabled') ? elem.getAttribute('ng-disabled') : elem.getAttribute('data-ng-disabled');
                    return scope.$eval(ngDisabled);
                }

                scope.$watch(attrs.ajaxDisabler, function (value) {
                    var isAlreadyDisabled = elem[0].getAttribute('disabled');
                    var isNgDisabled = getNgDisabled(elem[0]);
                    if (value) {
                        if (!isAlreadyDisabled && !isNgDisabled) elem.attr('disabled', !value);
                        elem.addClass('btn-loading');
                    } else {
                        if (!isAlreadyDisabled && !isNgDisabled) elem[0].removeAttribute('disabled');
                        elem.removeClass('btn-loading');
                    }
                });
            }
        };
    })
;