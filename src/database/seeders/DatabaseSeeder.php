<?php

namespace Database\Seeders;

use App\Models\FacebookFriend;
use Faker\Provider\Person;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    private $last_user_id = 1;

    const MAX_DEPTH = 4;

    const MAX_FRIENDS = 10;

    private $count = 0;


    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        FacebookFriend::query()->truncate();

        for ($i = 0; $i < self::MAX_FRIENDS; $i++) {
            $this->createFriend(1,1);
        }
        print "created: $this->count\n";
    }


    private function createFriend($my_user_id, int $depth)
    {
        if ($depth > SELF::MAX_DEPTH) {
            return;
        }
        $new_friend_name = (rand(0, 1) ? Person::firstNameMale() : Person::firstNameFemale()) . '-' . substr(md5(uniqid('', true))
            ,0,8);

        $facebook_friend = new FacebookFriend();
        $facebook_friend->user_id = $my_user_id;
        $facebook_friend->friend_id = ++$this->last_user_id;
        $facebook_friend->friend_name = $new_friend_name;
        $facebook_friend->save();
        $this->count++;


        // he has 10 more friends
        for ($i = 0; $i < self::MAX_FRIENDS; $i++) {
            $this->createFriend($facebook_friend->friend_id, $depth+1);
        }
    }
}
