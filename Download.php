<?php
class YouTubeDownloader {
    private $video_id;
    private $video_title;
    private $video_url;
    private $link_pattern;
    public function __construct(){
        $this->link_pattern = "/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed)\/))([^\?&\"'>]+)/";
    }
    public function setUrl($url){
        $this->video_url = $url;
    }
    private function getVideoInfo(){
        return file_get_contents("https://www.youtube.com/get_video_info?video_id=".$this->extractVideoId($this->video_url)."&cpn=CouQulsSRICzWn5E&eurl&el=adunit");
    }
    private function extractVideoId($video_url){
        $parsed_url = parse_url($video_url);
        if($parsed_url["path"] == "youtube.com/watch"){
            $this->video_url = "https://www.".$video_url;
        }elseif($parsed_url["path"] == "www.youtube.com/watch"){
            $this->video_url = "https://".$video_url;
        }
        if(isset($parsed_url["query"])){
            $query_string = $parsed_url["query"];
            parse_str($query_string, $query_arr);
            if(isset($query_arr["v"])){
                return $query_arr["v"];
            }
        }   
    }
    public function getDownloader($url){
        if(preg_match($this->link_pattern, $url)){
            return $this;
        }
        return false;
    }
    public function getVideoDownloadLink(){
        parse_str($this->getVideoInfo(), $data);
        $this->video_title = $data["title"];
        $stream_map_arr = $this->getStreamArray();
        $final_stream_map_arr = array();
        foreach($stream_map_arr as $stream){
            parse_str($stream, $stream_data);
            $stream_data["title"] = $this->video_title;
            $stream_data["mime"] = $stream_data["type"];
            $mime_type = explode(";", $stream_data["mime"]);
            $stream_data["mime"] = $mime_type[0];
            $start = stripos($mime_type[0], "/");
            $format = ltrim(substr($mime_type[0], $start), "/");
            $stream_data["format"] = $format;
            unset($stream_data["type"]);
            $final_stream_map_arr [] = $stream_data;         
        }
        return $final_stream_map_arr;
    }
    private function getStreamArray(){
        parse_str($this->getVideoInfo(), $data);
        $stream_link = $data["url_encoded_fmt_stream_map"];
        return explode(",", $stream_link); 
    }
    public function hasVideo(){
        $valid = true;
        parse_str($this->getVideoInfo(), $data);
        if($data["status"] == "fail"){
            $valid = false;
        } 
        return $valid;
    }
     
}