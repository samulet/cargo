'use strict';

angular.module('website.top.menu', [])

    .directive('topPrivateMenu', function () {
        return {
            restrict: 'E',
            templateUrl: 'app/modules/top_menu/top_menu.html',
            controller: function ($scope, $http, $location, REST_CONFIG, storageFactory, errorFactory) {
                $scope.showCataloguesDropDown = false;
                $scope.showCompaniesDropDown = false;
                $scope.showPlacesDropDown = false;

                $scope.getClass = function (path) {
                    if ('/' + $location.path().substr(1, path.length) == path) {
                        return "active";
                    } else {
                        return "";
                    }
                };

                (function fetDropdownData() {
                    getCompanies();
                    getCatalogues();
                    getPlaces();
                })();

                function getCatalogues() {
                    $scope.catalogues = storageFactory.getSessionCatalogues();
                    if (!$scope.catalogues) {
                        $http.get(REST_CONFIG.BASE_URL + '/ref')
                            .success(function (data) {
                                $scope.catalogues = data._embedded.references;
                                storageFactory.setCataloguesForSession($scope.catalogues);
                                $scope.showCataloguesDropDown = true;
                            }).error(function (data, status) {
                                errorFactory.resolve(data, status);
                            }
                        );
                    } else {
                        $scope.showCataloguesDropDown = true;
                    }
                }

                function getCompanies() {
                    $scope.companies = storageFactory.getSessionCompanies();
                    if (!$scope.companies) {
                        $http.get(REST_CONFIG.BASE_URL + '/companies')
                            .success(function (data) {
                                $scope.companies = data._embedded.companies;
                                storageFactory.setCompaniesForSession($scope.companies);
                                $scope.showCompaniesDropDown = true;
                            }).error(function (data, status) {
                                errorFactory.resolve(data, status);
                            }
                        );
                    } else {
                        $scope.showCompaniesDropDown = true;
                    }
                }

                function getPlaces() {
                    $scope.places = storageFactory.getSessionPlaces();
                    if (!$scope.places) {
                        $http.get(REST_CONFIG.BASE_URL + '/places')
                            .success(function (data) {
                                $scope.places = data._embedded.places;
                                storageFactory.setPlacesForSession($scope.places);
                                $scope.showPlacesDropDown = true;
                            }).error(function (data, status) {
                                errorFactory.resolve(data, status);
                            }
                        );
                    } else {
                        $scope.showPlacesDropDown = true;
                    }
                }

                $scope.openCatalogueCard = function (company) {
                    //TODO placeholder
                };

                $scope.openCompanyCard = function (company) {
                    //TODO placeholder
                };

                $scope.openPlaceCard = function (company) {
                    //TODO placeholder
                };
            }
        };
    })

    .directive('userMenu', function () {
        return {
            restrict: 'E',
            templateUrl: 'app/modules/top_menu/user_menu.html',
            controller: function ($scope, $rootScope, redirectFactory, storageFactory, $modal) {
                $scope.isSelectAccountAndCompanyModalOpened = false;
                $scope.isCompaniesManagementOpened = false;
                $scope.isPlacesManagementOpened = false;

                $rootScope.$watch(function () {
                    return localStorage.getItem(storageFactory.storage.local.selectedAccount);
                }, function (newValue) {
                    $scope.accountName = (newValue) ? JSON.parse(newValue).title : '(Нет аккаунта)';
                });

                $rootScope.$watch(function () {
                    return localStorage.getItem(storageFactory.storage.local.selectedCompany);
                }, function (newValue) {
                    $scope.companyShortName = (newValue) ? JSON.parse(newValue).short : '(Юр. Лицо не выбрано)';
                });

                function openSelectAccountAndCompanyModal() {
                    $scope.isSelectAccountAndCompanyModalOpened = true;
                    $scope.selectAccountAndCompanyModal = $modal.open({
                        templateUrl: 'selectAccountAndCompanyModalContent.html',
                        scope: $scope,
                        backdrop: 'static',
                        controller: 'selectAccountAndCompanyModalController'
                    });
                }

                function openImportCompaniesModal() {
                    $scope.isImportCompaniesModalOpened = true;
                    $scope.importCompaniesModal = $modal.open({
                        templateUrl: 'importCompaniesModalContent.html',
                        scope: $scope,
                        backdrop: 'static',
                        controller: 'importCompaniesModalController'
                    });
                }

                function openImportPlacesModal() {
                    $scope.isImportPlacesModalOpened = true;
                    $scope.importPlacesModal = $modal.open({
                        templateUrl: 'importPlacesModalContent.html',
                        scope: $scope,
                        backdrop: 'static',
                        controller: 'importPlacesModalController'
                    });
                }

                function openCompaniesManagementModal() {
                    $scope.isCompaniesManagementOpened = true;
                    $scope.companiesManagementModal = $modal.open({
                        templateUrl: 'companiesManagementContent.html',
                        scope: $scope,
                        backdrop: 'static',
                        windowClass: 'modal_huge',
                        controller: 'companiesManagementController'
                    });
                }

                function openPlacesManagementModal() {
                    $scope.isPlacesManagementOpened = true;
                    $scope.placesManagementModal = $modal.open({
                        templateUrl: 'placesManagementContent.html',
                        scope: $scope,
                        backdrop: 'static',
                        windowClass: 'modal_huge',
                        controller: 'placesManagementController'
                    });
                }

                function closeModal(modal) {
                    modal.close();
                }

                $scope.closeSelectAccountAndCompanyModal = function () {
                    closeModal($scope.selectAccountAndCompanyModal);
                };

                $scope.showSelectAccountAndCompanyModal = function () {
                    openSelectAccountAndCompanyModal();
                };

                $scope.showImportCompaniesModal = function () {
                    openImportCompaniesModal();
                };

                $scope.showCompaniesManagementModal = function () {
                    openCompaniesManagementModal();
                };

                $scope.showPlacesManagementModal = function () {
                    openPlacesManagementModal();
                };

                $scope.closeCompaniesManagementModal = function () {
                    closeModal($scope.companiesManagementModal);
                };

                $scope.closePlacesManagementModal = function () {
                    closeModal($scope.placesManagementModal);
                };

                $scope.showImportPlacesModal = function () {
                    openImportPlacesModal();
                };

                $scope.closeImportCompaniesModal = function () {
                    closeModal($scope.importCompaniesModal);
                };

                $scope.closeImportPlacesModal = function () {
                    closeModal($scope.importPlacesModal);
                };

                $scope.logout = function () {
                    redirectFactory.logout();
                };
            }
        };
    })

    .controller('selectAccountAndCompanyModalController', ['$scope', '$http', 'REST_CONFIG', 'errorFactory', 'storageFactory', function ($scope, $http, REST_CONFIG, errorFactory, storageFactory) {
        $scope.options = [];
        $scope.selectAccountAndCompanyMessages = [];

        if ($scope.isSelectAccountAndCompanyModalOpened) {
            getCompaniesForAccounts();
        }

        function getAccounts(callback) {
            $http.get(REST_CONFIG.BASE_URL + '/accounts')
                .success(function (data) {
                    $scope.accounts = data._embedded.accounts;
                    callback($scope.accounts);
                }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.selectAccountAndCompanyMessages);
                }
            );
        }

        function getCompanies(account, callback) {
            if (account) {
                $http.get(REST_CONFIG.BASE_URL + '/accounts/' + account.account_uuid + '/companies')
                    .success(function (data) {
                        $scope.companies = data._embedded.companies;
                        callback($scope.companies, account);
                    }).error(function (data, status) {
                        errorFactory.resolve(data, status, $scope.selectAccountAndCompanyMessages);
                    }
                );
            }
        }

        function pushCompaniesAndAccount(companies, account) {
            for (var j in companies) {
                if (companies.hasOwnProperty(j)) {
                    $scope.options.push({
                        account: account,
                        company: companies[j]
                    });
                }
            }
        }

        function getCompaniesForAccounts() {
            getAccounts(function (accounts) {
                for (var k in accounts) {
                    if (accounts.hasOwnProperty(k)) {
                        getCompanies(accounts[k], pushCompaniesAndAccount);
                    }
                }
            });
        }

        $scope.selectOption = function (option) {
            $scope.tempSelectedAccount = option;
        };

        $scope.saveAccountAndCompany = function () {
            if ($scope.tempSelectedAccount) {
                storageFactory.setSelectedAccount($scope.tempSelectedAccount.account);
                storageFactory.setSelectedCompany($scope.tempSelectedAccount.company);
            } else {
                storageFactory.removeSelectedAccount();
                storageFactory.removeSelectedCompany();
            }
            $scope.closeSelectAccountAndCompanyModal();
        };

    }])
;