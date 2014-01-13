'use strict';

angular.module('website', [
        'ngRoute',
        'ngAnimate',
        'ui.bootstrap',
        'env.config',
        'website.constants',
        'website.top.menu',
        'website.sign',
        'website.user.profile',
        'website.user.param',
        'website.dashboard',
        'website.account',
        'website.public.offer',
        'website.page.errors',
        'website.custom.attrs',
        'website.storage',
        'website.error',
        'website.redirect',
        'website.cookies',
        'ui.select2',
        'ngGrid',
        'website.catalogue'
    ])
    .config(['$routeProvider', '$httpProvider', '$locationProvider', 'ACCESS_LEVEL', 'ROUTES', function ($routeProvider, $httpProvider, $locationProvider, ACCESS_LEVEL, ROUTES) {
        var pathToIncs = 'html/pages/';
        $routeProvider.when(ROUTES.START_PAGE, {redirectTo: ROUTES.DASHBOARD});
        $routeProvider.when(ROUTES.START_PAGE_ALT, {redirectTo: ROUTES.DASHBOARD});
        $routeProvider.when(ROUTES.NOT_FOUND, {templateUrl: pathToIncs + 'errors/404.html', access: ACCESS_LEVEL.PUBLIC});
        $routeProvider.when(ROUTES.USER_PROFILE, {templateUrl: pathToIncs + 'user_profile/user_profile.html', access: ACCESS_LEVEL.AUTHORIZED});
        $routeProvider.when(ROUTES.DASHBOARD, {templateUrl: pathToIncs + 'dashboard/dashboard.html', access: ACCESS_LEVEL.AUTHORIZED});
        $routeProvider.when(ROUTES.ACCOUNT, {templateUrl: pathToIncs + 'account/account.html', access: ACCESS_LEVEL.AUTHORIZED});
        $routeProvider.when(ROUTES.PUBLIC_OFFER, {templateUrl: pathToIncs + 'public_offer/public_offer.html', access: ACCESS_LEVEL.PUBLIC});

        $routeProvider.otherwise({redirectTo: ROUTES.DASHBOARD});

        $locationProvider.html5Mode(false);
        $locationProvider.hashPrefix('!');

        var interceptor = ['$location', '$q', '$rootScope', function ($location, $q, $rootScope) {
            return {
                'request': function (config) {
                    $rootScope.isAjaxLoading = true;
                    return config || $q.when(config);
                },
                'response': function (response) {
                    $rootScope.isAjaxLoading = false;
                    return response || $q.when(response);
                },
                'responseError': function (rejection) {
                    $rootScope.isAjaxLoading = false;
                    return $q.reject(rejection);
                }
            };
        }];

        $httpProvider.interceptors.push(interceptor);

    }])
    .run(['$rootScope', 'ACCESS_LEVEL', 'ROUTES', 'cookiesFactory', 'redirectFactory', 'storageFactory', '$http', 'userParamsFactory', function ($rootScope, ACCESS_LEVEL, ROUTES, cookiesFactory, redirectFactory, storageFactory, $http, userParamsFactory) {
        $rootScope.ROUTES = ROUTES;
        $rootScope.isAjaxLoading = false;
        $rootScope.messages = [];

        userParamsFactory.getApiRoutes();

        $rootScope.$watch(function () {
            return localStorage.getItem(storageFactory.storage.local.selectedAccount);
        }, function (newValue) {
            if (newValue) {
                $http.defaults.headers.common['X-App-Account'] = JSON.parse(newValue).account_uuid;
            }
        });

        $rootScope.$watch(function () {
            return localStorage.getItem(storageFactory.storage.local.selectedCompany);
        }, function (newValue) {
            if (newValue) {
                $http.defaults.headers.common['X-App-Company'] = JSON.parse(newValue).uuid;
            }
        });

        $rootScope.$on("$routeChangeStart", function (event, next, current) {
            var isToken = !!storageFactory.getToken();
            if (isToken) {
                $http.defaults.headers.common['X-Auth-UserToken'] = storageFactory.getToken();
            } else {
                redirectFactory.goSignIn();
            }
        });

        userParamsFactory.prepareUser();
    }])
;

angular.module('website.constants', [])
    .constant('RESPONSE_STATUS', {
        OK: 200,
        CREATED: 201,
        ACCEPTED: 202,
        NO_CONTENT: 204,
        NOT_MODIFIED: 304,
        BAD_REQUEST: 400,
        UNAUTHORIZED: 401,
        FORBIDDEN: 403,
        NOT_FOUND: 404,
        METHOD_NOT_ALLOWED: 405,
        PROXY_AUTHENTICATION_REQUIRED: 407,
        UNPROCESSABLE_ENTITY: 422,
        INTERNAL_SERVER_ERROR: 500
    })
    .constant('ACCESS_LEVEL', {
        PUBLIC: 0,
        AUTHORIZED: 1,
        ADMIN: 2
    })
    .constant('ROUTES', {
        START_PAGE: '/',
        START_PAGE_ALT: '',
        DASHBOARD: '/dashboard',
        ACCOUNT: '/account',
        PUBLIC_OFFER: '/public/offer',
        USER_PROFILE: '/user/profile',
        LOGOUT: '/user/logout',
        NOT_FOUND: '/404'
    })
    .constant('MESSAGES', {
        ERROR: {
            UNAUTHORIZED: 'Не удалось авторизироваться',
            INTERNAL_SERVER_ERROR: 'Внутренняя ошибка сервера',
            UNKNOWN_ERROR: 'Неизвестная ошибка, попробуйте позже',
            CANNOT_BE_DONE_ERROR: 'Невозможно выполнить операцию, попробуйте позже'
        }
    })
;
'use strict';

angular.module('catalogue', [])

    .directive('website.catalogue', function () {
        return {
            restrict: 'A',
            scope: {
                getData: '=catalogue',
            },
            controller: function ($scope, $modal) {
                $scope.details = {};

                function openCatalogueModal() {
                    $scope.isCatalogueModalOpened = true;
                    $scope.catalogueModal = $modal.open({
                        templateUrl: 'catalogueModalContent.html',
                        backdrop: 'static',
                        scope: $scope,
                        controller: 'catalogueModalController'
                    });
                }

                $scope.openCatalogue = function () {
                    if ($scope.details.value) {
                        $scope.selectedModel = $scope.details.value;
                        $scope.selectedOption = $scope.details;
                    }
                    openCatalogueModal();
                };

                function closeCatalogueModal() {
                    $scope.catalogueModal.close();
                }

                $scope.closeCatalogue = function () {
                    closeCatalogueModal();
                };
            },
            compile: function (scope, element) {
                return function (scope, elem) {
                    scope.catalogueElement = element;
                    element.$$element[0].setAttribute('readonly', 'true');
                    element.$$element.on('click', function (event) {
                        event.preventDefault();
                        scope.openCatalogue();
                    });
                };
            }
        };
    })

    .directive('catalogueModal', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/templates/catalog.html'
        };
    })

    .controller('catalogueModalController', ['$scope', function ($scope) {

        if ($scope.isCatalogueModalOpened) {
            $scope.data = $scope.getData();
        }

        function updateOptionDetails(option) {
            $scope.details.firstName = option.firstName;
            $scope.details.lastName = option.lastName;
            $scope.details.age = option.age;
            $scope.details.value = option.value;
            $scope.details.description = option.description;
        }

        function findSelectedOption(value) {
            for (var i = 0; i <= $scope.data.length - 1; i++) {
                if (value && $scope.data[i].value === +value) {
                    return $scope.data[i];
                }
            }
            return null;
        }

        $scope.changeSelectedOption = function (value) {
            if (value) {
                $scope.selectedOption = findSelectedOption(value);
                updateOptionDetails($scope.selectedOption);
            }
        };

        $scope.setSelectedOptions = function () {
            if ($scope.selectedOption) {
                $scope.catalogueElement.$$element[0].value = $scope.selectedOption.description;
            } else {
                $scope.catalogueElement.$$element[0].value = "";
            }
            $scope.closeCatalogue();
        };
    }])
