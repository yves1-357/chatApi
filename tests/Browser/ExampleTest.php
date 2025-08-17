<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    /**
     * test "chat".
     */
    public function testBasicExample(): void
    {
        $user = \App\Models\User::factory()->create();

        $this->browse(function (Browser $browser) use ($user){
            $browser
            ->loginAs($user)
            ->visit('/chat')
            ->waitForText('Stella', 10)
            ->assertSee('Stella')

            ->click('@new-chat-button')
            ->waitFor('@chat-input', 5)
            ->assertVisible('@chat-input');

        });
    }


    /**
     * test "selection-modeles".
     */

    public function testModelSelector()
{
    $user = \App\Models\User::factory()->create();
    $this->browse(function (Browser $browser)  use ($user){
        $browser
        ->loginAs($user)
            ->visit('/chat')
            ->waitFor('@model-selector', 5)
            ->click('@model-selector')
            ->click('@model-google-gemini-pro-1-5')
            ->assertSee('Gemini Pro 1.5');  // vérifie que le nom du modèle affiche
    });
}


/**
     * Nouvelle conv + vide et demarre une autre ".

     */

public function testNewChatCreatesConversation()
{
    $user = \App\Models\User::factory()->create();

    $this->browse(function (Browser $browser) use ($user){
        $browser
        ->loginAs($user)
        ->visit('/chat')
                ->waitFor('@chat-input')
                ->type('@chat-input', 'Test')
                ->press('@send-button')
                ->waitForText('Test')
                ->click('@new-chat-button')
                ->waitFor('@chat-input')
                ->assertDontSee('Test');
    });
}



/**
     * user sauvegarde instruction ".

     */

public function testCustomInstructionSave()
{
    $user = \App\Models\User::factory()->create();

    $this->browse(function (Browser $browser) use ($user){
        $browser
        ->loginAs($user)
        ->visit('/chat')
                ->waitFor('@open-instructions-modal')
                ->click('@open-instructions-modal')
                ->waitFor('@save-instructions')
                ->type('@custom-instructions-input', 'Sois bref et utilise des emojis')
                ->click('@save-instructions');

    });
}

/**
     * user supprime instruction ".

     */

public function testDeleteCustomInstruction()
{
    $user = \App\Models\User::factory()->create();
    $this->browse(function (Browser $browser) use ($user){
        $browser
        ->loginAs($user)
        ->visit('/chat')
                ->waitFor('@open-instructions-modal')
                ->click('@open-instructions-modal')
                ->waitFor('@custom-instructions-input')
                ->type('@custom-instructions-input', 'Test Dusk')
                ->waitFor('@delete-instructions')
                ->click('@delete-instructions');

    });
}

/*
user inscription

*/

public function testUserCanRegister()
{
    $this->browse(function (Browser $browser) {
        $browser->visit('/register')
                ->type('@register-name', 'Test')
                ->type('@register-email', 'ZeusZola@hotmail.be')
                ->type('@register-password', 'ZeoB23you')
                ->press('@register-submit')
                ->waitForLocation('/login')
                ->assertPathIs('/login');

    });
}

/*
user connexion

*/

public function testUserCanLogin()
{
    $this->browse(function (Browser $browser) {
        $browser->visit('/login')
                ->type('@login-email', 'ZeusZola@hotmail.be')
                ->type('@login-password', 'ZeoB23you')
                ->press('@login-submit')
                ->waitForLocation('/chat')
                ->assertPathIs('/chat')
                ->assertAuthenticated(); // Vérification importante

    });
}

}
