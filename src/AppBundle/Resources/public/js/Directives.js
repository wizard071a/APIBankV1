apibank.directive('ngCustomerListDirective', function($location) {
    return function($scope, element, attrs) {
        angular.element('button:last').bind('click', function(){
            $scope.$apply(function() {
                $location.path('/transactions/' + $scope.customer.customerId);
            });
        });
    };
});

apibank.directive('ngTransactionListDirective', function($location, $http, $route, $rootScope) {
    return function($scope, element, attrs) {
        angular.element('button.editBtn:last').bind('click', function(){
            $scope.$apply(function() {
                $location.path('/transaction/edit/' + $scope.transaction.customerId + '/' + $scope.transaction.transactionId);
            });
        });
        angular.element('button.deleteBtn:last').bind('click', function(){
            $scope.$apply(function() {
                $http.delete('api/v1/transaction/' + $scope.transaction.transactionId + '?' + $rootScope.apikey).then(function (response) {
                    $route.reload();
                });
            });
        });
    };
});