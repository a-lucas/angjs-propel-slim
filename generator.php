<?php

/*
/**
 * URLS
\
$app->get('/URLS/urls/:id', function($websiteID){
    echo  \MODELS\URLS\urlsQuery::create()->filterByWebsiteid($websiteID)->find()->toJSON(false) ;
});
$app->get('/URLS/url/:id', function($urlID){
    echo  \MODELS\URLS\urlsQuery::create()->findPk($urlID)->toJSON(false);
});
$app->put('/URLS/url/:id', function($urlID){
    $m = \MODELS\URLS\urlsQuery::create()->findPk($urlID);
    $m->importFrom('JSON', jreq());
    $m->save();
});
$app->post('/URLS/url', function(){
    $m = new \MODELS\URLS\urls();
    $m->importFrom('JSON', jreq());
    $m->save();
});
$app->delete('/URLS/url/:id', function($urlID){
    $m = \MODELS\URLS\urlsQuery::create()->findPk($urlID);
    $m->delete();
}); */

echo "Enter your propel Model path (ex : \MODELS\URLS\urls : \n";
$model = trim(fgets(STDIN));



$data = explode("\\",$model);
if($data[0]==""){
    unset($data[0]);
}
if(count($data)!=3){
    echo "The class path must contain 3 elements\n";
    exit(1);
}
$d = [];
foreach($data as $dd){
    $d[]=$dd;
}
$data=$d;
$U = $data[1];
$className = $data[2];
if(substr($className, -3)=="ies"){    
    $singular = substr($className, 0, -3)."y";
    $plural = $className;
}
elseif(substr($className, -1)=="s"){
    $singular = substr($className, 0, -1);
    $plural = $className;
}    
else{
    $singular = substr($className, 0, -1);
    $plural = $className;
}


$str='
/**
 * '.strtoupper($data[2]).'
 */    

$app->get("/'.$U."/".$plural.'", function(){
    echo  '.$model.'Query::create()->find()->toJSON(false) ;
});
$app->get("/'.$U.'/'.$singular.'/:id", function($id){
    echo  '.$model.'Query::create()->findPk($id)->toJSON(false);
});
$app->put("/'.$U.'/'.$singular.'/:id", function($id){
    $m = '.$model.'Query::create()->findPk($id);
    $m->importFrom("JSON", jreq());
    $m->save();
});
$app->post("/'.$U.'/'.$singular.'", function(){
    $m = new '.$model.'();
    $m->importFrom("JSON", jreq());
    $m->save();
});
$app->delete("/'.$U.'/'.$singular.'/:id", function($id){
    $m = '.$model.'Query::create()->findPk($id);
    $m->delete();
});
';

echo $str;

$aN  = ucfirst($data[2]);
$angular = "
app.factory('$aN', function(\$http,\$q) {
     return {
        getAll: function() {
            var deferred = \$q.defer();
            \$http.get('/api/index.php/$U/$plural').then(function(c) { 
                    deferred.resolve(c.data);
            }, function(reason) {
                    deferred.reject(reason);
            });
            return deferred.promise;
        },
        get: function(id) {
            var deferred = \$q.defer();
            \$http.get('/api/index.php/$U/$singular/'+id).then(function(c) { 
                    deferred.resolve(c.data);
            }, function(reason) {
                    deferred.reject(reason);
            });
            return deferred.promise;
        },
        doSave : function(s){
            var deferred = \$q.defer();
            \$http.put('/api/index.php/$U/$singular/'+ s.id , s).then(function() { 
                    deferred.resolve();
            }, function(reason) {
                    deferred.reject(reason);
            });
            return deferred.promise;
        },
        doCreate : function(s){
            var deferred = \$q.defer();
            \$http.post('/api/index.php/$U/$singular' , s).then(function(result) { 
                    deferred.resolve(result.data);
            }, function(reason) {
                    deferred.reject(reason);
            });
            return deferred.promise;
        },
        doDelete : function(s){
            var deferred = \$q.defer();
            \$http.delete('/api/index.php/$U/$singular/'+s.id).then(function() { 
                    deferred.resolve();
            }, function(reason) {
                    deferred.reject(reason);
            });
            return deferred.promise;
        }
    };
});
        
";


echo $angular;  
?>
