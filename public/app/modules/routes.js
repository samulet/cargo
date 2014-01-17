'use strict';

angular.module('website.routes', [])
    .constant('ROUTES', {
        START_PAGE: '/',
        START_PAGE_ALT: '',
        DASHBOARD: '/dashboard',
        ACCOUNT: '/account',
        PUBLIC_OFFER: '/public/offer',
        USER_PROFILE: '/user/profile',
        LOGOUT: '/user/logout',
        LINKING: '/linking/:type',
        NOT_FOUND: '/404'
    })

    .filter('routeFilter', ['ROUTES', function (ROUTES) {

        var params = {
            type: '/:type'
        };

        function getLinkingUrlWithParams(paramValue) {
            return (ROUTES.LINKING).replace(params.type, params.type.replace(params.type, '/' + paramValue));
        }

        function getLinkingUrl(url, value) {
            if (url === ROUTES.LINKING) {
                return getLinkingUrlWithParams(value);
            }

            return url;
        }

        return getLinkingUrl;
    }])
;