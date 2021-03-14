<?php

namespace Database\Factories;

use App\Models\FacebookFriend;
use Illuminate\Database\Eloquent\Factories\Factory;

class FacebookFriendFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FacebookFriend::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        return [
            'user_id' => $user_id,
            'friend_id' => $friend_id,
            'friend_name' => self::$friends[$friend_id]
        ];
    }
}
