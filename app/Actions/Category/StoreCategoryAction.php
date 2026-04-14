<?php

namespace App\Actions\Category;

use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;

class StoreCategoryAction
{
    public function handle(StoreCategoryRequest $request): Category
    {
        return Category::create($request->validated());
    }
}
