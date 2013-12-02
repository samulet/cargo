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
            link: function (scope, elem, attrs) {
                scope.showAddForm = false;
                scope.phones = attrs.model;
                scope.tempPhone = {};

                scope.remove = function (from, element) {
                    var index = scope.phones[from].indexOf(element);
                    if (index !== -1) scope.phones[from].splice(index, 1);
                };

                scope.addPhone = function () {
                    scope.phones.push(scope.tempPhone);
                    scope.tempPhone = {};
                };

            }
        };
    })

    .directive('addAddressForm', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/templates/addAddressTemplate.html',
            link: function (scope, elem, attrs) {
                scope.showAddForm = false;
                scope.addresses = attrs.model;
                scope.tempAddress = {};

                scope.remove = function (from, element) {
                    var index = scope.addresses[from].indexOf(element);
                    if (index !== -1) scope.addresses[from].splice(index, 1);
                };

                scope.addAddress = function () {
                    scope.addresses.push(scope.tempAddress);
                    scope.tempAddress = {};
                };

            }
        };
    })

    .directive('addSiteForm', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/templates/addSiteTemplate.html',
            link: function (scope, elem, attrs) {
                scope.showAddForm = false;
                scope.sites = attrs.model;
                scope.tempSite = {};

                scope.remove = function (from, element) {
                    var index = scope.sites[from].indexOf(element);
                    if (index !== -1) scope.sites[from].splice(index, 1);
                };

                scope.addAddress = function () {
                    scope.sites.push(scope.tempSite);
                    scope.tempSite = {};
                };

            }
        };
    })

    .directive('addEmailForm', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/templates/addEmailTemplate.html',
            link: function (scope, elem, attrs) {
                scope.showAddForm = false;
                scope.emails = attrs.model;
                scope.tempEmail = {};

                scope.remove = function (from, element) {
                    var index = scope.emails[from].indexOf(element);
                    if (index !== -1) scope.emails[from].splice(index, 1);
                };

                scope.addAddress = function () {
                    scope.emails.push(scope.tempEmail);
                    scope.tempEmail = {};
                };

            }
        };
    })
;