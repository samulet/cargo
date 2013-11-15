'use strict';

angular.module('website', [
        'ngRoute',
        'ngAnimate',
        'env.config',
        'website.constants',
        'common.factories',
        'common.directives',
        'website.top.menu',
        'website.sign',
        'website.dashboard',
        'website.user.profile',
        'website.page.errors'
    ])
    .config(['$routeProvider', '$httpProvider', '$locationProvider', 'ACCESS_LEVEL', 'ROUTES', function ($routeProvider, $httpProvider, $locationProvider, ACCESS_LEVEL, ROUTES) {
        var pathToIncs = 'pages/';
        $routeProvider.when(ROUTES.START_PAGE, {redirectTo: ROUTES.SIGN_IN});
        $routeProvider.when(ROUTES.START_PAGE_ALT, {redirectTo: ROUTES.SIGN_IN});
        $routeProvider.when(ROUTES.SIGN_IN, {templateUrl: pathToIncs + 'sign_in.html', controller: 'signInController', access: ACCESS_LEVEL.PUBLIC});
        $routeProvider.when(ROUTES.SIGN_UP, {templateUrl: pathToIncs + 'sign_up.html', controller: 'signUpController', access: ACCESS_LEVEL.PUBLIC});
        $routeProvider.when(ROUTES.NOT_FOUND, {templateUrl: pathToIncs + '404.html', controller: 'pageNotFoundController', access: ACCESS_LEVEL.PUBLIC});
        $routeProvider.when(ROUTES.DASHBOARD, {templateUrl: pathToIncs + 'dashboard.html', controller: 'dashBoardController', access: ACCESS_LEVEL.AUTHORIZED});
        $routeProvider.when(ROUTES.USER_PROFILE, {templateUrl: pathToIncs + 'user_profile.html', controller: 'userProfileController', access: ACCESS_LEVEL.AUTHORIZED});

        //$routeProvider.otherwise({redirectTo: '/404'});
        $routeProvider.otherwise({redirectTo: ROUTES.SIGN_IN}); //TODO remove this hack after solve redirect problem

        $locationProvider.html5Mode(false);
        $locationProvider.hashPrefix('!');

    }])
    .filter('routeFilter', function () {
        return function (route) {
            return   '/#!' + route;
        };
    })
    .run(['$rootScope', 'ACCESS_LEVEL', 'ROUTES', function ($rootScope, ACCESS_LEVEL, ROUTES) {
        $rootScope.ROUTES = ROUTES;
        /* $rootScope.$on("$routeChangeStart", function (event, currRoute, prevRoute) {   //TODO or $routeChangeSuccess instead of $routeChangeStart?

         */
        /*if (currRoute.access >= ACCESS_LEVEL.AUTHORIZED && !cookieFactory.getItem(COOKIE.TOKEN)) {
         //TODO redirect or smt else
         }*/
        /*
         });*/

    }])
;
