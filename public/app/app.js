'use strict';

angular.module('website', [
        'ngRoute',
        'ngAnimate',
        'website.env.config',
        'website.constants',
        'website.top.menu',
        'website.user.profile',
        'website.form.blocks',
        'website.user.param',
        'website.dashboard',
        'website.account',
        'website.public.offer',
        'website.custom.attrs',
        'website.storage',
        'website.error',
        'website.redirect',
        'website.cookies',
        'website.modal',
        'website.catalogue',
        'website.import',
        'website.entities.linking',
        'ui.bootstrap',
        'ui.select2',
        'ngGrid'
    ])
    .config(['$routeProvider', '$httpProvider', '$locationProvider', 'ACCESS_LEVEL', 'ROUTES', function ($routeProvider, $httpProvider, $locationProvider, ACCESS_LEVEL, ROUTES) {
        var pathToIncs = 'app/pages/';
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
