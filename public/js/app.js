;
'use strict';

angular.module('website', [
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
