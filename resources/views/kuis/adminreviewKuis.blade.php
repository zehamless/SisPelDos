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
</head>
<body>
<div id="surveyContainer"></div>
<script type="text/javascript">

    {{--const jsonData = {!! $jsonData !!};--}}
    const jsonJawaban = {!! $jsonJawaban !!};
    const files = JSON.parse(jsonJawaban.files);
    // console.log(JSON)

    // Now you can access the jawaban object
    const jawaban = files.jawaban;
    const jsonData = files.pertanyaan;

    // console.log(jsonJawaban); // Outputs: {"1":"vv","2":["gg"]};

    const kuisData = {
        completedHtml: "<h4>Terimakasih!</h4>",
        title: jsonData.judul,
        // showTOC: true,
        // showProgressBar: 'auto',
        // progressBarType: "questions",
        // progressBarShowPageNumbers: true,
        // progressBarShowPageTitles: true,
        // showTimerPanel: "bottom",
        // startSurveyText: 'Mulai',
        showTimerPanelMode: "survey",
        // maxTimeToFinish: jsonData.durasi * 60,
        pages: jsonData.map(function (kuisItem, index) {
            let answer;
            let options;
            let jawabanBenar = kuisItem.jawaban[0].data.jawaban_benar;
            let jawabanOption = kuisItem.jawaban[0].data.jawaban_option;

            if (!Array.isArray(jawabanBenar) && jawabanBenar) {
                answer = jawabanOption[parseInt(jawabanBenar)];
            } else {
                answer = jawabanBenar.filter(item => !isNaN(item)).map(index => jawabanOption[parseInt(index)]);
            }
            // console.log(answer)
            options = kuisItem.jawaban[0].data.jawaban_option.map((item) => {
                if (!Array.isArray(answer)) {
                    if (item == answer) {
                        return item + ' <span style="color:green"> (Benar)</span>';
                    }
                } else {
                    if (answer.includes(item)) {
                        return item + ' <span style="color:green"> (Benar)</span>';
                    }
                }
                return item;
            });
            // console.log(options)
            return {
                name: 'Soal ' + (index + 1),
                navigationTitle: 'Soal ' + (index + 1),
                elements: [
                    {
                        type: kuisItem.jawaban[0].type.toLowerCase() == 'radio' ? 'radiogroup' : 'checkbox',
                        name: String(kuisItem.id),
                        title: kuisItem.pertanyaan,
                        navigationTitle: 'Soal ' + (index + 1),
                        choices: options,
                        correctAnswer: answer + ' <span style="color:green"> (Benar)</span>',
                    }
                ]
            };
        })
    };
    // console.log(kuisData);
    const survey = new Survey.Model(kuisData);

    let surveyData = jsonData.reduce((acc, kuisItem, index) => {
        acc[kuisItem.id] = jawaban[index + 1];
        return acc;
    }, {});
    jsonData.map(function (kuisItem, index) {
        let answer;
        let options;
        let jawabanBenar = kuisItem.jawaban[0].data.jawaban_benar;
        let jawabanOption = kuisItem.jawaban[0].data.jawaban_option;

        if (!Array.isArray(jawabanBenar) && jawabanBenar) {
            answer = jawabanOption[parseInt(jawabanBenar)];
        } else {
            answer = jawabanBenar.filter(item => !isNaN(item)).map(index => jawabanOption[parseInt(index)]);
        }
   for (let key in surveyData) {
    if (Array.isArray(surveyData[key])) {
        surveyData[key] = surveyData[key].map(item => {
            if (Array.isArray(answer) && answer.includes(item)) {
                return item + ' <span style="color:green"> (Benar)</span>';
            } else {
                return item;
            }
        });
    } else {
        if (surveyData[key] == answer) {
            surveyData[key] += ' <span style="color:green"> (Benar)</span>';
        }
    }
}

    })
    // console.log(surveyData)
    survey.data = surveyData;

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

    survey.mode = "display";
    survey.questionsOnPageMode = "singlePage";
    survey.showProgressBar = "off";


    $(function () {
        $("#surveyContainer").Survey({model: survey});
    });


</script>
</body>
</html>
