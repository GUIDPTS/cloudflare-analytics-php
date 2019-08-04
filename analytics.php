<?php
    function getJSON($url){
        $headers = array(
            "X-Auth-Email: YOUR_EMAIL@DOMAIN.COM",
            "X-Auth-Key: YOUR_API_ID",
            "Content-Type: text/plain",
        );
        $curl = curl_init($url);
        curl_setopt($curl,CURLOPT_URL,$url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl,CURLOPT_BINARYTRANSFER,true);
        curl_setopt($curl,CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);
        curl_setopt($curl,CURLINFO_HEADER_OUT,true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        $json = curl_exec($curl);
        curl_close($curl);
        return $json;
    }
    $data = fopen("data.json","w");
    $json = getJSON("https://api.cloudflare.com/client/v4/zones/YOUR_ZONE_ID/analytics/dashboard?since=-10080&until=0&continuous=true");
    fwrite($data,$json);
    fclose($data);
?>
<head>
    <Title>Cinema Cloudflare CDN 服务状态</Title>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
    <style type="text/css" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.css"></style>
    <style type="text/css" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css"></style>
</head>
<body>
    <div style="display: block;width: 50%;height: 50%;float: left;">
        <canvas id="basic"></canvas>
    </div>
    <div style="display: block;width: 50%;height: 50%;float: right;">
        <canvas id="type"></canvas>
    </div>
    <script>
        var httpRequest = new XMLHttpRequest(),data;
        httpRequest.open("GET","./data.json",true);
        httpRequest.send();
        httpRequest.onreadystatechange = function(){
            data = JSON.parse(httpRequest.responseText);
            //basic
            var timeArray = new Array();
            var uniqueArray = new Array();
            var requestsArray = new Array();
            var percentCachedArray = new Array();
            var totalDataArray = new Array();
            var cachedArray = new Array();
            for(var i = 0;i < 7;i++){
                timeArray[i] = new Date(data.result.timeseries[i].since).toDateString();
                uniqueArray[i] = data.result.timeseries[i].uniques.all;
                uniqueArray[i] = Math.round(parseInt(uniqueArray[i]) / 10);
                requestsArray[i] = data.result.timeseries[i].requests.all;
                requestsArray[i] = Math.round(parseInt(requestsArray[i]) / 100);
                percentCachedArray[i] = parseInt(data.result.timeseries[i].bandwidth.cached) / parseInt(data.result.timeseries[i].bandwidth.all) * 100;
                totalDataArray[i] = data.result.timeseries[i].bandwidth.all;
                cachedArray[i] = data.result.timeseries[i].bandwidth.cached;
                totalDataArray[i] = Math.round(parseInt(totalDataArray[i]) / 1024 / 1024);
                cachedArray[i] = Math.round(parseInt(cachedArray[i]) / 1024 / 1024);
            }
            var ctx = document.getElementById("basic").getContext("2d");
            var lineCharData = new Chart(ctx,{
                type: "line",
                data: {
                    labels: [timeArray[0],timeArray[1],timeArray[2],timeArray[3],timeArray[4],timeArray[5],timeArray[6]],
                    datasets: [
                        {
                            label: 'Unique Visitors / 10',
                            backgroundColor: ['rgba(255, 99, 132, 0.2)','rgba(255, 99, 132, 0.2)','rgba(255, 99, 132, 0.2)','rgba(255, 99, 132, 0.2)','rgba(255, 99, 132, 0.2)','rgba(255, 99, 132, 0.2)','rgba(255, 99, 132, 0.2)'],
                            borderColor: ['rgba(255, 99, 132, 1)','rgba(255, 99, 132, 1)','rgba(255, 99, 132, 1)','rgba(255, 99, 132, 1)','rgba(255, 99, 132, 1)','rgba(255, 99, 132, 1)','rgba(255, 99, 132, 1)'],
                            data: [uniqueArray[0],uniqueArray[1],uniqueArray[2],uniqueArray[3],uniqueArray[4],uniqueArray[5],uniqueArray[6]]
                        },
                        {
                            label: 'Total Requests / 100',
                            backgroundColor: ['rgba(54, 162, 235, 0.2)','rgba(54, 162, 235, 0.2)','rgba(54, 162, 235, 0.2)','rgba(54, 162, 235, 0.2)','rgba(54, 162, 235, 0.2)','rgba(54, 162, 235, 0.2)','rgba(54, 162, 235, 0.2)'],
                            borderColor: ['rgba(54, 162, 235, 1)','rgba(54, 162, 235, 1)','rgba(54, 162, 235, 1)','rgba(54, 162, 235, 1)','rgba(54, 162, 235, 1)','rgba(54, 162, 235, 1)','rgba(54, 162, 235, 1)'],
                            data: [requestsArray[0],requestsArray[1],requestsArray[2],requestsArray[3],requestsArray[4],requestsArray[5],requestsArray[6]]
                        },
                        {
                            label: 'Percent Cached(%)',
                            backgroundColor: ['rgba(255, 206, 86, 0.2)','rgba(255, 206, 86, 0.2)','rgba(255, 206, 86, 0.2)','rgba(255, 206, 86, 0.2)','rgba(255, 206, 86, 0.2)','rgba(255, 206, 86, 0.2)','rgba(255, 206, 86, 0.2)'],
                            borderColor: ['rgba(255, 206, 86, 1)','rgba(255, 206, 86, 1)','rgba(255, 206, 86, 1)','rgba(255, 206, 86, 1)','rgba(255, 206, 86, 1)','rgba(255, 206, 86, 1)','rgba(255, 206, 86, 1)'],
                            data: [percentCachedArray[0],percentCachedArray[1],percentCachedArray[2],percentCachedArray[3],percentCachedArray[4],percentCachedArray[5],percentCachedArray[6]]
                        },
                        {
                            label: 'Total Data Served(MB)',
                            backgroundColor: ['rgba(153, 102, 255, 0.2)','rgba(153, 102, 255, 0.2)','rgba(153, 102, 255, 0.2)','rgba(153, 102, 255, 0.2)','rgba(153, 102, 255, 0.2)','rgba(153, 102, 255, 0.2)','rgba(153, 102, 255, 0.2)'],
                            borderColor: ['rgba(153, 102, 255, 1)','rgba(153, 102, 255, 1)','rgba(153, 102, 255, 1)','rgba(153, 102, 255, 1)','rgba(153, 102, 255, 1)','rgba(153, 102, 255, 1)','rgba(153, 102, 255, 1)'],
                            data: [totalDataArray[0],totalDataArray[1],totalDataArray[2],totalDataArray[3],totalDataArray[4],totalDataArray[5],totalDataArray[6]]
                        },
                        {
                            label: 'Data Cached(MB)',
                            backgroundColor: ['rgba(255, 159, 64, 0.2)','rgba(255, 159, 64, 0.2)','rgba(255, 159, 64, 0.2)','rgba(255, 159, 64, 0.2)','rgba(255, 159, 64, 0.2)','rgba(255, 159, 64, 0.2)','rgba(255, 159, 64, 0.2)'],
                            borderColor: ['rgba(255, 159, 64, 1)','rgba(255, 159, 64, 1)','rgba(255, 159, 64, 1)','rgba(255, 159, 64, 1)','rgba(255, 159, 64, 1)','rgba(255, 159, 64, 1)','rgba(255, 159, 64, 1)'],
                            data: [cachedArray[0],cachedArray[1],cachedArray[2],cachedArray[3],cachedArray[4],cachedArray[5],cachedArray[6]]
                        }
                    ]
                }
            })
            //content type
            var cssArray = new Array();
            var emptyArray = new Array();
            var gifArray = new Array();
            var htmlArray = new Array();
            var jsArray = new Array();
            var jpegArray = new Array();
            var jsonArray = new Array();
            var octetStreamArray = new Array();
            var otherArray = new Array();
            var plainArray = new Array();
            var pngArray = new Array();
            var svgArray = new Array();
            var svgArray = new Array();
            var xmlArray = new Array();
            for(var i = 0;i < 7;i++){
                cssArray[i] = data.result.timeseries[i].bandwidth.content_type.hasOwnProperty("css") ? data.result.timeseries[i].bandwidth.content_type.css : 0;
                emptyArray[i] = data.result.timeseries[i].bandwidth.content_type.hasOwnProperty("empty") ? data.result.timeseries[i].bandwidth.content_type.empty : 0;
                gifArray[i] = data.result.timeseries[i].bandwidth.content_type.hasOwnProperty("gif") ? data.result.timeseries[i].bandwidth.content_type.gif : 0;
                htmlArray[i] = data.result.timeseries[i].bandwidth.content_type.hasOwnProperty("html") ? data.result.timeseries[i].bandwidth.content_type.html : 0;
                jsArray[i] = data.result.timeseries[i].bandwidth.content_type.hasOwnProperty("javascript") ? data.result.timeseries[i].bandwidth.content_type.javascript : 0;
                jpegArray[i] = data.result.timeseries[i].bandwidth.content_type.hasOwnProperty("jpeg") ? data.result.timeseries[i].bandwidth.content_type.jpeg : 0;
                jsonArray[i] = data.result.timeseries[i].bandwidth.content_type.hasOwnProperty("json") ? data.result.timeseries[i].bandwidth.content_type.json : 0;
                octetStreamArray[i] = data.result.timeseries[i].bandwidth.content_type.hasOwnProperty("octetStream") ? data.result.timeseries[i].bandwidth.content_type.octetStream : 0;
                otherArray[i] = data.result.timeseries[i].bandwidth.content_type.hasOwnProperty("other") ? data.result.timeseries[i].bandwidth.content_type.other : 0;
                plainArray[i] = data.result.timeseries[i].bandwidth.content_type.hasOwnProperty("plain") ? data.result.timeseries[i].bandwidth.content_type.plain : 0;
                pngArray[i] = data.result.timeseries[i].bandwidth.content_type.hasOwnProperty("png") ? data.result.timeseries[i].bandwidth.content_type.png : 0;
                svgArray[i] = data.result.timeseries[i].bandwidth.content_type.hasOwnProperty("svg") ? data.result.timeseries[i].bandwidth.content_type.svg : 0;
                xmlArray[i] = data.result.timeseries[i].bandwidth.content_type.hasOwnProperty("xml") ? data.result.timeseries[i].bandwidth.content_type.xml : 0;
            }
            var ctx2 = document.getElementById("type").getContext("2d");
            var barChartData = new Chart(ctx2,{
                type: 'bar',
                data: {
                    labels: [timeArray[0],timeArray[1],timeArray[2],timeArray[3],timeArray[4],timeArray[5],timeArray[6]],
                    datasets: [
                        {
                            label: 'css',
                            backgroundColor: ['rgba(255, 99, 132, 0.2)','rgba(255, 99, 132, 0.2)','rgba(255, 99, 132, 0.2)','rgba(255, 99, 132, 0.2)','rgba(255, 99, 132, 0.2)','rgba(255, 99, 132, 0.2)','rgba(255, 99, 132, 0.2)'],
                            borderColor: ['rgba(255, 99, 132, 1)','rgba(255, 99, 132, 1)','rgba(255, 99, 132, 1)','rgba(255, 99, 132, 1)','rgba(255, 99, 132, 1)','rgba(255, 99, 132, 1)','rgba(255, 99, 132, 1)'],
                            data: [cssArray[0],cssArray[1],cssArray[2],cssArray[3],cssArray[4],cssArray[5],cssArray[6]]
                        },
                        {
                            label: 'empty',
                            backgroundColor: ['rgba(54, 162, 235, 0.2)','rgba(54, 162, 235, 0.2)','rgba(54, 162, 235, 0.2)','rgba(54, 162, 235, 0.2)','rgba(54, 162, 235, 0.2)','rgba(54, 162, 235, 0.2)','rgba(54, 162, 235, 0.2)'],
                            borderColor: ['rgba(54, 162, 235, 1)','rgba(54, 162, 235, 1)','rgba(54, 162, 235, 1)','rgba(54, 162, 235, 1)','rgba(54, 162, 235, 1)','rgba(54, 162, 235, 1)','rgba(54, 162, 235, 1)'],
                            data: [emptyArray[0],emptyArray[1],emptyArray[2],emptyArray[3],emptyArray[4],emptyArray[5],emptyArray[6]]
                        },
                        {
                            label: 'gif',
                            backgroundColor: ['rgba(255, 206, 86, 0.2)','rgba(255, 206, 86, 0.2)','rgba(255, 206, 86, 0.2)','rgba(255, 206, 86, 0.2)','rgba(255, 206, 86, 0.2)','rgba(255, 206, 86, 0.2)','rgba(255, 206, 86, 0.2)'],
                            borderColor: ['rgba(255, 206, 86, 1)','rgba(255, 206, 86, 1)','rgba(255, 206, 86, 1)','rgba(255, 206, 86, 1)','rgba(255, 206, 86, 1)','rgba(255, 206, 86, 1)','rgba(255, 206, 86, 1)'],
                            data: [gifArray[0],gifArray[1],gifArray[2],gifArray[3],gifArray[4],gifArray[5],gifArray[6]]
                        },
                        {
                            label: 'html',
                            backgroundColor: ['rgba(153, 102, 255, 0.2)','rgba(153, 102, 255, 0.2)','rgba(153, 102, 255, 0.2)','rgba(153, 102, 255, 0.2)','rgba(153, 102, 255, 0.2)','rgba(153, 102, 255, 0.2)','rgba(153, 102, 255, 0.2)'],
                            borderColor: ['rgba(153, 102, 255, 1)','rgba(153, 102, 255, 1)','rgba(153, 102, 255, 1)','rgba(153, 102, 255, 1)','rgba(153, 102, 255, 1)','rgba(153, 102, 255, 1)','rgba(153, 102, 255, 1)'],
                            data: [htmlArray[0],htmlArray[1],htmlArray[2],htmlArray[3],htmlArray[4],htmlArray[5],htmlArray[6]]
                        },
                        {
                            label: 'js',
                            backgroundColor: ['rgba(255, 159, 64, 0.2)','rgba(255, 159, 64, 0.2)','rgba(255, 159, 64, 0.2)','rgba(255, 159, 64, 0.2)','rgba(255, 159, 64, 0.2)','rgba(255, 159, 64, 0.2)','rgba(255, 159, 64, 0.2)'],
                            borderColor: ['rgba(255, 159, 64, 1)','rgba(255, 159, 64, 1)','rgba(255, 159, 64, 1)','rgba(255, 159, 64, 1)','rgba(255, 159, 64, 1)','rgba(255, 159, 64, 1)','rgba(255, 159, 64, 1)'],
                            data: [jsArray[0],jsArray[1],jsArray[2],jsArray[3],jsArray[4],jsArray[5],jsArray[6]]
                        },
                        {
                            label: 'jpeg',
                            backgroundColor: ['rgba(25, 159, 64, 0.2)','rgba(25, 159, 64, 0.2)','rgba(25, 159, 64, 0.2)','rgba(25, 159, 64, 0.2)','rgba(25, 159, 64, 0.2)','rgba(25, 159, 64, 0.2)','rgba(25, 159, 64, 0.2)'],
                            borderColor: ['rgba(25, 159, 64, 1)','rgba(25, 159, 64, 1)','rgba(25, 159, 64, 1)','rgba(25, 159, 64, 1)','rgba(25, 159, 64, 1)','rgba(25, 159, 64, 1)','rgba(25, 159, 64, 1)'],
                            data: [jpegArray[0],jpegArray[1],jpegArray[2],jpegArray[3],jpegArray[4],jpegArray[5],jpegArray[6]]
                        },
                        {
                            label: 'json',
                            backgroundColor: ['rgba(25, 15, 64, 0.2)','rgba(25, 15, 64, 0.2)','rgba(25, 15, 64, 0.2)','rgba(25, 15, 64, 0.2)','rgba(25, 15, 64, 0.2)','rgba(25, 15, 64, 0.2)','rgba(25, 15, 64, 0.2)'],
                            borderColor: ['rgba(25, 15, 64, 1)','rgba(25, 15, 64, 1)','rgba(25, 15, 64, 1)','rgba(25, 15, 64, 1)','rgba(25, 15, 64, 1)','rgba(25, 15, 64, 1)','rgba(25, 15, 64, 1)'],
                            data: [jsonArray[0],jsonArray[1],jsonArray[2],jsonArray[3],jsonArray[4],jsonArray[5],jsonArray[6]]
                        },
                        {
                            label: 'octetStream',
                            backgroundColor: ['rgba(255, 10, 64, 0.2)','rgba(255, 10, 64, 0.2)','rgba(255, 10, 64, 0.2)','rgba(255, 10, 64, 0.2)','rgba(255, 10, 64, 0.2)','rgba(255, 10, 64, 0.2)','rgba(255, 10, 64, 0.2)'],
                            borderColor: ['rgba(255, 10, 64, 1)','rgba(255, 10, 64, 1)','rgba(255, 10, 64, 1)','rgba(255, 10, 64, 1)','rgba(255, 10, 64, 1)','rgba(255, 10, 64, 1)','rgba(255, 10, 64, 1)'],
                            data: [octetStreamArray[0],octetStreamArray[1],octetStreamArray[2],octetStreamArray[3],octetStreamArray[4],octetStreamArray[5],octetStreamArray[6]]
                        },
                        {
                            label: 'other',
                            backgroundColor: ['rgba(55, 10, 64, 0.2)','rgba(55, 10, 64, 0.2)','rgba(55, 10, 64, 0.2)','rgba(55, 10, 64, 0.2)','rgba(55, 10, 64, 0.2)','rgba(55, 10, 64, 0.2)','rgba(55, 10, 64, 0.2)'],
                            borderColor: ['rgba(55, 10, 64, 1)','rgba(55, 10, 64, 1)','rgba(55, 10, 64, 1)','rgba(55, 10, 64, 1)','rgba(55, 10, 64, 1)','rgba(55, 10, 64, 1)','rgba(55, 10, 64, 1)'],
                            data: [otherArray[0],otherArray[1],otherArray[2],otherArray[3],otherArray[4],otherArray[5],otherArray[6]]
                        },
                        {
                            label: 'plain',
                            backgroundColor: ['rgba(55, 100, 64, 0.2)','rgba(55, 100, 64, 0.2)','rgba(55, 100, 64, 0.2)','rgba(55, 100, 64, 0.2)','rgba(55, 100, 64, 0.2)','rgba(55, 100, 64, 0.2)','rgba(55, 100, 64, 0.2)'],
                            borderColor: ['rgba(55, 100, 64, 1)','rgba(55, 100, 64, 1)','rgba(55, 100, 64, 1)','rgba(55, 100, 64, 1)','rgba(55, 100, 64, 1)','rgba(55, 100, 64, 1)','rgba(55, 100, 64, 1)'],
                            data: [plainArray[0],plainArray[1],plainArray[2],plainArray[3],plainArray[4],plainArray[5],plainArray[6]]
                        },
                        {
                            label: 'png',
                            backgroundColor: ['rgba(64, 100, 10, 0.2)','rgba(64, 100, 10, 0.2)','rgba(64, 100, 10, 0.2)','rgba(64, 100, 10, 0.2)','rgba(64, 100, 10, 0.2)','rgba(64, 100, 10, 0.2)','rgba(64, 100, 10, 0.2)'],
                            borderColor: ['rgba(64, 100, 10, 1)','rgba(64, 100, 10, 1)','rgba(64, 100, 10, 1)','rgba(64, 100, 10, 1)','rgba(64, 100, 10, 1)','rgba(64, 100, 10, 1)','rgba(64, 100, 10, 1)'],
                            data: [pngArray[0],pngArray[1],pngArray[2],pngArray[3],pngArray[4],pngArray[5],pngArray[6]]
                        },
                        {
                            label: 'svg',
                            backgroundColor: ['rgba(240, 12, 10, 0.2)','rgba(240, 12, 10, 0.2)','rgba(240, 12, 10, 0.2)','rgba(240, 12, 10, 0.2)','rgba(240, 12, 10, 0.2)','rgba(240, 12, 10, 0.2)','rgba(240, 12, 10, 0.2)'],
                            borderColor: ['rgba(240, 12, 10, 1)','rgba(240, 12, 10, 1)','rgba(240, 12, 10, 1)','rgba(240, 12, 10, 1)','rgba(240, 12, 10, 1)','rgba(240, 12, 10, 1)','rgba(240, 12, 10, 1)'],
                            data: [svgArray[0],svgArray[1],svgArray[2],svgArray[3],svgArray[4],svgArray[5],svgArray[6]]
                        },
                        {
                            label: 'xml',
                            backgroundColor: ['rgba(24, 12, 100, 0.2)','rgba(24, 12, 100, 0.2)','rgba(24, 12, 100, 0.2)','rgba(24, 12, 100, 0.2)','rgba(24, 12, 100, 0.2)','rgba(24, 12, 100, 0.2)','rgba(24, 12, 100, 0.2)'],
                            borderColor: ['rgba(24, 12, 100, 1)','rgba(24, 12, 100, 1)','rgba(24, 12, 100, 1)','rgba(24, 12, 100, 1)','rgba(24, 12, 100, 1)','rgba(24, 12, 100, 1)','rgba(24, 12, 100, 1)'],
                            data: [xmlArray[0],xmlArray[1],xmlArray[2],xmlArray[3],xmlArray[4],xmlArray[5],xmlArray[6]]
                        }
                    ]
                }
            })
        }
    </script>
</body>
