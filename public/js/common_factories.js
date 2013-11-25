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

                //str += '; domain=' + ; //TODO should set domain (check it with 'localhost')

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