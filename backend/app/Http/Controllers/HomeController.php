<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Socialite;
use Barryvanveen\Lastfm\Lastfm;
use GuzzleHttp\Client;

class HomeController extends Controller
{
    protected $google;

    public $config = [
        'client_id' => '533569723196-v0u28pbupm1qgr13c7d3kfa3p62ta3bo.apps.googleusercontent.com',
        'client_secret' => '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',
        'redirect' => 'http://localhost:8000/api/googlecallback',
    ];

    public function getAllUsers(Request $request)
    {
        return response()->json(User::latest()->get());
    }

    public function redirectToGoogle()
    {
        $permissions = ['email','profile'];
        $user =  Socialite::driver('google',$this->config)->stateless()->scopes($permissions);
       // print_r($user->redirect());die;
        return $user->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
      
            $user = Socialite::driver('google')->user();
           // print_r($user);die;
            $access_token = $user->token;
            //return $access_token;
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://youtube.googleapis.com/youtube/v3/subscriptions?key=AIzaSyC1kSPVQ9UjP6hu7IcGXtdRy72hV1d-s6c');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
    
            $headers = array();
           // $headers[] = 'Authorization: Bearer [AIzaSyC1kSPVQ9UjP6hu7IcGXtdRy72hV1d-s6c]';
            $headers[] = 'Authorization: Bearer ['.$access_token.']';
            $headers[] = 'Accept: application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
            echo $result;
      
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }


    public function fmchannels(Request $request)
    {
      
        $lastfm = new Lastfm(new Client(), '2194097be55ffce443859bc77914f011');
        $albums = $lastfm->userTopAlbums('amosjackson')->limit(10)->get();
        // $tracks = $lastfm->userWeeklyTopTracks('Oneapptech', new \DateTime('2022-07-03'));
        // $albumss = $lastfm->userWeeklyTopAlbums('Oneapptech', new \DateTime('2022-07-03'));  
        // $artists = $lastfm->userWeeklyTopArtists('Oneapptech', new \DateTime('2022-07-03'));
        
        foreach($albums as $val){
           $data[] = [
             'album' => $val['name'],
             'artist' => $val['artist']['name'],
             'image' => $val['image'][3]['#text'],
             'album_url' => $val['url']

           ];  
           
        }
      //  print_r($albums);die;   
       return response()->json($data);          
    }
}
