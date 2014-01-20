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
            existed: [],
            linkedForSelectedExisted: [],
            selectedExistedItem: [],
            selectedImportedItem: [],
            selectedLinkedForSelectedExisted: []
        };

        $scope.importedPageData = [];
        $scope.existedPageData = [];
        $scope.linkedForSelectedExistedPageData = [];

        var FilterOptions = function (filterText, useExternalFilter) {
            return {
                filterText: filterText,
                useExternalFilter: useExternalFilter
            };
        };

        var GridOptions = function (data, columnDefs, totalServerItems, pagingOptions, filterOptions, selectedItems) {
            return {
                data: data,
                columnDefs: columnDefs,
                totalServerItems: totalServerItems,
                pagingOptions: pagingOptions,
                filterOptions: filterOptions,
                selectedItems: selectedItems,
                enablePaging: true,
                enableSorting: true,
                multiSelect: false,
                keepLastSelected: false,
                showFooter: true,
                //showFilter= true,
                showColumnMenu: true,
                showSelectionCheckbox: true
            };
        };

        $scope.importedTotalServerItems = 0;
        $scope.existedTotalServerItems = 0;
        $scope.linkedForSelectedExistedTotalServerItems = 0;

        var PagingOptions = function (pageSizes, pageSize, currentPage) {
            return {
                pageSizes: pageSizes,
                pageSize: pageSize,
                currentPage: currentPage
            };
        };

        $scope.importedPagingOptions = new PagingOptions([10, 30, 100], 10, 1);
        $scope.existedPagingOptions = new PagingOptions([10, 30, 100], 10, 1);
        $scope.linkedForSelectedExistedPagingOptions = new PagingOptions([10, 30, 100], 10, 1);

        $scope.importedFilterOptions = new FilterOptions("", true);
        $scope.existedFilterOptions = new FilterOptions("", true);
        $scope.linkedForSelectedExistedFilterOptions = new FilterOptions("", true);

        var importedItemsUrl;
        var existedItemsUrl;
        var itemsName;
        var itemName;
        var specificParams = [];

        checkRouteParams();

        function checkRouteParams() {
            if ($routeParams.type == 'companies') {
                importedItemsUrl = REST_CONFIG.BASE_URL + '/service/import/company-intersect';
                existedItemsUrl = REST_CONFIG.BASE_URL + '/companies';
                itemsName = 'companies';
                itemName = 'company';
            } else if ($routeParams.type == 'places') {
                importedItemsUrl = REST_CONFIG.BASE_URL + '/service/import/place-intersect';
                existedItemsUrl = REST_CONFIG.BASE_URL + '/places';
                itemsName = 'places';
                itemName = 'place';
                specificParams.push('type', 'type');
            }
        }

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

            var arr = [];

            if (items.linked && items.unlinked) {
                arr = items.linked.concat(items.unlinked);
            } else {
                arr = items.slice(0);
            }

            for (var i = 0; i <= arr.length - 1; i++) {
                if (!arr[i].link) {
                    resultItems.unlinkedItems.push(arr[i]);
                } else {
                    resultItems.linkedItems.push(arr[i]);
                }
            }

            return resultItems;
        }

        function manageImportedItemsByLinking(importedItems, page, pageSize) {
            var splittedImportedItems = splitItemsByLink(importedItems);
            $scope.items.imported.linked = splittedImportedItems.linkedItems;
            $scope.items.imported.unlinked = splittedImportedItems.unlinkedItems;
            $scope.importedPageData = getDataByPage($scope.items.imported.unlinked, page, pageSize);
            $scope.importedTotalServerItems = $scope.items.imported.unlinked.length;
        }

        function getImportedItems(page, pageSize) {
            if ($scope.items.imported.linked.length > 0 || $scope.items.imported.unlinked.length > 0) {
                manageImportedItemsByLinking($scope.items.imported, page, pageSize);
            } else {
                $http.get(importedItemsUrl).success(function (data) {
                    manageImportedItemsByLinking(data._embedded[itemsName], page, pageSize);
                }).error(function (data, status) {
                        errorFactory.resolve(data, status, $scope.linkingProcessMessages);
                    }
                );
            }
        }

        function getExistedItems(page, pageSize) {
            if ($scope.items.existed.length > 0) {
                $scope.existedPageData = getDataByPage($scope.items.existed, page, pageSize);
                $scope.existedTotalServerItems = $scope.items.existed.length;
            } else {
                $http.get(existedItemsUrl).success(function (data) {
                    $scope.items.existed = data._embedded[itemsName];
                    $scope.existedPageData = getDataByPage($scope.items.existed, page, pageSize);
                    $scope.existedTotalServerItems = $scope.items.existed.length;
                }).error(function (data, status) {
                        errorFactory.resolve(data, status, $scope.linkingProcessMessages);
                    }
                );
            }
        }

        function getLinkedItems(page, pageSize) {
            $scope.items.linkedForSelectedExisted = [];
            var selectedExistedItem = $scope.items.selectedExistedItem;
            if (selectedExistedItem.length > 0) {
                for (var k in selectedExistedItem) {
                    if (selectedExistedItem.hasOwnProperty(k)) {
                        var linkedItems = $scope.items.imported.linked;
                        if (linkedItems.length > 0) {
                            for (var i = 0; i <= linkedItems.length - 1; i++) {
                                if (linkedItems[i].link === selectedExistedItem[k].uuid) {
                                    $scope.items.linkedForSelectedExisted.push(linkedItems[i]);
                                }
                            }
                        }
                    }
                }
            }
            $scope.linkedForSelectedExistedPageData = getDataByPage($scope.items.linkedForSelectedExisted, page, pageSize);
            $scope.linkedForSelectedExistedTotalServerItems = $scope.items.linkedForSelectedExisted.length;
        }

        getImportedItems($scope.importedPagingOptions.currentPage, $scope.importedPagingOptions.pageSize);
        getExistedItems($scope.existedPagingOptions.currentPage, $scope.existedPagingOptions.pageSize);

        function addSpecificItemParams(params, i) {
            params[itemName] = $scope.items.selectedExistedItem[i] ? $scope.items.selectedExistedItem[i].uuid : null;

            if (specificParams.type) {
                params[specificParams.type] = $scope.items.selectedImportedItem[i].type;
            }
        }

        $scope.addItemsLink = function () {
            for (var i = 0; i <= $scope.items.selectedImportedItem.length - 1; i++) {
                var selectedImportedItem = $scope.items.selectedImportedItem[i];
                var params = {
                    source: selectedImportedItem.source,
                    id: selectedImportedItem.id
                };

                addSpecificItemParams(params, i);
                sendLinkItemsQuery(params, refreshGrids(i));
            }
        };

        function sendLinkItemsQuery(params, callback) {
            $http.post(importedItemsUrl, params).success(function () {
                if (callback) {
                    callback();
                }
            }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.linkingProcessMessages);
                }
            );
        }

        function refreshGrids(i) {//TODO
            $scope.items.selectedImportedItem[i].link = $scope.items.selectedExistedItem[i] ? $scope.items.selectedExistedItem[i].uuid : 'some';
            getImportedItems($scope.importedPagingOptions.currentPage, $scope.importedPagingOptions.pageSize);
            getLinkedItems();
            //$scope.items.selectedImportedItem = [];
            //$scope.items.selectedLinkedForSelectedExisted = [];
        }

        /*function getElementByValue() {

         }*/

        $scope.removeItemsLink = function () { //TODO
            /*$http.delete(REST_CONFIG.BASE_URL + '/service/import/company-intersect/' + $scope.linkedItem.source + '-' + $scope.linkedItem.id)
             .success(function () {
             getImportedItems(function () {
             getLinkedItems();
             getAllSystemItems();
             });
             }).error(function (data, status) {
             errorFactory.resolve(data, status, $scope.linkingProcessMessages);
             }
             );*/
        };

        $scope.removeItem = function () {//TODO
            /* $http.delete(REST_CONFIG.BASE_URL + '/companies/' + $scope.existedItem.uuid)
             .success(function () {
             getImportedItems();
             getLinkedItems();
             getAllSystemItems();
             }).error(function (data, status) {
             errorFactory.resolve(data, status, $scope.linkingProcessMessages);
             }
             );*/
        };

        $scope.importedGridOptions = new GridOptions('importedPageData', [
            { field: "name", displayName: 'Название'},
            { field: "source", displayName: 'Источник'}
        ], $scope.importedTotalServerItems, $scope.importedPagingOptions, $scope.importedFilterOptions, $scope.items.selectedImportedItem);

        $scope.existedGridOptions = new GridOptions('items.existed', [
            { field: "short", displayName: 'Название'},
            { field: "inn", displayName: 'ИНН'}
        ], $scope.existedTotalServerItems, $scope.existedPagingOptions, $scope.existedFilterOptions, $scope.items.selectedExistedItem);

        $scope.linkedForSelectedExistedGridOptions = new GridOptions('items.linkedForSelectedExisted', [
            { field: "name", displayName: 'Название'},
            { field: "source", displayName: 'Источник'}
        ], $scope.linkedForSelectedExistedTotalServerItems, $scope.linkedForSelectedExistedPagingOptions, $scope.linkedForSelectedExistedFilterOptions, $scope.items.selectedLinkedForSelectedExisted);

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

        $scope.$watch('items.selectedExistedItem', function (newVal, oldVal) {
            if (newVal !== oldVal) {
                getLinkedItems($scope.linkedForSelectedExistedPagingOptions.currentPage, $scope.linkedForSelectedExistedPagingOptions.pageSize);
            }
        }, true);
    }
    ])
;