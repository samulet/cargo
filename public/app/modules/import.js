'use strict';

angular.module('website.import', [])
    .controller('importCompaniesModalController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS) {
        $scope.importCompaniesMessages = [];
        $scope.noCompaniesToImport = false;
        $scope.stat = {
            new: 0,
            changed: 0,
            exists: 0,
            processed: 0
        };

        function onError(data, status) {
            if (status === RESPONSE_STATUS.NOT_FOUND) {
                $scope.noCompaniesToImport = true;
            } else {
                errorFactory.resolve(data, status, $scope.importCompaniesMessages);
            }
        }

        function fetchImportStatistic() {
            for (var i = 0; i <= 0; i++) {
                var externalService = $scope.extServiceCompanies[i];
                $scope.stat.new = $scope.stat.new + externalService.stat.new;
                $scope.stat.changed = $scope.stat.changed + externalService.stat.changed;
                $scope.stat.exists = $scope.stat.exists + externalService.stat.exists;
                $scope.stat.processed = $scope.stat.processed + externalService.stat.processed;
            }
        }

        $scope.importCompanies = function () {
            $http.get(REST_CONFIG.BASE_URL + '/service/import/company')
                .success(function (data) {
                    $scope.isImportCompaniesComplete = true;
                    $scope.extServiceCompanies = data._embedded.ext_service_company;
                    fetchImportStatistic();
                }).error(function (data, status) {
                    onError(data, status);
                }
            );
        };
    }])

    .controller('importPlacesModalController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS) {
        $scope.importPlacesMessages = [];
        $scope.noPlacesToImport = false;
        $scope.stat = {
            new: 0,
            changed: 0,
            exists: 0,
            processed: 0
        };

        function onError(data, status) {
            if (status === RESPONSE_STATUS.NOT_FOUND) {
                $scope.noPlacesToImport = true;
            } else {
                errorFactory.resolve(data, status, $scope.importPlacesMessages);
            }
        }

        function fetchImportStatistic() {
            for (var i = 0; i <= 0; i++) {
                var externalService = $scope.extServicePlaces[i];
                $scope.stat.new = $scope.stat.new + externalService.stat.new;
                $scope.stat.changed = $scope.stat.changed + externalService.stat.changed;
                $scope.stat.exists = $scope.stat.exists + externalService.stat.exists;
                $scope.stat.processed = $scope.stat.processed + externalService.stat.processed;
            }
        }

        $scope.importPlaces = function () {
            $http.get(REST_CONFIG.BASE_URL + '/service/import/place')
                .success(function (data) {
                    $scope.isImportPlacesComplete = true;
                    $scope.extServicePlaces = data._embedded.places;
                    fetchImportStatistic();
                }).error(function (data, status) {
                    onError(data, status);
                }
            );
        };
    }])
;