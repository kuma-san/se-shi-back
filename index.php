<?php
require 'vendor/autoload.php';
include 'conf/orm.php';

$app = new \Slim\Slim();
$app->response->headers->set('Content-Type', 'application/json');

//ユーザ情報API
$app->post('/api/user', function() use ($app) {
  $data = json_decode($app->request->getBody(), true);

  //バリデーション
  if ($data['idfv'] === "" || strlen($data['idfv']) !== 36 || $data['timestamp'] === "") {
    $app->halt(400);
  }

  try {
    $user = ORM::for_table('user')->find_one($data['idfv']);
  } catch (Exception $e) {
    $app->halt(500, $e->getMessage());
  }
  if ($user) {
    $app->halt(400);
  }
  try{
    $user = ORM::for_table('user')->create();
    $user->set($data);
    $user->save();
  } catch (Exception $e) {
    $app->halt(500, $e->getMessage());
  }

  $app->halt(200);
});

$app->put('/api/user', function() use ($app) {
  $app->halt(501);
});

$app->post('/api/devicetoken', function() use ($app) {
  $data = json_decode($app->request->getBody(), true);

  if ($data['idfv'] === "" || strlen($data['idfv']) !== 36 || $data['device_token'] === "" || $data['device_token'] === 36 || $data['timestamp'] === "") {
    $app->halt(400);
  }

  try {
    $deviceToken = ORM::for_table('device_token')->create();
    $deviceToken->set($data);
    $deviceToken->save();
  } catch (Exception $e) {
    $app->halt(500, $e->getMessage());
  }

    $app->halt(200);
});


//検査データAPI

$app->post('/api/analysis/id', function() use ($app) {
  $data = json_decode($app->request->getBody(), true);

  if ($data['idfv'] === "" || strlen($data['idfv']) !== 36 || $data['timestamp'] === "") {
    $app->halt(400);
  }
  try {
    $analysisId = ORM::for_table('analysis_id')->find_one($data['analysisid']);
  } catch (Exception $e) {
    $app->halt(500, $e->getMessage());
  }
  if ($analysisId) {
    $app->halt(400);
  }
  try{
     $analysisId = ORM::for_table('analysis_id')->create();
     $analysisId->set($data);
     $analysisId->save();
  } catch (Exception $e) {
    $app->halt(500, $e->getMessage());
  }

  $app->halt(200);
});

$app->get('/api/analysis/:analysisid', function($analysisid) use ($app) {
  try {
    $result = ORM::for_table('analyzed_data')
      ->where('analysisid', $analysisid)
      ->find_one()
      ->as_array();
  //TODO: returns 500 even if $result is empty
  } catch (Exception $e) {
    $app->halt(500, $e->getMessage());
  }
  if (!$result) {
    $app->halt(404);
  } else {
    $app->response->setBody(json_encode($result));
  }

});

$app->post('/api/analysis', function() use ($app) {
  $data = json_decode($app->request->getBody(), true);

  if ($data['analysisid'] === "" || $data['concentration'] === "" || $data['momentum'] === "" || $data['timestamp'] === "") {
    $app->halt(400);
  }

  try {
    $analysis = ORM::for_table('analyzed_data')->find_one($data['analysisid']);
  } catch (Exception $e) {
    $app->halt(500, $e->getMessage());
  }
  if ($analysis) {
    $app->halt(400);
  }
  try{
     $analysisId = ORM::for_table('analyzed_data')->create();
     $analysisId->set($data);
     $analysisId->save();
  } catch (Exception $e) {
    $app->halt(500, $e->getMessage());
  }

  $app->halt(200);
});

$app->get('/api/analysis/rank/concentration/:concentration/momentum/:momentum', function($concentration, $momentum) use ($app){
  $app->halt(501);
});

$app->run();

