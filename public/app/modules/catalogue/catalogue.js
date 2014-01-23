'use strict';

angular.module('website.catalogue', [])

    .directive('catalogue', function () {
        return {
            restrict: 'E',
            templateUrl: 'app/modules/catalogue/catalogue.html',
            scope: {
                url: '=',
                itemsName: '=',
                model: '=',
                stringSearch: '='
            },
            controller: function ($scope, $http) {
                var ajaxParams = {
                    url: $scope.url,
                    dataType: 'json',
                    results: getResults
                };

                $scope.details = {};
                $scope.data = [];

                if ($scope.stringSearch === true) {
                    ajaxParams.data = getQueryParams;
                }else{

                }

                $scope.select2Options = {
                    minimumInputLength: 3,
                    ajax: ajaxParams
                };

                function getResults(data, page) {
                    return {
                        results: data._embedded[$scope.itemsName]
                    };
                }

                function getQueryParams(text, page) {
                    return {
                        search: text,
                        page: page,
                        page_limit: 10
                    };
                }

                function getData() {
                    $http.get($scope.url)
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