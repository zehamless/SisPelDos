# Learning Management System
*by Mahez Pradana*
### Table of Contents
**[Installation Instructions(ID)](#instalasi)**<br>
**[Installation Instructions(EN)](#installation)**<br>

## Instalasi
### Kebutuhan Server 
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

### PDDIKTI API Workflow
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

### Schedule & Command List
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
### Referensi performa server
https://medium.com/@dimdev/9-php-runtimes-performance-benchmark-cbc0527b9df9
https://medium.com/beyn-technology/hola-frankenphp-laravel-octane-servers-comparison-pushing-the-boundaries-of-performance-d3e7ad8e652c

## Installation
### Server Requirements
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
- PostgreSQL / Other SQL databases

**Clone Repository**
1. Install dependencies
    ```sh
    composer install
    cp .env.example .env
    php artisan key:generate
    ```
2. Fill in the *.env* file based on *.env.example*. It is recommended to set *APP_ENV=production* and *APP_DEBUG=false*.
3. Create the storage link
    ```sh
    php artisan storage:link
    ```
   If images or files don't appear or are not detected, there may be a difference in how the link is created, such as on cPanel hosting.
4. Deploy the database after configuring the database section in *.env* by running the following command:
    ```sh
    php artisan migrate --seed
    ```
5. Build CSS using Node.js
    ```sh
    npm run build
    ```
   Alternatively, use the compiled file by extracting and moving the *build* folder into the *public* or *public_html* folder.
6. Optimize the application
    ```sh
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
    php artisan filament:optimize
    ```

### PDDIKTI API Workflow
- Registration (app/Filament/User/Pages/Auth/Register.php)
    - The Search button fetches data from the API with
      String $search
        ```sh
        Http::get('https://api-frontend.kemdikbud.go.id/hit/' . $search)->json();
        // function fetchDosenData#164
        
        // Returns
        array:3 [▼ // app\Filament\User\Pages\Auth\Register.php:149
        "dosen" => array:22 [▶]
        "prodi" => array:1 [▶]
        "pt" => array:1 [▶]
        ]
        
        // Then map the dosen array
        array:2 [▼ // app\Filament\User\Pages\Auth\Register.php:134
          "/data_dosen/xx" => "BUDI xx, NIDN: 0000000, PT: UNIVERSITAS, Prodi: AKUNTANSI"
          "/data_dosen/xx" => "BUDI xx, NIDN: 0000000, PT: UNIVERSITAS, Prodi: KEBIDANAN"
          ]
          
          // Save and extract data based on state/key, and set NIDN and NAME as hidden input
        ```
- Profile Data Synchronization (app/Filament/Pages/Auth/EditProfile.php)
    ```sh
    private function hitDosenApiController(mixed $param)
    {
        try {
            // Retrieve ID based on dosen search
            $getId = Http::get('https://pddikti.kemdikbud.go.id/api/pencarian/dosen/' . $param)->json();
            // Get profile data based on ID
            $response = Http::get('https://pddikti.kemdikbud.go.id/api/dosen/profile/' . $getId[0]['id'])->json();
            return $response;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    // Then set each array key as a hidden input value
    ```

### Schedule & Command List
- Chatbot Train to update the chatbot model with the latest data
    ```sh
    php artisan chatbot
    ```
- Delete unused files
    ```sh
    php artisan delete:unused-file
    ```
- Unpublish all expired training courses
    ```sh
    php artisan unpublish:pelatihan
    ```
- Run the schedule
    ```sh
    php artisan schedule:work
    
    To run the schedule:
        $schedule->command('delete:unused-file')->daily()->timezone('Asia/Jakarta')->runInBackground();
        $schedule->job(new TerjadwalJob())->everyMinute()->timezone('Asia/Jakarta');
        $schedule->command('unpublish:pelatihan')->daily()->timezone('Asia/Jakarta');
        $schedule->command('chatbot')->monthly()->timezone('Asia/Jakarta');
    ```

### Server Performance References
https://medium.com/@dimdev/9-php-runtimes-performance-benchmark-cbc0527b9df9  
https://medium.com/beyn-technology/hola-frankenphp-laravel-octane-servers-comparison-pushing-the-boundaries-of-performance-d3e7ad8e652c

## View
![image](https://github.com/user-attachments/assets/67aad54f-0ba0-4e11-abc0-cb0f7cad3203)
![image](https://github.com/user-attachments/assets/7389e32b-5329-433d-8762-5d1239e39cb5)
![image](https://github.com/user-attachments/assets/32639fdc-c2e2-4114-805f-52e1b1184864)
![image](https://github.com/user-attachments/assets/4c576390-32b7-48bd-8198-3266ec28631c)
![image](https://github.com/user-attachments/assets/890af9e4-1a9c-4fdb-92f1-959b37fc14f0)





