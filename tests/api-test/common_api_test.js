'use strict';

angular.module('test', [])

    .constant("WEB_CONFIG", {
        "PROTOCOL": "http",
        "HOST": "cargo",
        "HOST_CONTEXT": "",
        "PORT": "8000",
        "DOMAIN": "cargo.dev",
        "BASE_URL": "http://cargo.dev:8000"
    })

    .constant("REST_CONFIG", {
        "PROTOCOL": "http",
        "HOST": "cargo",
        "HOST_CONTEXT": "/api",
        "PORT": "8000",
        "DOMAIN": "cargo.dev",
        "BASE_URL": "http://cargo.dev:8000/api"
    })

    .directive('tile', function () {
        return {
            restrict: 'E',
            templateUrl: 'tile_template.html',
            scope: {
                title: '=title',
                route: '=route'
            },
            link: function (scope, elem, attrs) {

            }
        };
    })

    .controller('apiTestController', ['$scope', '$http', 'REST_CONFIG', function ($scope, $http, REST_CONFIG) {

        $scope.api = [
            {
                title: 'Cargo API Root',
                routes: [
                    {
                        method: 'GET',
                        url: '/api/',
                        status: null
                    }
                ]
            },
            {
                title: 'Account',
                routes: [
                    {
                        method: 'GET',
                        url: '/api/accounts{/uuid}',
                        status: null
                    },
                    {
                        method: 'DELETE',
                        url: '/api/accounts{/uuid}',
                        status: null
                    },
                    {
                        method: 'PATCH',
                        url: '/api/accounts{/uuid}',
                        status: null
                    }
                ]
            }
        ]
    }])
;
