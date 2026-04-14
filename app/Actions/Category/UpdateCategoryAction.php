<?php

namespace App\Actions\Category;

use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;

class UpdateCategoryAction
{
    public function handle(UpdateCategoryRequest $request, Category $category): Category
    {
        $category->update($request->validated());

        return $category->refresh();
    }
}
