function tinyMCEInit(selector) {
    tinymce.remove();
    tinymce.init($.extend({
        selector: selector,
        toolbar: [
            "undo redo | styleselect | table | bold italic underline strikethrough | link image | code | forecolor backcolor",
            "alignleft aligncenter alignright | textcolor | emoticons | youTube"
        ],
        plugins: 'advlist autolink link image lists charmap print preview table textcolor emoticons lists code colorpicker, youTube',
        language: "ru",
        menubar: "insert edit table tools format view",
        height: $(window).height() * 0.5,
        browser_spellcheck: true,
        setup: function (ed) {
            ed.on('keydown', function (event) {
                if (event.ctrlKey && event.keyCode == 13) {
                    event.preventDefault();
                    $('[name="topic_reply"]').trigger('click');
                }
            });
        }
    }));
}