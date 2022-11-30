<html>
    <head>
        <title>Parsed Text Converter</title>
        <link rel="stylesheet" href="assets/css/style.css">
        <script src="assets/js/script.js" type="text/javascript"></script>
    </head>
    <body>
        <div id="drop_file_zone" ondrop="upload_file(event)" ondragover="return false">
            <div id="drag_upload_file">
                <p>Перетащи сюда файл</p>
                <p>или</p>
                <p><input type="button" value="Выбери из проводника" onclick="file_explorer();" /></p>
                <input type="file" id="selectfile" />
                <p>После конвертации файл будет предложено скачать</p>
                <p id="link"></p>
            </div>
        </div>
    </body>
</html>
