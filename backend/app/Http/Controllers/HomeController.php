<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Socialite;
use Barryvanveen\Lastfm\Lastfm;
use Alaouy\Youtube\Facades\Youtube;
use GuzzleHttp\Client;

class HomeController extends Controller
{
   
    public function getAllUsers(Request $request)
    {
        return response()->json(User::latest()->get());
    }


    public function fmchannels(Request $request)
    {
      
        $lastfm = new Lastfm(new Client(), '2194097be55ffce443859bc77914f011');
        $albums = $lastfm->userTopAlbums('amosjackson')->limit(10)->get();
       
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

    public function youtubeChannels(Request $request)
    {
       // $videoList = Youtube::getPopularVideos('us');
       // $channel = Youtube::getChannelByName('bbcnews');
      // channel id of bbc news = UC16niRr50-MSBwiO3YDb3RA
      // channel id of cnbc news = UCQIycDaLsBpMKjOCeaKUYVg
      // channel id of cnn news = UCupvZG-5ko_eiXAupbDfxWw
      // channel id of nbc news = UCeY0bbntWzzVIaj2z3QigXg
      // channel id of dw news = UCnZNNo1svw5R-xUMYqSi64A
       $channel = Youtube::getChannelById('UC16niRr50-MSBwiO3YDb3RA');
       $playlists = Youtube::getPlaylistsByChannelId('UC16niRr50-MSBwiO3YDb3RA');
       foreach($playlists['results'] as $value){
        $data[] = [
            'channeltitle' => $value->snippet->channelTitle,
            'newstitle' => $value->snippet->title,
            'image' => $value->snippet->thumbnails->high->url,
            'playlistid' => $value->id
        ];
       }
      // print_r($data);die;
       return response()->json($data); 
    }
}
