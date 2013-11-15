'use strict';

angular.module('website', [
        'ngRoute',
        'ngAnimate',
        'env.config',
        'website.constants',
        'common.factories',
        'website.top.menu',
        'website.sign',
        'website.dashboard',
        'website.user.profile',
        'website.page.errors'
    ])
    .config(['$routeProvider', '$httpProvider', '$locationProvider', 'ACCESS_LEVEL', 'ROUTES', function ($routeProvider, $httpProvider, $locationProvider, ACCESS_LEVEL, ROUTES) {
        var pathToIncs = 'pages/';
        $routeProvider.when(ROUTES.START_PAGE, {redirectTo: ROUTES.SIGN_IN});
        $routeProvider.when(ROUTES.START_PAGE_ALT, {redirectTo: ROUTES.SIGN_IN});
        $routeProvider.when(ROUTES.SIGN_IN, {templateUrl: pathToIncs + 'sign_in.html', controller: 'signInController', access: ACCESS_LEVEL.PUBLIC});
        $routeProvider.when(ROUTES.SIGN_UP, {templateUrl: pathToIncs + 'sign_up.html', controller: 'signUpController', access: ACCESS_LEVEL.PUBLIC});
        $routeProvider.when(ROUTES.NOT_FOUND, {templateUrl: pathToIncs + '404.html', controller: 'pageNotFoundController', access: ACCESS_LEVEL.PUBLIC});
        $routeProvider.when(ROUTES.DASHBOARD, {templateUrl: pathToIncs + 'dashboard.html', controller: 'dashBoardController', access: ACCESS_LEVEL.AUTHORIZED});
        $routeProvider.when(ROUTES.USER_PROFILE, {templateUrl: pathToIncs + 'user_profile.html', controller: 'userProfileController', access: ACCESS_LEVEL.AUTHORIZED});

        //$routeProvider.otherwise({redirectTo: '/404'});
        $routeProvider.otherwise({redirectTo: ROUTES.SIGN_IN}); //TODO remove this hack after solve redirect problem

        $locationProvider.html5Mode(false);
        $locationProvider.hashPrefix('!');

    }])
    .filter('routeFilter', function () {
        return function (route) {
            return   '/#!' + route;
        };
    })
    .directive('alert', function () {
        return {
            restrict: 'EA',
            template: "<div class='alert alert-dismissable' ng-class='type && \"alert-\" + type'>\n <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>\n <div ng-transclude></div>\n</div>\n",
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

    .run(['$rootScope', 'ACCESS_LEVEL', 'ROUTES', function ($rootScope, ACCESS_LEVEL, ROUTES) {
        $rootScope.ROUTES = ROUTES;
        /* $rootScope.$on("$routeChangeStart", function (event, currRoute, prevRoute) {   //TODO or $routeChangeSuccess instead of $routeChangeStart?

         */
        /*if (currRoute.access >= ACCESS_LEVEL.AUTHORIZED && !cookieFactory.getItem(COOKIE.TOKEN)) {
         //TODO redirect or smt else
         }*/
        /*
         });*/

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
        SIGN_UP: '/sign/up',
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

angular.module('common.factories', [
        'website.constants'
    ])
    .factory('storageFactory', ['$http', function ($http) {
        var userKey = 'user';

        function get(key) {
            var value = localStorage.getItem(key);
            return value ? JSON.parse(value) : null;
        }

        function set(key, value) {
            localStorage.setItem(key, JSON.stringify(value));
        }

        function remove(key) {
            localStorage.removeItem(key);
        }

        return {
            getUser: function () {
                return get(userKey);
            },

            setUser: function (user) {
                set(userKey, user);
            }

        };
    }])

    .factory('redirectFactory', [function () {
        var isOldBrowser = navigator.userAgent.match(/MSIE\s(?!9.0)/);  // IE8 and lower

        function redirectOldBrowserCompatable(url) {
            var referLink = document.createElement('a');
            referLink.href = url;
            document.body.appendChild(referLink);
            referLink.click();
        }

        function redirectTo(url) {
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
                redirectTo('/');
            },
            goSignIn: function () {
                redirectTo('/sign/in');
            },
            goSignUp: function () {
                redirectTo('/sign/up');
            },
            goDashboard: function () {
                redirectTo('/dashboard');
            },
            redirectCustomPath: function (path) {
                redirectTo(path);
            }
        };
    }])

    .factory('cookieFactory', [function () {
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

                //str += '; domain=' + ; //TODO should set domain (check that it's work with 'localhost')

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
                document.cookie = 'token=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/;';
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
        }
    }])
;
"use strict";

angular.module("env.config", [])

    .constant("REST_CONFIG", {
        "PROTOCOL": "http",
        "HOST": "localhost",
        "HOST_CONTEXT": "",
        "PORT": "8080",
        "DOMAIN": "localhost",
        "BASE_URL": "http://localhost:8080"
    })

;
'use strict';

angular.module('website.dashboard', [])

    .controller('dashBoardController', ['$scope', function ($scope) {

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

    .controller('signUpController', ['$scope', '$http', 'storageFactory', function ($scope, $http, storageFactory) {
        //
    }])
    .controller('signInController', ['$scope', '$rootScope', '$http', 'storageFactory', 'errorFactory', 'redirectFactory', function ($scope, $rootScope, $http, storageFactory, errorFactory, redirectFactory) {
        $rootScope.pageTitle = 'Вход';

        /*$scope.messages = []; //TODO remove this, when done
         $scope.messages.push(errorFactory.resolve({error: 'CustomError'}, 400));*/

        function onError(data, status) {
            errorFactory.resolve(data, status, true);
        }

        $scope.signIn = function () {
            console.log($scope.signInData);
            $http.post('', { //TODO still don't know what the url to login
                email: $scope.signInData.email,
                password: $scope.signInData.password
            }).success(function (data) {
                    storageFactory.setUser(data.user); //TODO hope we should save a user
                    redirectFactory.goDashboard(); //TODO where we go now? Who knows...
                }).error(onError);
        };
    }])
;
'use strict';

angular.module('website.user.profile', [])

    .controller('userProfileController', ['$scope', '$rootScope', '$http', 'storageFactory', 'errorFactory', 'redirectFactory', function ($scope, $rootScope, $http, storageFactory, errorFactory, redirectFactory) {
        $rootScope.pageTitle = 'Профиль';
        $rootScope.bodyColor = 'filled_bg';

        $scope.editMode = true; //TODO false

        $scope.profileData = {
            tempPhone: {},
            social: {},
            personal: {},
            passport: {},
            phones: [],
            other: {}
        };

        $scope.startEdit = function () {
            $scope.editMode = true;
        };

        $scope.addPhone = function () {
            $scope.profileData.phones.push($scope.profileData.tempPhone);
            $scope.profileData.tempPhone = {};
        };

        $scope.removePhone = function (phone) {
            var index = $scope.profileData.phones.indexOf(phone);
            if (index !== -1) {
                $scope.profileData.phones.splice(index, 1);
            }


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
            templateUrl: 'partials/public/top_menu.html',
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
            templateUrl: 'partials/private/top_menu.html',
            controller: function ($scope) {
                //
            }
        };
    })
;