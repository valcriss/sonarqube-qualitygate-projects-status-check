#!/usr/bin/env php
<?php


/**
Enter here the auth token you get from <your-sonarqube-instance-url>/account/security
*/
$sonarQubeToken = "";

/**
Enter here your-sonarqube-instance-url ending with /api/
example : https://sonar.example.com/api/
*/
$sonarQubeUrl = "";

if($argc !== 2)
{
    echo "usage : sonar_check project_key\n";
    return;
}

$projectKey = $argv[1];

echo "Checking quality gate for project : $projectKey \n";

while(isBuildInProgress($sonarQubeUrl,$sonarQubeToken))
{
    echo "Waiting for build compute ends\n";
    sleep(1);
}

$isError = isBuildStatusError($sonarQubeUrl,$sonarQubeToken,$projectKey);

exit(($isError) ? 1 : 0);

function isBuildStatusError($sonarQubeUrl,$sonarQubeToken,$projectKey)
{
    $result = callApi($sonarQubeUrl,$sonarQubeToken,"qualitygates/project_status?projectKey=$projectKey");
    echo "------------------------------------------------------\n";
    echo "Project Status : " . $result->projectStatus->status . "\n";
    echo "------------------------------------------------------\n";
    foreach ($result->projectStatus->conditions as $condition)
    {
        echo $condition->metricKey ." : " . $condition->status . "\n";
    }
    echo "------------------------------------------------------\n";
    return $result->projectStatus->status === "ERROR";
}

function isBuildInProgress($sonarQubeUrl,$sonarQubeToken)
{
    $result = callApi($sonarQubeUrl,$sonarQubeToken,"ce/activity?status=IN_PROGRESS");
    return count($result->tasks)>0;
}


function callApi($sonarQubeUrl,$sonarQubeToken, $route)
{
    $ch = curl_init($sonarQubeUrl.$route);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERPWD, $sonarQubeToken . ":");
    $output = curl_exec($ch);
    curl_close($ch);
    return json_decode($output);
}
