'use strict';

angular.module('website.linking', [])

    .controller('linkingController', ['$scope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', '$routeParams', function ($scope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS, $routeParams) {
        window.ngGrid.i18n.ru = {
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
        window.ngGrid.i18n.en = window.ngGrid.i18n.ru;

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

        var filterOptions = {
            constructor: function (filterText, useExternalFilter) {
                this.filterText = filterText;
                this.useExternalFilter = useExternalFilter;
                return this;
            }
        };

        var gridOptions = {
            constructor: function (data, columnDefs, totalServerItems, pagingOptions, filterOptions) {
                this.data = data;
                this.columnDefs = columnDefs;
                this.totalServerItems = totalServerItems;
                this.pagingOptions = pagingOptions;
                this.filterOptions = filterOptions;
                this.enablePaging = true;
                this.enableSorting = true;
                this.multiSelect = false;
                this.showFooter = true;
                //this.showFilter= true;
                this.showColumnMenu = true;
                this.showSelectionCheckbox = true;
                return this;
            }
        };

        $scope.importTotalServerItems = 0;
        $scope.existedTotalServerItems = 0;
        $scope.linkedForSelectedExistedTotalServerItems = 0;

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
        $scope.linkedForSelectedExistedPagingOptions = Object.create(pagingOptions).constructor([10, 30, 100], 10, 1);

        $scope.importedFilterOptions = Object.create(filterOptions).constructor("", true);
        $scope.existedFilterOptions = Object.create(filterOptions).constructor("", true);
        $scope.linkedForSelectedExistedFilterOptions = Object.create(filterOptions).constructor("", true);

        var importedItemsUrl = REST_CONFIG.BASE_URL + '/service/import/company-intersect'; //TODO
        var existedItemsUrl = REST_CONFIG.BASE_URL + '/companies'; //TODO
        var itemsName = 'companies'; //TODO

        function getDataByPage(data, page, pageSize) {
            var pageData = data.slice((page - 1) * pageSize, page * pageSize);
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
                $scope.importedPageData = getDataByPage($scope.items.imported.unlinked, page, pageSize);
                $scope.importedTotalServerItems = $scope.items.imported.unlinked.length;
                setImportGrid();
            }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.linkingProcessMessages);
                }
            );
        }

        function getExistedItems(page, pageSize) {
            $http.get(existedItemsUrl).success(function (data) {
                $scope.items.existed = data._embedded[itemsName];
                $scope.existedPageData = getDataByPage($scope.items.existed, page, pageSize);
                $scope.existedTotalServerItems = $scope.items.existed.length;
                setExistedGrid();
            }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.linkingProcessMessages);
                }
            );
        }

        function getLinkedItems(page, pageSize) {
            $scope.items.linkedForSelectedExisted = [];
            var selectedExistedItem = $scope.selectedExistedItem;
            var linkedItems = $scope.items.imported.linked;
            if (linkedItems.length > 0 && selectedExistedItem) {
                for (var i = 0; i <= linkedItems.length - 1; i++) {
                    if (linkedItems[i].link === selectedExistedItem.uuid) {
                        $scope.items.linkedForSelectedExisted.push(linkedItems[i]);
                    }
                }
            }
            $scope.linkedForSelectedExistedPageData = getDataByPage($scope.items.linkedForSelectedExisted, page, pageSize);
            $scope.linkedForSelectedExistedTotalServerItems = $scope.items.linkedForSelectedExisted.length;
            setLinkedForSelectedExistedGrid();
        }

        setImportGrid();
        getImportedItems($scope.importedPagingOptions.currentPage, $scope.importedPagingOptions.pageSize);
        getExistedItems($scope.existedPagingOptions.currentPage, $scope.existedPagingOptions.pageSize);
        getLinkedItems($scope.linkedForSelectedExistedPagingOptions.currentPage, $scope.linkedForSelectedExistedPagingOptions.pageSize);

        function setImportGrid() {
            $scope.importedGridOptions = Object.create(gridOptions).constructor($scope.importedPageData, [
                { field: "name", displayName: 'Название'},
                { field: "source", displayName: 'Источник'}
            ], $scope.importTotalServerItems, $scope.importedPagingOptions, $scope.importedFilterOptions);
        }

        function setExistedGrid() {
            $scope.existedGridOptions = Object.create(gridOptions).constructor($scope.items.existed, [
                { field: "short", displayName: 'Название'},
                { field: "inn", displayName: 'ИНН'}
            ], $scope.existedTotalServerItems, $scope.existedPagingOptions, $scope.existedFilterOptions);
        }

        function setLinkedForSelectedExistedGrid() {
            $scope.linkedForSelectedExistedGridOptions = Object.create(gridOptions).constructor($scope.items.linkedForSelectedExisted, [
                { field: "name", displayName: 'Название'},
                { field: "source", displayName: 'Источник'}
            ], $scope.linkedForSelectedExistedTotalServerItems, $scope.linkedForSelectedExistedPagingOptions, $scope.linkedForSelectedExistedFilterOptions);
        }

        $scope.$watch('importedPagingOptions', function (newVal, oldVal) {
            if (newVal !== oldVal) {
                getImportedItems($scope.importedPagingOptions.currentPage, $scope.importedPagingOptions.pageSize);
            }
        }, true);

        $scope.$watch('existedPagingOptions', function (newVal, oldVal) {
            if (newVal !== oldVal) {
                getExistedItems($scope.existedPagingOptions.currentPage, $scope.existedPagingOptions.pageSize);
            }
        }, true);

        $scope.$watch('linkedForSelectedExistedPagingOptions', function (newVal, oldVal) {
            if (newVal !== oldVal) {
                getLinkedItems($scope.linkedForSelectedExistedPagingOptions.currentPage, $scope.linkedForSelectedExistedPagingOptions.pageSize);
            }
        }, true);
    }
    ])
;