;
"use strict";

 angular.module("env.config", [])

.constant("WEB_CONFIG", {
  "PROTOCOL": "http",
  "HOST": "cargo",
  "HOST_CONTEXT": "",
  "PORT": "8000",
  "DOMAIN": "cargo.dev",
  "BASE_URL": "http://cargo.dev:8000"
})

.constant("REST_CONFIG", {
  "PROTOCOL": "http",
  "HOST": "cargo",
  "HOST_CONTEXT": "/api",
  "PORT": "8000",
  "DOMAIN": "cargo.dev",
  "BASE_URL": "http://cargo.dev:8000/api"
})

;
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

    .directive('addCompanyWizard', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/templates/addCompanyWizardTemplate.html',
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
                        if ($scope.companyData.tax[k].date_accounting) $scope.companyData.tax[k].date_accounting = getTimestamp($scope.companyData.fms.date_accounting);
                        if ($scope.companyData.tax[k].date_registration) $scope.companyData.tax[k].date_registration = getTimestamp($scope.companyData.fms.date_registration);
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

    .directive("modalShow", function ($parse) {
        return {
            restrict: "A",
            link: function (scope, element, attrs) {

                scope.showModal = function (visible, elem) {
                    if (!elem)
                        elem = element;

                    if (visible)
                        elem.modal("show");
                    else
                        elem.modal("hide");
                };

                scope.$watch(attrs.modalShow, function (newValue, oldValue) {
                    scope.showModal(newValue, attrs.$$element);
                });

                element.bind("hide.bs.modal", function () {
                    $parse(attrs.modalShow).assign(scope, false);
                    if (!scope.$$phase && !scope.$root.$$phase)
                        scope.$apply();
                });
            }
        };
    })
;
'use strict';

angular.module('common.factories', [
        'website.constants'
    ])
    .factory('storageFactory', ['$http', 'cookiesFactory', '$rootScope', function ($http, cookiesFactory, $rootScope) {
        var storage = {
            cookie: {
                token: 'token',
                sessionId: 'PHPSESSID'
            },
            local: {
                accounts: 'accounts',
                apiRoutes: 'api_routes',
                user: 'user',
                selectedAccount: 'selected_account',
                selectedCompany: 'selected_company'
            },
            rootScope: {
                companies: 'companies',
                catalogues: 'catalogues',
                places: 'places'
            }
        };

        function get(key) {
            var value = localStorage.getItem(key);
            return value ? JSON.parse(value) : null;
        }

        function getCookie(key) {
            return cookiesFactory.getItem(key);
        }

        function setValueForSession(key, value) {
            $rootScope[key] = value;
        }

        function getSessionValue(key) {
            return $rootScope[key];
        }

        function addCookie(key, value, expires, secure) {
            return cookiesFactory.setItem(key, value, expires, secure);
        }

        function removeCookie(key) {
            return cookiesFactory.removeItem(key);
        }

        function set(key, value) {
            if (!key) throw "Invalid key for a value: " + value;
            if (!value) throw "Invalid value for a key :" + key;
            localStorage.setItem(key, JSON.stringify(value));
        }

        function remove(key) {
            return localStorage.removeItem(key);
        }

        return { //TODO add fallback to cookies
            storage: storage,
            getApiRoutes: function () {
                return get(storage.local.apiRoutes);
            },
            setApiRoutes: function (routes) {
                set(storage.local.apiRoutes, routes);
            },
            getUser: function () {
                return get(storage.local.user);
            },
            setUser: function (user) {
                set(storage.local.user, user);
            },
            getAccounts: function () {
                return get(storage.local.accounts);
            },
            setAccounts: function (accounts) {
                set(storage.local.accounts, accounts);
            },
            getToken: function () {
                return getCookie(storage.cookie.token);
            },
            removeSessionId: function () {
                return removeCookie(storage.cookie.sessionId);
            },
            removeToken: function () {
                return removeCookie(storage.cookie.token);
            },
            setSelectedAccount: function (account) {
                set(storage.local.selectedAccount, account);
            },
            getSelectedAccount: function () {
                return get(storage.local.selectedAccount);
            },
            removeSelectedAccount: function () {
                return remove(storage.local.selectedAccount);
            },
            setSelectedCompany: function (company) {
                set(storage.local.selectedCompany, company);
            },
            getSelectedCompany: function () {
                return get(storage.local.selectedCompany);
            },
            removeSelectedCompany: function () {
                return remove(storage.local.selectedCompany);
            },
            setCompaniesForSession: function (companies) {
                setValueForSession(storage.rootScope.companies, companies);
            },
            setPlacesForSession: function (places) {
                setValueForSession(storage.rootScope.places, places);
            },
            setCataloguesForSession: function (catalogues) {
                setValueForSession(storage.rootScope.catalogues, catalogues);
            },
            getSessionCompanies: function () {
                return getSessionValue(storage.rootScope.companies);
            },
            getSessionPlaces: function () {
                return getSessionValue(storage.rootScope.places);
            },
            getSessionCatalogues: function () {
                return getSessionValue(storage.rootScope.catalogues);
            }
        };
    }])

    .factory('redirectFactory', ['ROUTES', '$location', 'WEB_CONFIG', 'storageFactory', function (ROUTES, $location, WEB_CONFIG, storageFactory) {
        var isOldBrowser = navigator.userAgent.match(/MSIE\s(?!9.0)/);  // IE8 and lower

        function redirectOldBrowserCompatable(url) {
            var referLink = document.createElement('a');
            referLink.href = url;
            document.body.appendChild(referLink);
            referLink.click();
        }

        function redirectTo(url) {
            if (isOldBrowser) {
                redirectOldBrowserCompatable('#!' + url);
            } else {
                $location.path(url);
            }
        }

        function redirectToNonAngular(url) {
            if (isOldBrowser) {
                redirectOldBrowserCompatable(url);
            } else {
                window.location.href = url;
            }
        }

        function openNewWindow(url) {
            if (isOldBrowser) {
                redirectOldBrowserCompatable(url);
            } else {
                window.open(url);
            }
        }

        return {
            goHomePage: function () {
                redirectTo(ROUTES.START_PAGE);
            },
            goSignIn: function () {
                redirectToNonAngular(WEB_CONFIG.BASE_URL);
            },
            goDashboard: function () {
                redirectTo(ROUTES.DASHBOARD);
            },
            logout: function () {
                storageFactory.removeToken();
                storageFactory.removeSessionId();
                localStorage.clear();
                redirectTo(WEB_CONFIG.BASE_URL + '/user/logout');
            },
            redirectCustomPath: function (path) {
                redirectTo(path);
            }
        };
    }])

    .factory('cookiesFactory', ['WEB_CONFIG', function (WEB_CONFIG) {
        return {
            setItem: function (name, value, expires, secure) {
                if (!name || !value) return false;
                var str = name + '=' + encodeURIComponent(value);

                if (expires) {
                    var now = new Date();
                    now.setTime(now.getTime() + (expires * 24 * 60 * 60 * 1000));
                    str += '; expires=' + now.toUTCString();
                }

                str += '; path=/';
                str += '; domain=' + WEB_CONFIG.DOMAIN; //Attention: get exception when localhost
                if (secure)  str += '; secure';

                document.cookie = str;
                return true;
            },
            getItem: function (name) {
                var r = document.cookie.match("(^|;) ?" + name + "=([^;]*)(;|$)");
                if (r) return r[2];
                else return null;
            },
            removeItem: function (name) {
                document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/;';
            }
        };
    }])

    .factory('errorFactory', ['RESPONSE_STATUS', 'MESSAGES', '$rootScope', 'redirectFactory', function (RESPONSE_STATUS, MESSAGES, $rootScope, redirectFactory) {

        function isUnauthorized(status) {
            return (status === RESPONSE_STATUS.UNAUTHORIZED || status === RESPONSE_STATUS.FORBIDDEN || status === RESPONSE_STATUS.PROXY_AUTHENTICATION_REQUIRED);
        }

        function getError(status, data) {
            var type = (status >= 400) ? 'danger' : 'success';

            if (isUnauthorized(status)) {
                return {msg: MESSAGES.ERROR.UNAUTHORIZED, type: type};
            }

            if (status === RESPONSE_STATUS.NOT_FOUND || status === RESPONSE_STATUS.INTERNAL_SERVER_ERROR) {
                return {msg: MESSAGES.ERROR.INTERNAL_SERVER_ERROR, type: type};
            }

            if (status === RESPONSE_STATUS.UNPROCESSABLE_ENTITY) {
                return {msg: MESSAGES.ERROR.CANNOT_BE_DONE_ERROR, type: type};
            }

            if (data.message) {
                return {msg: data.message, type: type};
            }

            if (data.error) {
                return {msg: data.error, type: type};
            }

            return {msg: MESSAGES.ERROR.UNKNOWN_ERROR, type: type};
        }

        return {
            resolve: function (data, status, container, isLoginPage) {
                if (status === RESPONSE_STATUS.UNAUTHORIZED && !isLoginPage) {
                    redirectFactory.logout();
                }

                if (container) {
                    if (angular.isArray(container) && container.length > 0) {
                        for (var i = 0; i <= container.length; i++) {
                            if (container[i].msg === getError(status, data).msg) {
                                return;
                            }
                        }
                        container.push(getError(status, data));
                    } else {
                        container.push(getError(status, data));
                    }
                } else {
                    $rootScope.messages.push(getError(status, data));
                }
            },
            isUnauthorized: function (status) {
                return isUnauthorized(status);
            }
        };
    }])

    .
    factory('userParamsFactory', ['$http', 'storageFactory', 'errorFactory', 'REST_CONFIG', function ($http, storageFactory, errorFactory, REST_CONFIG) {

        function onError(data, status) {
            if (!errorFactory.isUnauthorized(status)) {
                errorFactory.resolve(data, status);
            }
        }

        function getAccounts() {
            $http.get(REST_CONFIG.BASE_URL + '/accounts')
                .success(function (data) {
                    var accounts = data._embedded.accounts;
                    if (accounts.length === 1) {
                        storageFactory.setSelectedAccount(accounts[0]);
                        getCompanies(accounts[0], true);
                    } else if (accounts.length === 0) {
                        storageFactory.removeSelectedAccount();
                        storageFactory.removeSelectedCompany();
                    }
                }).error(function (data, status) {
                    onError(data, status);
                }
            );
        }

        function getCompanies(account, isSetSelected) {
            $http.get(REST_CONFIG.BASE_URL + '/accounts/' + account.account_uuid + '/companies')
                .success(function (data) {
                    var companies = data._embedded.companies;
                    if (companies.length === 1 && isSetSelected === true) {
                        storageFactory.setSelectedCompany(companies[0]);
                    }
                    getUser();
                }).error(function (data, status) {
                    onError(data, status);
                }
            );
        }

        function getApiRoutes() {
            $http.get(REST_CONFIG.BASE_URL + '/meta').success(function (data) {
                storageFactory.setApiRoutes(data._embedded.resource_meta);
            }).error(function (data, status) {
                    errorFactory.resolve(data, status);
                }
            );
        }

        function getUser() {//TODO api didn't work yet
            // $http.get(REST_CONFIG.BASE_URL + '/profile').success(function (data) {
            // storageFactory.setUser(data._embedded.user);
            //}).error(function (data, status) {
            // errorFactory.resolve(data, status)
            //     }
            // );
        }

        return {
            getApiRoutes: function (isForce) {
                if (isForce !== true) {
                    var routes = storageFactory.getApiRoutes();
                    if (!routes) {
                        getApiRoutes();
                    }
                } else {
                    getApiRoutes();
                }
            },
            prepareUser: function () {
                $http.defaults.headers.common['X-Auth-UserToken'] = storageFactory.getToken();
                var selectedAccount = storageFactory.getSelectedAccount();
                var selectedCompany = storageFactory.getSelectedCompany();
                if (!selectedAccount || !selectedCompany) {
                    getAccounts();
                }

                if (!storageFactory.getUser()) {
                    getUser();
                }
            }
        };
    }])
;
'use strict';

angular.module('website.cookies', [])
    .factory('cookiesFactory', ['WEB_CONFIG', function (WEB_CONFIG) {
        return {
            setItem: function (name, value, expires, secure) {
                if (!name || !value) return false;
                var str = name + '=' + encodeURIComponent(value);

                if (expires) {
                    var now = new Date();
                    now.setTime(now.getTime() + (expires * 24 * 60 * 60 * 1000));
                    str += '; expires=' + now.toUTCString();
                }

                str += '; path=/';
                str += '; domain=' + WEB_CONFIG.DOMAIN; //Attention: get exception when localhost
                if (secure)  str += '; secure';

                document.cookie = str;
                return true;
            },
            getItem: function (name) {
                var r = document.cookie.match("(^|;) ?" + name + "=([^;]*)(;|$)");
                if (r) return r[2];
                else return null;
            },
            removeItem: function (name) {
                document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/;';
            }
        };
    }])
;
'use strict';

angular.module('website.error', [])

    .factory('errorFactory', ['RESPONSE_STATUS', 'MESSAGES', '$rootScope', 'redirectFactory', function (RESPONSE_STATUS, MESSAGES, $rootScope, redirectFactory) {

        function isUnauthorized(status) {
            return (status === RESPONSE_STATUS.UNAUTHORIZED || status === RESPONSE_STATUS.FORBIDDEN || status === RESPONSE_STATUS.PROXY_AUTHENTICATION_REQUIRED);
        }

        function getError(status, data) {
            var type = (status >= 400) ? 'danger' : 'success';

            if (isUnauthorized(status)) {
                return {msg: MESSAGES.ERROR.UNAUTHORIZED, type: type};
            }

            if (status === RESPONSE_STATUS.NOT_FOUND || status === RESPONSE_STATUS.INTERNAL_SERVER_ERROR) {
                return {msg: MESSAGES.ERROR.INTERNAL_SERVER_ERROR, type: type};
            }

            if (status === RESPONSE_STATUS.UNPROCESSABLE_ENTITY) {
                return {msg: MESSAGES.ERROR.CANNOT_BE_DONE_ERROR, type: type};
            }

            if (data.message) {
                return {msg: data.message, type: type};
            }

            if (data.error) {
                return {msg: data.error, type: type};
            }

            return {msg: MESSAGES.ERROR.UNKNOWN_ERROR, type: type};
        }

        return {
            resolve: function (data, status, container, isLoginPage) {
                if (status === RESPONSE_STATUS.UNAUTHORIZED && !isLoginPage) {
                    redirectFactory.logout();
                }

                if (container) {
                    if (angular.isArray(container) && container.length > 0) {
                        for (var i = 0; i <= container.length; i++) {
                            if (container[i].msg === getError(status, data).msg) {
                                return;
                            }
                        }
                        container.push(getError(status, data));
                    } else {
                        container.push(getError(status, data));
                    }
                } else {
                    $rootScope.messages.push(getError(status, data));
                }
            },
            isUnauthorized: function (status) {
                return isUnauthorized(status);
            }
        };
    }])
;
'use strict';

angular.module('website.redirect', [])
    .factory('redirectFactory', ['ROUTES', '$location', 'WEB_CONFIG', 'storageFactory', function (ROUTES, $location, WEB_CONFIG, storageFactory) {
        var isOldBrowser = navigator.userAgent.match(/MSIE\s(?!9.0)/);  // IE8 and lower

        function redirectOldBrowserCompatable(url) {
            var referLink = document.createElement('a');
            referLink.href = url;
            document.body.appendChild(referLink);
            referLink.click();
        }

        function redirectTo(url) {
            if (isOldBrowser) {
                redirectOldBrowserCompatable('#!' + url);
            } else {
                $location.path(url);
            }
        }

        function redirectToNonAngular(url) {
            if (isOldBrowser) {
                redirectOldBrowserCompatable(url);
            } else {
                window.location.href = url;
            }
        }

        function openNewWindow(url) {
            if (isOldBrowser) {
                redirectOldBrowserCompatable(url);
            } else {
                window.open(url);
            }
        }

        return {
            goHomePage: function () {
                redirectTo(ROUTES.START_PAGE);
            },
            goSignIn: function () {
                redirectToNonAngular(WEB_CONFIG.BASE_URL);
            },
            goDashboard: function () {
                redirectTo(ROUTES.DASHBOARD);
            },
            logout: function () {
                storageFactory.removeToken();
                storageFactory.removeSessionId();
                localStorage.clear();
                redirectTo(WEB_CONFIG.BASE_URL + '/user/logout');
            },
            redirectCustomPath: function (path) {
                redirectTo(path);
            }
        };
    }])
;
'use strict';

angular.module('website.storage', [])
    .factory('storageFactory', ['$http', 'cookiesFactory', '$rootScope', function ($http, cookiesFactory, $rootScope) {
        var storage = {
            cookie: {
                token: 'token',
                sessionId: 'PHPSESSID'
            },
            local: {
                accounts: 'accounts',
                apiRoutes: 'api_routes',
                user: 'user',
                selectedAccount: 'selected_account',
                selectedCompany: 'selected_company'
            },
            rootScope: {
                companies: 'companies',
                catalogues: 'catalogues',
                places: 'places'
            }
        };

        function get(key) {
            var value = localStorage.getItem(key);
            return value ? JSON.parse(value) : null;
        }

        function getCookie(key) {
            return cookiesFactory.getItem(key);
        }

        function setValueForSession(key, value) {
            $rootScope[key] = value;
        }

        function getSessionValue(key) {
            return $rootScope[key];
        }

        function addCookie(key, value, expires, secure) {
            return cookiesFactory.setItem(key, value, expires, secure);
        }

        function removeCookie(key) {
            return cookiesFactory.removeItem(key);
        }

        function set(key, value) {
            if (!key) throw "Invalid key for a value: " + value;
            if (!value) throw "Invalid value for a key :" + key;
            localStorage.setItem(key, JSON.stringify(value));
        }

        function remove(key) {
            return localStorage.removeItem(key);
        }

        return { //TODO add fallback to cookies
            storage: storage,
            getApiRoutes: function () {
                return get(storage.local.apiRoutes);
            },
            setApiRoutes: function (routes) {
                set(storage.local.apiRoutes, routes);
            },
            getUser: function () {
                return get(storage.local.user);
            },
            setUser: function (user) {
                set(storage.local.user, user);
            },
            getAccounts: function () {
                return get(storage.local.accounts);
            },
            setAccounts: function (accounts) {
                set(storage.local.accounts, accounts);
            },
            getToken: function () {
                return getCookie(storage.cookie.token);
            },
            removeSessionId: function () {
                return removeCookie(storage.cookie.sessionId);
            },
            removeToken: function () {
                return removeCookie(storage.cookie.token);
            },
            setSelectedAccount: function (account) {
                set(storage.local.selectedAccount, account);
            },
            getSelectedAccount: function () {
                return get(storage.local.selectedAccount);
            },
            removeSelectedAccount: function () {
                return remove(storage.local.selectedAccount);
            },
            setSelectedCompany: function (company) {
                set(storage.local.selectedCompany, company);
            },
            getSelectedCompany: function () {
                return get(storage.local.selectedCompany);
            },
            removeSelectedCompany: function () {
                return remove(storage.local.selectedCompany);
            },
            setCompaniesForSession: function (companies) {
                setValueForSession(storage.rootScope.companies, companies);
            },
            setPlacesForSession: function (places) {
                setValueForSession(storage.rootScope.places, places);
            },
            setCataloguesForSession: function (catalogues) {
                setValueForSession(storage.rootScope.catalogues, catalogues);
            },
            getSessionCompanies: function () {
                return getSessionValue(storage.rootScope.companies);
            },
            getSessionPlaces: function () {
                return getSessionValue(storage.rootScope.places);
            },
            getSessionCatalogues: function () {
                return getSessionValue(storage.rootScope.catalogues);
            }
        };
    }])
;
'use strict';

angular.module('website.user.param,', [])
    .factory('userParamsFactory', ['$http', 'storageFactory', 'errorFactory', 'REST_CONFIG', function ($http, storageFactory, errorFactory, REST_CONFIG) {
        function onError(data, status) {
            if (!errorFactory.isUnauthorized(status)) {
                errorFactory.resolve(data, status);
            }
        }

        function getAccounts() {
            $http.get(REST_CONFIG.BASE_URL + '/accounts')
                .success(function (data) {
                    var accounts = data._embedded.accounts;
                    if (accounts.length === 1) {
                        storageFactory.setSelectedAccount(accounts[0]);
                        getCompanies(accounts[0], true);
                    } else if (accounts.length === 0) {
                        storageFactory.removeSelectedAccount();
                        storageFactory.removeSelectedCompany();
                    }
                }).error(function (data, status) {
                    onError(data, status);
                }
            );
        }

        function getCompanies(account, isSetSelected) {
            $http.get(REST_CONFIG.BASE_URL + '/accounts/' + account.account_uuid + '/companies')
                .success(function (data) {
                    var companies = data._embedded.companies;
                    if (companies.length === 1 && isSetSelected === true) {
                        storageFactory.setSelectedCompany(companies[0]);
                    }
                    getUser();
                }).error(function (data, status) {
                    onError(data, status);
                }
            );
        }

        function getApiRoutes() {
            $http.get(REST_CONFIG.BASE_URL + '/meta').success(function (data) {
                storageFactory.setApiRoutes(data._embedded.resource_meta);
            }).error(function (data, status) {
                    errorFactory.resolve(data, status);
                }
            );
        }

        function getUser() {//TODO api didn't work yet
            // $http.get(REST_CONFIG.BASE_URL + '/profile').success(function (data) {
            // storageFactory.setUser(data._embedded.user);
            //}).error(function (data, status) {
            // errorFactory.resolve(data, status)
            //     }
            // );
        }

        return {
            getApiRoutes: function (isForce) {
                if (isForce !== true) {
                    var routes = storageFactory.getApiRoutes();
                    if (!routes) {
                        getApiRoutes();
                    }
                } else {
                    getApiRoutes();
                }
            },
            prepareUser: function () {
                $http.defaults.headers.common['X-Auth-UserToken'] = storageFactory.getToken();
                var selectedAccount = storageFactory.getSelectedAccount();
                var selectedCompany = storageFactory.getSelectedCompany();
                if (!selectedAccount || !selectedCompany) {
                    getAccounts();
                }

                if (!storageFactory.getUser()) {
                    getUser();
                }
            }
        };
    }])
;
'use strict';

angular.module('website.account', [])

    .controller('accountController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', 'storageFactory', '$modal', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS, storageFactory, $modal) {
        $rootScope.pageTitle = 'Аккаунт';
        $scope.companyModal = null;
        $scope.selectedAccount = null;
        $scope.showCompanyWizard = false;
        $scope.showConfirmationModal = false;

        $scope.prepareAddCompany = function (account) {
            $scope.showConfirmationModal = true;
            $scope.selectedAccount = account;
            openCompanyModal();
        };

        $scope.launchCompanyWizard = function () {
            $scope.showConfirmationModal = false;
            $scope.showCompanyWizard = true;
        };

        function openCompanyModal() {
            $scope.companyModal = $modal.open({
                templateUrl: 'addCompanyModalContent.html',
                scope: $scope,
                backdrop: 'static',
                controller: 'addCompanyModalController'
            });
        }

        function closeCompanyModal() {
            $scope.companyModal.close();
            $scope.selectedAccount = null;
            $scope.showConfirmationModal = false;
            $scope.showCompanyWizard = false;
        }

        $scope.closeCompanyModal = function () {
            closeCompanyModal();
        };

        getAccounts();

        $scope.getAccounts = function () {
            getAccounts();
        };

        function getAccounts() {
            $http.get(REST_CONFIG.BASE_URL + '/accounts')
                .success(function (data) {
                    var accounts = data._embedded.accounts;
                    $scope.accounts = accounts;
                    if (accounts.length === 1) {
                        $scope.firstAccount = data._embedded.accounts[0];
                    }
                }).error(function (data, status) {
                    errorFactory.resolve(data, status);
                }
            );
        }

        $scope.removeAccount = function (account) {
            $http.delete(REST_CONFIG.BASE_URL + '/accounts/' + account.account_uuid)
                .success(function () {
                    getAccounts();
                }).error(function (data, status) {
                    errorFactory.resolve(data, status);
                }
            );
        };
    }])

    .controller('addCompanyModalController', ['$scope', '$http', 'REST_CONFIG', 'errorFactory', '$timeout', function ($scope, $http, REST_CONFIG, errorFactory, $timeout) {

        $scope.openCatalog = function () {
            //placeholder
        };

        $scope.openDatePopup = function (isOpen) {
            $timeout(function () {
                $scope[isOpen] = true;
            });
        };
    }])
