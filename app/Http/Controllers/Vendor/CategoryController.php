<?php

namespace App\Http\Controllers\Vendor;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Translation;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    function index(Request $request)
    {
        $key = explode(' ', $request['search']) ?? null;
        $categories=Category::with('childes')->where(['position'=>0])->latest()

        ->when(isset($key) , function($query) use($key){
            $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
        })

        ->paginate(config('default_pagination'));
        return view('vendor-views.category.index',compact('categories'));
    }

    public function get_all(Request $request){
        $data = Category::where('name', 'like', '%'.$request->q.'%')->limit(8)->get([DB::raw('id, CONCAT(name, " (", if(position = 0, "'.translate('messages.main').'", "'.translate('messages.sub').'"),")") as text')]);
        if(isset($request->all))
        {
            $data[]=(object)['id'=>'all', 'text'=>'All'];
        }
        return response()->json($data);
    }

    function sub_index(Request $request)
    {
        $key = explode(' ', $request['search']) ?? null;
        $categories=Category::with(['parent'])->where(['position'=>1])
        ->when(isset($key) , function($query) use($key){
            $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
        })
        ->latest()->paginate(config('default_pagination'));
        return view('vendor-views.category.sub-index',compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|unique:categories|max:100',
            'image'  => 'nullable|max:2048',
            'name.0' => 'required',
        ], [
            'name.required'   => translate('messages.Name is required!'),
            'name.0.required' => translate('default_name_is_required'),
        ]);

        $category = new Category();
        $category->name      = $request->name[array_search('default', $request->lang)];
        $category->image     = $request->has('image') ? Helpers::upload(dir:'category/',format: 'png',image: $request->file('image')) : 'def.png';
        $category->parent_id = 0;
        $category->position  = 0;
        $category->save();

        $data = [];
        $default_lang = str_replace('_', '-', app()->getLocale());

        foreach($request->lang as $index=>$key) {
            if($default_lang == $key && !($request->name[$index])){
                if ($key != 'default') {
                    $data[] = [
                        'translationable_type' => 'App\Models\Category',
                        'translationable_id'   => $category->id,
                        'locale'               => $key,
                        'key'                  => 'name',
                        'value'                => $category->name,
                    ];
                }
            } else {
                if ($request->name[$index] && $key != 'default') {
                    $data[] = [
                        'translationable_type' => 'App\Models\Category',
                        'translationable_id'   => $category->id,
                        'locale'               => $key,
                        'key'                  => 'name',
                        'value'                => $request->name[$index],
                    ];
                }
            }
        }

        if(count($data)) {
            Translation::insert($data);
        }

        Toastr::success(translate('messages.category_added_successfully'));
        return back();
    }

    public function edit($id)
    {
        $category = Category::withoutGlobalScope('translate')->findOrFail($id);
        return response()->json([
            'id'    => $category->id,
            'name'  => $category->name,
            'image' => $category->image_full_url,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'   => 'required|max:100|unique:categories,name,'.$id,
            'image'  => 'nullable|max:2048',
            'name.0' => 'required',
        ],[
            'name.0.required' => translate('default_name_is_required'),
        ]);

        $category = Category::find($id);
        $slug = Str::slug($request->name[array_search('default', $request->lang)]);
        $category->slug  = $category->slug ?: "{$slug}{$category->id}";
        $category->name  = $request->name[array_search('default', $request->lang)];
        $category->image = $request->has('image') ? Helpers::update(dir:'category/', old_image:$category->image, format:'png', image:$request->file('image')) : $category->image;
        $category->save();

        $default_lang = str_replace('_', '-', app()->getLocale());

        foreach($request->lang as $index=>$key) {
            if($default_lang == $key && !($request->name[$index])){
                if (isset($category->name) && $key != 'default') {
                    Translation::updateOrInsert(
                        ['translationable_type' => 'App\Models\Category', 'translationable_id' => $category->id, 'locale' => $key, 'key' => 'name'],
                        ['value' => $category->name]
                    );
                }
            } else {
                if ($request->name[$index] && $key != 'default') {
                    Translation::updateOrInsert(
                        ['translationable_type' => 'App\Models\Category', 'translationable_id' => $category->id, 'locale' => $key, 'key' => 'name'],
                        ['value' => $request->name[$index]]
                    );
                }
            }
        }

        Toastr::success(translate('messages.category_updated_successfully'));
        return back();
    }

    public function delete(Request $request)
    {
        $category = Category::findOrFail($request->id);
        if ($category?->childes?->count() == 0) {
            $category?->translations()?->delete();
            $category->delete();
            Toastr::success(translate('messages.Category removed!'));
        } else {
            Toastr::warning(translate('messages.remove_sub_categories_first'));
        }
        return back();
    }
}
