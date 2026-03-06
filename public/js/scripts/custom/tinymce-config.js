function initTinyMCE(selector, uploadUrl, csrfToken) {
    if ($(selector).length > 0) {
        tinymce.init({
            selector: selector,
            theme: "modern",
            height: 300,
            relative_urls: false,
            remove_script_host: false,
            plugins: [
                "advlist autolink link image imagetools lists charmap  print preview hr anchor pagebreak spellchecker",
                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime nonbreaking",
                "save table contextmenu directionality emoticons template paste textcolor",
            ],
            toolbar:
                "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons",
            style_formats: [
                { title: "Bold text", inline: "b" },
                {
                    title: "Red text",
                    inline: "span",
                    styles: { color: "#ff0000" },
                },
                {
                    title: "Red header",
                    block: "h1",
                    styles: { color: "#ff0000" },
                },
                { title: "Example 1", inline: "span", classes: "example1" },
                { title: "Example 2", inline: "span", classes: "example2" },
                { title: "Table styles" },
                { title: "Table row 1", selector: "tr", classes: "tablerow1" },
            ],
            images_upload_handler: function (blobInfo, success, failure) {
                var xhr, formData;
                xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open("POST", uploadUrl);
                xhr.setRequestHeader("X-CSRF-Token", csrfToken);
                xhr.onload = function () {
                    var json;
                    if (xhr.status != 200) {
                        failure("HTTP Error: " + xhr.status);
                        return;
                    }
                    json = JSON.parse(xhr.responseText);

                    if (!json || typeof json.location != "string") {
                        failure("Invalid JSON: " + xhr.responseText);
                        return;
                    }
                    success(json.location);
                };
                formData = new FormData();
                formData.append("file", blobInfo.blob(), blobInfo.filename());
                xhr.send(formData);
            },
        });
    }
}
