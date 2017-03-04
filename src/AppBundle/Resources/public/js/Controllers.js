apibank.controller('Customers', function ($scope, $http, $location) {
    $http.get('api/v1/customers').then(function (response) {
        $scope.customers = response.data;
    });
});

apibank.controller('Transactions', function ($scope, $http, $routeParams, $location, $httpParamSerializer) {
    $scope.filteredTodos = []
        , $scope.currentPage = 1
        , $scope.numPerPage = 5
        , $scope.maxSize = 50;

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
            'limit' : $scope.numPerPage
        };
        $scope.data.offset = ($scope.currentPage - 1) * $scope.numPerPage;

        var qs = $httpParamSerializer($scope.data);
        $http.get('api/v1/transactions/' + $routeParams.customer_id + '?' + qs).then(function (response) {
            $scope.transactions = response.data;
        });
    };

    $scope.setTransactionsOnPage($scope.numPerPage);
});

apibank.controller('TransactionsAdd', function ($scope, $http, $routeParams, $location) {
    $scope.transactionList = function () {
        $location.path('/transactions/' + $routeParams.customer_id);
    };

    $scope.addTransaction = function () {
        var amount = angular.element('#InputAmount').val();
        if (0 < amount) {
            $http.post('api/v1/transaction/' + $routeParams.customer_id, {'amount': amount}).then(function (response) {
                $location.path('/transactions/' + $routeParams.customer_id);
            });
        }
    };
});

apibank.controller('TransactionsEdit', function ($scope, $http, $routeParams, $location) {
    var path = $routeParams.customer_id + '/' + $routeParams.transaction_id;
    $scope.transactionList = function () {
        $location.path('/transactions/' + $routeParams.customer_id);
    };
    $http.get('api/v1/transaction/' + path).then(function (response) {
        $scope.amount = response.data.amount;
    });
    $scope.updateTransaction = function () {
        var amount = angular.element('#InputAmount').val();
        $http.put('api/v1/transaction/' + path, {'amount': amount}).then(function (response) {
            $location.path('/transactions/' + $routeParams.customer_id);
        });
    };
});