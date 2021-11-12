<?php

namespace App\Http\Controllers\PublicControllers\Rooms;

use App\Helpers\bbbHelpers;
use App\Http\Controllers\Controller;
use App\Room;
use BigBlueButton\BigBlueButton;
use BigBlueButton\Parameters\GetMeetingInfoParameters;
use Illuminate\Http\Request;
use BigBlueButton\Parameters\IsMeetingRunningParameters;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AttendeesRoomController extends Controller
{
    //
    public function join(Request $request)
    {

        try{
            $validator = Validator::make($request->all(),[
                'name' =>'required'
            ],[
                'name.required' =>'Name Required'
            ]);

            if ($validator->fails())
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }


            $room = Room::where('url',decrypt($request->input('room')))->firstOrFail();
            $credentials = bbbHelpers::setCredentials();

            if (!$credentials)
            {
                return redirect(\Illuminate\Support\Facades\URL::to('settings'))->with(['danger'=>'Please Enter Settings']);
            }
            $bbb = new BigBlueButton($credentials['base_url'],$credentials['secret']);
            $getMeetingInfoParams = new GetMeetingInfoParameters(decrypt($request->input('room')),decrypt($room->attendee_password));
            $participant = $bbb->getMeetingInfo($getMeetingInfoParams);


            $ismeetingRunningParams =  new IsMeetingRunningParameters(decrypt($request->input('room')));
            $response =$bbb->isMeetingRunning($ismeetingRunningParams);

            if ($response->getRawXml()->running == 'false')
            {

                return response()->json(['notStart'=>true]);

            }
            else{

                if ($room->maximum_people > $participant->getRawXml()->participantCount )
                {
                    $joinMeetingParams = [

                        'meetingId' => decrypt($request->input('room')),
                        'username'  => $request->input('name'),
                        'password'  => decrypt($room->attendee_password)
                    ];

                    $url = bbbHelpers::joinMeeting($joinMeetingParams);
                    return response()->json(['url'=>$url]);
                }
                else{

                    return response()->json(['full'=>true]);

                }


            }

        }catch (\Exception $exception)
        {
            return redirect()->json(['danger'=>$exception->getMessage()]);
        }


    }

    public function authAttendeeJoin(Request $request)
    {

        try{
            $room = Room::where('url',$request->meeting)->firstOrFail();

            $credentials = bbbHelpers::setCredentials();

            if (!$credentials)
            {
                return redirect(\Illuminate\Support\Facades\URL::to('settings'))->with(['danger'=>'Please Enter Settings']);
            }
            $bbb = new BigBlueButton($credentials['base_url'],$credentials['secret']);

            $getMeetingInfoParams = new GetMeetingInfoParameters($request->meeting,decrypt($room->attendee_password));
            $participant = $bbb->getMeetingInfo($getMeetingInfoParams);

            $ismeetingRunningParams =  new IsMeetingRunningParameters($request->meeting);
            $response =$bbb->isMeetingRunning($ismeetingRunningParams);

            if ($response->getRawXml()->running == 'false')
            {

                return response()->json(['notStart'=>true]);
            }
            else{

                if ($room->maximum_people > $participant->getRawXml()->participantCount )
                {
                    $joinMeetingParams = [
                        'meetingId' => $request->meeting,
                        'username'  => Auth::user()->username,
                        'password'  => decrypt($room->attendee_password)
                    ];
                    $url = bbbHelpers::joinMeeting($joinMeetingParams);
                    return response()->json(['url'=>$url]);
                }
                else{

                    return response()->json(['full'=>true]);
                }



            }


        }catch (\Exception $exception)
        {
            return redirect()->json(['danger'=>$exception->getMessage()]);
        }

    }
}
