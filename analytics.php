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
    <div style="display: block;width: 80%;height: 80%;">
        <canvas id="unique"></canvas>
        <canvas id="requests"></canvas>
        <canvas id="percentCached"></canvas>
        <canvas id="totalData"></canvas>
        <canvas id="cached"></canvas>
    </div>
    <script>
        var httpRequest = new XMLHttpRequest(),data;
        httpRequest.open("GET","./data.json",true);
        httpRequest.send();
        httpRequest.onreadystatechange = function(){
            data = JSON.parse(httpRequest.responseText);
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
            var ctx = document.getElementById("unique").getContext("2d");
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
        }
        
        
    </script>
</body>