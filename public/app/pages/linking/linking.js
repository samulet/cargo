'use strict';

angular.module('website.linking', [])

    .controller('linkingController', ['$scope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', '$routeParams', function ($scope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS, $routeParams) {
        window.ngGrid.i18n['ru'] = {
            ngAggregateLabel: 'элементы',
            ngGroupPanelDescription: 'Перетащите сюда заголовок колонки для группировки по этой колонке.',
            ngSearchPlaceHolder: 'Поиск...',
            ngMenuText: 'Выберите Колонки:',
            ngShowingItemsLabel: 'Отображаемые Элементы:',
            ngTotalItemsLabel: 'Всего:',
            ngSelectedItemsLabel: 'Выбранно:',
            ngPageSizeLabel: 'Размер страницы:',
            ngPagerFirstTitle: 'Первая',
            ngPagerNextTitle: 'Следующая',
            ngPagerPrevTitle: 'Предведущая',
            ngPagerLastTitle: 'Последняя'
        };
        window.ngGrid.i18n['en'] = window.ngGrid.i18n['ru'];

        $scope.linkingProcessMessages = [];
        $scope.items = {
            imported: {
                linked: [],
                unlinked: []
            },
            existed: {},
            linkedForSelectedExisted: {},
            selectedExistedItem: null,
            selectedUnlinkedItem: null,
            selectedLinkedItem: null
        };

        $scope.importedPageData = [];
        $scope.existedPageData = [];
        $scope.linkedForSelectedExistedPageData = [];

        $scope.filterOptions = {
            filterText: "",
            useExternalFilter: true
        };

        $scope.totalServerItems = 0;

        var pagingOptions = {
            constructor: function (pageSizes, pageSize, currentPage) {
                this.pageSizes = pageSizes;
                this.pageSize = pageSize;
                this.currentPage = currentPage;
                return this;
            }
        };

        $scope.importedPagingOptions = Object.create(pagingOptions).constructor([10, 30, 100], 10, 1);
        $scope.existedPagingOptions = Object.create(pagingOptions).constructor([10, 30, 100], 10, 1);
        $scope.linkedPagingOptions = Object.create(pagingOptions).constructor([10, 30, 100], 10, 1);

        var importedItemsUrl = REST_CONFIG.BASE_URL + '/service/import/company-intersect'; //TODO
        var existedItemsUrl = REST_CONFIG.BASE_URL + '/companies'; //TODO
        var itemsName = 'companies'; //TODO

        function getDataByPage(data, page, pageSize) { //todo works now only with single grid
            var pageData = data.slice((page - 1) * pageSize, page * pageSize);
            $scope.totalServerItems = data.length;
            $scope.pagingOptions.pageSizes.push($scope.totalServerItems);
            if (!$scope.$$phase) {
                $scope.$apply();
            }

            return pageData;
        }

        function splitItemsByLink(items) {
            var resultItems = {
                linkedItems: [],
                unlinkedItems: []
            };

            for (var i = 0; i <= items.length - 1; i++) {
                if (!items[i].link) {
                    resultItems.unlinkedItems.push(items[i]);
                } else {
                    resultItems.linkedItems.push(items[i]);
                }
            }

            return resultItems;
        }

        function getImportedItems(page, pageSize) {
            $http.get(importedItemsUrl).success(function (data) {
                var resultItems = splitItemsByLink(data._embedded[itemsName]);
                $scope.items.imported.linked = resultItems.linkedItems;
                $scope.items.imported.unlinked = resultItems.unlinkedItems;
                $scope.importedPageData = getDataByPage($scope.importedItems.unlinkedItems, page, pageSize);
            }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.linkingProcessMessages);
                }
            );
        }

        function getExistedItems(page, pageSize) {
            $http.get(existedItemsUrl).success(function (data) {
                $scope.existedItems = data._embedded[itemsName];
                $scope.importedPageData = getDataByPage($scope.existedItems, page, pageSize)
            }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.linkingProcessMessages);
                }
            );
        }

        function getLinkedItems(page, pageSize) {
            $scope.items.linkedForSelectedExisted = [];
            var selectedExistedItem = $scope.selectedExistedItem;
            var linkedItems = $scope.imported.linked;
            if (linkedItems.length > 0 && selectedExistedItem) {
                for (var i = 0; i <= linkedItems.length - 1; i++) {
                    if (linkedItems[i].link === selectedExistedItem.uuid) {
                        $scope.items.linkedForSelectedExisted.push(linkedItems[i]);
                        $scope.linkedForSelectedExistedPageData = getDataByPage($scope.items.linkedForSelectedExisted, page, pageSize);
                    }
                }
            }
        }

        $scope.$watch('importedPagingOptions', function (newVal, oldVal) {
            if (newVal !== oldVal) {
                getImportedItems($scope.importedPagingOptions.pageSize, $scope.importedPagingOptions.currentPage);
            }
        }, true);

        $scope.$watch('existedPagingOptions', function (newVal, oldVal) {
            if (newVal !== oldVal) {
                getExistedItems($scope.existedPagingOptions.pageSize, $scope.existedPagingOptions.currentPage);
            }
        }, true);

        $scope.$watch('linkedPagingOptions', function (newVal, oldVal) {
            if (newVal !== oldVal) {
                getLinkedItems($scope.linkedPagingOptions.pageSize, $scope.linkedPagingOptions.currentPage);
            }
        }, true);

        /*$scope.$watch('filterOptions.filterText', function (newVal, oldVal) {
         if (newVal !== oldVal) {
         filterResults($scope.filterOptions.filterText);
         }
         }, true);

         function filterResults(filterText) {

         }*/

        $scope.gridOptions = {
            constructor: function (data, columnDefs, totalServerItems, pagingOptions, filterOptions) {
                this.data = data;
                this.columnDefs = columnDefs;
                this.totalServerItems = totalServerItems;
                this.pagingOptions = pagingOptions;
                this.filterOptions = filterOptions;
                return this;
            },
            enablePaging: true,
            enableSorting: true,
            multiSelect: false,
            showFooter: true,
            //showFilter: true,
            showColumnMenu: true,
            showSelectionCheckbox: true,
        };

        $scope.importedGridOptions = bject.create(pagingOptions).constructor($scope.items.imported.unlinked, [
            { field: "name", displayName: 'Название'},
            { field: "source", displayName: 'Источник'}
        ], $scope.totalServerItems, $scope.importedPagingOptions, $scope.filterOptions);//TODO replace $scope.totalServerItems and $scope.filterOptions


    }
    ])

    .controller('companiesManagementController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS) {
        $scope.linkingProcessMessages = [];
        $scope.importedCompanies = [];
        $scope.existedCompanies = [];
        $scope.linkedCompanies = [];
        var unlinkedImportedCompanies = [];
        var linkedImportedCompanies = [];

        if ($scope.isCompaniesManagementOpened) {
            getAllSystemCompanies();
            getImportedCompanies();
        }

        function onError(data, status) {
            if (status != RESPONSE_STATUS.NOT_FOUND) {
                errorFactory.resolve(data, status, $scope.linkingProcessMessages);
            }
        }

        function getAllSystemCompanies() {
            $http.get(REST_CONFIG.BASE_URL + '/companies').success(function (data) {
                $scope.existedCompany = null;
                $scope.existedCompanies = data._embedded.companies;
            }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.linkingProcessMessages);
                }
            );
        }

        function getImportedCompanies(callback) {
            $http.get(REST_CONFIG.BASE_URL + '/service/import/company-intersect').success(function (data) {
                $scope.importedCompany = null;
                var importedCompanies = data._embedded.companies;
                unlinkedImportedCompanies = [];
                linkedImportedCompanies = [];
                for (var i = 0; i <= importedCompanies.length - 1; i++) {
                    if (!importedCompanies[i].link) {
                        unlinkedImportedCompanies.push(importedCompanies[i]);
                    } else {
                        linkedImportedCompanies.push(importedCompanies[i]);
                    }
                }
                $scope.importedCompanies = unlinkedImportedCompanies;
                if (callback) {
                    callback();
                }
            }).error(function (data, status) {
                    onError(data, status);
                }
            );
        }

        $scope.getImportedCompanies = function () {
            getImportedCompanies();
        };

        $scope.getExistedCompanies = function () {
            getAllSystemCompanies();
        };

        $scope.addCompaniesLink = function () {
            var params = {
                source: $scope.importedCompany.source,
                id: $scope.importedCompany.id
            };

            if ($scope.existedCompany) {
                params.company = $scope.existedCompany.uuid;
            }

            $http.post(REST_CONFIG.BASE_URL + '/service/import/company-intersect', params).success(function () {
                getImportedCompanies(function () {
                    getLinkedCompanies();
                    getAllSystemCompanies();
                });
            }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.linkingProcessMessages);
                }
            );
        };

        $scope.removeCompaniesLink = function () {
            $http.delete(REST_CONFIG.BASE_URL + '/service/import/company-intersect/' + $scope.linkedCompany.source + '-' + $scope.linkedCompany.id)
                .success(function () {
                    getImportedCompanies(function () {
                        getLinkedCompanies();
                        getAllSystemCompanies();
                    });
                }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.linkingProcessMessages);
                }
            );
        };

        $scope.removeCompany = function () {
            $http.delete(REST_CONFIG.BASE_URL + '/companies/' + $scope.existedCompany.uuid)
                .success(function () {
                    getImportedCompanies();
                    getLinkedCompanies();
                    getAllSystemCompanies();
                }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.linkingProcessMessages);
                }
            );
        };

        function getLinkedCompanies() {
            $scope.linkedCompanies = [];
            var existedCompany = $scope.existedCompany;
            if (linkedImportedCompanies.length > 0 && existedCompany) {
                for (var i = 0; i <= linkedImportedCompanies.length - 1; i++) {
                    if (linkedImportedCompanies[i].link === existedCompany.uuid) {
                        $scope.linkedCompanies.push(linkedImportedCompanies[i]);
                    }
                }
            }
        }

        $scope.selectImportedCompany = function (company) {
            $scope.importedCompany = company;
        };

        $scope.selectLinkedCompany = function (company) {
            $scope.linkedCompany = company;
        };

        $scope.selectExistedCompany = function (company) {
            $scope.existedCompany = company;
            getLinkedCompanies();
        };
    }
    ])

    .controller('placesManagementController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS) {
        $scope.placesManagementMessages = [];
        $scope.importedPlaces = [];
        $scope.existedPlaces = [];
        $scope.linkedPlaces = [];
        var unlinkedImportedPlaces = [];
        var linkedImportedPlaces = [];

        if ($scope.isPlacesManagementOpened) {
            getAllSystemPlaces();
            getImportedPlaces();
        }

        function onError(data, status) {
            if (status != RESPONSE_STATUS.NOT_FOUND) {
                errorFactory.resolve(data, status, $scope.placesManagementMessages);
            }
        }

        function getAllSystemPlaces() {
            $http.get(REST_CONFIG.BASE_URL + '/places').success(function (data) {
                $scope.existedPlaces = data._embedded.places;
            }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.placesManagementMessages);
                }
            );
        }

        function getImportedPlaces(callback) {
            $http.get(REST_CONFIG.BASE_URL + '/service/import/place-intersect')
                .success(function (data) {
                    $scope.importedPlaces = null;
                    var importedPlaces = data._embedded.places;
                    unlinkedImportedPlaces = [];
                    linkedImportedPlaces = [];
                    for (var i = 0; i <= importedPlaces.length - 1; i++) {
                        if (!importedPlaces[i].link) {
                            unlinkedImportedPlaces.push(importedPlaces[i]);
                        } else {
                            linkedImportedPlaces.push(importedPlaces[i]);
                        }
                    }
                    $scope.importedPlaces = unlinkedImportedPlaces;
                    if (callback) {
                        callback();
                    }
                }).error(function (data, status) {
                    onError(data, status);
                }
            );
        }

        $scope.getImportedPlaces = function () {
            getImportedPlaces();
        };

        $scope.getExistedPlaces = function () {
            getAllSystemPlaces();
        };

        $scope.addPlacesLink = function () {
            var placeUuid = $scope.existedPlace ? $scope.existedPlace.uuid : null;
            $http.post(REST_CONFIG.BASE_URL + '/service/import/place-intersect',
                {source: $scope.importedPlace.source,
                    id: $scope.importedPlace.id,
                    place: placeUuid,
                    type: $scope.importedPlace.type})
                .success(function () {
                    getImportedPlaces(function () {
                        getLinkedPlaces();
                        getAllSystemPlaces();
                    });
                }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.placesManagementMessages);
                }
            );
        };

        $scope.removePlace = function () {
            $http.delete(REST_CONFIG.BASE_URL + '/places/' + $scope.existedPlace.uuid)
                .success(function () {
                    getImportedPlaces();
                    getLinkedPlaces();
                    getAllSystemPlaces();
                }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.placesManagementMessages);
                }
            );
        };

        $scope.removePlacesLink = function () {
            $http.delete(REST_CONFIG.BASE_URL + '/service/import/place-intersect/' + $scope.linkedPlace.source + '-' + $scope.linkedPlace.type + '-' + $scope.linkedPlace.id)
                .success(function () {
                    getImportedPlaces(function () {
                        getLinkedPlaces();
                        getAllSystemPlaces();
                    });
                }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.placesManagementMessages);
                }
            );
        };

        function getLinkedPlaces() {
            $scope.linkedPlaces = [];
            var existedPlace = $scope.existedPlace;
            if (linkedImportedPlaces.length > 0 && existedPlace) {
                for (var i = 0; i <= linkedImportedPlaces.length - 1; i++) {
                    if (linkedImportedPlaces[i].link === existedPlace.uuid) {
                        $scope.linkedPlaces.push(linkedImportedPlaces[i]);
                    }
                }
            }
        }

        $scope.selectImportedPlace = function (place) {
            $scope.importedPlace = place;
        };

        $scope.selectLinkedPlace = function (place) {
            $scope.linkedPlace = place;
        };

        $scope.selectExistedPlace = function (place) {
            $scope.existedPlace = place;
            getLinkedPlaces();
        };
    }
    ])
;