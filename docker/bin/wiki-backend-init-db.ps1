$ErrorActionPreference = "Stop"
$PSNativeCommandUseErrorActionPreference = $true

Get-Content data/init.sql | docker exec -i wiki-backend-db mysql -pVIzP6LTScyYy
Get-Content data/init.sql | docker exec -i wiki-backend-tests-db mysql -proh6shiD
