'use strict';

angular.module('website.modal', [])
    .directive('modalShow', function () {
        return {
            restrict: 'A',
            templateUrl: 'app/modules/modal/modal.html',
            replace: true,
            //transclude: true,
            /*link: function (scope, elem, attrs) {
             scope.showClass = null;

             function closeModal() {
             attrs.ariaHidden = "true";
             scope.showClass = null;
             attrs.modalShow = false;
             }

             scope.closeModal = function () {
             closeModal();
             };

             scope.$watch(attrs.modalShow, function (value) {
             if (value) {
             attrs.ariaHidden = "false";
             scope.showClass = 'display_block modal_background';
             setTimeout(function(){
             scope.showClass = scope.showClass + ' in';
             scope.$apply();
             }, 500);
             } else {
             //closeModal();
             }
             });

             }*/
            link: function (scope, elem, attrs) {
                scope.modalShow = attrs.modalShow;
            },
            controller: function ($scope, $timeout) {
                $scope.showClass = null;

                function closeModal() {
                    // attrs.ariaHidden = "true";
                    $scope.showClass = null;
                    $scope.modalShow = false;
                }

                $scope.closeModal = function () {
                    closeModal();
                };

                $scope.$watch(function () {
                        return $scope.modalShow;
                    }, function (value) {
                        if ($scope[value] === true) {
                            //attrs.ariaHidden = "false";
                            $scope.showClass = 'display_block modal_background';
                            $timeout(function () {
                                $scope.showClass = $scope.showClass + ' in';
                            }, 200);
                        } else {
                            //closeModal();
                        }
                    }
                );
            }
        }
            ;
    })
;