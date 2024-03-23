$('document').ready(function (){
    console.log('register.js loaded')
    $('#register-form').submit(function (e){
        e.preventDefault()
    })
    $('#registerButton').attr('disabled', true)
    $('#registerButton').click(function () {
        registerAccount();
    });
    $('#buttonSearchDosen').click(function (){
        getDosen()
    })
    $('#listDosen').on('click', 'button', function () {
        const nidn = $(this).attr('id');
        const name = $(this).find('span').first().text();
        const pt = $(this).find('span').eq(2).text();
        const prodi = $(this).find('span').eq(3).text();
        const link = encodeURIComponent($(this).find('span').last().text().split('/')[2]);
        setDosen(nidn, name, pt, prodi, link);
        $('#closeModal').click();
        console.log(link);
    });

})
function getDosen() {
    const dosen = $('#data_dosen').val();
    const encodedInput = encodeURIComponent(dosen);
    const searchButton = $('#buttonSearchDosen');
    searchButton.addClass('loading');
    $('#listDosen').empty();

    axios.get(getDosenRoute, {
        params: {
            dosen: encodedInput
        }
    }).then(response => {
        console.log(response);
        const dataDosen = response.data.dosen;
        if (response.data.dosen.length === 1 && response.data.dosen[0].text.includes('Cari')) {
            $('#listDosen').append(`<button type="button" disabled
                                        class="py-3 px-4 my-2 mx-5 items-center gap-x-0.5 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                                        <span class="text-lg font-bold">Data tidak ditemukan</span>
                                </button>`)
            searchButton.removeClass('loading');
            return 0;
        }
        dataDosen.forEach(dosen => {
            const text = dosen.text;
            const nidnMatch = text.match(/NIDN : (\d+)/);
            const ptMatch = text.match(/PT : (.+?),/);
            const prodiMatch = text.match(/Prodi : (.+)/);
            const name = text.split(',')[0];
            const link = dosen['website-link'];
            setTimeout(() => {
                $('#listDosen').append(
                    `<button type="button" id="${nidnMatch[1]}" class="btn btn-neutral">
                    <div class="flex items-center gap-x-2 w-full">
                        <div class="flex flex-col w-full">
                            <span class="text-sm font-semibold text-left" style="text-align: left">${name}</span>
                            <span class="text-xs text-gray-400 text-left" style="text-align: left">${nidnMatch ? nidnMatch[1] : ''}</span>
                        </div>
                        <div class="flex flex-col w-full">
                            <span class="text-xs text-gray-400 text-right">${ptMatch ? ptMatch[1] : ''}</span>
                            <span class="text-xs text-gray-400 text-right">${prodiMatch ? prodiMatch[1] : ''}</span>
                            <span class="hidden sm:hidden md:hidden lg:hidden xl:hidden">${link}</span>
                        </div>
                    </div>
                </button>`
                )
            }, 1);
        })
        searchButton.removeClass('loading');
    }).catch(error => {
        console.log(error);
        searchButton.removeClass('loading');
        swal.fire({
            title: 'Terjadi kesalahan pada server PDDIKTI',
            text: 'Server PDDIKTI tidak merespon, silahkan coba lagi nanti',
            icon: 'error'
        })
    });
}
function setDosen(nidn, name, pt, prodi, link) {
    $('#searchPDDIKTI').find('#closeSearchPDDIKTI').click();
    $('#selectedDosen').find('button').remove();
    $('#selected').remove();
    $('#selectedDosen').append(`
        <button type="button"
        class="btn btn-neutral">
            <div class="flex items-center gap-x-2 w-full">
                <div class="flex flex-col w-full">
                    <span class="text-sm font-semibold text-left" style="text-align: left" id="selected-name">${name}</span>
                    <span class="text-xs font-semibold text-left" style="text-align: left" id="selected-nidn">${nidn}</span>
                </div>
                <div class="flex flex-col w-full">
                    <span class="text-xs text-gray-400 text-right" id="selected-pt">${pt}</span>
                    <span class="text-xs text-gray-400 text-right" id="selected-prodi">${prodi}</span>
                    <span class="hidden sm:hidden md:hidden lg:hidden xl:hidden" id="selected-link">${link}</span>
                </div>
            </div>
        </button>
    `);
    $('#registerButton').attr('disabled', false);
}
async function getDataDosen(link) {
    try {
        console.log('route:', getDataRoute);
        const response = await axios.get(getDataRoute, {
            params: {
                link: link
            }
        });
        return response.data.dataumum;
    } catch (error) {
        console.log(error);
    }
}
async function registerAccount() {

    const submitButton = $('#registerButton');
    const nidn = $('#selected-nidn').text();
    const nm_sdm = $('#selected-name').text();
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    const result = await swal.fire({
        title: 'Apakah data yang anda masukkan sudah benar?',
        text: 'Pastikan data yang anda masukkan sudah benar, data yang sudah disubmit tidak dapat diubah',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, data sudah benar',
        cancelButtonText: 'Batal'
    });

    if (!result.isConfirmed) return;

    console.log('nidn:', nidn)
    console.log('selected-link:', $('#selected-link').text());
    // const data = await getDataDosen($('#selected-link').text());
    // console.log('Data Dosen:', data);
    submitButton.addClass('loading');
    // if (data === undefined) {
    //     swal.fire({
    //         title: 'Terjadi kesalahan pada server PDDIKTI',
    //         text: 'Server PDDIKTI tidak merespon, silahkan coba lagi nanti',
    //         icon: 'error'
    //     });
    //     submitButton.removeClass('loading');
    //     return;
    // }

    const formData = new FormData($('#register-form')[0]);
    formData.append('no_induk', nidn);
    formData.append('nama', nm_sdm);
    // formData.append('jenis_kelamin', data.jk);
    formData.append('universitas', $('#selected-pt').text());
    formData.append('prodi', $('#selected-prodi').text());
    formData.append('link', $('#selected-link').text());
    // formData.append('jabatan_fungsional', data.fungsional);
    // formData.append('pendidikan_tertinggi', data.pend_tinggi);
    // formData.append('status_kerja', data.ikatankerja);
    // formData.append('status_dosen', data.statuskeaktifan);
    formData.append('_token', csrfToken);
    console.log(formData);
    await axios.post(registerRoute, formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
        }
    }).then(
        submitButton.removeClass('loading'),
    ).catch(error => {
        console.log(error);
        swal.fire({
            title: 'Terjadi kesalahan pada server',
            text: 'Server tidak merespon, silahkan coba lagi nanti',
            icon: 'error'
        });
    })
}

