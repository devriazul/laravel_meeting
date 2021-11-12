@extends('public.layouts.app')
@section('pagename', 'BigBlueButton')
@section('css')
    <link rel="stylesheet" href="{{asset('css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/front.css?var=time()')}}">
@stop

@section('content')
  <div id="data">
      @include('includes.meetingJoinForm')
  </div>


@stop
@section('script')
    <script type="text/javascript" src={{asset('js/jquery.dataTables.min.js')}}></script>
    <!-- Data Bootstarp -->
    <script type="text/javascript" src={{asset('js/dataTables.bootstrap4.min.js')}}></script>
    <script>
        var join = '{{route("meetingAttendeesJoin")}}';
    </script>
    <script src="{{asset('js/front/meetings/bbb-front.js')}}">

    </script>
@stop
