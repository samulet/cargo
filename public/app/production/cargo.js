'use strict';

angular.module('website', [
        'ngRoute',
        'ngAnimate',
        'website.env.config',
        'website.constants',
        'website.top.menu',
        'website.user.profile',
        'website.form.blocks',
        'website.user.param',
        'website.dashboard',
        'website.account',
        'website.public.offer',
        'website.custom.attrs',
        'website.storage',
        'website.error',
        'website.redirect',
        'website.cookies',
        'website.modal',
        'ui.bootstrap',
        'ui.select2',
        'ngGrid',
        'website.catalogue'
    ])
    .config(['$routeProvider', '$httpProvider', '$locationProvider', 'ACCESS_LEVEL', 'ROUTES', function ($routeProvider, $httpProvider, $locationProvider, ACCESS_LEVEL, ROUTES) {
        var pathToIncs = 'app/pages/';
        $routeProvider.when(ROUTES.START_PAGE, {redirectTo: ROUTES.DASHBOARD});
        $routeProvider.when(ROUTES.START_PAGE_ALT, {redirectTo: ROUTES.DASHBOARD});
        $routeProvider.when(ROUTES.NOT_FOUND, {templateUrl: pathToIncs + 'errors/404.html', access: ACCESS_LEVEL.PUBLIC});
        $routeProvider.when(ROUTES.USER_PROFILE, {templateUrl: pathToIncs + 'user_profile/user_profile.html', access: ACCESS_LEVEL.AUTHORIZED});
        $routeProvider.when(ROUTES.DASHBOARD, {templateUrl: pathToIncs + 'dashboard/dashboard.html', access: ACCESS_LEVEL.AUTHORIZED});
        $routeProvider.when(ROUTES.ACCOUNT, {templateUrl: pathToIncs + 'account/account.html', access: ACCESS_LEVEL.AUTHORIZED});
        $routeProvider.when(ROUTES.PUBLIC_OFFER, {templateUrl: pathToIncs + 'public_offer/public_offer.html', access: ACCESS_LEVEL.PUBLIC});

        $routeProvider.otherwise({redirectTo: ROUTES.DASHBOARD});

        $locationProvider.html5Mode(false);
        $locationProvider.hashPrefix('!');

        var interceptor = ['$location', '$q', '$rootScope', function ($location, $q, $rootScope) {
            return {
                'request': function (config) {
                    $rootScope.isAjaxLoading = true;
                    return config || $q.when(config);
                },
                'response': function (response) {
                    $rootScope.isAjaxLoading = false;
                    return response || $q.when(response);
                },
                'responseError': function (rejection) {
                    $rootScope.isAjaxLoading = false;
                    return $q.reject(rejection);
                }
            };
        }];

        $httpProvider.interceptors.push(interceptor);

    }])
    .run(['$rootScope', 'ACCESS_LEVEL', 'ROUTES', 'cookiesFactory', 'redirectFactory', 'storageFactory', '$http', 'userParamsFactory', function ($rootScope, ACCESS_LEVEL, ROUTES, cookiesFactory, redirectFactory, storageFactory, $http, userParamsFactory) {
        $rootScope.ROUTES = ROUTES;
        $rootScope.isAjaxLoading = false;
        $rootScope.messages = [];

        userParamsFactory.getApiRoutes();

        $rootScope.$watch(function () {
            return localStorage.getItem(storageFactory.storage.local.selectedAccount);
        }, function (newValue) {
            if (newValue) {
                $http.defaults.headers.common['X-App-Account'] = JSON.parse(newValue).account_uuid;
            }
        });

        $rootScope.$watch(function () {
            return localStorage.getItem(storageFactory.storage.local.selectedCompany);
        }, function (newValue) {
            if (newValue) {
                $http.defaults.headers.common['X-App-Company'] = JSON.parse(newValue).uuid;
            }
        });

        $rootScope.$on("$routeChangeStart", function (event, next, current) {
            var isToken = !!storageFactory.getToken();
            if (isToken) {
                $http.defaults.headers.common['X-Auth-UserToken'] = storageFactory.getToken();
            } else {
                redirectFactory.goSignIn();
            }
        });

        userParamsFactory.prepareUser();
    }])
;

angular.module('website.constants', [])
    .constant('RESPONSE_STATUS', {
        OK: 200,
        CREATED: 201,
        ACCEPTED: 202,
        NO_CONTENT: 204,
        NOT_MODIFIED: 304,
        BAD_REQUEST: 400,
        UNAUTHORIZED: 401,
        FORBIDDEN: 403,
        NOT_FOUND: 404,
        METHOD_NOT_ALLOWED: 405,
        PROXY_AUTHENTICATION_REQUIRED: 407,
        UNPROCESSABLE_ENTITY: 422,
        INTERNAL_SERVER_ERROR: 500
    })
    .constant('ACCESS_LEVEL', {
        PUBLIC: 0,
        AUTHORIZED: 1,
        ADMIN: 2
    })
    .constant('ROUTES', {
        START_PAGE: '/',
        START_PAGE_ALT: '',
        DASHBOARD: '/dashboard',
        ACCOUNT: '/account',
        PUBLIC_OFFER: '/public/offer',
        USER_PROFILE: '/user/profile',
        LOGOUT: '/user/logout',
        NOT_FOUND: '/404'
    })
    .constant('MESSAGES', {
        ERROR: {
            UNAUTHORIZED: 'Не удалось авторизироваться',
            INTERNAL_SERVER_ERROR: 'Внутренняя ошибка сервера',
            UNKNOWN_ERROR: 'Неизвестная ошибка, попробуйте позже',
            CANNOT_BE_DONE_ERROR: 'Невозможно выполнить операцию, попробуйте позже'
        }
    })
