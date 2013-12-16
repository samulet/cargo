'use strict';

angular.module('website.top.menu', [])

    .directive('topPrivateMenu', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/partials/private/top_menu.html',
            controller: function ($scope, $location, storageFactory) {
                var user = storageFactory.getUser();
                var selectedAccount = storageFactory.getSelectedAccount();
                var selectedCompany = storageFactory.getSelectedCompany();

                $scope.accountShowModal = false;
                $scope.companyShowModal = false;

                $scope.displayedName = (user) ? user.name : 'Пользователь';
                $scope.accountName = (selectedAccount) ? selectedAccount.title : '(Нет аккаунта)';
                $scope.companyShortName = (selectedCompany) ? selectedCompany.short : '(Юр. Лицо не выбрано)';

                $scope.getClass = function (path) {
                    if ('/' + $location.path().substr(1, path.length) == path) {
                        return "active"
                    } else {
                        return ""
                    }
                }
            }
        };
    })

    .directive('userMenu', function () {
        return {
            restrict: 'E',
            templateUrl: 'html/partials/private/user_menu.html',
            scope: {
                accountShowModal: '=accountShowModal',
                companyShowModal: '=companyShowModal',
                displayedName: '=displayedName',
                accountName: '=accountName',
                companyShortName: '=companyShortName'
            },
            controller: function ($scope, redirectFactory, storageFactory) {
                $scope.showSelectAccountPopup = function () {
                    $scope.accountShowModal = true;
                };

                $scope.showSelectCompanyPopup = function () {
                    $scope.companyShowModal = true;
                };

                $scope.logout = function () {
                    storageFactory.removeToken();
                    localStorage.clear();
                    redirectFactory.logout();
                }
            }
        };
    })

    .controller('selectAccountModalController', ['$scope', '$http', 'REST_CONFIG', 'errorFactory', function ($scope, $http, REST_CONFIG, errorFactory) {
        //TODO
    }])

    .controller('selectCompanyModalController', ['$scope', '$http', 'REST_CONFIG', 'errorFactory', function ($scope, $http, REST_CONFIG, errorFactory) {
        //TODO
    }])
;