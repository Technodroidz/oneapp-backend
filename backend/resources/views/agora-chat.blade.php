@extends('layouts.app')

@section('content')
    <agora-chat :allusers="{{ $users }}" authuserid="9" authuser="Vikash Rai"
        agora_id="{{ env('AGORA_APP_ID') }}" />
@endsection