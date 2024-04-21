<?php

use Diglactic\Breadcrumbs\Breadcrumbs;

Breadcrumbs::for('dashboard', function ($trail) {
    $trail->push('Dashboard', route('user-dashboard'));
});
Breadcrumbs::for('pelatihan', function ($trail, $pelatihan) {
    $trail->parent('dashboard');
    $trail->push($pelatihan->judul, route('pelatihan.show', $pelatihan));
});
Breadcrumbs::for('materi', function ($trail, $pelatihan, $materi) {
    $trail->parent('pelatihan', $pelatihan);
    $trail->push($materi->judul, route('materi.show', [$pelatihan, $materi]));
});

