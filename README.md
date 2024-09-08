# Pelatihan Dosen
*by Mahez Pradana*
## Instalasi
Kebutuhan Server:
- PHP >= 8.1
- Ctype PHP Extension
- cURL PHP Extension
- DOM PHP Extension
- Fileinfo PHP Extension
- Filter PHP Extension
- Hash PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PCRE PHP Extension
- PDO PHP Extension
- Session PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- PostgreSQL / SQL lainnya

**Clone Repository**
1. Instal depedensi
    ```sh
    composer install
    cp .env.example .env
    php artisan key:generate
    ```
2. Isi *.env* sesuai *.env.example*
   disarankan *APP_ENV=production* dan *APP_DEBUG=false*
3. Lakukan storage:link
    ```sh
    php artisan storage:link
    ```
   Apabila gambar atau file tidak muncul/terdetek maka ada perbedaan pada cara melakukan link, seperti pada hosting cpanel.
4. Deploy database
   dilakukan setelah mengisi *.env* bagian database dan command dibawah
    ```sh
    php artisan migrate --seed
    ```
5. Build Css
   lakukan dengan nodejs
    ```sh
    npm run build
    ```
   atau menggunakan compiled file dengan ekstrak dan memindahkan folder *build* ke dalam folder *public* atau *public_html*
5. Optimasi
    ```sh
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
    php artisan filament:optimize
    ```

## PDDIKTI API Workflow
- Registrasi (app/Filament/User/Pages/Auth/Register.php)
    - Search button akan melakukan fetch terhadap API
      String $search
        ```sh
        Http::get('https://api-frontend.kemdikbud.go.id/hit/' . $search)->json();
        //function fetchDosenData#164
        
        //Menghasilkan
        array:3 [▼ // app\Filament\User\Pages\Auth\Register.php:149
        "dosen" => array:22 [▶]
        "prodi" => array:1 [▶]
        "pt" => array:1 [▶]
        ]
        
        //kemudian mapping array dosen
        array:2 [▼ // app\Filament\User\Pages\Auth\Register.php:134
          "/data_dosen/xx" => "BUDI xx, NIDN : 0000000, PT : UNIVERSITAS, Prodi : AKUNTANSI"
          "/data_dosen/xx" => "BUDI xx, NIDN : 0000000, PT : UNIVERSITAS, Prodi : KEBIDANAN"
          ]
          
          //Simpan dan ekstrak data berdasarkan state/key dan jadikan NIDN dan NAMA sebagai hidden input
        ```
- Sinkronasi Data profil (app/Filament/Pages/Auth/EditProfile.php)
    ```sh
    private function hitDosenApiController(mixed $param)
    {
        try {
            //Dapatkan id berdasarkan pencarian dosen
            $getId = Http::get('https://pddikti.kemdikbud.go.id/api/pencarian/dosen/' . $param)->json();
            //Dapatkan data profil berdasarkan id
            $response = Http::get('https://pddikti.kemdikbud.go.id/api/dosen/profile/' . $getId[0]['id'])->json();
            return $response;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    //Kemudian set setiap array key menjadi value hidden input
    ```

## Schedule & Command List
- Chatbot Train untuk memperbarui model chatbot dengan data terbaru
    ```sh
    php artisan chatbot
    ```
- Menghapus file yang tidak digunakan
    ```sh
    php artisan delete:unused-file
    ```
- Untuk unpublish semua pelatihan yang expired
    ```sh
    php artisan unpublish:pelatihan
    ```
- Untuk menjalankan schedule
    ```sh
    php artisan schedule:work
    
    untuk menjalankan
        $schedule->command('delete:unused-file')->daily()->timezone('Asia/Jakarta')->runInBackground();
        $schedule->job(new TerjadwalJob())->everyMinute()->timezone('Asia/Jakarta');
        $schedule->command('unpublish:pelatihan')->daily()->timezone('Asia/Jakarta');
        $schedule->command('chatbot')->monthly()->timezone('Asia/Jakarta');
    ```
## Referensi performa server
https://medium.com/@dimdev/9-php-runtimes-performance-benchmark-cbc0527b9df9
https://medium.com/beyn-technology/hola-frankenphp-laravel-octane-servers-comparison-pushing-the-boundaries-of-performance-d3e7ad8e652c
