<?php

class router {
  static $routes = [];
  static function path($reqM, $uri, $hdl) {
    $uri = preg_replace('#\{(.*?)\}#', '([^\/]+)', $uri);
    return self::$routes[] = [$reqM, "#^$uri$#", $hdl];
  }
  static function handleRequest() {
    $REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];
    $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

    foreach(self::$routes as $r) {
      [$reqM, $uri, $hdl] = $r;
      if($reqM !== $REQUEST_METHOD) continue;
      if(preg_match($uri, $path, $matches)) {
        array_shift($matches);
        call_user_func_array($hdl, $matches);
        return 'suc';
      }
    }
    move("/");
  }
}

function get($uri, $hdl) {
  router::path("GET", $uri, $hdl);
}
function post($uri, $hdl) {
  router::path("POST", $uri, $hdl);
}