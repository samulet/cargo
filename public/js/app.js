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

        $rootScope.$watch(function () { //TODO check a performance here
            return localStorage.getItem('selected_account'); //TODO check with storageFactory.getSelectedAccount();
        }, function (newValue) {
            $rootScope.selectedAccount = newValue;
            $http.defaults.headers.common['X-App-Account'] = newValue;
        });

        $rootScope.$watch(function () {
            return localStorage.getItem('selected_company'); //TODO check with storageFactory.getSelectedCompany();
        }, function (newValue) {
            $rootScope.selectedCompany = newValue;
            $http.defaults.headers.common['X-App-Company'] = newValue;
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
