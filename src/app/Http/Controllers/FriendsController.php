<?php


namespace App\Http\Controllers;


use App\Models\FacebookFriend;
use App\Repositories\FacebookFriendRepository;
use Illuminate\Http\Request;

/**
 * Renders the friends page.
 */
class FriendsController extends Controller
{

    /**
     * @var \FacebookFriendRepository
     */
    private FacebookFriendRepository $facebook_friend_repository;

    public function __construct(FacebookFriendRepository $facebook_friend_repository)
    {
        $this->facebook_friend_repository = $facebook_friend_repository;
    }

    /**
     * Show the profile for a given user.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $path2 = [];
        $user_id = $request->get('user_id');
        $duration2 = 0;

        $error = '';

        if (($from != '' && !is_numeric($from)) || ($to != '' && !is_numeric($to))) {
            $error = "Invalid from or to entered";
        }

        if (!$error && $from && $to) {
            $start = microtime(true);
            $path2 = $this->facebook_friend_repository->findShortestPath2($from, $to);
            $duration2 = round(microtime(true) - $start, 2);
        }

        $all_friends = FacebookFriend::query()->paginate(10);

        $friends = $user_id ? $this->facebook_friend_repository->findByUserId($user_id) : $all_friends;

        return view(
            'friends',
            [
                'user_id' => $user_id,
                'from' => $from,
                'error'=> $error,
                'to' => $to,
                'duration2' => $duration2,
                'path2' => $path2,
                'friends' => $friends
            ]
        );
    }

}
