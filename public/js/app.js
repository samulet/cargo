'use strict';

angular.module('website', [
        'ngRoute',
        'env.config',
        'website.constants',
        'common.factories',
        'website.top.menu',
        'website.sign',
        'website.dashboard',
        'website.page.errors'
    ])
    .config(['$routeProvider', '$httpProvider', '$locationProvider', 'ACCESS_LEVEL', 'ROUTES', function ($routeProvider, $httpProvider, $locationProvider, ACCESS_LEVEL, ROUTES) {
        var pathToIncs = 'public/pages/';
        $routeProvider.when(ROUTES.START_PAGE, {templateUrl: pathToIncs + 'sign_in.html', controller: 'signInController', access: ACCESS_LEVEL.PUBLIC});
        $routeProvider.when(ROUTES.START_PAGE_ALT, {templateUrl: pathToIncs + 'sign_in.html', controller: 'signInController', access: ACCESS_LEVEL.PUBLIC});
        $routeProvider.when(ROUTES.SIGN_IN, {templateUrl: pathToIncs + 'sign_in.html', controller: 'signInController', access: ACCESS_LEVEL.PUBLIC});
        $routeProvider.when(ROUTES.SIGN_UP, {templateUrl: pathToIncs + 'sign_up.html', controller: 'signUpController', access: ACCESS_LEVEL.PUBLIC});
        $routeProvider.when(ROUTES.NOT_FOUND, {templateUrl: pathToIncs + '404.html', controller: 'pageNotFoundController', access: ACCESS_LEVEL.PUBLIC});
        $routeProvider.when(ROUTES.DASHBOARD, {templateUrl: pathToIncs + 'dashboard.html', controller: 'dashBoardController', access: ACCESS_LEVEL.AUTHORIZED});

        $routeProvider.otherwise({redirectTo: '/404'});

        $locationProvider.html5Mode(false);
        $locationProvider.hashPrefix('!');

    }])
    .filter('routeFilter', function () {
        return function (route) {
            return   '/#!' + route;
        };
    })
    .directive('alert', function () {
        return {
            restrict: 'EA',
            template: "<div class='alert alert-dismissable' ng-class='type && \"alert-\" + type'>\n <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>\n <div ng-transclude></div>\n</div>\n",
            transclude: true,
            replace: true,
            scope: {
                type: '=',
                close: '&'
            },
            link: function (scope, iElement, iAttrs) {
                scope.closeable = "close" in iAttrs;
            }
        };
    })

    .run(['$rootScope', 'ACCESS_LEVEL', 'ROUTES', function ($rootScope, ACCESS_LEVEL, ROUTES) {
        $rootScope.ROUTES = ROUTES;
       /* $rootScope.$on("$routeChangeStart", function (event, currRoute, prevRoute) {   //TODO or $routeChangeSuccess instead of $routeChangeStart?

            *//*if (currRoute.access >= ACCESS_LEVEL.AUTHORIZED && !cookieFactory.getItem(COOKIE.TOKEN)) {
             //TODO redirect or smt else
             }*//*
        });*/

    }])
;
