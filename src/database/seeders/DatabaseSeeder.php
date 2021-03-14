<?php

namespace Database\Seeders;

use App\Models\FacebookFriend;
use Faker\Provider\Person;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    private $last_user_id = 1;

    const MAX_DEPTH = 5;

    const MAX_FRIENDS = 10;

    private $count = 0;

    private $friends = [];


    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        FacebookFriend::query()->truncate();

        for ($i = 0; $i < self::MAX_FRIENDS; $i++) {
            $this->createFriend(1, 1);
            print "Writing...\n";
        }

        if (count($this->friends) > 0) {
            DB::table('facebook_friends')->insert($this->friends);
            $this->friends = [];
        }
        print "Created: $this->count friends\n";
    }


    private function createFriend($my_user_id, int $depth)
    {
        if (count($this->friends) > 1000) {
            DB::table('facebook_friends')->insert($this->friends);
            $this->friends = [];
        }

        if ($depth > self::MAX_DEPTH) {
            return;
        }

        $new_friend_name = (rand(0, 1) ? Person::firstNameMale() : Person::firstNameFemale()) . '-' . substr(
                md5(uniqid('', true))
                ,
                0,
                8
            );

        $new_friend_id = ++$this->last_user_id;
        $this->friends[] = ['user_id' => $my_user_id, 'friend_id' => $new_friend_id, 'friend_name' => $new_friend_name];
        $this->count++;

        // he has 10 more friends
        for ($i = 0; $i < self::MAX_FRIENDS; $i++) {
            $this->createFriend($new_friend_id, $depth + 1);
        }
    }
}
