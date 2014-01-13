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