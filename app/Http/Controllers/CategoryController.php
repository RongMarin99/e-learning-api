<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function get(Request $request)
    {
        $table_size = $request->input('table_size');
        $filter = $request->input('filter');
        if (empty($table_size)) {
            $table_size = 10;
        }
        $data = Category::lists($filter)->orderBy('id', 'DESC')->paginate($table_size);
        $response = [
            'pagination' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
            ],
            'data' => $data->items(),
            'success' => 1,
        ];
        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        $form = $request['data'];
        $data = new Category();
        $data->setData($form);
        $data->save();
        return $this->responseWithData($data);
    }

    public function update(Request $request)
    {
        $form = $request['data'];
        $data = Category::find($form['id']);
        $data->setData($form);
        $data->save();
        return $this->responseWithData($data);
    }

    public function delete(Request $request)
    {
        $id = $request['id'];
        Category::find($id)->delete();
    }
}
