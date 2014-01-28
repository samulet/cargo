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
                $scope.showCatalogueModal = false;

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

                $scope.showModal = function () {
                    $scope.showCatalogueModal = true;
                };
            }
        };
    })
;