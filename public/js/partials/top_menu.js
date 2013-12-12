'use strict';

angular.module('website.top.menu', [])

    .directive('topPrivateMenu', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/partials/private/top_menu.html',
            controller: function ($scope, $location) {
                $scope.getClass = function (path) {
                    if ('/' + $location.path().substr(1, path.length) == path) {
                        return "active"
                    } else {
                        return ""
                    }
                }
            }
        };
    })

    .directive('userMenu', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/partials/private/user_menu.html',
            controller: function ($scope, $location) {
              //TODO
            }
        };
    })
;