;
'use strict';

angular.module('website.dashboard', [])

    .controller('dashboardController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', 'storageFactory', '$modal', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS, storageFactory, $modal) {
        $rootScope.pageTitle = 'dashboard';
        $scope.accountModal = null;
        $scope.accountData = [];
        $scope.firstAccount = null;
        $scope.showAccountRegistration = false;
        $scope.showCompanyWizard = false;
        checkForAccounts();

        //TODO remove (just demo for a catalogs tests)
        $scope.catalogModel = null;

        $scope.getData = function () {
            return [
                {value: 1, description: 'Петров В.', firstName: 'Василий', lastName: 'Петров', age: '21' },
                {value: 2, description: 'Антонов К.', firstName: 'Константин', lastName: 'Антонов', age: '37' },
                {value: 3, description: 'Яковлев Б.', firstName: 'Борис', lastName: 'Яковлев', age: '17' },
                {value: 4, description: 'Туполев М.', firstName: 'Марат', lastName: 'Туполев', age: '33' },
                {value: 5, description: 'Лавочкин С.', firstName: 'Серафим', lastName: 'Лавочкин', age: '24' }
            ];
        };
        //TODO END remove

        function checkForAccounts() {
            $scope.showAccountRegistration = true;
            getAccounts();
        }

        function openAccountModal() {
            $scope.accountModal = $modal.open({
                templateUrl: 'registrationModalContent.html',
                backdrop: 'static',
                scope: $scope,
                controller: 'registrationModalController'
            });
        }

        function closeAccountModal() {
            $scope.accountModal.close();
        }

        $scope.closeAccountModal = function () {
            closeAccountModal();
        };

        $scope.getAccounts = function () {
            getAccounts();
        };

        function onError(data, status) {
            if (status === RESPONSE_STATUS.NOT_FOUND) {
                openAccountModal();
            } else {
                errorFactory.resolve(data, status);
            }
        }

        function getAccounts() {
            $http.get(REST_CONFIG.BASE_URL + '/accounts')
                .success(function (data) {
                    var accounts = data._embedded.accounts;
                    $scope.accounts = accounts;
                    if (accounts.length === 1) {
                        $scope.firstAccount = data._embedded.accounts[0];
                        storageFactory.setSelectedAccount($scope.firstAccount);
                    } else if (accounts.length === 0) {
                        storageFactory.removeSelectedAccount();
                        storageFactory.removeSelectedCompany();
                    }
                }).error(onError);
        }

        $scope.addAccount = function () {
            $scope.showAccountRegistration = true;
            $scope.showCompanyWizard = false;
            openAccountModal();
        };

        $scope.removeAccount = function (account) {
            $http.delete(REST_CONFIG.BASE_URL + '/accounts/' + account.account_uuid)
                .success(function () {
                    getAccounts();
                }).error(onError);
        };
    }])

    .controller('registrationModalController', ['$scope', '$http', 'REST_CONFIG', 'errorFactory', '$timeout', function ($scope, $http, REST_CONFIG, errorFactory, $timeout) {
        $scope.registrationModalMessages = [];

        $scope.openCatalog = function () {
            //placeholder
        };

        $scope.openDatePopup = function (isOpen) {
            $timeout(function () {
                $scope[isOpen] = true;
            });
        };

        $scope.saveAccountData = function () {
            $http.post(REST_CONFIG.BASE_URL + '/accounts', {title: $scope.accountData.title})
                .success(function () {
                    $scope.getAccounts();
                    $scope.showAccountRegistration = false;
                    $scope.showCompanyWizard = true;
                }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.registrationModalMessages);
                }
            );
        };
    }])
