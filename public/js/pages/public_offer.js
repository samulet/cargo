'use strict';

angular.module('website.public.offer', [])

    .controller('publicOfferController', ['$scope', '$rootScope', function ($scope, $rootScope) {
        $rootScope.pageTitle = 'Аккаунт';
    }])
;