;
"use strict";

 angular.module("website.env.config", [])

.constant("WEB_CONFIG", {
  "PROTOCOL": "http",
  "HOST": "cargo",
  "HOST_CONTEXT": "",
  "PORT": "8000",
  "DOMAIN": "cargo.dev",
  "BASE_URL": "http://cargo.dev:8000"
})

.constant("REST_CONFIG", {
  "PROTOCOL": "http",
  "HOST": "cargo",
  "HOST_CONTEXT": "/api",
  "PORT": "8000",
  "DOMAIN": "cargo.dev",
  "BASE_URL": "http://cargo.dev:8000/api"
})

;
'use strict';

angular.module('website.account', [])

    .controller('accountController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', 'storageFactory', '$modal', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS, storageFactory, $modal) {
        $rootScope.pageTitle = 'Аккаунт';
        $scope.companyModal = null;
        $scope.selectedAccount = null;
        $scope.showCompanyWizard = false;
        $scope.showConfirmationModal = false;

        $scope.prepareAddCompany = function (account) {
            $scope.showConfirmationModal = true;
            $scope.selectedAccount = account;
            openCompanyModal();
        };

        $scope.launchCompanyWizard = function () {
            $scope.showConfirmationModal = false;
            $scope.showCompanyWizard = true;
        };

        function openCompanyModal() {
            $scope.companyModal = $modal.open({
                templateUrl: 'addCompanyModalContent.html',
                scope: $scope,
                backdrop: 'static',
                controller: 'addCompanyModalController'
            });
        }

        function closeCompanyModal() {
            $scope.companyModal.close();
            $scope.selectedAccount = null;
            $scope.showConfirmationModal = false;
            $scope.showCompanyWizard = false;
        }

        $scope.closeCompanyModal = function () {
            closeCompanyModal();
        };

        getAccounts();

        $scope.getAccounts = function () {
            getAccounts();
        };

        function getAccounts() {
            $http.get(REST_CONFIG.BASE_URL + '/accounts')
                .success(function (data) {
                    var accounts = data._embedded.accounts;
                    $scope.accounts = accounts;
                    if (accounts.length === 1) {
                        $scope.firstAccount = data._embedded.accounts[0];
                    }
                }).error(function (data, status) {
                    errorFactory.resolve(data, status);
                }
            );
        }

        $scope.removeAccount = function (account) {
            $http.delete(REST_CONFIG.BASE_URL + '/accounts/' + account.account_uuid)
                .success(function () {
                    getAccounts();
                }).error(function (data, status) {
                    errorFactory.resolve(data, status);
                }
            );
        };
    }])

    .controller('addCompanyModalController', ['$scope', '$http', 'REST_CONFIG', 'errorFactory', '$timeout', function ($scope, $http, REST_CONFIG, errorFactory, $timeout) {

        $scope.openCatalog = function () {
            //placeholder
        };

        $scope.openDatePopup = function (isOpen) {
            $timeout(function () {
                $scope[isOpen] = true;
            });
        };
    }])
;
'use strict';

