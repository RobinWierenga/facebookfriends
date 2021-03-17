<?php

namespace App\Repositories;

use App\Models\FacebookFriend;
use Illuminate\Pagination\LengthAwarePaginator;

class FacebookFriendRepository
{
    // the max depth of friends of friends you want to search for
    const MAX_DEPTH = 5;


    public function findByUserId(int $user_id): LengthAwarePaginator
    {
        return FacebookFriend::query()->where('user_id', $user_id)->paginate(10);
    }

    /**
     * Version 2. Still not using joins but subqueries which should be superior to joins. In this
     * example application it was a factor 100 times faster then the v1 version which retrieved the data.
     *
     * @param $from
     * @param $to
     * @return array
     */
    public function findShortestPath2($from, $to): array
    {
        if ($from == $to) {
            return [$from, $to];
        }

        $user = $this->findWhoKnows($from, $to);
        if (!$user) {
            return [];
        }

        $i = 0;
        $path = [$to, $user];

        /**
         * By executing the search in a loop we can backtrack how $from knows $to
         * This could be optimized by limiting the amount of where user_id in (...) subqueries by looking at the found
         * degree but i'm out of time for now :-)
         */
        while ($user != $from && $i < self::MAX_DEPTH) {
            $user = $this->findWhoKnows($from, $user);
            $path[] = $user;
            $i++;
        }

        return array_reverse($path);
    }

    /**
     * Returns the first person who knows $to which is somehow a connected friend of $to
     * @param int $from
     * @param int $to
     * @return int
     */
    private function findWhoKnows(int $from, int $to): int
    {
        $sql = $this->buildQuery($from, $to, self::MAX_DEPTH);

        $results = FacebookFriend::getConnectionResolver()->connection()->select($sql)[0];
        $results = json_decode(json_encode($results), true);

        for ($i = 1; $i <= self::MAX_DEPTH; $i++) {
            if (isset($results['level' . $i])) {
                return $results['level' . $i];
            }
        }
        return 0;
    }


    /**
     * Constructs something like this:
     *
     *         $sql = "select (select user_id from facebook_friends where friend_id = $to) as level1,
     *                 (select user_id from facebook_friends where user_id in (select friend_id from facebook_friends where user_id = $from)
     *                and friend_id = $to) as level2,
     *                (select user_id from facebook_friends where user_id in (select friend_id from facebook_friends where user_id in (select friend_id from facebook_friends where user_id = $from))
     *                and friend_id = $to) as level3,
     *                (select user_id from facebook_friends where user_id in (select friend_id from facebook_friends where user_id in (select friend_id from facebook_friends where user_id in (select friend_id from facebook_friends where user_id = 1$from))
     *                and friend_id = $to) as level4,
     *                (select user_id from facebook_friends where user_id in (select friend_id from facebook_friends where user_id in (select friend_id from facebook_friends where user_id in (select friend_id from facebook_friends where user_id in (select friend_id from facebook_friends where user_id = $from))))
     *                and friend_id = $to) as level5";
     *
     * @param int $from
     * @param int $to
     * @param int $level
     * @return string
     */
    private function buildQuery(int $from, int $to, int $level): string
    {
        $template_1 = "select user_id from facebook_friends where user_id in (";
        $template_2 = "select friend_id from facebook_friends where user_id";

        $sql = "select (select user_id from facebook_friends where user_id=$from and friend_id = $to) as level1";

        // completely unreadable.. so read the comment above
        if ($level > 1) {
            for ($i = 2; $i <= $level; $i++) {
                $sql .= ", (";
                for ($j = 1; $j <= $i; $j++) {
                    if ($j == 1) {
                        $sql .= $template_1;
                    } else {
                        if ($j == $i) {
                            $sql .= $template_2 . " = $from";
                            for ($k = 1; $k < $j; $k++) {
                                $sql .= ")";
                            }
                        } else {
                            $sql .= $template_2 . " in (";
                        }
                    }
                }
                $sql .= " and friend_id = $to) as level$i";
            }
        }

        return $sql;
    }


}
