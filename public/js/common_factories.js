'use strict';

angular.module('common.factories', [
        'website.constants'
    ])
    .factory('storageFactory', ['$http', 'cookieFactory', '$rootScope', function ($http, cookieFactory, $rootScope) {
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
            }
        };

        function get(key) {
            var value = localStorage.getItem(key);
            return value ? JSON.parse(value) : null;
        }

        function getCookie(key) {
            return cookieFactory.getItem(key);
        }

        function removeCookie(key) {
            return cookieFactory.removeItem(key);
        }

        function set(key, value) {
            if (!key) throw "Invalid key for a value: " + value;
            if (!value) throw "Invalid value for a key :" + key;
            localStorage.setItem(key, JSON.stringify(value));
        }

        function remove(key) {
            localStorage.removeItem(key);
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
            setSelectedCompany: function (company) {
                set(storage.local.selectedCompany, company);
            },
            getSelectedCompany: function () {
                return get(storage.local.selectedCompany);
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

    .factory('errorFactory', ['RESPONSE_STATUS', 'MESSAGES', '$rootScope', 'redirectFactory', function (RESPONSE_STATUS, MESSAGES, $rootScope, redirectFactory) {
        return {
            resolve: function (data, status, isLoginPage) {
                var type = (status >= 400) ? 'danger' : 'success';

                if (status === RESPONSE_STATUS.UNAUTHORIZED) {
                    if (isLoginPage === true) {
                        return {msg: MESSAGES.ERROR.UNAUTHORIZED, type: type};
                    } else {
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

    .factory('userParamsFactory', ['$http', 'storageFactory', 'errorFactory', 'REST_CONFIG', function ($http, storageFactory, errorFactory, REST_CONFIG) {
        function getAccounts() {
            $http.get(REST_CONFIG.BASE_URL + '/accounts')
                .success(function (data) {
                    var accounts = data['_embedded'].accounts;
                    if (accounts.length === 1) {
                        storageFactory.setSelectedAccount(accounts[0]);
                        getCompanies(accounts[0], true);
                    } else if (accounts.length === 0) {
                        storageFactory.setSelectedAccount(null);
                        storageFactory.setSelectedCompany(null);
                    }
                }).error(errorFactory.resolve);
        }

        function getCompanies(account, isSetSelected) {
            $http.get(REST_CONFIG.BASE_URL + '/accounts/' + account['account_uuid'] + '/companies')
                .success(function (data) {
                    var companies = data['_embedded'].companies;
                    if (companies.length === 1 && isSetSelected === true) {
                        storageFactory.setSelectedCompany(companies[0]);
                    }
                }).error(errorFactory.resolve);
        }

        function getApiRoutes() {
            $http.get(REST_CONFIG.BASE_URL + '/meta').success(function (data) {
                storageFactory.setApiRoutes(data['_embedded']['resource_meta']);
            }).error(errorFactory.resolve);
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
                var selectedAccount = storageFactory.getSelectedAccount();
                var selectedCompany = storageFactory.getSelectedCompany();
                if (!selectedAccount || !selectedCompany) {
                    getAccounts();
                }
            }
        }
    }])
;