<?php
include_once 'Download.php';
$handler = new YouTubeDownloader();

// Video URL
$youtubeURL = 'https://www.youtube.com/watch?v=b5h4UUBMu-Q';
// Video URL

if(!empty($youtubeURL) && !filter_var($youtubeURL, FILTER_VALIDATE_URL) === false){
    $downloader = $handler->getDownloader($youtubeURL);
    $downloader->setUrl($youtubeURL);
    if($downloader->hasVideo()){
        $videoDownloadLink = $downloader->getVideoDownloadLink();
        $videoTitle = $videoDownloadLink[0]['title'];
        $videoQuality = $videoDownloadLink[0]['quality'];
        $videoFormat = $videoDownloadLink[0]['format'];
        $videoFileName = strtolower(str_replace(' ', '_', $videoTitle)).'.'.$videoFormat;
        $downloadURL = $videoDownloadLink[0]['url'];
        $fileName = preg_replace('/[^A-Za-z0-9.\_\-]/', '', basename($videoFileName));
        if(!empty($downloadURL)){
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$fileName");
            header("Content-Type: application/zip");
            header("Content-Transfer-Encoding: binary");
            readfile($downloadURL);
        }
    }else{
        echo "The video is not found, please check YouTube URL.";
    }
}else{
    echo "Please provide valid YouTube URL.";
}
?>