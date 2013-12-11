angular.module('website.constants', [])
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
        AUTHORIZED: 1,
        ADMIN: 2
    })
    .constant('ROUTES', {
        START_PAGE: '/',
        START_PAGE_ALT: '',
        DASHBOARD: '/dashboard',
        ACCOUNT: '/account',
        PUBLIC_OFFER: '/public/offer',
        USER_PROFILE: '/user/profile',
        NOT_FOUND: '/404'
    })
    .constant('MESSAGES', {
        ERROR: {
            UNAUTHORIZED: 'Не удалось авторизироваться',
            INTERNAL_SERVER_ERROR: 'Внутренняя ошибка сервера'
        }
    })
;