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

    .directive('addJuridicWizard', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/templates/addJuridicWizardTemplate.html',
            scope: {
                juridicData: '=model',
                isShowIndicator: '=show'
            },
            link: function (scope, elem, attrs) {
                scope.temp = {};
            },
            controller: function ($scope, $http, REST_CONFIG, errorFactory, $timeout) {
                $scope.today = new Date();

                $scope.juridicData = {
                    contacts: {
                        phones: [],
                        emails: [],
                        sites: [],
                        addresses: []
                    },
                    founders: [],
                    accounts: [],
                    authorized_persons: [],
                    pfr: {},
                    fms: {},
                    okved: [],
                    licenses: [],
                    applicants: [],
                    tax: {},
                    persons: []
                };

                $scope.openCatalog = function () {
                    //placeholder
                };

                $scope.openDatePopup = function (isOpen) {
                    $timeout(function () {
                        $scope[isOpen] = true;
                    });
                };

                $scope.nextStep = function () {
                    $scope.addJuridicStep++;
                };

                $scope.prevStep = function () {
                    $scope.addJuridicStep--;
                };

                $scope.saveData = function (isLastStep) {
                    if (isLastStep) {
                        $http.post(REST_CONFIG.BASE_URL + '/accounts/' + $scope.firstAccount['account_uuid'] + '/companies', $scope.juridicData)
                            .success(function () {
                                $scope.closeAccountModal();
                                $scope.addJuridicStep = 0;
                            }).error(errorFactory.resolve);
                    } else {
                        $scope.nextStep();
                    }
                };
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

    .directive('addFounderForm', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/templates/addFounderTemplate.html',
            scope: {
                founders: '=model'
            },
            link: function (scope, elem, attrs) {
                scope.temp = {};

                scope.remove = function (element) {
                    var index = scope.founders.indexOf(element);
                    if (index !== -1) scope.founders.splice(index, 1);
                };

                scope.add = function () {
                    scope.founders.push(scope.temp);
                    scope.temp = {};
                };
            }
        };
    })

    .directive('addAuthorizedPersonForm', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/templates/addAuthorizedPersonTemplate.html',
            scope: {
                authorizedPersons: '=model'
            },
            link: function (scope, elem, attrs) {
                scope.temp = {};

                scope.remove = function (element) {
                    var index = scope.authorizedPersons.indexOf(element);
                    if (index !== -1) scope.authorizedPersons.splice(index, 1);
                };

                scope.add = function () {
                    scope.authorizedPersons.push(scope.temp);
                    scope.temp = {};
                };
            }
        };
    })

    .directive('addLicenseForm', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/templates/addLicenseTemplate.html',
            scope: {
                licenses: '=model'
            },
            link: function (scope, elem, attrs) {
                scope.temp = {};
                scope.today = new Date();

                scope.remove = function (element) {
                    var index = scope.licenses.indexOf(element);
                    if (index !== -1) scope.licenses.splice(index, 1);
                };

                scope.add = function () {
                    scope.licenses.push(scope.temp);
                    scope.temp = {};
                };

                scope.openDatePopup = function (isOpen) {
                    /* $timeout(function () { //TODO add $timeout
                     scope[isOpen] = true;
                     });*/
                };
            }
        };
    })

    .directive('addApplicantsForm', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/templates/addApplicantsTemplate.html',
            scope: {
                applicants: '=model'
            },
            link: function (scope, elem, attrs) {
                scope.temp = {};

                scope.remove = function (element) {
                    var index = scope.applicants.indexOf(element);
                    if (index !== -1) scope.applicants.splice(index, 1);
                };

                scope.add = function () {
                    scope.applicants.push(scope.temp);
                    scope.temp = {};
                };
            }
        };
    })

    .directive('addPersonsForm', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/templates/addPersonsTemplate.html',
            scope: {
                persons: '=model'
            },
            link: function (scope, elem, attrs) {
                scope.temp = {};

                scope.remove = function (element) {
                    var index = scope.persons.indexOf(element);
                    if (index !== -1) scope.persons.splice(index, 1);
                };

                scope.add = function () {
                    scope.persons.push(scope.temp);
                    scope.temp = {};
                };
            }
        };
    })

    .directive('addOkvedForm', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/templates/addOkvedTemplate.html',
            scope: {
                okveds: '=model'
            },
            link: function (scope, elem, attrs) {
                scope.temp = {};

                scope.remove = function (element) {
                    var index = scope.okveds.indexOf(element);
                    if (index !== -1) scope.okveds.splice(index, 1);
                };

                scope.add = function () {
                    var okved = scope.temp.partZero + scope.temp.partFirst + '.'
                        + scope.temp.partSecond + scope.temp.partThird + '.'
                        + scope.temp.partFourth + scope.temp.partFifth;
                    scope.okveds.push(okved);
                    scope.temp = {};
                };
            }
        };
    })

    .directive('addBankAccountForm', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/templates/addBankAccountsTemplate.html',
            scope: {
                bankAccounts: '=model'
            },
            link: function (scope, elem, attrs) {
                scope.temp = {};

                scope.remove = function (element) {
                    var index = scope.bankAccounts.indexOf(element);
                    if (index !== -1) scope.bankAccounts.splice(index, 1);
                };

                scope.add = function () {
                    scope.bankAccounts.push(scope.temp);
                    scope.temp = {};
                };
            }
        };
    })
;