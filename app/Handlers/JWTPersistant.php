<?php

use Eljam\GuzzleJwt\JwtMiddleware;
use Eljam\GuzzleJwt\Manager\JwtManager;
use Eljam\GuzzleJwt\Persistence\SimpleCacheTokenPersistence;
use Eljam\GuzzleJwt\Strategy\Auth\JsonAuthStrategy;
use Eljam\GuzzleJwt\Strategy\Auth\QueryAuthStrategy;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Cache;

require_once 'vendor/autoload.php';


$authStrategy = new JsonAuthStrategy(
  [
    'username' => 'peter50',
    'password' => 'password',
    'json_fields' => ['username', 'password'],
  ]
);

$cache = Cache::store('file');
$ttl = config('jwt.ttl');
$cacheKey = 'myUniqueKey' . time();

$persistenceStrategy = new SimpleCacheTokenPersistence($cache, $ttl, $cacheKey);
// $persistenceStrategy = new MyCustomPersistence();

// dd($persistenceStrategy);

$baseUri = 'https://devapi.graphiclead.com';

// Create authClient
$authClient = new Client(['base_uri' => $baseUri]);

//Create the JwtManager
$jwtManager = new JwtManager(
  $authClient,
  $authStrategy,
  $persistenceStrategy,
  [
    'token_url' => '/lv1/login',
    'token_key' => 'token',
    'expire_key' => 'expires_in'
  ]
);

// Create a HandlerStack
$stack = HandlerStack::create();

// Add middleware
$stack->push(new JwtMiddleware($jwtManager));

$client = new Client(['handler' => $stack, 'base_uri' => $baseUri]);

try {

  $response2 = $client->post('lv1/imageqr', [
    'json' => [
      'data' => 'dbjhsgsgjsgjs',
      'image' => [
        'method' => 'upload'
      ],
      'attributes' => [
        'error_connection' => 'L',
        'quiet_zone' => 0,
        'version' => 5,
        'rotate' => 90,
        'eye_shape' => 'rounded'
      ],
      'output' => [
        'format' => 'png',
        'callback_success' => 'http://aditya.com/image?success',
        'callback_failure' => 'http://aditya.com/image?failure'
      ]
    ]
  ]);

  $data = $response2->getBody()->getContents();
  $data = json_decode($data, true);
  // dd($data);
  if (isset($data['upload_url'])) {

    // $file =  (storage_path('app/public/ImagesToSave/') . 'logo1.jpg');
    $file =  file_get_contents(\storage_path('images/') . 'logo1.jpg');

    // $file = Storage::get(\storage_path('images/logo1.jpg'));
    // dd($file);
    $fh = fopen('php://memory', 'w+b');
    fwrite($fh, $file);
    $contentType = mime_content_type($fh);
    fclose($fh);

    // dd($contentType);

    $opts = [
      // auth
      // 'body' => fopen(\storage_path('images/') . 'logo1.jpg', "r"),
      'body' => $file,
      'headers' => ['Content-Type' => $contentType],
    ];

    $endpoint = substr($data['upload_url'], 31);

    $response3 = $client->put($endpoint, $opts);

    // if ($response3->getStatusCode() == '201' || $response3->getStatusCode() == '200') {
    // dd($response3->getBody());
    // }

    $imageid = $data['id'];
    $response4 = $client->get("lv1/imageqr/$imageid");
    dd($response4->getBody()->getContents());
  }
} catch (Exception $e) {
  echo $e->getMessage();
}

//response
//{"data":"pong"}
