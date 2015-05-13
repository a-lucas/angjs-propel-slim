# Code generator for AngularJS 1.*, Propel 1.* and SLIM framework

- Propel is a simple PHP ORM that automatically generates sophisticated models from a database structure
- SLIM is a small PHP framework that ease the design and implementation of a REST API.
- AngularJS is a javascript framework with dual binding and a separtion of concerns.

This code generator will help you generate 2 files for a Propel defined Model: 

1. PHP code based on Propel and SLIM that defines a CRUD REST API.
2. Javascript code based AngularJS service definition, that communicates with the REST API defined previously.
 
The aim of this generator is to speed up the process of implementaing from scratch an AngularJS application comunicating with a PHP backend wth SLIM and Propel.

# How to run

sudo install php-cli :

    sudo apt-get install php5-cli

download this generator.php file : 

    git clone https://github.com/a-lucas/angjs-propel-slim.git .
    
run the generator :

    php generator.php


The generator will ask you to enter your model className. 
Your Propel's namespace option must be activated and you will need to enter the className prepended with the NameSpace.

For example, if Propel generates a model called Products, and the Products.php file starts like this : 

    <?
    namespace \MODELS\PRODUCT
    
    class Products extends ....
    
    
Then you should type : `\MODELS\PRODUCT\Products` 


# Generated code structure

## PHP Slim code

The code generated will looks like this : 

    $app->get("/PRODUCT/Products", function(){
        echo  \MODELS\PRODUCT\ProductsQuery::create()->find()->toJSON(false) ;
    });
    $app->get("/PRODUCT/Product/:id", function($id){
        echo  \MODELS\PRODUCT\ProductsQuery::create()->findPk($id)->toJSON(false);
    });
    $app->put("/PRODUCT/Product/:id", function($id){
        $m = \MODELS\PRODUCT\ProductsQuery::create()->findPk($id);
        $m->importFrom("JSON", jreq());
        $m->save();
    });
    $app->post("/PRODUCT/Product", function(){
        $m = new \MODELS\PRODUCT\Products();
        $m->importFrom("JSON", jreq());
        $m->save();
    });
    $app->delete("/PRODUCT/Product/:id", function($id){
        $m = \MODELS\PRODUCT\ProductsQuery::create()->findPk($id);
        $m->delete();
    });

## AngularJS factory definition
    
    var app= angular.module('ProductsFactory')'
    app.factory('Products', function($http,$q) {
         return {
            getAll: function() {
                var deferred = $q.defer();
                $http.get('http://www.mydomain.com/api/index.php/PRODUCT/Products').then(function(c) {
                        deferred.resolve(c.data);
                }, function(reason) {
                        deferred.reject(reason);
                });
                return deferred.promise;
            },
            get: function(id) {
                var deferred = $q.defer();
                $http.get('http://www.mydomain.com/api/index.php/PRODUCT/Product/'+id).then(function(c) {
                        deferred.resolve(c.data);
                }, function(reason) {
                        deferred.reject(reason);
                });
                return deferred.promise;
            },
            doSave : function(s){
                var deferred = $q.defer();
                $http.put('http://www.mydomain.com/api/index.php/PRODUCT/Product/'+ s.id , s).then(function() {
                        deferred.resolve();
                }, function(reason) {
                        deferred.reject(reason);
                });
                return deferred.promise;
            },
            doCreate : function(s){
                var deferred = $q.defer();
                $http.post('http://www.mydomain.com/api/index.php/PRODUCT/Product' , s).then(function(result) {
                        deferred.resolve(result.data);
                }, function(reason) {
                        deferred.reject(reason);
                });
                return deferred.promise;
            },
            doDelete : function(s){
                var deferred = $q.defer();
                $http.delete('http://www.mydomain.com/api/index.php/PRODUCT/Product/'+s.id).then(function() {
                        deferred.resolve();
                }, function(reason) {
                        deferred.reject(reason);
                });
                return deferred.promise;
            }
        };
    });

## Config values

You can edit the domainName, and the api relative path in the `config.php` file


# How to use the code : 

## SLIM

- Copy paste the php generated code in a file called `product.php`
- In your SLIM `index.php`
  - Load Propel framework
  - include `product.php`
  
## AngularJS

- Save the generated code to a file `/factory/products.js`
- Include it in your index.html
- Define it in your angular app definition :  ` angular.module('YourApp',['ProductsFactory'])`
- Use it in your controller : 

    app.controller("YourController",function($scope, ProductsFactory){
        
        $scope.products = ProductsFactory.getAll();
         
    });
 
 