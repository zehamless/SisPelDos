<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kuis</title>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <link href="https://unpkg.com/survey-jquery/defaultV2.min.css" type="text/css" rel="stylesheet">
    <script type="text/javascript" src="https://unpkg.com/survey-jquery/survey.jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/showdown/2.1.0/showdown.min.js"
            integrity="sha512-LhccdVNGe2QMEfI3x4DVV3ckMRe36TfydKss6mJpdHjNFiV07dFpS2xzeZedptKZrwxfICJpez09iNioiSZ3hA=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div id="surveyContainer"></div>
<script type="text/javascript">

    const jsonData = {!! $jsonData !!};
    // console.log(jsonData);

    const kuisData = {
        completedHtml: "<h4>Terimakasih!</h4>",
        title: jsonData.judul,
        showTOC: true,
        showProgressBar: 'auto',
        progressBarType: "questions",
        progressBarShowPageNumbers: true,
        progressBarShowPageTitles: true,
        showTimerPanel: "bottom",
        startSurveyText: 'Mulai',
        showTimerPanelMode: "survey",
        maxTimeToFinish: jsonData.durasi*60,
        pages: jsonData.kuis.map(function (kuisItem, index) {
            // console.log(kuisItem.id)
            return {
                name: 'Soal ' + (index + 1),
                navigationTitle: 'Soal ' + (index + 1),
                elements: [
                    {
                        type: kuisItem.jawaban[0].type.toLowerCase() == 'radio' ? 'radiogroup' : 'checkbox',
                        name: String(index+1),
                        title: kuisItem.pertanyaan,
                        choicesOrder: 'random',
                        navigationTitle: 'Soal ' + (index + 1),
                        choices: kuisItem.jawaban[0].data.jawaban_option,
                    }
                ]
            };
        })
    };
    const survey = new Survey.Model(kuisData);

    var converter = new showdown.Converter();
    survey.onTextMarkdown.add((survey, options) => {
        options.html = converter.makeHtml(options.text).slice(3, -4);
        const el = options.element;
        if (!el || el.getType() !== "page" || options.name !== "navigationTitle") return;

        const isCompleted = el.getProgressInfo().questionCount === el.getProgressInfo().answeredQuestionCount;
        const hasErrors = el.questions.some(question => question.isEmpty());

        let html = options.text;
        if (hasErrors) {
            html += html.includes("<span style='color:red'>*</span>") ? "" : "<span style='color:red'>*</span>";
        } else {
            html = html.replace("<span style='color:red'>*</span>", "");
        }

        options.html = !isCompleted ? "<strong>" + html + "</strong>" : html;
    });

    $(function () {
        $("#surveyContainer").Survey({model: survey});
    });
</script>
</body>
</html>
