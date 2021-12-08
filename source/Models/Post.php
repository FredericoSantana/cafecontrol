<?php

namespace Source\Models;

use Source\Core\Model;

/**
 * Class Post
 * @package Source\Models
 */
class Post extends Model
{
  /** @var bool */
  private $all;

  /**
   * Post constructor.
   * @param bool $all = ignore status and post_at
   */
  public function __construct(bool $all = false)
  {
    $this->all = $all;
    parent::__construct("posts", ["id"], ["title", "id", "subtitle", "content"]);

  }

  /**
   * @param string|null $terms
   * @param string|null $params
   * @param string $columns
   * @return Model
   */
  public function find(?string $terms = null, ?string $params = null, string $columns = "*"): Model
  {
    if (!$this->all) {
      $terms = "status = :status AND post_at <= NOW()" . ($terms ? " AND {$terms}" : "");
      $params = "status=post" . ($params ? "&{$params}" : "");
    }
    return parent::find($terms, $params, $columns);
  }

  /**
   * @param string $uri
   * @param string $columns
   * @return Post|null
   */
  public function findByUri(string $uri, string $columns = "*"): ?Post
  {
    $find = $this->find("uri = :uri", "uri={$uri}", $columns);
    return $find->fetch();
  }

  /**
   * @return User|null
   */
  public function author(): ?User
  {
    if ($this->author) {
      return (new User())->findById($this->author);
    }
    return null;
  }

  /**
   * @return Category|null
   */
  public function category(): ?Category
  {
    if ($this->category) {
      return (new Category())->findById($this->category);
    }
    return null;
  }
}