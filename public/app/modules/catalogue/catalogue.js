'use strict';

angular.module('website.catalogue', [])

    .directive('catalogue', function () {
        return {
            restrict: 'E',
            templateUrl: 'app/modules/catalogue/catalogue.html',
            scope: {
                url: '=',
                itemsName: '@',
                model: '=',
                stringSearch: '=', //TODO make if true, send search text to the server
                searchField: '='
            },
            controller: function ($scope, $http) {
                $scope.details = {};
                $scope.data = [];

                if (!$scope.searchField) {
                    $scope.searchField = 'name';
                }

                function getData(query) {
                    $http.get($scope.url).success(function (data) {
                        var resultItems = {
                            results: []
                        };

                        var items = data._embedded[$scope.itemsName];

                        for (var i = 0; i <= items.length - 1; i++) {
                            var text = items[i][$scope.searchField];
                            if (query.matcher(query.term, text)) {
                                resultItems.results.push({
                                    text: text, //TODO should be key and value in server's response
                                    id: items[i].uuid
                                });
                            }
                        }

                        query.callback(resultItems);
                    }).error(function (data) {
                            console.log('error ' + data);//TODO
                        });
                }

                $scope.select2Options = {
                    minimumInputLength: 2,
                    allowClear: true,
                    query: getData
                };
            }
        };
    })

    .controller('catalogueModalController', ['$scope', function ($scope) {

        /* if ($scope.isCatalogueModalOpened) {
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
         };*/
    }])
;