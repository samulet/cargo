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
    .constant('ROUTES', {
        START_PAGE: '/',
        START_PAGE_ALT: '',
        SIGN_UP: '/sign/up',
        SIGN_IN: '/sign/in',
        NOT_FOUND: '/404'
    })
    .config(['$routeProvider', '$httpProvider', '$locationProvider', 'ACCESS_LEVEL', 'ROUTES', function ($routeProvider, $httpProvider, $locationProvider, ACCESS_LEVEL, ROUTES) {
        var pathToIncs = 'pages/';
        $routeProvider.when(ROUTES.START_PAGE, {templateUrl: pathToIncs + 'sign_in.html', controller: 'signInController', access: ACCESS_LEVEL.PUBLIC});
        $routeProvider.when(ROUTES.START_PAGE_ALT, {templateUrl: pathToIncs + 'sign_in.html', controller: 'signInController', access: ACCESS_LEVEL.PUBLIC});
        $routeProvider.when(ROUTES.SIGN_IN, {templateUrl: pathToIncs + 'sign_in.html', controller: 'signInController', access: ACCESS_LEVEL.PUBLIC});
        $routeProvider.when(ROUTES.SIGN_UP, {templateUrl: pathToIncs + 'sign_up.html', controller: 'signUpController', access: ACCESS_LEVEL.PUBLIC});

        $routeProvider.when(ROUTES.NOT_FOUND, {templateUrl: pathToIncs + '404.html', controller: 'pageNotFoundController', access: ACCESS_LEVEL.PUBLIC});

        $routeProvider.otherwise({redirectTo: '/404'});

        $locationProvider.html5Mode(false);
        $locationProvider.hashPrefix('!');

    }])
    .filter('routeFilter', ['localeFactory', '$filter', function (localeFactory, $filter) {
        return function (route) {
            return   '/#!' + route;
        };
    }])
    .run(['$rootScope', 'ACCESS_LEVEL', function ($rootScope, ACCESS_LEVEL) {
        $rootScope.$on("$routeChangeStart", function (event, currRoute, prevRoute) {   //TODO or $routeChangeSuccess instead of $routeChangeStart?

            /*if (currRoute.access >= ACCESS_LEVEL.AUTHORIZED && !cookieFactory.getItem(COOKIE.TOKEN)) {
             //TODO redirect or smt else
             }*/
        });

    }])
;
