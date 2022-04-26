<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DistrictController extends Controller
{
//    public function __construct()
//    {
//        $this->middleware('auth');
//    }

    public function index(Request $request)
    {
        $districtQuery=District::query();

        if($request->has('name')) {
            $districtQuery->where('name', 'like', '%'.$request->get('name').'%');
        }
        if($request->has('parent_id')) {
            $districtQuery->where('parent_id', '=', $request->get('parent_id'));
        }
        if($request->has('limit')&&$request->has('page')) {
            $page = $request->page;
            $limit = $request->limit;
            $offset = ($page - 1) * $limit;
            $count = count($districtQuery->get());
            $districts = $districtQuery->limit($limit)->offset($offset)->get();

        }
        else
        {
            $count = count($districtQuery->get());
            $districts = $districtQuery->get();
        }
        $countries=Country::get();
        $cities=City::get();
//        print_r($cities);
//        die();

        $datas=treeForTreeTable($countries,$cities,$districts);
        clearEmptyChildren($datas);
        return response()->json(['data' => $datas, 'total' => $count]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'=>['required','string'],
            'parent_id'=>['required','integer',Rule::exists('cities','id')]
        ]);

        if ($validator->fails())
        {
            return validationError($validator->errors());
        }


        $model= new District();
        $model->name=$request->name;
        $model->parent_id=$request->parent_id;
        $model->save();

        return createSuccess($model);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'=>['required','string'],
            'parent_id'=>['required','integer',Rule::exists('cities','id')],

        ]);

        if ($validator->fails())
        {
            return validationError($validator->errors());
        }

        $model= District::find($request->id);
        $model->name=$request->name;
        $model->parent_id=$request->parent_id;
        $model->save();

        return updateSuccess($model);
    }
    public function delete($id)
    {
        $district=checkIfExist('District','id',$id);
        if($district==0){
            return notFoundError($id);
        }

        else{
            District::find($id)->delete();
            return deleted();
        }

    }
    public function single($id)
    {

        $model=District::query()
            ->select('*')
            ->where('id', $id)
            ->first();
        if($model!=null) {
            return response()->json($model);
        }
        else{
            return notFoundError($id);
        }
    }
}
