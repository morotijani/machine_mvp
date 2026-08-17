<?php
$ch = curl_init("http://localhost/machine_mvp/public/api/sync/receive");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, "test");
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/octet-stream"]);
echo curl_exec($ch);
