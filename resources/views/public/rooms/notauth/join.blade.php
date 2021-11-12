@extends('public.layouts.app')
@section('pagename', 'BigBlueButton')
@section('css')

    <link rel="stylesheet" href="{{asset('css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/front.css?var=time()')}}">
@stop
@section('content')
    <div id="data">


    <div class="container-fluid mt-4">
        <div class="row bg-light pt-5 pl-4">
            <div class="col-md-12" style="padding-left: 12%;">
                <h6 class="text-left"> You have been invited to join</h6>
                <h2 class="text-left mb-3 ">
                    {{ $pageName }}'s Meeting
                </h2>
                <hr class="mt-2 float-left w-25">
            </div>
        </div>
        <div class="row bg-light pt-3">
            <div class="col-md-5"  style="padding-left: 13%;">
                <span class="avatar mb-1" >{{strtoupper(substr($room->user->username,0,1))}}</span>
                <h5 class="font-weight-normal ml-2" style="display: inline-block">{{ucwords($room->user->username)}} (Owner) </h5>
            </div>
            <div class="col-md-6 mt-2" >
                <form  action="{{route('attendeeJoin')}}"  method="Post" id="frm">
                    @csrf
                    <input type="hidden" value="{{encrypt($room->url)}}" name="room">
                    <div class="input-group">
                        <input   class="form-control join-form h-25" placeholder="Enter your name!" value="" autofocus="autofocus" type="text" name="name">
                        <span class="input-group-append">
                            <button class="btn btn-primary btn-sm px-5  join-form" type="submit">
                               Join
                            </button>
                        </span>
                    </div>
                </form>
                <div id="errorDiv">
                    <span class="has-error text-danger float-left mb-3" id="error"></span>
                </div>
                <br><br><br><br>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row mt-2">
            <div class="col-md-6">
                <h5>Public Rooms Recordings</h5>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="table-responsive">
                <div class="col-md-12">
                    @if (count($roomsRecordingList) > 0)

                        <table class="table  table-hover" id="InvitedMeetingTable" >
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Playback</th>
                                <th>Published</th>
                                <th>Length</th>
                                <th>Users</th>
                                <th>Format</th>
                                <th>Started</th>
                                <th>Ended</th>


                            </tr>
                            </thead>
                            <tbody>

                            @foreach($roomsRecordingList as  $list)

                                <tr>
                                    <td>{{$list->name}}</td>
                                    <td><a class="btn btn-sm btn-info" href="{{$list->playback->format->url}}">Watch</a></td>
                                    <td>{{ucwords($list->state)}}</td>
                                    <td>{{\App\Helpers\Helper::formatBytes($list->rawSize)}}</td>
                                    <td class="text-center">{{$list->participants}}</td>
                                    <td>{{ucwords($list->playback->format->type)}}</td>
                                    @foreach (\App\Room::where('url',$list->metadata->meetingId)->get() as $meeting)
                                        <td>{{\Carbon\Carbon::parse($meeting->start_date)->format('M d,yy g:i A')}}</td>
                                        <td>{{\Carbon\Carbon::parse($meeting->end_date)->format('M d,yy g:i A')}}</td>
                                    @endforeach
                                </tr>

                            @endforeach

                            @else
                                No Recording Found.
                            @endif

                            </tbody>
                        </table>

                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
@section('script')
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" src={{asset('js/jquery.dataTables.min.js')}}></script>

    <!-- Data Bootstarp -->
    <script type="text/javascript" src={{asset('js/dataTables.bootstrap4.min.js')}}></script>
    <script>
        var join ='{{route("attendeeJoin")}}';
    </script>
    <script src="{{asset('js/front/meetings/bbb-front.js')}}"></script>

@stop
