<?php
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Film;
use App\Models\User;

class FilmTest extends TestCase
{

   public function setUp(): void
    {
        parent::setUp();

     // Create a user for testing
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
    
    }

    

    /** @test */
    public function it_can_search_films_by_keyword()
    {
      
        // Providing Keyword
        $keyword = 'zee';

        // Define query parameters
        $queryParams = [
            'keyword' => $keyword
        ];

        // Act as the authenticated user
         $response = $this->get('/api/film-search?keyword='.$keyword);
         $responseData = $response->json();
         //dd($responseData);
            if(isset($responseData['error'])){
                 $response->assertStatus(404);
                $this->fail('No Film Found');
               
              // $response->assertStatus(404, "Response is: " . $response->getContent());
        }
            else{
                $response->assertStatus(200)->assertJsonFragment([
                    'message' => 'Film Retrieved Successfully.'
                 ]);
            }
                    
    }
      
    
    /** @test */
    public function it_can_delete_a_film()
    {
        // Arrange - Create a film for deletion
        //$film = Film::factory()->create();
         $urlRandomValue = 'https://api.example.com/characters/'.rand();
        $apiRandomValue = rand();
        // Arrange
        $film = Film::factory()->create([
            'title' => 'Zeeshan Film',
            'characters' => 'Sample characters',
            'url' => $urlRandomValue,
            'api_id' => $apiRandomValue,
        ]);


        // Act - Make a DELETE request to delete the film
        $response = $this->post("/api/film/{$film->id}");

        // Assert - Check the response
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Film Deleted Successfully.',
                 ]);

        // Additional assertions if needed
        $this->assertDatabaseMissing('films', ['id' => $film->id]);
    }
    

    // Other test methods for authenticated API endpoints...
}
