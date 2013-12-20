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
    .run(['$rootScope', 'ACCESS_LEVEL', 'ROUTES', 'cookieFactory', 'redirectFactory', 'storageFactory', '$http', 'userParamsFactory', function ($rootScope, ACCESS_LEVEL, ROUTES, cookieFactory, redirectFactory, storageFactory, $http, userParamsFactory) {
        $rootScope.ROUTES = ROUTES;
        $rootScope.isAjaxLoading = false;
        $rootScope.messages = [];

        userParamsFactory.getApiRoutes();
        userParamsFactory.prepareUser();

        $rootScope.$watch(function () {
            return localStorage.getItem(storageFactory.storage.local.selectedAccount);
        }, function (newValue) {
            if (newValue) {
                console.log('X-App-Account: ' + JSON.parse(newValue)['account_uuid']);//TODO remove
                $http.defaults.headers.common['X-App-Account'] = JSON.parse(newValue)['account_uuid'];
            }
        });

        $rootScope.$watch(function () {
            return localStorage.getItem(storageFactory.storage.local.selectedCompany);
        }, function (newValue) {
            if (newValue) {
                console.log('X-App-Company: ' + JSON.parse(newValue)['company_uuid']);//TODO remove
                $http.defaults.headers.common['X-App-Company'] = JSON.parse(newValue)['company_uuid'];
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

    }])
;
