'use strict';

angular.module('website.modal', [])
    .directive('modalShow', function () {
        return {
            restrict: 'A',
            scope: {
                modalShow: '='
            },
            templateUrl: 'app/modules/modal/modal.html',
            replace: true,
            transclude: true,
            controller: function ($scope) {
                $scope.showClass = null;

                $scope.$watch($scope.modalShow, function (value) {
                    if ($scope.modalShow) {
                        $scope.showClass = 'in display_block';
                    } else {

                    }
                });

            }
        };
    })
;