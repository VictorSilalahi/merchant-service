<?php

namespace App;

use App\Category;
use League\Fractal;

class FlatCategoryTransformer extends Fractal\TransformerAbstract
{
  private $_middleUrl = "";

  public function setMiddleUrl($url)
  {
    $this->_middleUrl = $url;
  }

  public function transform(Category $category)
  {

    return [
      "guid" => (string) $category->guid,
      "parent_guid" => (string) $category->parentCategory->guid,
      "category_name" => (string) $category->category_name,
      "description" => (string) $category->description,
      "images" => (string) $category->images,
      "status" => (int) $category->status,
      "updated_by" => (string) $category->updated_by,
      "created_at"   => (string) $category->created_at->format("Y-m-d H:i:s"),
      "updated_at"   => (string) $category->updated_at->format("Y-m-d H:i:s"),
      "links" => [
        "self" => $this->_middleUrl . "/category/{$category->guid}",
      ]
    ];
  }
}