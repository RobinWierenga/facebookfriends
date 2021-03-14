<?php

namespace App\Repositories;

use App\Models\FacebookFriend;
use Illuminate\Pagination\LengthAwarePaginator;

class FacebookFriendRepository
{
    // the max amount of friends of friends you want to search for
    const MAX_DEPTH = 4;

    private $all_friends = [];

    private $last_index = 0;

    public function findByUserId(int $user_id): LengthAwarePaginator
    {
        return FacebookFriend::query()->where('user_id', $user_id)->paginate(10);
    }

    /*
    * Complexity
    * nr of friends ^ depth
    * So with 10 friends each it's 10*10*10*10*10 we have to search 100.000 members max.
    *
    * Assumption: each friend has 10 new friends, off course in real friendships a group knows each other.
    * The goal is to load as least data as possible without using joins, assumption query by index is fast so we might break it up into a couple of queries
    *
    * We will search breath first.
    *
    */
    public function findShortestPath($from_user_id, $to_user_id): array
    {
        $path[] = $from_user_id;

        // nice try
        if ($from_user_id == $to_user_id) {
            return $path;
        }

        // first level
        $found = $this->searchForFriend($from_user_id, $to_user_id);

        if ($found) {
            $path[] = $to_user_id;
            return $path;
        }

        for ($i = 0; $i < self::MAX_DEPTH; $i++) {
            // continue the search at the last index, this way it acts as a queue
            $array_slice = array_slice(array_keys($this->all_friends), $this->last_index);

            $new_index = count($this->all_friends);

            // we dont need value but this is a convenient way to get just the keys
            foreach ($array_slice as $friend) {
                $found = $this->searchForFriend($friend, $to_user_id);
                if ($found) {
                    return $this->reconstructPath($from_user_id, $to_user_id);
                }
            }
            $this->last_index = $new_index;
        }

        return [];
    }

    /**
     * Retrieves all friends and adds them to the all_friends array excluding
     * duplicates.
     *
     * If the friend_id is found the process is halted and true is returned.
     *
     * @param $user_id the user for which you want to retrieve its friends
     * @param $friend_id the friend you are looking for
     * @return bool true if found, false otherwise
     */
    private function searchForFriend($user_id, $friend_id): bool
    {
        $friends = FacebookFriend::query()
            ->where('user_id', $user_id)->get(['friend_id'])->toArray();

        foreach ($friends as $friend) {
            if (!array_key_exists($friend['friend_id'], $this->all_friends)) {
                $this->all_friends[$friend['friend_id']] = $user_id;
            }
            if ($friend['friend_id'] == $friend_id) {
                return true;
            }
        }
        return false;
    }

    /**
     * Reconstructs the path via which the from user id knows the friend.
     *
     * @param int $from_user_id the source
     * @param int $friend_id the friend to look for
     * @return array an array of user_id's which lead up to the friend_id
     */
    private function reconstructPath(int $from_user_id, int $friend_id)
    {
        $source = $this->all_friends[$friend_id];
        $path[] = $friend_id;
        $path[] = $source;
        while ($source != $from_user_id) {
            $source = $this->all_friends[$source];
            $path[] = $source;
        }
        $path = array_reverse($path);
        return $path;
    }

}
