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

        $response = Http::get('https://swapi.py4e.com/api/people/');

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
        try {
            // Fetch data from SWAPI for Films
            $response = Http::get('https://swapi.py4e.com/api/films');
            $data = $response->json();

            // Store data in the database for Films
            foreach ($data['results'] as $item) {
                Film::updateOrCreate([
                    'api_id' => Str::of(basename($item['url'])),
                    'title' => $item['title'],
                    'characters' => json_encode($item['characters']),
                    'url' => $item['url'],
                    'created_at' => Carbon::parse($item['created']),
                    'updated_at' => Carbon::parse($item['edited']),
                ]);
            }

            // Fetch data from SWAPI for People
            $response = Http::get('https://swapi.py4e.com/api/people');
            $data = $response->json();

            // Store data in the database for People (Characters)
            foreach ($data['results'] as $item) {
                $character = Character::updateOrCreate([
                    'name' => $item['name'],
                    'api_id' => Str::of(basename($item['url'])),
                    // ... (other fields)
                ]);

                // Make Association of Characters and Films
                foreach ($item['films'] as $filmUrl) {
                    $filmId = basename($filmUrl);
                    $filmCharacter = new FilmCharacter();
                    $filmCharacter->character_id = $character->id;
                    $filmCharacter->film_id = $filmId;
                    $filmCharacter->save();
                }

                $this->info('SWAPI data fetched and stored successfully.');
            }
        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
        }
    
    }
}
