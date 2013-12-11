'use strict';

angular.module('website', [
        'ngRoute',
        'ngAnimate',
        'ui.bootstrap',
        'env.config',
        'website.constants',
        'common.factories',
        'common.directives',
        'website.top.menu',
        'website.sign',
        'website.user.profile',
        'website.dashboard',
        'website.account',
        'website.public.offer',
        'website.page.errors'
    ])
    .config(['$routeProvider', '$httpProvider', '$locationProvider', 'ACCESS_LEVEL', 'ROUTES', function ($routeProvider, $httpProvider, $locationProvider, ACCESS_LEVEL, ROUTES) {
        var pathToIncs = 'html/pages/';
        $routeProvider.when(ROUTES.START_PAGE, {redirectTo: ROUTES.DASHBOARD});
        $routeProvider.when(ROUTES.START_PAGE_ALT, {redirectTo: ROUTES.DASHBOARD});
        $routeProvider.when(ROUTES.NOT_FOUND, {templateUrl: pathToIncs + '404.html', access: ACCESS_LEVEL.PUBLIC});
        $routeProvider.when(ROUTES.USER_PROFILE, {templateUrl: pathToIncs + 'user_profile.html', access: ACCESS_LEVEL.AUTHORIZED});
        $routeProvider.when(ROUTES.DASHBOARD, {templateUrl: pathToIncs + 'dashboard.html', access: ACCESS_LEVEL.AUTHORIZED});
        $routeProvider.when(ROUTES.ACCOUNT, {templateUrl: pathToIncs + 'account.html', access: ACCESS_LEVEL.AUTHORIZED});
        $routeProvider.when(ROUTES.PUBLIC_OFFER, {templateUrl: pathToIncs + 'public_offer.html', access: ACCESS_LEVEL.PUBLIC});

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
    .filter('routeFilter', function () {
        return function (route) {
            return '/#!' + route;
        };
    })
    .run(['$rootScope', 'ACCESS_LEVEL', 'ROUTES', 'cookieFactory', 'redirectFactory', 'storageFactory', '$http', function ($rootScope, ACCESS_LEVEL, ROUTES, cookieFactory, redirectFactory, storageFactory, $http) {
        $rootScope.ROUTES = ROUTES;
        $rootScope.isAjaxLoading = false;

        $rootScope.$on("$routeChangeStart", function (event, next, current) {
            var isToken = !!storageFactory.getToken();
            if (isToken) {
                $http.defaults.headers.common['X-Auth-UserToken'] = storageFactory.getToken();
            } else {
                redirectFactory.goSignIn();
            }
        });

    }])
;

angular.module('website.constants', [])
    .constant('RESPONSE_STATUS', {
        OK: 200,
        CREATED: 201,
        ACCEPTED: 202,
        NOT_MODIFIED: 304,
        BAD_REQUEST: 400,
        UNAUTHORIZED: 401,
        FORBIDDEN: 403,
        NOT_FOUND: 404,
        METHOD_NOT_ALLOWED: 405,
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
        NOT_FOUND: '/404'
    })
    .constant('MESSAGES', {
        ERROR: {
            UNAUTHORIZED: 'Не удалось авторизироваться',
            INTERNAL_SERVER_ERROR: 'Внутренняя ошибка сервера'
        }
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

    .directive('addJuridicWizard', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/templates/addJuridicWizardTemplate.html',
            scope: {
                juridicData: '=model',
                account: '=account',
                modal: '=modal',
                close: '&close'
            },
            controller: function ($scope, $http, REST_CONFIG, errorFactory, $timeout, $filter) {
                $scope.today = new Date();
                $scope.wizardStep = 0;
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
                    $http.post(REST_CONFIG.BASE_URL + '/accounts/' + $scope.account['account_uuid'] + '/companies', $scope.juridicData)
                        .success(function () {
                            if ($scope.modal) {
                                $scope.modal.close();
                            }
                            $scope.wizardStep = 0;
                        }).error(errorFactory.resolve);
                };

                function prepareDatesFormat() {
                    if ($scope.juridicData.pfr.date_registration) $scope.juridicData.pfr.date_registration = getTimestamp($scope.juridicData.pfr.date_registration);
                    if ($scope.juridicData.fms.date_registration) $scope.juridicData.fms.date_registration = getTimestamp($scope.juridicData.fms.date_registration);
                    if ($scope.juridicData.misc.documentDate) $scope.juridicData.misc.documentDate = getTimestamp($scope.juridicData.misc.documentDate);
                    for (var k in $scope.juridicData.tax) {
                        if ($scope.juridicData.tax[k].date_accounting) $scope.juridicData.tax[k].date_accounting = getTimestamp($scope.juridicData.fms.date_accounting);
                        if ($scope.juridicData.tax[k].date_registration) $scope.juridicData.tax[k].date_registration = getTimestamp($scope.juridicData.fms.date_registration);
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
'use strict';

angular.module('common.factories', [
        'website.constants'
    ])
    .factory('storageFactory', ['$http', 'cookieFactory', function ($http, cookieFactory) {
        var storage = {
            cookie: {
                token: 'token'
            },
            local: {
                accounts: 'accounts',
                user: 'user'
            }
        };

        function get(key) {
            var value = localStorage.getItem(key);
            return value ? JSON.parse(value) : null;
        }

        function getCookie(key) {
            return cookieFactory.getItem(key);
        }

        function set(key, value) {
            localStorage.setItem(key, JSON.stringify(value));
        }

        function remove(key) {
            localStorage.removeItem(key);
        }

        return {
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
            }
        };
    }])

    .factory('redirectFactory', ['ROUTES', '$location', 'WEB_CONFIG', function (ROUTES, $location, WEB_CONFIG) {
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
                redirectTo(WEB_CONFIG.BASE_URL + '/user/logout');
            },
            redirectCustomPath: function (path) {
                redirectTo(path);
            }
        };
    }])

    .factory('cookieFactory', ['WEB_CONFIG', function (WEB_CONFIG) {
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

    .factory('errorFactory', ['RESPONSE_STATUS', 'MESSAGES', '$rootScope', 'redirectFactory', 'cookieFactory', function (RESPONSE_STATUS, MESSAGES, $rootScope, redirectFactory, cookieFactory) {
        return {
            resolve: function (data, status, isLoginPage) {
                var type = (status >= 400) ? 'danger' : 'success';

                if (status === RESPONSE_STATUS.UNAUTHORIZED) {
                    if (isLoginPage === true) {
                        return {msg: MESSAGES.ERROR.UNAUTHORIZED, type: type};
                    } else {
                        cookieFactory.removeItem("token");
                        return redirectFactory.logout();
                    }
                } else if (status === RESPONSE_STATUS.NOT_FOUND || status === RESPONSE_STATUS.INTERNAL_SERVER_ERROR) {
                    return {msg: MESSAGES.ERROR.INTERNAL_SERVER_ERROR, type: type};
                } else {
                    return {msg: data.error, type: type};
                }
            }
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

angular.module('website.account', [])

    .controller('accountController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', 'storageFactory', '$modal', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS, storageFactory, $modal) {
        $rootScope.pageTitle = 'Аккаунт';
        $rootScope.bodyColor = 'filled_bg';
        $scope.companyModal = null;
        $scope.selectedAccount = null;
        $scope.showJuridicWizard = false;
        $scope.showConfirmationModal = false;

        $scope.prepareAddCompany = function (account) {
            $scope.showConfirmationModal = true;
            $scope.selectedAccount = account;
            openCompanyModal();
        };

        $scope.launchCompanyWizard = function () {
            $scope.showConfirmationModal = false;
            $scope.showJuridicWizard = true;
        };

        function openCompanyModal() {
            $scope.companyModal = $modal.open({
                templateUrl: 'addCompanyModalContent.html',
                scope: $scope,
                controller: 'addCompanyModalController'
            });
        }

        function closeCompanyModal() {
            $scope.companyModal.close();
            $scope.selectedAccount = null;
            $scope.showConfirmationModal = false;
            $scope.showJuridicWizard = false;
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
                }).error(errorFactory.resolve);
        }

        $scope.removeAccount = function (account) {
            $http.delete(REST_CONFIG.BASE_URL + '/accounts/' + account['account_uuid'])
                .success(function () {
                    getAccounts();
                }).error(errorFactory.resolve);
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

        /*
         $scope.saveAccountData = function () {
         $http.post(REST_CONFIG.BASE_URL + '/accounts', {title: $scope.accountData.title})
         .success(function () {
         $scope.getAccounts();
         $scope.showAccountRegistration = false;
         $scope.showJuridicWizard = true;
         }).error(errorFactory.resolve);
         };*/
    }])
;
'use strict';

angular.module('website.dashboard', [])

    .controller('dashboardController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', 'storageFactory', '$modal', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS, storageFactory, $modal) {
        $rootScope.pageTitle = 'dashboard';
        $rootScope.bodyColor = 'filled_bg';
        $scope.accountModal = null;
        $scope.accountData = [];
        $scope.firstAccount = null;
        $scope.showAccountRegistration = false;
        $scope.showJuridicWizard = false;
        checkForAccounts();

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
                    }
                }).error(onError);
        }

        $scope.removeAccount = function (account) {
            $http.delete(REST_CONFIG.BASE_URL + '/accounts/' + account.account_uuid)
                .success(function () {
                    getAccounts();
                }).error(onError);
        };
    }])

    .controller('registrationModalController', ['$scope', '$http', 'REST_CONFIG', 'errorFactory', '$timeout', function ($scope, $http, REST_CONFIG, errorFactory, $timeout) {

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
                    $scope.showJuridicWizard = true;
                }).error(errorFactory.resolve);
        };
    }])
