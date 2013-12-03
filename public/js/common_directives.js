'use strict';

angular.module('common.directives', [])

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

    .directive('addPhoneForm', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/templates/addPhoneTemplate.html',
            scope: {
                phones: '=model'
            },
            link: function (scope, elem, attrs) {
                scope.temp = {};

                scope.remove = function (element) {
                    var index = scope.phones.indexOf(element);
                    if (index !== -1) scope.phones.splice(index, 1);
                };

                scope.add = function () {
                    scope.phones.push(scope.temp);
                    scope.temp = {};
                };

            }
        };
    })

    .directive('addAddressForm', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/templates/addAddressTemplate.html',
            scope: {
                addresses: '=model'
            },
            link: function (scope, elem, attrs) {
                scope.temp = {};

                scope.remove = function (element) {
                    var index = scope.addresses.indexOf(element);
                    if (index !== -1) scope.addresses.splice(index, 1);
                };

                scope.add = function () {
                    scope.addresses.push(scope.temp);
                    scope.temp = {};
                };

            }
        };
    })

    .directive('addSiteForm', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/templates/addSiteTemplate.html',
            scope: {
                sites: '=model'
            },
            link: function (scope, elem, attrs) {
                scope.temp = {};

                scope.remove = function (element) {
                    var index = scope.sites.indexOf(element);
                    if (index !== -1) scope.sites.splice(index, 1);
                };

                scope.add = function () {
                    scope.sites.push(scope.temp);
                    scope.temp = {};
                };

            }
        };
    })

    .directive('addEmailForm', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/templates/addEmailTemplate.html',
            scope: {
                emails: '=model'
            },
            link: function (scope, elem, attrs) {
                scope.temp = {};

                scope.remove = function (element) {
                    var index = scope.emails.indexOf(element);
                    if (index !== -1) scope.emails.splice(index, 1);
                };

                scope.add = function () {
                    scope.emails.push(scope.temp);
                    scope.temp = {};
                };

            }
        };
    })
;