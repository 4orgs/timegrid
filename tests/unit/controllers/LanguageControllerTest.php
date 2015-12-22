<?php


class LanguageControllerTest extends TestCase
{
    /**
     * Test switch language to English.
     *
     * @covers \App\Http\Controllers\LanguageController::switchLang
     * @test
     */
    public function it_switches_language_to_english_us()
    {
        $applocale = 'en_US.utf8';
        $this->call('GET', "/lang/$applocale");

        $this->assertSessionHas('language', 'en');
        $this->assertSessionHas('applocale', $applocale);
        $this->assertResponseStatus(302);
    }

    /**
     * Test switch language to Spanish Spain.
     *
     * @covers \App\Http\Controllers\LanguageController::switchLang
     * @test
     */
    public function it_switches_language_to_spanish_es()
    {
        $applocale = 'es_ES.utf8';
        $this->call('GET', "/lang/$applocale");

        $this->assertSessionHas('language', 'es');
        $this->assertSessionHas('applocale', $applocale);
        $this->assertResponseStatus(302);
    }
}
