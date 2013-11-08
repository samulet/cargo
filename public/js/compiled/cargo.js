;
'use strict';

angular.module('website', [
        'env.config',
        'common.factories',
        'website.top.menu',
        'website.sign',
        'website.mainPage'
    ])
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
    .config(['$routeProvider', '$httpProvider', '$locationProvider', 'ACCESS_LEVEL', function ($routeProvider, $httpProvider, $locationProvider, ACCESS_LEVEL) {
        var pathToIncs = 'pages/';
        $routeProvider.when('/', {templateUrl: pathToIncs + 'main_page.html', controller: 'mainPageController', access: ACCESS_LEVEL.PUBLIC});
        $routeProvider.when('/sign/up', {templateUrl: pathToIncs + 'sign_up.html', controller: 'signUpController', access: ACCESS_LEVEL.PUBLIC});
        $routeProvider.when('/sign/in', {templateUrl: pathToIncs + 'sign_in.html', controller: 'signInController', access: ACCESS_LEVEL.PUBLIC});

        $routeProvider.when('/404', {templateUrl: pathToIncs + '404.html', controller: 'pageNotFoundController', access: ACCESS_LEVEL.PUBLIC});

        $routeProvider.otherwise({redirectTo: '/404'});

        $locationProvider.html5Mode(false);
        $locationProvider.hashPrefix('!');

    }])

    .run(['$rootScope', 'ACCESS_LEVEL', function ($rootScope, ACCESS_LEVEL) {
        $rootScope.$on("$routeChangeStart", function (event, currRoute, prevRoute) {   //TODO or $routeChangeSuccess instead of $routeChangeStart?

            /*if (currRoute.access >= ACCESS_LEVEL.AUTHORIZED && !cookieFactory.getItem(COOKIE.TOKEN)) {
             //TODO redirect or smt else
             }*/
        });

    }])
;

;
'use strict';

angular.module('common.factories', [])
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

        }
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
            goBusinessHomePage: function () {
                redirectTo(businessUrl + '/home');
            },
            redirectCustomPath: function (path) {
                redirectTo(path);
            }
        }
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