let fileobj;

function upload_file(e) {
    e.preventDefault();
    fileobj = e.dataTransfer.files[0];
    ajax_file_upload(fileobj);
}

function file_explorer() {
    document.getElementById('selectfile').click();
    document.getElementById('selectfile').onchange = function() {
        fileobj = document.getElementById('selectfile').files[0];
        ajax_file_upload(fileobj);
    };
}

function ajax_file_upload(file_obj) {
    if (file_obj !== undefined) {
        const formData = new FormData();
        formData.append('file', file_obj);

        fetch('/receiver.php', {
            method: 'POST',
            body: formData
        })
            .then((response) => {
                if (response.status !== 200) {
                    throw new Error ("Bad Response");
                }
                return response.blob();
            })
            .then((data) => {
                const url = window.URL.createObjectURL(data);
                const anchor = document.createElement("a");
                anchor.href = window.URL.createObjectURL(data);
                anchor.innerHTML = "результат"
                anchor.download = 'parsed.xlsx';
                anchor.click();
                document.querySelector('#link').appendChild(anchor);
                document.querySelector('#drop_file_zone').style.height = "220px";
                window.URL.revokeObjectURL(url);
                document.removeChild(anchor);

                file_obj.value = "";
            })
    }
}
