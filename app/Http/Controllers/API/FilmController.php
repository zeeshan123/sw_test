<?php

namespace App\Http\Controllers\API;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Film;
use App\Models\FilmCharacter;
use Validator;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\Film as FilmResource;
use Illuminate\Support\Str;

class FilmController extends BaseController
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
        $minutes = 60;
        $films = Cache::remember('films', $minutes, function () {
           // return DB::table('users')->get();
            $films = Film::select('*');
            return $films->get();
        });
        
          return $this->sendResponse(FilmResource::collection($films), 'Films Retrieved Successfully.');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showById($id)
    {
        $film = Film::find($id);
  
        if (is_null($film)) {
            return $this->sendError('Film not found.');
        }
   
        return $this->sendResponse(new FilmResource($film), 'Film Retrieved Successfully.');
    }

    /**
     * Display a listing of the resource by Id.
     *
     * @return \Illuminate\Http\Response
     */
    public function searchFilm(Request $request)
    {
        try {
             $input = $request->all();

            $validator = Validator::make($input, [
                'keyword' => 'required'
            ]);

            if ($validator->fails()) {
                throw new \Exception('Validation Error.', $validator->errors());
            }

            $films = Film::select('*');

            if (isset($request->keyword) && !empty($request->keyword)) {
                $films->where('title', 'like', '%' . trim($request->keyword) . '%');
            }
           
            if (!$films->count()) {
              //  dd(12345);
                return response()->json(['error' => 'No films found for the provided keyword.'], 404);
            }

            return $this->sendResponse(FilmResource::collection($films->get()), 'Film Retrieved Successfully.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }


     /**
     * Update the specified resource in storage.
     *  
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateFilm(Request $request)
    {
       
        try {
            $input = $request->all();

            $validator = Validator::make($input, [
                'id' => 'required',
                'title' => 'required',
                'url' => 'required'
            ]);

            if ($validator->fails()) {
                throw new \Exception('Validation Error.', $validator->errors());
            }

            $film = Film::find($request->id);

            if (!$film) {
                throw new \Exception('Film not found.');
            }

            $film->title = $input['title'];
            $film->url = $input['url'];
            
            // Get api_id by url basename
            $film->api_id = Str::of(basename($input['url']));
            
            $film->save();

            return $this->sendResponse(new FilmResource($film), 'Film Updated Successfully.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode());
        }

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
        $film = Film::find($id);

        if (!$film) {
            throw new \Exception('Film not found.');
        }

        $film->delete();

        // Delete all related characters for this film
        FilmCharacter::where('film_id', $id)->delete();

        return $this->sendResponse([], 'Film Deleted Successfully.');

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], $e->getCode());
        }
    }
}