;
'use strict';

angular.module('website.public.offer', [])

    .controller('publicOfferController', ['$scope', '$rootScope', function ($scope, $rootScope) {
        $rootScope.pageTitle = 'Аккаунт';
    }])
;
'use strict';

angular.module('website.user.profile', [])

    .controller('userProfileController', ['$scope', '$rootScope', '$http', 'storageFactory', 'errorFactory', 'redirectFactory', '$timeout', 'REST_CONFIG', function ($scope, $rootScope, $http, storageFactory, errorFactory, redirectFactory, $timeout, REST_CONFIG) {
        $rootScope.pageTitle = 'Профиль';

        $scope.editMode = false;

        $scope.profileData = {
            socials: [],
            personal: {},
            passport: {},
            phones: [],
            addresses: [],
            sites: [],
            emails: [],
            photo: {},
            eSignature: {},
            other: {}
        };

        $scope.startEdit = function () {
            $scope.editMode = true;
        };

        $scope.cancelEdit = function () {
            $scope.editMode = false;
        };

        $scope.saveEdit = function () {
            $http.post(REST_CONFIG.BASE_URL + '/profiles' + storageFactory.getUser(), $scope.profileData)
                .success(function (data) {
                    //storageFactory.setUser(data.user);
                }).error(function (data, status) {
                    errorFactory.resolve(data, status);
                }
            );
        };

        $scope.openDatePopup = function (isOpen) {
            $timeout(function () {
                $scope[isOpen] = true;
            });
        };

        $scope.today = new Date();

        function getTimestamp(aDate, callback) {
            if (aDate) {
                if (new Date(aDate) !== 'Invalid Date') {
                    aDate = aDate.split("-");
                    var newDate = aDate[2] + "/" + aDate[1] + "/" + aDate[0];
                    return callback(new Date(newDate).getTime());
                } else {
                    var day = aDate.slice(0, 2);
                    var month = aDate.slice(3, 5);
                    var year = aDate.slice(6);
                    var birthDate = new Date(+year, (+month) - 1, +day);
                    if (birthDate !== 'Invalid Date') {
                        return callback((birthDate).getTime());
                    } else {
                        return errorFactory.resolve({error: 'Неверный формат даты'});
                    }
                }
            }
            return callback(null);
        }

    }])