angular.module('website.dashboard', [])

    .controller('dashboardController', ['$scope', '$rootScope', '$http', 'REST_CONFIG', 'errorFactory', 'RESPONSE_STATUS', 'storageFactory', '$modal', function ($scope, $rootScope, $http, REST_CONFIG, errorFactory, RESPONSE_STATUS, storageFactory, $modal) {
        $rootScope.pageTitle = 'dashboard';
        $scope.accountModal = null;
        $scope.accountData = [];
        $scope.firstAccount = null;
        $scope.showAccountRegistration = false;
        $scope.showCompanyWizard = false;
        checkForAccounts();

        //TODO remove (just demo for a catalogs tests)
        $scope.catalogModel = null;

        $scope.getData = function () {
            return [
                {value: 1, description: 'Петров В.', firstName: 'Василий', lastName: 'Петров', age: '21' },
                {value: 2, description: 'Антонов К.', firstName: 'Константин', lastName: 'Антонов', age: '37' },
                {value: 3, description: 'Яковлев Б.', firstName: 'Борис', lastName: 'Яковлев', age: '17' },
                {value: 4, description: 'Туполев М.', firstName: 'Марат', lastName: 'Туполев', age: '33' },
                {value: 5, description: 'Лавочкин С.', firstName: 'Серафим', lastName: 'Лавочкин', age: '24' }
            ];
        };
        //TODO END remove

        function checkForAccounts() {
            $scope.showAccountRegistration = true;
            getAccounts();
        }

        function openAccountModal() {
            $scope.accountModal = $modal.open({
                templateUrl: 'registrationModalContent.html',
                backdrop: 'static',
                scope: $scope,
                controller: 'registrationModalController'
            });
        }

        function closeAccountModal() {
            $scope.accountModal.close();
        }

        $scope.closeAccountModal = function () {
            closeAccountModal();
        };

        $scope.getAccounts = function () {
            getAccounts();
        };

        function onError(data, status) {
            if (status === RESPONSE_STATUS.NOT_FOUND) {
                openAccountModal();
            } else {
                errorFactory.resolve(data, status);
            }
        }

        function getAccounts() {
            $http.get(REST_CONFIG.BASE_URL + '/accounts')
                .success(function (data) {
                    var accounts = data._embedded.accounts;
                    $scope.accounts = accounts;
                    if (accounts.length === 1) {
                        $scope.firstAccount = data._embedded.accounts[0];
                        storageFactory.setSelectedAccount($scope.firstAccount);
                    } else if (accounts.length === 0) {
                        storageFactory.removeSelectedAccount();
                        storageFactory.removeSelectedCompany();
                    }
                }).error(onError);
        }

        $scope.addAccount = function () {
            $scope.showAccountRegistration = true;
            $scope.showCompanyWizard = false;
            openAccountModal();
        };

        $scope.removeAccount = function (account) {
            $http.delete(REST_CONFIG.BASE_URL + '/accounts/' + account.account_uuid)
                .success(function () {
                    getAccounts();
                }).error(onError);
        };
    }])

    .controller('registrationModalController', ['$scope', '$http', 'REST_CONFIG', 'errorFactory', '$timeout', function ($scope, $http, REST_CONFIG, errorFactory, $timeout) {
        $scope.registrationModalMessages = [];

        $scope.openCatalog = function () {
            //placeholder
        };

        $scope.openDatePopup = function (isOpen) {
            $timeout(function () {
                $scope[isOpen] = true;
            });
        };

        $scope.saveAccountData = function () {
            $http.post(REST_CONFIG.BASE_URL + '/accounts', {title: $scope.accountData.title})
                .success(function () {
                    $scope.getAccounts();
                    $scope.showAccountRegistration = false;
                    $scope.showCompanyWizard = true;
                }).error(function (data, status) {
                    errorFactory.resolve(data, status, $scope.registrationModalMessages);
                }
            );
        };
    }])
;
'use strict';

angular.module('website.public.offer', [])

    .controller('publicOfferController', ['$scope', '$rootScope', function ($scope, $rootScope) {
        $rootScope.pageTitle = 'Аккаунт';
    }])
;
'use strict';

angular.module('website.user.profile', [])

    .controller('userProfileController', ['$scope', '$rootScope', '$http', 'storageFactory', 'errorFactory', 'redirectFactory', '$timeout', 'REST_CONFIG', function ($scope, $rootScope, $http, storageFactory, errorFactory, redirectFactory, $timeout, REST_CONFIG) {
        $rootScope.pageTitle = 'Профиль';

        $scope.editMode = false;

        $scope.profileData = {
            socials: [],
            personal: {},
            passport: {},
            phones: [],
            addresses: [],
            sites: [],
            emails: [],
            photo: {},
            eSignature: {},
            other: {}
        };

        $scope.startEdit = function () {
            $scope.editMode = true;
        };

        $scope.cancelEdit = function () {
            $scope.editMode = false;
        };

        $scope.saveEdit = function () {
            $http.post(REST_CONFIG.BASE_URL + '/profiles' + storageFactory.getUser(), $scope.profileData)
                .success(function (data) {
                    //storageFactory.setUser(data.user);
                }).error(function (data, status) {
                    errorFactory.resolve(data, status);
                }
            );
        };

        $scope.openDatePopup = function (isOpen) {
            $timeout(function () {
                $scope[isOpen] = true;
            });
        };

        $scope.today = new Date();

        function getTimestamp(aDate, callback) {
            if (aDate) {
                if (new Date(aDate) !== 'Invalid Date') {
                    aDate = aDate.split("-");
                    var newDate = aDate[2] + "/" + aDate[1] + "/" + aDate[0];
                    return callback(new Date(newDate).getTime());
                } else {
                    var day = aDate.slice(0, 2);
                    var month = aDate.slice(3, 5);
                    var year = aDate.slice(6);
                    var birthDate = new Date(+year, (+month) - 1, +day);
                    if (birthDate !== 'Invalid Date') {
                        return callback((birthDate).getTime());
                    } else {
                        return errorFactory.resolve({error: 'Неверный формат даты'});
                    }
                }
            }
            return callback(null);
        }

    }])
;
'use strict';

angular.module('website.top.menu', [])

    .directive('topPrivateMenu', function () {
        return {
            restrict: 'E',
            templateUrl: 'app/partials/private/top_menu.html',
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
            templateUrl: 'app/partials/private/user_menu.html',
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