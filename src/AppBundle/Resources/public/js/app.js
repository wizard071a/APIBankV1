var apibank = angular.module('ApiBank', ['ngRoute']);

apibank.config(function($routeProvider) {
    $routeProvider
        .when('/customers', {
            controller  : 'Customers',
            templateUrl: "customers_view/list"
        })
        .when('/transactions/:customer_id', {
            controller  : 'Transactions',
            templateUrl: function(params){ return 'transactions_view/list/' + params.customer_id; }
        })
        .when('/transaction/add/:customer_id', {
            controller  : 'TransactionsAdd',
            templateUrl: function(params){ return 'transaction_view/add/' + params.customer_id; }
        })
        .when('/transaction/edit/:customer_id/:transaction_id', {
            controller  : 'TransactionsEdit',
            templateUrl: function(params){ return 'transaction_view/edit/' + params.customer_id + '/' + params.transaction_id; }
        })
        .otherwise({
            redirectTo: "/customers"
        })
});

apibank.factory("genericServices", function($http, $rootScope, $httpParamSerializer) {
    return {
        getApiKey: function(callback) {
            if (!$rootScope.apikey) {
                $http.get('getApiKey').then(function (response) {
                    $rootScope.apikey = $httpParamSerializer({'apikey' : response.data.api_key});
                    callback && callback();
                });
            } else {
                callback && callback();
            }
        }
    }
});