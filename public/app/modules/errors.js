'use strict';

angular.module('website.error', [])

    .factory('errorFactory', ['RESPONSE_STATUS', 'MESSAGES', '$rootScope', 'redirectFactory', function (RESPONSE_STATUS, MESSAGES, $rootScope, redirectFactory) {

        function isUnauthorized(status) {
            return (status === RESPONSE_STATUS.UNAUTHORIZED || status === RESPONSE_STATUS.FORBIDDEN || status === RESPONSE_STATUS.PROXY_AUTHENTICATION_REQUIRED);
        }

        function getError(status, data) {
            var type = (status >= 400) ? 'danger' : 'success';

            if (isUnauthorized(status)) {
                return {msg: MESSAGES.ERROR.UNAUTHORIZED, type: type};
            }

            if (status === RESPONSE_STATUS.NOT_FOUND || status === RESPONSE_STATUS.INTERNAL_SERVER_ERROR) {
                return {msg: MESSAGES.ERROR.INTERNAL_SERVER_ERROR, type: type};
            }

            if (status === RESPONSE_STATUS.UNPROCESSABLE_ENTITY) {
                return {msg: MESSAGES.ERROR.CANNOT_BE_DONE_ERROR, type: type};
            }

            if (data.message) {
                return {msg: data.message, type: type};
            }

            if (data.error) {
                return {msg: data.error, type: type};
            }

            return {msg: MESSAGES.ERROR.UNKNOWN_ERROR, type: type};
        }

        return {
            resolve: function (data, status, container, isLoginPage) {
                if (status === RESPONSE_STATUS.UNAUTHORIZED && !isLoginPage) {
                    redirectFactory.logout();
                }

                if (container) {
                    if (angular.isArray(container) && container.length > 0) {
                        for (var i = 0; i <= container.length; i++) {
                            if (container[i].msg === getError(status, data).msg) {
                                return;
                            }
                        }
                        container.push(getError(status, data));
                    } else {
                        container.push(getError(status, data));
                    }
                } else {
                    $rootScope.messages.push(getError(status, data));
                }
            },
            isUnauthorized: function (status) {
                return isUnauthorized(status);
            }
        };
    }])
;