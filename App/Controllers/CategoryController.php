<?php

namespace App\Controllers;

use App\Models\Category;
use Core\Exceptions\HttpNotFoundException;
use Core\Exceptions\ValidationException;
use Core\Http\JsonResponse;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\UploadedFile;
use Core\Validation\RuleBuilder as Rule;
use Throwable;

class CategoryController
{
    /**
     * @param Request $request
     * @return Response
     * @throws Throwable
     */
    public function index(Request $request): Response
    {
        $categories = Category::all();
        return view('admin/categories', [
            'categories' => $categories,
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => Rule::new()->required()->minLength(2)->maxLength(128),
            'description' => Rule::new()->nullable()->minLength(2)->maxLength(512),
            'thumbnail' => Rule::new()->nullable()->file()->image(),
            'parent_id' => Rule::new()->nullable()->numeric()->exists(Category::$table, 'id'),
            'visibility' => Rule::new()->nullable(),
        ]);

        if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
            $thumbnail = $request->file('thumbnail')->store('category_thumbnails');
        } else {
            $thumbnail = null;
        }

        if ($request->input('visibility') !== null) {
            $visibility = true;
        } else {
            $visibility = false;
        }

        $category = Category::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'thumbnail_path' => DIRECTORY_SEPARATOR . $thumbnail,
            'parent_id' => $request->input('parent_id'),
            'display' => $visibility,
        ]);

        return jsonResponse([
            'success' => true,
            'message' => 'Category created successfully',
            'category' => $category,
        ], 201);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $category = Category::find($id);
        if ($category === null) {
            throw new HttpNotFoundException('Category not found');
        }

        $thumbnail = $category->thumbnail_path;
        $category->delete();

        if ($thumbnail !== null) {
            UploadedFile::delete($thumbnail);
        }

        return jsonResponse([
            'success' => true,
            'message' => 'Category deleted successfully',
        ], 200);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name' => Rule::new()->required()->minLength(2)->maxLength(128),
            'description' => Rule::new()->nullable()->minLength(2)->maxLength(512),
            'thumbnail' => Rule::new()->nullable()->file()->image(),
            'parent_id' => Rule::new()->nullable()->numeric()->exists(Category::$table, 'id'),
            'visibility' => Rule::new()->nullable(),
        ]);

        $category = Category::find($id);
        if ($category === null) {
            throw new HttpNotFoundException('Category not found');
        }

        if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
            // Delete the old thumbnail if it exists
            if ($category->thumbnail_path !== null) {
                UploadedFile::delete($category->thumbnail_path);
            }

            // Store the new thumbnail
            $thumbnail = $request->file('thumbnail')->store('category_thumbnails');
            $category->thumbnail_path = DIRECTORY_SEPARATOR . $thumbnail;
        }

        if ($request->input('visibility') !== null) {
            $visibility = true;
        } else {
            $visibility = false;
        }

        $category->name = $request->input('name');
        $category->description = $request->input('description');
        $category->parent_id = $request->input('parent_id');
        $category->display = $visibility;
        $category->save();

        return jsonResponse([
            'success' => true,
            'message' => 'Category updated successfully',
            'category' => $category,
        ], 200);
    }
}