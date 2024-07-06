<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Modul</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            background-color: #ffffff;
        }
    </style>
</head>
<body>
<div class="container mt-5 p-3 bg-light rounded">
    <header class="mb-4">
        <h1 class="text-center">Modul {{$modul->judul}}</h1>
    </header>
    <div class="card shadow">
        <div class="card-body">
            <table class="table table-striped table-bordered" width="100%" id="rekapModul">
                <thead>
                <tr class="table-primary">
                    <th>Peserta</th>
                    @foreach($modul->noMateri as $tugas)
                        <th>
                            @if($tugas->jenis === 'tugas')
                                <a href="{{\App\Filament\Resources\TugasResource::getUrl('view', ['record'=>$tugas->id])}}">
                                    {{$tugas->judul}} ({{$tugas->jenis}})
                                    <i class="fas fa-external-link-alt" style="font-size: 12px;"></i>
                                </a>
                            @else
                                <a href="{{\App\Filament\Resources\KuisResource::getUrl('view', ['record'=>$tugas->id])}}">
                                    {{$tugas->judul}} ({{$tugas->jenis}})
                                    <i class="fas fa-external-link-alt" style="font-size: 12px;"></i>
                                </a>
                            @endif
                        </th>
                    @endforeach
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 5 JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<!-- DataTables Bootstrap 5 JS -->
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function () {
        $('#rekapModul').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: "{{ url()->current() }}",
            columns: [
                {data: 'peserta', name: 'peserta', searchable: true, orderable: false},
                    @foreach($modul->noMateri as $tugas)
                {
                    data: 'tugas_{{ $tugas->id }}', name: 'tugas_{{ $tugas->id }}', searchable: false
                },
                @endforeach
            ]
        });
    });
</script>
</body>
</html>
