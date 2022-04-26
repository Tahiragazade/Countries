<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CountryController extends Controller
{
//    public function __construct()
//    {
//        $this->middleware('auth');
//    }

    public function index(Request $request): JsonResponse
    {
        paginate($request, $limit, $offset);
        $countryQuery = Country::query();

        if($request->has('name')) {
            $countryQuery->where('name', 'like', filter($request->get('name')));
        }

        $count = $countryQuery->count();
        $countries = $countryQuery->limit($limit)->offset($offset)->get();


        return response()->json(['data' => $countries, 'total' => $count]);
    }

    public function tree(Request $request): JsonResponse
    {
        paginate($request, $limit, $offset);
        $countryQuery = Country::query();

        if($request->has('name')) {
            $countryQuery->where('name', 'like', filter($request->get('name')));
        }

        $count = $countryQuery->count();
        $countries = $countryQuery->limit($limit)->offset($offset)->get();

        $data = simpleTree($countries);
        return response()->json(['data' => $data, 'total' => $count]);
    }
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required','string','unique:countries'],
            //'parent_id'=>['integer']
        ]);

        if ($validator->fails())
        {
            return validationError($validator->errors());
        }

        $model= new Country($request->only([
            'name',
        ]));
        $model->save();

        return createSuccess($model);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', Rule::unique('countries')->ignore($request->id)],

        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        $model = Country::query()->find($request->id);
        $model->update($request->only([
            'name'
        ]));
        $model->save();

        return updateSuccess($model);
    }

    public function single($id)
    {
        $model= Country::query()->find($id);

        if (!$model) {
            return notFoundError($id);
        }

        return response()->json($model);
    }
    public function delete($id){
        $city=checkIfExist('City','parent_id',$id);
        $country=checkIfExist('Country','id',$id);
        if($country==0){
            return notFoundError($id);
        }
        if($city==1){
            return notDeleteError();
        }

        else{
            Country::find($id)->delete();
            return deleted();
        }
    }
}
