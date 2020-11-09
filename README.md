# sonarqube-qualitygate-projects-status-check
Small script used in our CI to check the quality status of a project in sonarqube

### Installation
edit the source file to configure $sonarQubeUrl and $sonarQubeToken values

### Usage
usage sonarcheck projectKey

Script will return 0 if the current project status is OK on sonarQube, or it will return 1

Tested with sonarqube 8.5.1
