'use strict';

angular.module('website.catalogue', [])

    .directive('catalogue', function () {
        return {
            restrict: 'E',
            templateUrl: 'app/modules/catalogue/catalogue.html',
            scope: {
                dataUrl: '=dataUrl',
                model: '=model'
            },
            controller: function ($scope, $http, $modal) {
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

                function getCompanies() {
                    $http.get($scope.dataUrl)
                        .success(function (data) {
                            $scope.companies = data._embedded.companies;
                            storageFactory.setCompaniesForSession($scope.companies);
                            $scope.showCompaniesDropDown = true;
                        }).error(function (data, status) {
                            errorFactory.resolve(data, status);
                        }
                    );
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