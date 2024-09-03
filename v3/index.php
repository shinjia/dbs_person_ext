<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .button {
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            margin: 10px;
        }
        .red { background-color: red; }
        .green { background-color: green; }
        .blue { background-color: blue; }
        .yellow { background-color: yellow; }
        .purple { background-color: purple; }
        .orange { background-color: orange; }
    </style>
</head>
<body>
<h1>dbs_person_ext --- v3</h1>
<p>自行由前台網頁及後台系統進入！</p>
<p>
    <button class="button red" onclick="window.location.href='install/install.php';">install/</button>
    <button class="button green" onclick="window.location.href='admin/';">後台管理 admin/</button>
    <button class="button blue" onclick="window.location.href='web/';">前台網頁 web/</button>
    <button class="button yellow" onclick="window.location.href='https://example4.com';">連結四</button>
    <button class="button purple" onclick="window.location.href='https://example5.com';">連結五</button>
    <button class="button orange" onclick="window.location.href='https://example6.com';">連結六</button>
</p>
</body>
</html>