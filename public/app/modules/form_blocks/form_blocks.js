'use strict';

angular.module('website.form.blocks', [])

    .directive('addCompanyWizard', function () {
        return {
            restrict: 'E',
            templateUrl: 'app/modules/form_blocks/addCompanyWizardTemplate.html',
            scope: {
                companyData: '=model',
                account: '=account',
                modal: '=modal',
                close: '&close'
            },
            controller: function ($scope, $http, REST_CONFIG, errorFactory, $timeout, $filter) {
                $scope.today = new Date();
                $scope.wizardStep = 0;
                $scope.companyData = {
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
                    misc: {},
                    okved: [],
                    licenses: [],
                    applicants: [],
                    tax: {},
                    persons: []
                };

                $scope.closable = $scope.close() ? true : false;

                $scope.openCatalog = function () {
                    //placeholder
                };

                $scope.openDatePopup = function (isOpen) {
                    $timeout(function () {
                        $scope[isOpen] = true;
                    });
                };

                $scope.nextStep = function () {
                    $scope.wizardStep++;
                };

                $scope.prevStep = function () {
                    $scope.wizardStep--;
                };

                $scope.saveData = function () {
                    prepareDatesFormat();
                    $http.post(REST_CONFIG.BASE_URL + '/accounts/' + $scope.account.account_uuid + '/companies', $scope.companyData)
                        .success(function () {
                            if ($scope.modal) {
                                $scope.modal.close();
                            }
                            $scope.wizardStep = -1;
                        }).error(function (data, status) {
                            errorFactory.resolve(data, status);
                        }
                    );
                };

                function prepareDatesFormat() {
                    if ($scope.companyData.pfr.date_registration) $scope.companyData.pfr.date_registration = getTimestamp($scope.companyData.pfr.date_registration);
                    if ($scope.companyData.fms.date_registration) $scope.companyData.fms.date_registration = getTimestamp($scope.companyData.fms.date_registration);
                    if ($scope.companyData.misc.documentDate) $scope.companyData.misc.documentDate = getTimestamp($scope.companyData.misc.documentDate);
                    for (var k in $scope.companyData.tax) {
                        if ($scope.companyData.tax.hasOwnProperty(k)) {
                            if ($scope.companyData.tax[k].date_accounting) $scope.companyData.tax[k].date_accounting = getTimestamp($scope.companyData.fms.date_accounting);
                            if ($scope.companyData.tax[k].date_registration) $scope.companyData.tax[k].date_registration = getTimestamp($scope.companyData.fms.date_registration);
                        }
                    }
                }

                function getTimestamp(date) {
                    return (new Date($filter('date', 'dd.MM.yyy')(date)).getTime() / 1000);
                }
            }
        };
    })

    .directive('addPhoneForm', function () {
        return {
            restrict: 'E',
            templateUrl: 'app/modules/form_blocks/addPhoneTemplate.html',
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
            templateUrl: 'app/modules/form_blocks/addAddressTemplate.html',
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
            templateUrl: 'app/modules/form_blocks/addSiteTemplate.html',
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
            templateUrl: 'app/modules/form_blocks/addEmailTemplate.html',
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
            templateUrl: 'app/modules/form_blocks/addFounderTemplate.html',
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
            templateUrl: 'app/modules/form_blocks/addAuthorizedPersonTemplate.html',
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
            templateUrl: 'app/modules/form_blocks/addLicenseTemplate.html',
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
            templateUrl: 'app/modules/form_blocks/addApplicantsTemplate.html',
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
            templateUrl: 'app/modules/form_blocks/addPersonsTemplate.html',
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
            templateUrl: 'app/modules/form_blocks/addOkvedTemplate.html',
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
                    var okved = scope.temp.partZero + scope.temp.partFirst +
                        '.' + scope.temp.partSecond + scope.temp.partThird +
                        '.' + scope.temp.partFourth + scope.temp.partFifth;
                    scope.okveds.push(okved);
                    scope.temp = {};
                };
            }
        };
    })

    .directive('addBankAccountForm', function () {
        return {
            restrict: 'E',
            templateUrl: 'app/modules/form_blocks/addBankAccountsTemplate.html',
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