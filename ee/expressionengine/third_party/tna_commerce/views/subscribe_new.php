

<? $this->view('errors'); ?>
{!-- ng-submit="subscribe_form()" ng-controller="validateCtrl" --}




<div>
    {!-- action="<?= $https_site_url ?>subscribe/payment" --}
    <form  action="<?= $https_site_url ?>subscribe/payment" ng-app="validationExample" ng-controller="subscribeController" novalidate id="subscribe_form" name="subscribe_form"  ng-submit="subscribeForm($event)" method="post">
        <input type="hidden" name="create_member" value="1" />
        <input type="hidden" name="XID" value="<?= $xid_hash ?>">

        <!-- This credit card fieldset is not required for free or external checkout (e.g., PayPal Express Checkout) payment methods. -->



        <? $this->view('subscription_type'); ?>

        <? $this->view('t_shirt_choices'); ?>


        {!-- ensure-unique="email" ng-focus --}

        <div class="form-group">
            <label for="email">Email Address</label>
            {!-- <input class="form-control" type="text" id="email" name="email"  maxlength="100" value="" ng-model="email" required /> --}

            <input class="form-control" type="email" name="email" ng-model="email" ensure-unique="email" required ng-focus>
            
            {!-- && subscribe_form.submitted --}

            <span style="color:red" ng-show="(subscribe_form.email.$dirty && subscribe_form.email.$invalid) || (subscribe_form.email.$invalid )">
                {!--
                <span ng-show="subscribe_form.email.$error.required && !subscribe_form.email.$focused">Email is required.</span>
                <span ng-show="subscribe_form.email.$error.email && !subscribe_form.email.$focused">Invalid email address.</span>
                --}
                <span style="font-weight:bold" class="error" ng-show="subscribe_form.email.$error.unique"> That email address is taken, please try another.</span></span>
                
                {!-- && !subscribe_form.email.$focused --}


        </div>



        {!--
        <span style="color:red;" class="error-container"  ng-show="subscribe_form.email.$dirty && subscribe_form.email.$invalid && !subscribe_form.email.$focused">
            <span class="error" ng-show="subscribe_form.email.$error.required"> Your email is required. </span>
            <span ng-show="subscribe_form.email.$error.email">Invalid email address.</span>

            <small class="error" ng-show="subscribe_form.email.$error.unique && !subscribe_form.email.$focused"> That email address is taken, please try another.</small>

        </span>
        --}



        <div class="form-group">
            {!-- <button type="submit" ng-disabled="subscribe_form.$invalid" class="button radius">Proceed to Checkout &raquo;</button> --}
            {!-- <input class="btn btn-success" type="submit" value="Proceed to Checkout &raquo;"> --}

            <input  type="submit" value="Proceed to Checkout &raquo;" ng-disabled="subscribe_form.email.$dirty && subscribe_form.email.$invalid && !subscribe_form.email.$pristine">
        </div>


    </form>
</div>


<script>

    $().ready(function() {
        $("#subscribe_form").validate({
            rules: {
                email: {
                    required: true,
                    email: true
                }
            }
        });

    });

</script>

<script>
    function validateCtrl($scope) {
        $scope.user = 'John Doe';
        $scope.email = 'john.doe@gmail.com';
    }
</script>


<script>

    var app = angular.module('validationExample', []);

    //alert("HELLO");


    app.controller('subscribeController', ['$scope', '$http', function($scope, $http) {
            $scope.submitted = false;
            $scope.subscribeForm = function(event) {
                //event.preventDefault();
                /*
                 $http({
                 method: 'POST',
                 url: '/subscribe/angular/',
                 data: {'email': $scope.email},
                 headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                 }).success(function(data) {
                 //alert("FORM SUBMIT ERROR");
                 $scope.subscribe_form.$setValidity('unique', data.isUnique);
                 alert('EMAIL: [' + $scope.email + ']' + ": UNIQUE = " + data.isUnique);
                 
                 
                 
                 //alert('['+$scope.email+']'+ ": UNIQUE = "+ data.isUnique);
                 });
                 
                 */
                //if (!$scope.subscribe_form.$valid) {
                //alert("ZONGO!");
                //event.preventDefault();
                // $scope.subscribe_form.submitted = true;
                //}


                //alert("BOING");


                //$scope.subscribe_form.$setValidity('unique', data.isUnique);
                //c.$setValidity('unique', data.isUnique);

                if ($scope.subscribe_form.$valid) {
                    //alert("subscribeForm VALID");
                    // Submit as normal
                } else {
                    //alert("subscribeForm INVALID");
                    $scope.subscribe_form.submitted = true;
                    event.preventDefault();
                }

            }
        }]);



    /*
     app.controller('subscribeController', ['$scope', function($scope) {
     $scope.submitted = false;
     $scope.submitForm = function(e) {
     alert("submitForm");
     $http({
     method: 'POST',
     url: '/subscribe/angular/' + attrs.ensureUnique,
     data: {'email': $scope.email.val()},
     headers: {'Content-Type': 'application/x-www-form-urlencoded'}
     }).success(function(data) {
     alert("FORM SUBMIT ERROR");
     $scope.subscribe_form.$setValidity('unique', data.isUnique);
     
     }).error(function(data) {
     e.preventDefault();
     });
     if ($scope.subscribe_form.$valid) {
     //alert("subscribeForm VALID");
     // Submit as normal
     } else {
     //alert("subscribeForm INVALID");
     $scope.subscribe_form.submitted = true;
     e.preventDefault();
     }
     };
     }]);
     */

    app.directive('ngFocus', [function() {
            var FOCUS_CLASS = "ng-focused";
            return {
                restrict: 'A',
                require: 'ngModel',
                link: function(scope, element, attrs, ctrl) {
                    ctrl.$focused = false;
                    element.bind('focus', function(evt) {
                        element.addClass(FOCUS_CLASS);
                        scope.$apply(function() {
                            ctrl.$focused = true;
                        });
                    }).bind('blur', function(evt) {
                        element.removeClass(FOCUS_CLASS);
                        scope.$apply(function() {
                            ctrl.$focused = false;
                        });
                    });
                }
            }
        }]);





    app.directive('ensureUnique', ['$http', '$timeout', function($http, $timeout) {
            var checking = null;
            return {
                require: 'ngModel',
                link: function(scope, ele, attrs, c) {
                    scope.$watch(attrs.ngModel, function(newVal) {
                        if (!checking) {
                            checking = $timeout(function() {
                                //alert('ensureUnique');
                                $http({
                                    method: 'POST',
                                    url: '/subscribe/angular/' + attrs.ensureUnique,
                                    data: {'email': ele.val()},
                                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                                }).success(function(data, status, headers, cfg) {
                                    //alert(data.isUnique);
                                    c.$setValidity('unique', data.isUnique);
                                    checking = null;

                                }).error(function(data, status, headers, cfg) {
                                    checking = null;
                                });
                            }, 500);
                        }
                    });
                }
            }
        }]);

</script>

<? $this->view('subscribe_js'); ?>

<? $this->view('shared_js'); ?>

<script>
    selected = $("#country").val();
    update_country(selected);
</script>








