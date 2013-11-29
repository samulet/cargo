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
        'website.page.errors'
    ])
    .config(['$routeProvider', '$httpProvider', '$locationProvider', 'ACCESS_LEVEL', 'ROUTES', function ($routeProvider, $httpProvider, $locationProvider, ACCESS_LEVEL, ROUTES) {
        var pathToIncs = 'html/pages/';
        $routeProvider.when(ROUTES.START_PAGE, {redirectTo: ROUTES.SIGN_IN});
        $routeProvider.when(ROUTES.START_PAGE_ALT, {redirectTo: ROUTES.SIGN_IN});
        $routeProvider.when(ROUTES.SIGN_IN, {templateUrl: pathToIncs + 'sign_in.html', access: ACCESS_LEVEL.PUBLIC});
        $routeProvider.when(ROUTES.NOT_FOUND, {templateUrl: pathToIncs + '404.html', access: ACCESS_LEVEL.PUBLIC});
        $routeProvider.when(ROUTES.USER_PROFILE, {templateUrl: pathToIncs + 'user_profile.html', access: ACCESS_LEVEL.AUTHORIZED});
        $routeProvider.when(ROUTES.DASHBOARD, {templateUrl: pathToIncs + 'dashboard.html', access: ACCESS_LEVEL.AUTHORIZED});

        //$routeProvider.otherwise({redirectTo: '/404'});
        $routeProvider.otherwise({redirectTo: ROUTES.SIGN_IN}); //TODO remove this hack after solve redirect problem

        $locationProvider.html5Mode(false);
        $locationProvider.hashPrefix('!');

        //$sceDelegateProvider.resourceUrlWhitelist(['self', 'http://api*.cargo.dev:8000/**']);
        //$httpProvider.defaults.useXDomain = true;
        //delete $httpProvider.defaults.headers.common['X-Requested-With'];

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
            }
            if (isToken && (next.originalPath === ROUTES.SIGN_IN)) {
                redirectFactory.goDashboard();
            } else if (!isToken && (next.access >= ACCESS_LEVEL.AUTHORIZED)) {
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
        AUTHORIZED: 1
    })
    .constant('ROUTES', {
        START_PAGE: '/',
        START_PAGE_ALT: '',
        SIGN_IN: '/sign/in',
        DASHBOARD: '/dashboard',
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
                    var ngDisabled = elem.getAttribute('ng-disabled') ? elem.getAttribute('ng-disabled') : elem.getAttribute('data-ng-disabled')
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

    .factory('redirectFactory', ['ROUTES', '$location', function (ROUTES, $location) {
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

        function openNewWindow(url) {
            if (isOldBrowser) {
                redirectOldBrowserCompatable(url);
            } else {
                window.open(url);
            }
        }

        return {
            goHomePage: function () {
                redirectTo(ROUTES.SIGN_IN);
            },
            goSignIn: function () {
                redirectTo(ROUTES.SIGN_IN);
            },
            goDashboard: function () {
                redirectTo(ROUTES.DASHBOARD);
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
                        redirectFactory.goHomePage();
                        return null;
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
  "HOST": "api.cargo",
  "HOST_CONTEXT": "",
  "PORT": "8000",
  "DOMAIN": "api.cargo.dev",
  "BASE_URL": "http://api.cargo.dev:8000"
})

;
'use strict';

angular.module('website.dashboard', [])

    .controller('dashboardController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', 'storageFactory', '$modal', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS, storageFactory, $modal) {
        $rootScope.pageTitle = 'dashboard';
        $rootScope.bodyColor = 'filled_bg';
        var accountModal;
        $scope.account = [];
        $scope.today = new Date();

        checkForAccounts();

        function checkForAccounts() {
            if (!storageFactory.getAccounts()) {
                $scope.registrationStep = 1;//TODO should be = 0
                getAccounts();
            }
        }

        function openAccountModal() {
            accountModal = $modal.open({
                templateUrl: 'registrationModalContent.html',
                backdrop: 'static',
                scope: $scope,
                controller: 'registrationModalController'
            });
        }

        function closeAccountModal() {
            accountModal.close();
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
                errorFactory.resolve(data, status, true);
            }
        }

        function getAccounts() {
            $http.get(REST_CONFIG.BASE_URL + '/accounts')
                .success(function (accounts) {
                    //  storageFactory.setAccounts(accounts);//TODO
                    openAccountModal();//TODO
                }).error(onError);
        }
    }])

    .controller('registrationModalController', ['$scope', '$http', 'REST_CONFIG', 'errorFactory', '$timeout', function ($scope, $http, REST_CONFIG, errorFactory, $timeout) {
        $scope.juridicData = {};

        $scope.openCatalog = function () {
            //placeholder
        };

        $scope.openDatePopup = function (isOpen) {
            $timeout(function () {
                $scope[isOpen] = true;
            });
        };

        $scope.saveAccountData = function () {
            $http.post(REST_CONFIG.BASE_URL + '/accounts', {name: $scope.account.name})
                .success(function () {
                    $scope.closeAccountModal();
                    $scope.getAccounts();
                    $scope.registrationStep = 1;
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
        $scope.showAddPhoneForm = false;
        $scope.showAddAddressForm = false;
        $scope.showAddEmailForm = false;

        var tempData = {
            phone: {},
            address: {},
            site: {},
            email: {}
        };

        $scope.tempData = tempData;

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

        function moveToProfileData(itemFrom, itemTo) {
            $scope.profileData[itemTo].push($scope.tempData[itemFrom]);
            $scope.tempData[itemFrom] = {};
        }

        $scope.addPhone = function () {
            moveToProfileData('phone', 'phones');
        };

        $scope.addEmail = function () {
            moveToProfileData('email', 'emails');
        };

        $scope.addAddress = function () {
            moveToProfileData('address', 'addresses');
        };

        $scope.addSite = function () {
            moveToProfileData('site', 'sites');
        };

        $scope.remove = function (from, element) {
            var index = $scope.profileData[from].indexOf(element);
            if (index !== -1) $scope.profileData[from].splice(index, 1);
        };

        $scope.cancelEdit = function () {
            $scope.editMode = false;
            $scope.showAddPhoneForm = false;
            $scope.showAddAddressForm = false;
            $scope.showAddEmailForm = false;
            $scope.tempData = tempData;
        };

        $scope.saveEdit = function () {
            $http.post('', $scope.profileData)
                .success(function (data) {
                    //storageFactory.setUser(data.user);
                }).error(errorFactory.resolve);
        };

        $scope.openDatePopup = function(isOpen) {
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

    .directive('topPublicMenu', function () {
        return {
            restrict: 'E',
            /*scope: {
             current: '=current'
             },*/
            templateUrl: 'html/partials/public/top_menu.html',
            controller: function ($scope) {
                //
            }
        };
    })

    .directive('topPrivateMenu', function () {
        return {
            restrict: 'E',
            /*scope: {
             current: '=current'
             },*/
            templateUrl: 'html/partials/private/top_menu.html',
            controller: function ($scope) {
                //
            }
        };
    })
;