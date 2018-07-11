<?php

namespace Tests\Browser;

use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class IndexTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function resource_index_can_be_viewed()
    {
        $this->seed();

        $users = User::find([1, 2, 3]);

        $this->browse(function (Browser $browser) use ($users) {
            $browser->loginAs(User::find(1))
                    ->visit(new Pages\UserIndex)
                    ->waitForUsers()
                    ->assertVisible('@users-1-row')
                    ->assertVisible('@users-2-row')
                    ->assertVisible('@users-3-row');
        });
    }

    /**
     * @test
     */
    public function resources_can_be_searched()
    {
        $this->seed();

        $this->browse(function (Browser $browser) {
            // Search For Single User By ID...
            $browser->loginAs(User::find(1))
                    ->visit(new Pages\UserIndex)
                    ->waitForUsers()
                    ->searchForUser('3')
                    ->assertMissing('@users-1-row')
                    ->assertMissing('@users-2-row')
                    ->assertVisible('@users-3-row');

            // Search For Single User By Name...
            $browser->loginAs(User::find(1))
                    ->visit(new Pages\UserIndex)
                    ->waitForUsers()
                    ->searchForUser('Taylor')
                    ->assertVisible('@users-1-row')
                    ->assertMissing('@users-2-row')
                    ->assertMissing('@users-3-row');
        });
    }
}
