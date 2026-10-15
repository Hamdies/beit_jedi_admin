<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Models\Food;
use App\Models\Category;
use App\Models\Translation;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

/**
 * Category management for the owner app.
 *
 * Beit Jedi is a single restaurant, so the owner manages the same global
 * categories the admin panel does. Unlike the public `categories` endpoints,
 * these list disabled categories too, so the owner can switch them back on.
 */
class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $categories = Category::withCount(['products', 'childes'])
            ->with(['childes' => function ($query) {
                $query->withCount(['products', 'childes'])->orderBy('priority', 'desc')->orderBy('id');
            }])
            ->where('position', 0)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    foreach (explode(' ', $search) as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                    $q->orWhereHas('translations', fn ($t) => $t->where('key', 'name')->where('value', 'like', "%{$search}%"));
                });
            })
            ->orderBy('priority', 'desc')->orderBy('id')
            ->get();

        return response()->json(Helpers::category_data_formatting($categories, true), 200);
    }

    public function childes($id)
    {
        $categories = Category::withCount(['products', 'childes'])
            ->where('parent_id', $id)
            ->orderBy('priority', 'desc')->orderBy('id')
            ->get();

        return response()->json(Helpers::category_data_formatting($categories, true), 200);
    }

    public function products(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $limit = $request->limit ?? 10;
        $offset = $request->offset ?? 1;
        $categoryId = $request->category_id;

        $paginator = Food::where('restaurant_id', $request['vendor']->restaurants[0]->id)
            ->when($request->sub_category == 1, fn ($q) => $q->where('category_id', $categoryId))
            ->when($request->sub_category != 1, fn ($q) => $q->whereHas('category', function ($c) use ($categoryId) {
                $c->whereId($categoryId)->orWhere('parent_id', $categoryId);
            }))
            ->latest()->paginate($limit, ['*'], 'page', $offset);

        return response()->json([
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => Helpers::product_data_formatting(data: $paginator->items(), multi_data: true, trans: true, local: app()->getLocale()),
        ], 200);
    }

    public function store(Request $request)
    {
        $parentId = (int) ($request->parent_id ?? 0);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->where('parent_id', $parentId)],
            'parent_id' => 'nullable|integer|exists:categories,id',
            'image' => 'nullable|image|max:2048',
        ], [
            'name.required' => translate('messages.Name is required!'),
            'name.unique' => translate('messages.category_name_already_exists'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $category = new Category();
        $category->name = trim($request->name);
        $category->image = $request->hasFile('image')
            ? Helpers::upload(dir: 'category/', format: 'png', image: $request->file('image'))
            : 'def.png';
        $category->parent_id = $parentId;
        $category->position = $parentId == 0 ? 0 : 1;
        $category->status = 1;
        $category->save();

        $this->saveNameTranslation($category);

        return response()->json([
            'message' => translate($parentId == 0 ? 'messages.category_added_successfully' : 'messages.sub_category_added_successfully'),
            'id' => $category->id,
        ], 200);
    }

    public function update(Request $request)
    {
        $category = Category::find($request->id);

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:categories,id',
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('categories', 'name')->where('parent_id', $category?->parent_id ?? 0)->ignore($request->id),
            ],
            'image' => 'nullable|image|max:2048',
        ], [
            'name.required' => translate('messages.Name is required!'),
            'name.unique' => translate('messages.category_name_already_exists'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $category->name = trim($request->name);
        if ($request->hasFile('image')) {
            $category->image = Helpers::update(dir: 'category/', old_image: $category->image, format: 'png', image: $request->file('image'));
        }
        $category->save();

        $this->saveNameTranslation($category);

        return response()->json([
            'message' => translate($category->parent_id == 0 ? 'messages.category_updated_successfully' : 'messages.sub_category_updated_successfully'),
        ], 200);
    }

    public function status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:categories,id',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $category = Category::find($request->id);
        $category->status = $request->status ? 1 : 0;
        $category->save();

        return response()->json([
            'message' => translate($category->parent_id == 0 ? 'messages.category_status_updated' : 'messages.sub_category_status_updated'),
        ], 200);
    }

    /**
     * `Category::name` reads the translation for the request's locale before
     * the base column, so a stale translation would hide a rename. Write the
     * name to the locale the owner app is using.
     */
    private function saveNameTranslation(Category $category): void
    {
        Translation::updateOrInsert(
            [
                'translationable_type' => Category::class,
                'translationable_id' => $category->id,
                'locale' => app()->getLocale(),
                'key' => 'name',
            ],
            ['value' => $category->getRawOriginal('name')]
        );
    }
}
