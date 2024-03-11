<?php
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Character;
use App\Models\User;

class CharacterTest extends TestCase
{

   public function setUp(): void
    {
        parent::setUp();

     // Create a user for testing
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
    
    }

    

    /** @test */
    public function it_can_search_characters_by_keyword()
    {
      
        // Providing Keyword
        $keyword = 'samsssple';

        // Define query parameters
        $queryParams = [
            'keyword' => $keyword
        ];

        // Act as the authenticated user
         $response = $this->get('/api/character-search?keyword='.$keyword);
         $responseData = $response->json();
         //dd($responseData);
            if(isset($responseData['error'])){
                 $response->assertStatus(404);
                $this->fail('No Characters Found');
               
              // $response->assertStatus(404, "Response is: " . $response->getContent());
        }
            else{
                $response->assertStatus(200)->assertJsonFragment([
                    'message' => 'Characters Retrieved Successfully.'
                 ]);
            }
                    
    }
      
    
    /** @test */
    public function it_can_delete_a_character()
    {
        
        $urlRandomValue = 'https://api.example.com/characters/'.rand();
        $apiRandomValue = rand();
        // Create a new character
        $character = Character::factory()->create([
            'name' => 'Sample Character'.rand(),
            'height' => '5.8',
            'hair_color' => 'blond',
            'skin_color' => 'brown',
            'eye_color' => 'brown',
            'gender' => 'male',
            'homeworld' => 'sample value',
            'species' => 'sample value',
            'starships' => 'sample value',
            'vehicles' => 'sample value',
            'films' => 'Sample Films',
            'url' => $urlRandomValue,
            'api_id' => $apiRandomValue,
        ]);


        // Delete request
        $response = $this->post("/api/character/{$character->id}");

        // Check the response
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Character Deleted Successfully.',
                 ]);

        // Additional assertions if needed
        $this->assertDatabaseMissing('characters', ['id' => $character->id]);
    }
    

    // Other test methods for authenticated API endpoints...
}
