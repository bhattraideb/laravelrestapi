<?php

namespace App\Http\Controllers;

use App\Meeting;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'meeting_id' => 'required',
            'user_id' => 'required'
        ]);

        $meeting_id = $request->input('meeting_id');
        $user_id = $request->input('user_id');

        $meeting = Meeting::findOrFail($meeting_id);
        $user = User::findOrFail($user_id);

        $message = [
            'msg' => 'User already registered for meeting',
            'meeting' => $meeting,
            'user' => $user,
            'unregister' => [
                'href' => 'api/v1/meeting/registration/'.$meeting_id,
                'method' => 'DELETE'
            ]
        ];

        if($meeting->users()->where('user.id', $user_id)->first()){
            return response()->json($message, 404);
        }

        $user->meetings()->attach($meeting);

        $response = [
            'msg' => 'User registered for meeting',
            'meeting' => $meeting,
            'user' => $user,
            'unregister' => [
                'href' => 'api/v1/meeting/registration/'.$meeting_id,
                'method' => 'DELETE'
            ]
        ];

        return response()->json($response, 201);
        //return 'It works fine!';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $meeting = Meeting::findOrFail($id);
        $meeting->users()->detach();

        $response = [
            'msg' => 'User unregistered for meeting',
            'meeting' => $meeting,
            //'user' => $user,
            'user' => 'tbd',
            'unregister' => [
                'href' => 'api/v1/meeting/registration/1',
                'method' => 'POST',
                'params' => 'user_id, meetin_id'
            ]
        ];

        /*$meeting = [
            'title' => 'Title',
            'description' => 'Description',
            'time' => 'Time',
            'user_id' => 'User Id',
            'view_meeting' => [
                'href' => 'api/v1/meeting/1',
                'method' => 'GET'
            ],
        ];

        $user = [
            'name' => 'Name'
        ];
        */

        return response()->json($response, 200);
    }
}
