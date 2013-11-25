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
        'website.dash',
        'website.page.errors'
    ])
    .config(['$routeProvider', '$httpProvider', '$locationProvider', 'ACCESS_LEVEL', 'ROUTES', function ($routeProvider, $httpProvider, $locationProvider, ACCESS_LEVEL, ROUTES) {
        var pathToIncs = 'html/pages/';
        $routeProvider.when(ROUTES.START_PAGE, {redirectTo: ROUTES.SIGN_IN});
        $routeProvider.when(ROUTES.START_PAGE_ALT, {redirectTo: ROUTES.SIGN_IN});
        $routeProvider.when(ROUTES.SIGN_IN, {templateUrl: pathToIncs + 'sign_in.html', controller: 'signInController', access: ACCESS_LEVEL.PUBLIC});
        $routeProvider.when(ROUTES.NOT_FOUND, {templateUrl: pathToIncs + '404.html', controller: 'pageNotFoundController', access: ACCESS_LEVEL.PUBLIC});
        $routeProvider.when(ROUTES.DASHBOARD, {templateUrl: pathToIncs + 'dashboard.html', controller: 'dashboardController', access: ACCESS_LEVEL.AUTHORIZED});
        $routeProvider.when(ROUTES.USER_PROFILE, {templateUrl: pathToIncs + 'user_profile.html', controller: 'userProfileController', access: ACCESS_LEVEL.AUTHORIZED});
        $routeProvider.when('/dash', {templateUrl: pathToIncs + 'dash.html', controller: 'dashController', access: ACCESS_LEVEL.AUTHORIZED});

        //$routeProvider.otherwise({redirectTo: '/404'});
        $routeProvider.otherwise({redirectTo: ROUTES.SIGN_IN}); //TODO remove this hack after solve redirect problem

        $locationProvider.html5Mode(false);
        $locationProvider.hashPrefix('!');

    }])
    .filter('routeFilter', function () {
        return function (route) {
            return '/#!' + route;
        };
    })
    .run(['$rootScope', 'ACCESS_LEVEL', 'ROUTES', 'cookieFactory', 'redirectFactory', 'STORAGE', function ($rootScope, ACCESS_LEVEL, ROUTES, cookieFactory, redirectFactory, STORAGE) {
        $rootScope.ROUTES = ROUTES;

        $rootScope.$on("$routeChangeStart", function (event, next, current) {

            var isToken = !!cookieFactory.getItem(STORAGE.COOKIE.TOKEN);
            var isPrivatePage = (next.access >= ACCESS_LEVEL.AUTHORIZED);
            var isSignInPage = (next.originalPath === ROUTES.SIGN_IN);

            if (isToken && isSignInPage) {
                redirectFactory.goDashboard();
            } else if (!isToken && isPrivatePage) {
                redirectFactory.goSignIn();
            }
        });

    }])
;
