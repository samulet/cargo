'use strict';

angular.module('catalogue', [])

    .directive('website.catalogue', function () {
        return {
            restrict: 'A',
            scope: {
                getData: '=catalogue',
            },
            controller: function ($scope, $modal) {
                $scope.details = {};

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
                    if ($scope.details.value) {
                        $scope.selectedModel = $scope.details.value;
                        $scope.selectedOption = $scope.details;
                    }
                    openCatalogueModal();
                };

                function closeCatalogueModal() {
                    $scope.catalogueModal.close();
                }

                $scope.closeCatalogue = function () {
                    closeCatalogueModal();
                };
            },
            compile: function (scope, element) {
                return function (scope, elem) {
                    scope.catalogueElement = element;
                    element.$$element[0].setAttribute('readonly', 'true');
                    element.$$element.on('click', function (event) {
                        event.preventDefault();
                        scope.openCatalogue();
                    });
                };
            }
        };
    })

    .directive('catalogueModal', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/templates/catalog.html'
        };
    })

    .controller('catalogueModalController', ['$scope', function ($scope) {

        if ($scope.isCatalogueModalOpened) {
            $scope.data = $scope.getData();
        }

        function updateOptionDetails(option) {
            $scope.details.firstName = option.firstName;
            $scope.details.lastName = option.lastName;
            $scope.details.age = option.age;
            $scope.details.value = option.value;
            $scope.details.description = option.description;
        }

        function findSelectedOption(value) {
            for (var i = 0; i <= $scope.data.length - 1; i++) {
                if (value && $scope.data[i].value === +value) {
                    return $scope.data[i];
                }
            }
            return null;
        }

        $scope.changeSelectedOption = function (value) {
            if (value) {
                $scope.selectedOption = findSelectedOption(value);
                updateOptionDetails($scope.selectedOption);
            }
        };

        $scope.setSelectedOptions = function () {
            if ($scope.selectedOption) {
                $scope.catalogueElement.$$element[0].value = $scope.selectedOption.description;
            } else {
                $scope.catalogueElement.$$element[0].value = "";
            }
            $scope.closeCatalogue();
        };
    }])
;