;
'use strict';

angular.module('website.top.menu', [])

    .directive('topPrivateMenu', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/partials/private/top_menu.html',
            controller: function ($scope, $http, $location, REST_CONFIG, storageFactory, errorFactory) {
                $scope.showCataloguesDropDown = false;
                $scope.showCompaniesDropDown = false;
                $scope.showPlacesDropDown = false;

                $scope.getClass = function (path) {
                    if ('/' + $location.path().substr(1, path.length) == path) {
                        return "active";
                    } else {
                        return "";
                    }
                };

                (function fetDropdownData() {
                    getCompanies();
                    getCatalogues();
                    getPlaces();
                })();

                function getCatalogues() {
                    $scope.catalogues = storageFactory.getSessionCatalogues();
                    if (!$scope.catalogues) {
                        $http.get(REST_CONFIG.BASE_URL + '/ref')
                            .success(function (data) {
                                $scope.catalogues = data._embedded.references;
                                storageFactory.setCataloguesForSession($scope.catalogues);
                                $scope.showCataloguesDropDown = true;
                            }).error(function (data, status) {
                                errorFactory.resolve(data, status);
                            }
                        );
                    } else {
                        $scope.showCataloguesDropDown = true;
                    }
                }

                function getCompanies() {
                    $scope.companies = storageFactory.getSessionCompanies();
                    if (!$scope.companies) {
                        $http.get(REST_CONFIG.BASE_URL + '/companies')
                            .success(function (data) {
                                $scope.companies = data._embedded.companies;
                                storageFactory.setCompaniesForSession($scope.companies);
                                $scope.showCompaniesDropDown = true;
                            }).error(function (data, status) {
                                errorFactory.resolve(data, status);
                            }
                        );
                    } else {
                        $scope.showCompaniesDropDown = true;
                    }
                }

                function getPlaces() {
                    $scope.places = storageFactory.getSessionPlaces();
                    if (!$scope.places) {
                        $http.get(REST_CONFIG.BASE_URL + '/places')
                            .success(function (data) {
                                $scope.places = data._embedded.places;
                                storageFactory.setPlacesForSession($scope.places);
                                $scope.showPlacesDropDown = true;
                            }).error(function (data, status) {
                                errorFactory.resolve(data, status);
                            }
                        );
                    } else {
                        $scope.showPlacesDropDown = true;
                    }
                }

                $scope.openCatalogueCard = function (company) {
                    //TODO placeholder
                };

                $scope.openCompanyCard = function (company) {
                    //TODO placeholder
                };

                $scope.openPlaceCard = function (company) {
                    //TODO placeholder
                };
            }
        };
    })

    .directive('userMenu', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/partials/private/user_menu.html',
            controller: function ($scope, $rootScope, redirectFactory, storageFactory, $modal) {
                $scope.isSelectAccountAndCompanyModalOpened = false;
                $scope.isCompaniesManagementOpened = false;
                $scope.isPlacesManagementOpened = false;

                $rootScope.$watch(function () {
                    return localStorage.getItem(storageFactory.storage.local.selectedAccount);
                }, function (newValue) {
                    $scope.accountName = (newValue) ? JSON.parse(newValue).title : '(Нет аккаунта)';
                });

                $rootScope.$watch(function () {
                    return localStorage.getItem(storageFactory.storage.local.selectedCompany);
                }, function (newValue) {
                    $scope.companyShortName = (newValue) ? JSON.parse(newValue).short : '(Юр. Лицо не выбрано)';
                });

                function openSelectAccountAndCompanyModal() {
                    $scope.isSelectAccountAndCompanyModalOpened = true;
                    $scope.selectAccountAndCompanyModal = $modal.open({
                        templateUrl: 'selectAccountAndCompanyModalContent.html',
                        scope: $scope,
                        backdrop: 'static',
                        controller: 'selectAccountAndCompanyModalController'
                    });
                }

                function openImportCompaniesModal() {
                    $scope.isImportCompaniesModalOpened = true;
                    $scope.importCompaniesModal = $modal.open({
                        templateUrl: 'importCompaniesModalContent.html',
                        scope: $scope,
                        backdrop: 'static',
                        controller: 'importCompaniesModalController'
                    });
                }

                function openImportPlacesModal() {
                    $scope.isImportPlacesModalOpened = true;
                    $scope.importPlacesModal = $modal.open({
                        templateUrl: 'importPlacesModalContent.html',
                        scope: $scope,
                        backdrop: 'static',
                        controller: 'importPlacesModalController'
                    });
                }

                function openCompaniesManagementModal() {
                    $scope.isCompaniesManagementOpened = true;
                    $scope.companiesManagementModal = $modal.open({
                        templateUrl: 'companiesManagementContent.html',
                        scope: $scope,
                        backdrop: 'static',
                        windowClass: 'modal_huge',
                        controller: 'companiesManagementController'
                    });
                }

                function openPlacesManagementModal() {
                    $scope.isPlacesManagementOpened = true;
                    $scope.placesManagementModal = $modal.open({
                        templateUrl: 'placesManagementContent.html',
                        scope: $scope,
                        backdrop: 'static',
                        windowClass: 'modal_huge',
                        controller: 'placesManagementController'
                    });
                }

                function closeModal(modal) {
                    modal.close();
                }

                $scope.closeSelectAccountAndCompanyModal = function () {
                    closeModal($scope.selectAccountAndCompanyModal);
                };

                $scope.showSelectAccountAndCompanyModal = function () {
                    openSelectAccountAndCompanyModal();
                };

                $scope.showImportCompaniesModal = function () {
                    openImportCompaniesModal();
                };

                $scope.showCompaniesManagementModal = function () {
                    openCompaniesManagementModal();
                };

                $scope.showPlacesManagementModal = function () {
                    openPlacesManagementModal();
                };

                $scope.closeCompaniesManagementModal = function () {
                    closeModal($scope.companiesManagementModal);
                };

                $scope.closePlacesManagementModal = function () {
                    closeModal($scope.placesManagementModal);
                };

                $scope.showImportPlacesModal = function () {
                    openImportPlacesModal();
                };

                $scope.closeImportCompaniesModal = function () {
                    closeModal($scope.importCompaniesModal);
                };

                $scope.closeImportPlacesModal = function () {
                    closeModal($scope.importPlacesModal);
                };

                $scope.logout = function () {
                    redirectFactory.logout();
                };
            }
        };
    })

    .controller('selectAccountAndCompanyModalController', ['$scope', '$http', 'REST_CONFIG', 'errorFactory', 'storageFactory', function ($scope, $http, REST_CONFIG, errorFactory, storageFactory) {
        $scope.options = [];
        $scope.selectAccountAndCompanyMessages = [];

        if ($scope.isSelectAccountAndCompanyModalOpened) {
            getCompaniesForAccounts();
        }

        function getAccounts(callback) {
            $http.get(REST_CONFIG.BASE_URL + '/accounts')
                .success(function (data) {
                    $scope.accounts = data._embedded.accounts;
                    callback($scope.accounts);
                }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.selectAccountAndCompanyMessages);
                }
            );
        }

        function getCompanies(account, callback) {
            if (account) {
                $http.get(REST_CONFIG.BASE_URL + '/accounts/' + account.account_uuid + '/companies')
                    .success(function (data) {
                        $scope.companies = data._embedded.companies;
                        callback($scope.companies, account);
                    }).error(function (data, status) {
                        errorFactory.resolve(data, status, $scope.selectAccountAndCompanyMessages);
                    }
                );
            }
        }

        function pushCompaniesAndAccount(companies, account) {
            for (var j in companies) {
                if (companies.hasOwnProperty(j)) {
                    $scope.options.push({
                        account: account,
                        company: companies[j]
                    });
                }
            }
        }

        function getCompaniesForAccounts() {
            getAccounts(function (accounts) {
                for (var k in accounts) {
                    if (accounts.hasOwnProperty(k)) {
                        getCompanies(accounts[k], pushCompaniesAndAccount);
                    }
                }
            });
        }

        $scope.selectOption = function (option) {
            $scope.tempSelectedAccount = option;
        };

        $scope.saveAccountAndCompany = function () {
            if ($scope.tempSelectedAccount) {
                storageFactory.setSelectedAccount($scope.tempSelectedAccount.account);
                storageFactory.setSelectedCompany($scope.tempSelectedAccount.company);
            } else {
                storageFactory.removeSelectedAccount();
                storageFactory.removeSelectedCompany();
            }
            $scope.closeSelectAccountAndCompanyModal();
        };

    }])

    .controller('importCompaniesModalController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS) {
        $scope.importCompaniesMessages = [];
        $scope.noCompaniesToImport = false;
        $scope.stat = {
            new: 0,
            changed: 0,
            exists: 0,
            processed: 0
        };

        function onError(data, status) {
            if (status === RESPONSE_STATUS.NOT_FOUND) {
                $scope.noCompaniesToImport = true;
            } else {
                errorFactory.resolve(data, status, $scope.importCompaniesMessages);
            }
        }

        function fetchImportStatistic() {
            for (var i = 0; i <= 0; i++) {
                var externalService = $scope.extServiceCompanies[i];
                $scope.stat.new = $scope.stat.new + externalService.stat.new;
                $scope.stat.changed = $scope.stat.changed + externalService.stat.changed;
                $scope.stat.exists = $scope.stat.exists + externalService.stat.exists;
                $scope.stat.processed = $scope.stat.processed + externalService.stat.processed;
            }
        }

        $scope.importCompanies = function () {
            $http.get(REST_CONFIG.BASE_URL + '/service/import/company')
                .success(function (data) {
                    $scope.isImportCompaniesComplete = true;
                    $scope.extServiceCompanies = data._embedded.ext_service_company;
                    fetchImportStatistic();
                }).error(function (data, status) {
                    onError(data, status);
                }
            );
        };
    }])

    .controller('importPlacesModalController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS) {
        $scope.importPlacesMessages = [];
        $scope.noPlacesToImport = false;
        $scope.stat = {
            new: 0,
            changed: 0,
            exists: 0,
            processed: 0
        };

        function onError(data, status) {
            if (status === RESPONSE_STATUS.NOT_FOUND) {
                $scope.noPlacesToImport = true;
            } else {
                errorFactory.resolve(data, status, $scope.importPlacesMessages);
            }
        }

        function fetchImportStatistic() {
            for (var i = 0; i <= 0; i++) {
                var externalService = $scope.extServicePlaces[i];
                $scope.stat.new = $scope.stat.new + externalService.stat.new;
                $scope.stat.changed = $scope.stat.changed + externalService.stat.changed;
                $scope.stat.exists = $scope.stat.exists + externalService.stat.exists;
                $scope.stat.processed = $scope.stat.processed + externalService.stat.processed;
            }
        }

        $scope.importPlaces = function () {
            $http.get(REST_CONFIG.BASE_URL + '/service/import/place')
                .success(function (data) {
                    $scope.isImportPlacesComplete = true;
                    $scope.extServicePlaces = data._embedded.places;
                    fetchImportStatistic();
                }).error(function (data, status) {
                    onError(data, status);
                }
            );
        };
    }])


    .controller('companiesManagementController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS) {
        $scope.companiesManagementMessages = [];
        $scope.importedCompanies = [];
        $scope.existedCompanies = [];
        $scope.linkedCompanies = [];
        var unlinkedImportedCompanies = [];
        var linkedImportedCompanies = [];

        if ($scope.isCompaniesManagementOpened) {
            getAllSystemCompanies();
            getImportedCompanies();
        }

        function onError(data, status) {
            if (status != RESPONSE_STATUS.NOT_FOUND) {
                errorFactory.resolve(data, status, $scope.companiesManagementMessages);
            }
        }

        function getAllSystemCompanies() {
            $http.get(REST_CONFIG.BASE_URL + '/companies').success(function (data) {
                $scope.existedCompany = null;
                $scope.existedCompanies = data._embedded.companies;
            }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.companiesManagementMessages);
                }
            );
        }

        function getImportedCompanies(callback) {
            $http.get(REST_CONFIG.BASE_URL + '/service/import/company-intersect').success(function (data) {
                $scope.importedCompany = null;
                var importedCompanies = data._embedded.companies;
                unlinkedImportedCompanies = [];
                linkedImportedCompanies = [];
                for (var i = 0; i <= importedCompanies.length - 1; i++) {
                    if (!importedCompanies[i].link) {
                        unlinkedImportedCompanies.push(importedCompanies[i]);
                    } else {
                        linkedImportedCompanies.push(importedCompanies[i]);
                    }
                }
                $scope.importedCompanies = unlinkedImportedCompanies;
                if (callback) {
                    callback();
                }
            }).error(function (data, status) {
                    onError(data, status);
                }
            );
        }

        $scope.getImportedCompanies = function () {
            getImportedCompanies();
        };

        $scope.getExistedCompanies = function () {
            getAllSystemCompanies();
        };

        $scope.addCompaniesLink = function () {
            var params = {
                source: $scope.importedCompany.source,
                id: $scope.importedCompany.id
            };

            if ($scope.existedCompany) {
                params.company = $scope.existedCompany.uuid;
            }

            $http.post(REST_CONFIG.BASE_URL + '/service/import/company-intersect', params).success(function () {
                getImportedCompanies(function () {
                    getLinkedCompanies();
                    getAllSystemCompanies();
                });
            }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.companiesManagementMessages);
                }
            );
        };

        $scope.removeCompaniesLink = function () {
            $http.delete(REST_CONFIG.BASE_URL + '/service/import/company-intersect/' + $scope.linkedCompany.source + '-' + $scope.linkedCompany.id)
                .success(function () {
                    getImportedCompanies(function () {
                        getLinkedCompanies();
                        getAllSystemCompanies();
                    });
                }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.companiesManagementMessages);
                }
            );
        };

        $scope.removeCompany = function () {
            $http.delete(REST_CONFIG.BASE_URL + '/companies/' + $scope.existedCompany.uuid)
                .success(function () {
                    getImportedCompanies();
                    getLinkedCompanies();
                    getAllSystemCompanies();
                }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.companiesManagementMessages);
                }
            );
        };

        function getLinkedCompanies() {
            $scope.linkedCompanies = [];
            var existedCompany = $scope.existedCompany;
            if (linkedImportedCompanies.length > 0 && existedCompany) {
                for (var i = 0; i <= linkedImportedCompanies.length - 1; i++) {
                    if (linkedImportedCompanies[i].link === existedCompany.uuid) {
                        $scope.linkedCompanies.push(linkedImportedCompanies[i]);
                    }
                }
            }
        }

        $scope.selectImportedCompany = function (company) {
            $scope.importedCompany = company;
        };

        $scope.selectLinkedCompany = function (company) {
            $scope.linkedCompany = company;
        };

        $scope.selectExistedCompany = function (company) {
            $scope.existedCompany = company;
            getLinkedCompanies();
        };
    }
    ])

    .controller('placesManagementController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS) {
        $scope.placesManagementMessages = [];
        $scope.importedPlaces = [];
        $scope.existedPlaces = [];
        $scope.linkedPlaces = [];
        var unlinkedImportedPlaces = [];
        var linkedImportedPlaces = [];

        if ($scope.isPlacesManagementOpened) {
            getAllSystemPlaces();
            getImportedPlaces();
        }

        function onError(data, status) {
            if (status != RESPONSE_STATUS.NOT_FOUND) {
                errorFactory.resolve(data, status, $scope.placesManagementMessages);
            }
        }

        function getAllSystemPlaces() {
            $http.get(REST_CONFIG.BASE_URL + '/places').success(function (data) {
                $scope.existedPlaces = data._embedded.places;
            }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.placesManagementMessages);
                }
            );
        }

        function getImportedPlaces(callback) {
            $http.get(REST_CONFIG.BASE_URL + '/service/import/place-intersect')
                .success(function (data) {
                    $scope.importedPlaces = null;
                    var importedPlaces = data._embedded.places;
                    unlinkedImportedPlaces = [];
                    linkedImportedPlaces = [];
                    for (var i = 0; i <= importedPlaces.length - 1; i++) {
                        if (!importedPlaces[i].link) {
                            unlinkedImportedPlaces.push(importedPlaces[i]);
                        } else {
                            linkedImportedPlaces.push(importedPlaces[i]);
                        }
                    }
                    $scope.importedPlaces = unlinkedImportedPlaces;
                    if (callback) {
                        callback();
                    }
                }).error(function (data, status) {
                    onError(data, status);
                }
            );
        }

        $scope.getImportedPlaces = function () {
            getImportedPlaces();
        };

        $scope.getExistedPlaces = function () {
            getAllSystemPlaces();
        };

        $scope.addPlacesLink = function () {
            var placeUuid = $scope.existedPlace ? $scope.existedPlace.uuid : null;
            $http.post(REST_CONFIG.BASE_URL + '/service/import/place-intersect',
                {source: $scope.importedPlace.source,
                    id: $scope.importedPlace.id,
                    place: placeUuid,
                    type: $scope.importedPlace.type})
                .success(function () {
                    getImportedPlaces(function () {
                        getLinkedPlaces();
                        getAllSystemPlaces();
                    });
                }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.placesManagementMessages);
                }
            );
        };

        $scope.removePlace = function () {
            $http.delete(REST_CONFIG.BASE_URL + '/places/' + $scope.existedPlace.uuid)
                .success(function () {
                    getImportedPlaces();
                    getLinkedPlaces();
                    getAllSystemPlaces();
                }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.placesManagementMessages);
                }
            );
        };

        $scope.removePlacesLink = function () {
            $http.delete(REST_CONFIG.BASE_URL + '/service/import/place-intersect/' + $scope.linkedPlace.source + '-' + $scope.linkedPlace.type + '-' + $scope.linkedPlace.id)
                .success(function () {
                    getImportedPlaces(function () {
                        getLinkedPlaces();
                        getAllSystemPlaces();
                    });
                }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.placesManagementMessages);
                }
            );
        };

        function getLinkedPlaces() {
            $scope.linkedPlaces = [];
            var existedPlace = $scope.existedPlace;
            if (linkedImportedPlaces.length > 0 && existedPlace) {
                for (var i = 0; i <= linkedImportedPlaces.length - 1; i++) {
                    if (linkedImportedPlaces[i].link === existedPlace.uuid) {
                        $scope.linkedPlaces.push(linkedImportedPlaces[i]);
                    }
                }
            }
        }

        $scope.selectImportedPlace = function (place) {
            $scope.importedPlace = place;
        };

        $scope.selectLinkedPlace = function (place) {
            $scope.linkedPlace = place;
        };

        $scope.selectExistedPlace = function (place) {
            $scope.existedPlace = place;
            getLinkedPlaces();
        };
    }
    ])
;