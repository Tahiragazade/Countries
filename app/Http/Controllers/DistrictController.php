<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\District;
use Illuminate\Http\JsonResponse;
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
    public function index(Request $request): JsonResponse
    {
        paginate ($request, $limit, $offset);
        $districtQuery=District::query();

        if($request->has('name')) {
            $districtQuery->where('name', 'like', filter($request->get('name')));
        }
        if($request->has('parent_id')) {
            $districtQuery->where('parent_id', '=', $request->get('parent_id'));
        }

        $count = count($districtQuery->get());
        $districts = $districtQuery->limit($request->get('limit'))->offset($request->get('offset'))->get();

        foreach ($districts as $district){
            $city = City::query()->find($district->parent_id);
            $district->parent_id = $city->name;
        }
        return response()->json(['data' => $districts, 'total' => $count]);
    }
    public function tree(Request $request): JsonResponse
    {
        paginate($request, $limit, $offset);
        $districtQuery=District::query();

        if($request->has('name')) {
            $districtQuery->where('name', 'like', filter($request->get('name')));
        }
        if($request->has('parent_id')) {
            $districtQuery->where('parent_id', '=', $request->get('parent_id'));
        }

            $count = count($districtQuery->get());
            $districts = $districtQuery->limit($request->get('limit'))->offset($request->get('offset'))->get();

        $countries = Country::get();
        $cities = City::get();

        $data = treeForThreeTable( $countries, $cities, $districts);
        clearEmptyChildren($data);
        return response()->json(['data' => $data, 'total' => $count]);
    }
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'=>['required','string',Rule::unique('districts')->where(function ($query) use ($request) {
                return $query->where('name', $request->name)
                    ->where('parent_id', $request->parent_id);
            })],
            'parent_id'=>['required','integer',Rule::exists('cities','id')]
        ]);

        if ($validator->fails())
        {
            return validationError($validator->errors());
        }

        $model= new District($request->only([
            'name',
            'parent_id',
        ]));
        $model->save();

        return createSuccess($model);
    }

    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'=>['required','string',Rule::unique('districts')->where(function ($query) use ($request) {
                return $query->where('name', $request->name)
                    ->where('parent_id', $request->parent_id);
            })->ignore($request->id)],
            'parent_id'=>['required','integer',Rule::exists('cities','id')],

        ]);

        if ($validator->fails())
        {
            return validationError($validator->errors());
        }

        $model= District::find($request->id);
        $model->update($request->only([
            'name',
            'parent_id',
        ]));
        $model->save();

        return updateSuccess($model);
    }
    public function delete($id)
    {
        $district=checkIfExist('District','id', $id);
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

        $model=District::query()->find($id);
        if($model!=null) {
            return response()->json($model);
        }
        else{
            return notFoundError($id);
        }
    }
}
