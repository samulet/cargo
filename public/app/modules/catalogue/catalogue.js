'use strict';

angular.module('website.catalogue', [])

    .directive('catalogue', function () {
        return {
            restrict: 'E',
            templateUrl: 'app/modules/catalogue/catalogue.html',
            scope: {
                dataUrl: '=dataUrl',
                itemsName: '=itemsName',
                model: '=model'
            },
            controller: function ($scope, $http) {
                $scope.details = {};
                $scope.data = [];

                $scope.select2Options = {
                    ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
                        url: $scope.dataUrl,
                        dataType: 'json',
                        data: function (term, page) {
                            return {
                                q: term, // search term
                                page_limit: 10,
                                apikey: "ju6z9mjyajq2djue3gbvv26t" // please do not use so this example keeps working
                            };
                        },
                        results: function (data, page) {
                            return {
                                results: data._embedded[$scope.itemsName]
                            };
                        }
                    }
                };

                function getData() {
                    $http.get($scope.dataUrl)
                        .success(function (data) {
                            $scope.data = data._embedded[$scope.itemsName];
                        }).error(function (data, status) {
                            //TODO
                        }
                    );
                }


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