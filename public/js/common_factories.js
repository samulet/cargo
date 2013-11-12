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



    .factory('messagesFactory', ['HTTP_STATUS', '$rootScope', 'redirectFactory', 'cookieFactory', function (HTTP_STATUS, $rootScope, redirectFactory, cookieFactory) {
        var container;

        function showMessage(container, type, messages) {
            if (!container) container = $rootScope.messages;
            container.type = type;
            container.msg = angular.isArray(messages) ? messages : [messages];
        }

        function parseErrors(data, status, isLoginPage) {
            var messages;
            if (status === HTTP_STATUS.UNAUTHORIZED) {
                if (isLoginPage === true) {
                    messages = window.messages.unauthorizedErrorMessage;
                } else {
                    cookieFactory.removeItem("token");
                    redirectFactory.goLoginPage();
                    return null;
                }
            } else if (status === HTTP_STATUS.NOT_FOUND || status === HTTP_STATUS.INTERNAL_SERVER_ERROR) {
                messages = window.messages.commonErrorMessage;
            } else {
                messages = data.error;
            }
            return messages;
        }


        return {
            showWarning: function (message) {
                showMessage($rootScope.messages, 'warning', message);
            },
            showInfo: function (message) {
                showMessage($rootScope.messages, 'info', message);
            },
            showSuccess: function (message) {
                showMessage($rootScope.messages, 'success', message);
            },
            showError: function (message) {
                showMessage($rootScope.messages, 'danger', message);
            },
            clear: function () {
                $rootScope.messages = {};
            },
            clearP: function () {
                delete container.type;
                delete container.msg;
            }
        }
    }])
;