<?php

use App\Models\Country;

function validationError($errors){

    return response()->json([
        'message' => 'Məlumatları doğru daxil edin',
        'code' => 400,
        'error' => $errors,
    ], 400);
}
function notFoundError($id){
    return response()->json([
        'message' => 'Məlumat Tapılmadı',
        'code'=>404,
        'error' => $id .' uyğun nəticə tapılmadı',
    ], 404);
}
function createSuccess($data){
    return response()->json([
        'message' => 'Məlumat Uğurla bazaya yazıldı',
        'code' => 200,
        'data' => $data ,
    ], 200);
}
function updateSuccess($data){
    return response()->json([
        'message' => 'Məlumat Uğurla Dəyişdirildi',
        'code'=>200,
        'data' => $data ,
    ], 200);
}
function checkIfExist($table,$column,$data){
    $class = 'App\Models\\' . $table;

    $model= $class::query()
        ->select('*')
        ->where(''.$column.'', $data)
        ->first();

    return $model != null ? 1 : 0;
}
function notDeleteError(){
    return response()->json([
        'message' => 'Məlumat Silinə Bilməz',
        'code'=>403,
        'data' => 'Bu id - ə bağlı başqa məlumatlar var' ,
    ], 403);
}
function deleted(){
    return response()->json([
        'message' => 'Məlumat Silindi',
        'code'=>200,
        'data' => 'Məlumat Uğurla Silindi' ,
    ], 200);
}

function simpleTree($datas){
    $tree = [];
    foreach ($datas as $data) {
            $tree[] = array(
                'key' => $data->id,
                'value' => $data->id,
                'title' => $data->name,
            );
    }
    return $tree;
}

function treeForTwoTable($first_table,$second_table){
    $tree = [];
foreach ($first_table as $first){
    $tree[] = array(
        'key' => $first->id,
        'value' => $first->id,
        'title' => $first->name,
        'children' => generateTree($second_table, $first->id)

    );

}
    return $tree;
}
function generateTree($datas,  $parent) {
    $tree = [];
    foreach ($datas as $data) {
        if($data->parent_id == $parent) {
            $tree[] = array(
                'key' => $data->id,
                'value' => $data->id,
                'title' => $data->name,
//                'children' => generateTreeThird($datas, $data->id)
            );
        }
    }

    return $tree;
}

function treeForTreeTable($first_table,$second_table,$third_table){
    $tree = [];
    foreach ($first_table as $first){
        $tree[] = array(
            'key' => $first->id,
            'value' => $first->id,
            'title' => $first->name,
            'children' => generateSecondTree($second_table, $first->id,$third_table)

        );

    }
    return $tree;
}
function generateSecondTree($datas, $parent,$third_table) {
    $tree = [];
    foreach ($datas as $data) {
        if($data->parent_id == $parent) {
            $tree[] = array(
                'key' => $data->id,
                'value' => $data->id,
                'title' => $data->name,
                'children' => generateTree($third_table, $data->id)
            );
        }
    }

    return $tree;
}

function clearEmptyChildren(&$tree)
{
    foreach ($tree as $key =>$value )
    {
        if(empty($value['children']))
        {
            unset($tree[$key]['children']);
        }
        else
        {
            clearEmptyChildren($tree[$key]['children']);
        }
    }
}

function paginate(\Illuminate\Http\Request $request, &$limit, &$offset) {
    $limit = $request->has('limit') ? intval($request->get('limit')) : 10;
    $page = $request->has('page') ? intval($request->get('page')) - 1 : 0;
    $offset = ($page) * $limit;
}
function filter($val) {
    return "%$val%";
}
