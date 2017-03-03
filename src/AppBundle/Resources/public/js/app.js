var apibank = angular.module('ApiBank', ['ngRoute']);

apibank.config(function($routeProvider) {
    $routeProvider
        .when('/customers', {
            controller  : 'Customers',
            templateUrl: "customers_list"
        })
        .when('/transactions/:customer_id', {
            controller  : 'Transactions',
            templateUrl: function(params){ return 'transactions_list/' + params.customer_id; }
        })
        .when('/transaction/add/:customer_id', {
            controller  : 'TransactionsAdd',
            templateUrl: function(params){ return 'transaction_add/' + params.customer_id; }
        })
        .otherwise({
            redirectTo: "/customers"
        })
});