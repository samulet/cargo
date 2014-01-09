'use strict';

angular.module('catalogue', [])

    .directive('catalogue', function () {
        return {
            restrict: 'A',
            scope: {
                getData: '=catalogue',
                options: '=catalogOptions'
            },
            controller: function ($scope, $modal) {
                function openCatalogueModal() {
                    $scope.isCatalogueModalOpened = true;
                    $scope.catalogueModal = $modal.open({
                        templateUrl: 'catalogueModalContent.html',
                        backdrop: 'static',
                        scope: $scope,
                        controller: 'catalogueModalController'
                    });
                }

                $scope.openCatalogue = function () {
                    openCatalogueModal();
                };

                function closeCatalogueModal() {
                    $scope.catalogueModal.close();
                }

                $scope.closeCatalogue = function () {
                    closeCatalogueModal();
                };
            },
            compile: function (scope, element, attrs) {
                return function (scope, elem) {
                    element.$$element[0].setAttribute('readonly', 'true');
                    element.$$element.on('click', function (event) {
                        event.preventDefault();
                        scope.openCatalogue();
                    });
                }
            }
        };
    })

    .directive('catalogueModal', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/templates/catalog.html'/*,
             scope: {},
             controller: function ($scope, $modal) {
             $scope.isCatalogueModalOpened = false;
             }*/
        };
    })

    .controller('catalogueModalController', ['$scope', function ($scope) {
        $scope.selected;
        $scope.details = {};

        /*scope.$watch(attrs.uiSelect2, function(opts) {
         elm.select2(opts);
         }, true);*/

        if ($scope.isCatalogueModalOpened) {
            $scope.data = $scope.getData();
            //jQuery('.select2-no-results').setAttr('onclick="alert(1);"');
        }

        function updateOptionDetails(option) {
            $scope.details.firstname = option.firstname;
            $scope.details.lastName = option.lastName;
            $scope.details.age = option.age;
        }

        function findSelectedOption() {
            for (var i = 0; i <= $scope.data.length - 1; i++) {
                if ($scope.selected && $scope.data[i].value === $scope.selected) {
                    return $scope.data[i];
                }
            }
            return null;
        }

        $scope.getSelectedValueByKey = function () {
            var selectedOption = findSelectedOption();
            if (selectedOption) {
                updateOptionDetails(selectedOption);
            }
        };

        $scope.setSelectedOptions = function () {
            $scope.selectedOption = $scope.selected;
            $scope.closeCatalogue();
        };

    }])
;