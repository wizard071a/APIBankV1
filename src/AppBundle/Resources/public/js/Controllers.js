apibank.controller('Customers', function ($scope, $http, $location, genericServices, $rootScope) {
    genericServices.getApiKey(function () {
        $http.get('api/v1/customers?' + $rootScope.apikey).then(function (response) {
            $scope.customers = response.data;
        });
    });
});

apibank.controller('Transactions', function ($scope, $http, $routeParams, $location, $httpParamSerializer, $rootScope, genericServices) {
    $scope.filteredTodos = []
        , $scope.currentPage = 1
        , $scope.numPerPage = 5
        , $scope.maxSize = 50
        , $scope.amount = ''
        , $scope.date = '';

    $( "#InputDate" ).datepicker();

    $scope.offset = ($scope.currentPage - 1) * $scope.numPerPage;
    $scope.data = {};
    $scope.addTransaction = function () {
        $location.path('/transaction/add/' + $routeParams.customer_id);
    };

    $scope.setTransactionsOnPage = function (transactionsOnPage) {
        $scope.numPerPage = transactionsOnPage;
        $scope.currentPage = 1;
        $scope.getTransactions();
    };

    $scope.setNextPage = function () {
        if ( $scope.numPerPage <= $scope.transactions.length ) {
            $scope.currentPage++;
            $scope.getTransactions();
        }
    };

    $scope.getPreviosPage = function () {
        if ( 1 < $scope.currentPage ) {
            $scope.currentPage--;
            $scope.getTransactions();
        }
    };

    $scope.getTransactions = function () {
        $scope.data = {
            'limit' : $scope.numPerPage,
            'amount' : $scope.amount,
            'date' : $scope.date
        };
        $scope.data.offset = ($scope.currentPage - 1) * $scope.numPerPage;

        var qs = $httpParamSerializer($scope.data);

        genericServices.getApiKey(function () {
            $http.get('api/v1/transactions/' + $routeParams.customer_id + '?' + qs + '&' + $rootScope.apikey).then(function (response) {
                $scope.transactions = response.data;
            });
        });
    };

    $scope.filterTransactons = function () {
        $scope.amount = angular.element('#InputAmount').val();
        $scope.date = angular.element('#InputDate').val();
        $scope.currentPage = 1;
        $scope.getTransactions();
    };

    $scope.setTransactionsOnPage($scope.numPerPage);
});

apibank.controller('TransactionsAdd', function ($scope, $http, $routeParams, $location, $rootScope, genericServices) {
    $scope.transactionList = function () {
        $location.path('/transactions/' + $routeParams.customer_id);
    };

    $scope.addTransaction = function () {
        var amount = angular.element('#InputAmount').val();
        if (0 < amount) {
            genericServices.getApiKey(function () {
                $http.post('api/v1/transaction/' + $routeParams.customer_id + '?' + $rootScope.apikey, {'amount': amount}).then(function (response) {
                    $location.path('/transactions/' + $routeParams.customer_id);
                });
            });
        }
    };
});

apibank.controller('TransactionsEdit', function ($scope, $http, $routeParams, $location, $rootScope, genericServices) {
    var path = $routeParams.customer_id + '/' + $routeParams.transaction_id;
    $scope.transactionList = function () {
        $location.path('/transactions/' + $routeParams.customer_id);
    };
    genericServices.getApiKey(function () {
        $http.get('api/v1/transaction/' + path + '?' + $rootScope.apikey).then(function (response) {
            $scope.amount = response.data.amount;
        });
    });
    $scope.updateTransaction = function () {
        var amount = angular.element('#InputAmount').val();
        genericServices.getApiKey(function () {
            $http.put('api/v1/transaction/' + path + '?' + $rootScope.apikey, {'amount': amount}).then(function (response) {
                $location.path('/transactions/' + $routeParams.customer_id);
            });
        });
    };
});