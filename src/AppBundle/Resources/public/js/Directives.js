apibank.directive('ngCustomerListDirective', function($location) {
    return function($scope, element, attrs) {
        angular.element('button:last').bind('click', function(){
            $scope.$apply(function() {
                $location.path('/transactions/' + $scope.customer.customerId);
            });
        });
    };
});