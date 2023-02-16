<?php

namespace App;

use App\Category;
use League\Fractal;

class CategoryTransformer extends Fractal\TransformerAbstract
{
    private $_middleUrl = "";

    public function setMiddleUrl($url)
    {
        $this->_middleUrl = $url;
    }

    private function getSubCategories($categories)
    {
        $data = [];
        foreach ($categories as $category) {
            $data[] = [
                "id" => (string) $category->id,
                "guid" => (string) $category->guid,
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
        return $data;
    }

    public function transform(Category $category)
    {
        $subCategories = $category->subCategories == null ? null : $this->getSubCategories($category->subCategories);

        return [
            "id" => (string) $category->id,
            "guid" => (string) $category->guid,
            "parent_guid" => $category->parentCategory == null ? null : (string) $category->parentCategory->guid,
            "category_name" => (string) $category->category_name,
            "description" => (string) $category->description,
            "images" => (string) $category->images,
            "status" => (int) $category->status,
            "updated_by" => (string) $category->updated_by,
            "created_at"   => (string) $category->created_at->format("Y-m-d H:i:s"),
            "updated_at"   => (string) $category->updated_at->format("Y-m-d H:i:s"),
            "sub_categories" => $subCategories,
            "links" => [
                "self" => $this->_middleUrl . "/category/{$category->guid}",
            ]
        ];
    }
}