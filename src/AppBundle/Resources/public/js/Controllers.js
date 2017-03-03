apibank.controller('Customers', function ($scope, $http, $location) {
    $http.get('api/v1/customers').then(function (response) {
        $scope.customers = response.data;
    });
});

apibank.controller('Transactions', function ($scope, $http, $routeParams, $location) {
    console.log($routeParams);
    $http.get('api/v1/transactions/' + $routeParams.customer_id).then(function (response) {
        $scope.transactions = response.data;
    });
    console.log($scope);
    $scope.addTransaction = function () {
        $location.path('/transaction/add/' + $routeParams.customer_id);
    };
});

apibank.controller('TransactionsAdd', function ($scope, $http, $routeParams, $location) {
    console.log($routeParams);
    $scope.transactionList = function () {
        $location.path('/transactions/' + $routeParams.customer_id);
    };

    $scope.addTransaction = function () {
        console.log('REST ADD START')
        var amount = angular.element('#InputAmount').val();
        console.log(amount);
        if (0 < amount) {
            $http.post('api/v1/transaction/' + $routeParams.customer_id, {'amount': amount}).then(function (response) {
                $location.path('/transactions/' + $routeParams.customer_id);
            });
        }
    };
});