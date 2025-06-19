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
        $this->browse(function (Browser $browser) {
            $browser
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
    $this->browse(function (Browser $browser) {
        $browser
            ->visit('/chat')
            ->waitFor('@model-selector', 5)
            ->click('@model-selector')
            ->click('@model-google-gemini-pro-1-5')
            ->assertSee('Gemini Pro 1.5');  // vérifie que le nom du modèle affiche
    });
}}
