<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Support\Facades\Http;
use App\Models\Character; 
use App\Models\Film;
use App\Models\FilmCharacter; 
use Illuminate\Support\Str;
class FetchSwData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetchswdata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function fetchPeople()
    {
        $response = Http::get('https://swapi.dev/api/people');

        if ($response->successful()) {
            $data = $response->json();
            return response()->json($data);
        } else {
            return response()->json(['error' => 'Failed to fetch data from SWAPI'], $response->status());
        }
    }

    public static function transformYear(string $born) : int|float
    {
        if (Str::contains($born, 'BBY')) {
            $born = Str::replace('BBY', '', $born);
            $born = -abs($born);
        }
        if (Str::contains($born, 'ABY')) {
            $born = Str::replace('ABY', '', $born);
        }
        return $born;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
   public function handle()
    {

        $response = Http::get('https://swapi.dev/api/films');
        $data = $response->json();
        foreach ($data['results'] as $item) {
          Film::updateOrCreate(
            [
                'api_id' => Str::of(basename($item['url'])),
                'title' => $item['title'],
                'characters' => json_encode($item['characters']),
                'url' => $item['url'],
                'created_at' => Carbon::parse($item['created']),
                'updated_at' => Carbon::parse($item['edited']),
            ]);
      }

        // Fetch data from SWAPI
        $response = Http::get('https://swapi.dev/api/people?page=2');
        $data = $response->json();
//dd($item['hair_color']);
        // Store data in your database
        foreach ($data['results'] as $item) {

          $character =   Character::updateOrCreate(
            [
                'name' => $item['name'],
                'api_id' => Str::of(basename($item['url'])),
                'height' => is_numeric($item['height']) ? $item['height'] : null,
                'mass' => is_numeric($item['mass']) ? $item['mass'] : null,
                'born' => $item['birth_year'] != 'unknown' ? self::transformYear($item['birth_year']) : null,
                'hair_color' => $item['hair_color'] ? $item['hair_color'] : null,
                'skin_color' => $item['skin_color'],
                'eye_color' => $item['eye_color'],
                'gender' => $item['gender'],
                'homeworld' => $item['homeworld'],
                'films' => json_encode($item['films']),
                'species' => json_encode($item['species']),
                'starships' => json_encode($item['starships']),
                'vehicles' => json_encode($item['vehicles']),
                'created' => Carbon::parse($item['created']),
                'edited' => Carbon::parse($item['edited']),
                'url' => $item['url']
            ],
                // Add other fields as needed
            );

            // Make Association of Characters and Films
           //dd($character->id);
             foreach ($item['films'] as $item) {
                $filmCharacter = new FilmCharacter();
                $filmCharacter->character_id = $character->id;
                $filmCharacter->film_id = basename($item);
                $filmCharacter->save();       
            }

            $this->info('SWAPI data fetched and stored successfully.');
        }
    }
}
