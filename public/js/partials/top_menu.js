'use strict';

angular.module('website.top.menu', [])

    .directive('topPrivateMenu', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/partials/private/top_menu.html',
            controller: function ($scope, $location) {
                $scope.getClass = function (path) {
                    if ('/' + $location.path().substr(1, path.length) == path) {
                        return "active";
                    } else {
                        return "";
                    }
                };
            }
        };
    })

    .directive('userMenu', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/partials/private/user_menu.html',
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


    .controller('companiesManagementController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS) {
        $scope.companiesManagementMessages = [];
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
                errorFactory.resolve(data, status, $scope.companiesManagementMessages);
            }
        }

        function getAllSystemCompanies() {
            $http.get(REST_CONFIG.BASE_URL + '/companies').success(function (data) {
                $scope.existedCompany = null;
                $scope.existedCompanies = data._embedded.companies;
            }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.companiesManagementMessages);
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
                    errorFactory.resolve(data, status, $scope.companiesManagementMessages);
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
                    errorFactory.resolve(data, status, $scope.companiesManagementMessages);
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
                    errorFactory.resolve(data, status, $scope.companiesManagementMessages);
                }
            );
        };

        function getLinkedCompanies() {
            $scope.linkedCompanies = [];
            var existedCompany = $scope.existedCompany;
            if (linkedImportedCompanies.length > 0 && existedCompany) {
                for (var i=0; i<= linkedImportedCompanies.length-1; i++) {
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
                for (var i=0; i<= linkedImportedPlaces.length-1; i++) {
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