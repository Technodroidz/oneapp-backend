<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Channel;
use App\Models\Fmchannel;
use Socialite;
use Barryvanveen\Lastfm\Lastfm;
use Alaouy\Youtube\Facades\Youtube;
use GuzzleHttp\Client;
use App\Classes\AgoraDynamicKey\RtcTokenBuilder;
use App\Events\MakeAgoraCall;

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
        // $tracks = $lastfm->userRecentTracks('amosjackson')->get();
        // print_r($tracks);die;
       
        foreach($albums as $val){
           $data = [
             'album' => $val['name'],
             'artist' => $val['artist']['name'],
             'image' => $val['image'][3]['#text'],
             'album_url' => $val['url']

           ]; 
            $res[] = Fmchannel::create($data);
         //  $data[] = $data;
        }
        // print_r($data);die;
      //  $res = Fmchannel::create($data);
      //  print_r($albums);die; 
        $result = Fmchannel::orderBy('id', 'DESC')->limit(10)->get();
      foreach($result as $vals){
          $datas[] = [
             'id' => $vals['id'],
             'album' => $vals['album'],
             'artist' => $vals['artist'],
             'image' => $vals['image'],
             'album_url' => $vals['album_url']

           ];  
      }
       return response()->json($datas);          
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
        //print_r($channel);die;
    //   foreach($playlists['results'] as $value){
    //     $data[] = [
    //         'channeltitle' => $value->snippet->channelTitle,
    //         'newstitle' => $value->snippet->title,
    //         'image' => $value->snippet->thumbnails->high->url,
    //         'playlistid' => $value->id
    //     ];
    //   }
       
        $API_Key    = 'AIzaSyC1kSPVQ9UjP6hu7IcGXtdRy72hV1d-s6c'; 
        $Channel_ID = 'UC16niRr50-MSBwiO3YDb3RA'; 
        $Max_Results = 10; 
         
        // Get videos from channel by YouTube Data API 
        $apiData = @file_get_contents('https://www.googleapis.com/youtube/v3/search?order=date&part=snippet&channelId='.$Channel_ID.'&maxResults='.$Max_Results.'&key='.$API_Key.''); 
        $videoList = json_decode($apiData); 
        foreach($videoList->items as $val){
           // print_r($val);
             $data[] = [
                   'videoid' => $val->id->videoId,
                   'title' => $val->snippet->title,
                   'description' => $val->snippet->description,
                   'image' => $val->snippet->thumbnails->high->url,
                 ];
        }//die;
    //     foreach($videoList->items as $item){ 
    //     // Embed video 
    //     if(isset($item->id->videoId)){ 
    //         echo ' 
    //         <div class="yvideo-box"> 
    //             <iframe width="280" height="150" src="https://www.youtube.com/embed/'.$item->id->videoId.'" frameborder="0" allowfullscreen></iframe> 
    //             <h4>'. $item->snippet->title .'</h4> 
    //         </div>'; 
    //     } 
    // }die; 
      // print_r($data);die;
       return response()->json($data); 
    }

    public function index(Request $request,$id)
    {
        // fetch all users apart from the authenticated user
        $users = User::where('id', '<>', $id)->get();
        return response()->json($users);
    }
    
    public function fetchFmChannels(Request $request,$id)
    {
       // print_r($request->id);
       // $id = $request->id;
       $fmchannel = Fmchannel::where('id', $id)->get();
      // print_r($fmchannel[0]['album_url']);die;
      $albumurl = $fmchannel[0]->album_url;
        return response()->json($albumurl);  
    }

    public function getUserDetails(Request $request,$id)
    {
        $users = User::where('id', $id)->get();
        return response()->json($users);
    }

    public function token(Request $request)
    {

        $appID = "f31feed969ef438b8f501a27c3b73ce6";
        $appCertificate = "c3b003ab79c043f5b025535c37446de1";
        $channelName = $request->channelName;
        $user = $request->username;
        $role = RtcTokenBuilder::RoleAttendee;
       // print_r($role);die;
        $expireTimeInSeconds = 3600;
        $currentTimestamp = now()->getTimestamp();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

        $token = RtcTokenBuilder::buildTokenWithUserAccount($appID, $appCertificate, $channelName, $user, $role, $privilegeExpiredTs);

        return response()->json($token);
    }
    
    public function saveChannel(Request $request)
    {
        $data = [
              'channelname' => $request->channelName,
              'token' => $request->token,
              'personcallingid' => $request->callerid,
              'personcalledid' => $request->calleeid
            ];
            
        $res = Channel::create($data);  
         return response()->json($res);
    }
    
    public function fetchCallDetails(Request $request)
    {
        
        $time = date('Y-m-d H:i:s', time());
       // print_r($request->userid);die;
        $res = Channel::orderBy('id', 'DESC')->limit(1)->get();
         return response()->json($res);
    }
    
    
    public function generateRandomString($length = 25) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function callUser(Request $request)
    {

        $data['userToCall'] = $request->user_to_call;
        $data['channelName'] = $request->channel_name;
        $data['from'] = $request->user_calling;

        return response()->json(broadcast(new MakeAgoraCall($data))->toOthers());
    }
}