;
'use strict';

angular.module('website.page.errors', [])

    .controller('pageNotFoundController', ['$scope', function ($scope) {
        //
    }])
;
'use strict';

angular.module('website.public.offer', [])

    .controller('publicOfferController', ['$scope', '$rootScope', function ($scope, $rootScope) {
        $rootScope.pageTitle = 'Аккаунт';
        $rootScope.bodyColor = 'filled_bg';
    }])
;
'use strict';

angular.module('website.sign', [])

    .controller('signInController', ['$scope', '$rootScope', function ($scope, $rootScope) {
        $rootScope.pageTitle = 'Вход';
    }])
;
'use strict';

angular.module('website.user.profile', [])

    .controller('userProfileController', ['$scope', '$rootScope', '$http', 'storageFactory', 'errorFactory', 'redirectFactory', '$timeout', function ($scope, $rootScope, $http, storageFactory, errorFactory, redirectFactory, $timeout) {
        $rootScope.pageTitle = 'Профиль';
        $rootScope.bodyColor = 'filled_bg';

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
            $http.post('', $scope.profileData)
                .success(function (data) {
                    //storageFactory.setUser(data.user);
                }).error(errorFactory.resolve);
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
            controller: function ($scope, $location) {
                $scope.getClass = function (path) {
                    if ('/' + $location.path().substr(1, path.length) == path) {
                        return "active"
                    } else {
                        return ""
                    }
                }
            }
        };
    })
;