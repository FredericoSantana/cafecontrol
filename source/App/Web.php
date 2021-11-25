<?php

namespace Source\App;

use Source\Core\Controller;

/**
 * Class Web
 * @package Source\App
 */
class Web extends Controller
{
  /**
   * Web constructor.
   */
  public function __construct()
  {
    parent::__construct(__DIR__ . "/../../themes/" . CONF_VIEW_THEME . "/");
  }

  /**
   * SITE HOME
   */
  public function home(): void
  {
    echo "<h1>Home</h1>";
  }

  /**
   * SITE NAV ERROR
   * @param array $data
   */
  public function error(array $data): void
  {
    echo "<h1>Error</h1>";
    var_dump($data);
  }
}