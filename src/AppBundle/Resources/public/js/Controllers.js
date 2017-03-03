apibank.controller('Customers', function ($scope, $http, $location) {
    $http.get('api/v1/customers').then(function (response) {
        $scope.customers = response.data;
    });
});

apibank.controller('Transactions', function ($scope, $http, $routeParams, $location) {
    $http.get('api/v1/transactions/' + $routeParams.customer_id).then(function (response) {
        $scope.transactions = response.data;
    });
    $scope.addTransaction = function () {
        $location.path('/transaction/add/' + $routeParams.customer_id);
    };
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
    console.log($routeParams);
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