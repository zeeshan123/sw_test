<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Character;
use App\Models\FilmCharacter;
use Validator;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\Character as CharacterResource;
use Illuminate\Support\Str;

class CharacterController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Caching response for 60 secs
        $seconds = 60;
        $characters = Cache::remember('characters', $seconds, function () {
             $characters = Character::select('*');
            return $characters->get();

        });

          return $this->sendResponse(CharacterResource::collection($characters), 'Characters Retrieved Successfully.');

    }

    /**
     * Display a listing of the resource by Id.
     *
     * @return \Illuminate\Http\Response
     */
    public function searchCharacter(Request $request)
    {
         //dd(1234);
        $characters = Character::select('*');
        if(isset($request->keyword) && !empty($request->keyword)){
          $characters->where('name', 'like', '%' . trim($request->keyword) . '%');
        }

        //dd($characters);

        if (!$characters->count()) {
              //  dd(12345);
                return response()->json(['error' => 'No Character found for the provided keyword.'], 404);
            }

            return $this->sendResponse(CharacterResource::collection($characters->get()), 'Characters Retrieved Successfully.');
    }

     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateCharacter(Request $request)
    {
    
        $input = $request->all();
       // dd($input);
        $validator = Validator::make($input, [
            'id' => 'required',
            'name' => 'required',
            'url' => 'required',
            'gender' => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $character = Character::find($request->id);
        $character->name = $input['name'];
        $character->height = $input['height'];
        $character->mass = $input['mass'];
        $character->hair_color = $input['hair_color'];
        $character->skin_color = $input['skin_color'];
        $character->gender = $input['gender'];
        $character->homeworld = $input['homeworld'];
        $character->url = $input['url'];

        //get api_id by url basename
        $character->api_id = Str::of(basename($input['url']));
        $character->save();
   
        return $this->sendResponse(new CharacterResource($character), 'Character Updated Successfully.');
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteById($id)
    {
    
        try {
        $character = Character::find($id);

        if (!$character) {
            throw new \Exception('Character not found.');
        }

        $character->delete();

        // Delete all related characters for this Character
        FilmCharacter::where('character_id', $id)->delete();

        return $this->sendResponse([], 'Character Deleted Successfully.');

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], $e->getCode());
        }
    }
}