apibank.controller('Customers', function ($scope, $http, $location) {
    $http.get('api/v1/customers').then(function (response) {
        $scope.customers = response.data;
    });
});

apibank.controller('Transactions', function ($scope, $http) {
    $http.get('api/v1/customers').then(function (response) {
        $scope.customers = response.data;
    });
});