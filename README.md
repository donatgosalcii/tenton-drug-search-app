# Aplikacioni për Kërkimin e Ilaçeve (Drug Search Application)

Ky është një aplikacion i vogël i ndërtuar me PHP Laravel që lejon përdoruesit e loguar (logged in) të kërkojnë informacione për ilaçe duke përdorur kodet NDC (National Drug Code). Aplikacioni fillimisht kontrollon një bazë të dhënash lokale dhe, nëse kodi nuk gjendet, bën një kërkesë në API-në e OpenFDA. Rezultatet e gjetura nga OpenFDA ruhen në bazën e të dhënave lokale për kërkime të mëvonshme. Përdoruesit gjithashtu mund të menaxhojnë ilaçet e ruajtura në databazën lokale dhe të eksportojnë rezultatet e kërkimit.

## Funksionalitetet Kryesore

*   **Autentifikimi i Përdoruesit:** Regjistrim dhe Logim (duke përdorur Laravel Breeze).
*   **Kërkim i Shumëfishtë NDC:** Përdoruesit mund të fusin një ose më shumë kode NDC të ndara me presje.
*   **Logjika e Kërkimit:**
    1.  Kontrollon së pari bazën e të dhënave lokale.
    2.  Nëse nuk gjendet lokalisht, kërkon në API-në e OpenFDA (një thirrje e vetme API për shumë kode NDC).
    3.  Rezultatet e gjetura nga OpenFDA ruhen në bazën e të dhënave lokale.
    4.  Shfaq rezultatet në një tabelë duke treguar burimin (Database, OpenFDA, ose Nuk u Gjet).
*   **Menaxhimi i Ilaçeve të Ruajtura:**
    *   Shfaqja e listës së të gjitha ilaçeve të ruajtura në databazën lokale me paginim.
    *   Mundësia për të shtuar manualisht një ilaç të ri në databazën lokale.
    *   Mundësia për të fshirë një ilaç të ruajtur nga databaza lokale.
*   **Eksportimi i Rezultateve:** Mundësia për të eksportuar rezultatet aktuale të kërkimit në një fajll CSV.
*   **Ndërfaqe Përdoruesi (UI):** Formular kërkimi dhe tabelë rezultatesh e thjeshtë dhe e qartë.
*   **Tregues i Ngarkimit (Spinner):** Shfaq një tregues vizual gjatë procesimit të kërkesës së kërkimit.

## Teknologjitë e Përdorura

*   PHP 8.3.6
*   Laravel Framework 12.15.0
*   MariaDB/MySQL
*   OpenFDA API
*   Tailwind CSS (përmes Laravel Breeze)
*   Vite (për menaxhimin e aseteve frontend)
*   Composer
*   NPM

## Udhëzime për Instalimin dhe Konfigurimin

Ky udhëzues supozon se keni një mjedis zhvillimi PHP të konfiguruar. Hapat specifikë për instalimin e PHP, Composer, Node.js, një serveri ueb dhe një baze të dhënash mund të ndryshojnë në varësi të sistemit tuaj operativ (Windows, macOS, Linux).

### Parakushtet Universale

*   **PHP:** Versioni 8.3.6. (Mund të instalohet përmes XAMPP, WAMP, MAMP, Homebrew në macOS, ose menaxherit të paketave në Linux).
*   **Composer:** Menaxheri i varësive për PHP. (Shihni [getcomposer.org](https://getcomposer.org) për udhëzime instalimi).
*   **Node.js dhe NPM:** Për menaxhimin e aseteve frontend. (Shihni [nodejs.org](https://nodejs.org)).
*   **Server Uebi (Opsional nëse përdorni `php artisan serve`):**
    *   **Linux:** Apache ose Nginx.
    *   **macOS:** Apache (i parainstaluar), MAMP, Valet, ose Nginx/Apache via Homebrew.
    *   **Windows:** XAMPP, WAMP, ose Laragon.
*   **Baza e të Dhënave:**
    *   **Linux:** MariaDB ose MySQL.
    *   **macOS:** MariaDB/MySQL via Homebrew, MAMP, DBngin.
    *   **Windows:** MariaDB/MySQL (zakonisht pjesë e XAMPP, WAMP, Laragon).
*   **Git:** (Opsional, për klonimin e projektit) - Mund të shkarkohet nga [git-scm.com](https://git-scm.com).

### Hapat e Përgjithshëm të Instalimit

1.  **Merrni Kodin e Projektit:**
    *   **Opsioni A:** Klononi repozitorin:
      ```bash
      git clone https://github.com/donatgosalcii/tenton-drug-search-app
      cd tenton-drug-search-app
      ```
    *   **Opsioni B (Nëse keni një arkiv .zip):** Unzip arkivin dhe shkoni në folderin/direktorin e projektit përmes terminalit/promptit të komandave.

2.  **Instaloni Varësitë PHP:**
    Në direktorinë rrënjë të projektit, ekzekutoni:
    ```bash
    composer install
    ```

3.  **Krijoni dhe Konfiguroni Fajllin e Mjedisit (`.env`):**
    *   Kopjoni `.env.example` në `.env` (nëse nuk ekziston tashmë):
      ```bash
      cp .env.example .env
      ```
      (Në Windows, mund të përdorni `copy .env.example .env`).
    *   Gjeneroni çelësin e aplikacionit:
      ```bash
      php artisan key:generate
      ```
    *   Hapni fajllin `.env` me një editor teksti dhe konfiguroni:
        *   `APP_NAME="Drug Search App"`
        *   `APP_URL=http://localhost:8000` (rekomandohet nëse përdorni `php artisan serve`) OSE `http://drugsearch.test` (nëse keni konfiguruar një host virtual).
        *   **Kredencialet e Bazës së të Dhënave:**
          ```env
          DB_CONNECTION=mysql
          DB_HOST=127.0.0.1
          DB_PORT=3306
          DB_DATABASE=drug_search_db # Sigurohuni që ky emër përputhet me atë që keni krijuar
          DB_USERNAME=drug_user    # Sigurohuni që ky emër përputhet me atë që keni krijuar
          DB_PASSWORD=your_actual_db_password # Fjalëkalimi për përdoruesin e bazës së të dhënave
          ```

4.  **Konfiguroni Bazën e të Dhënave:**
    *   Krijoni një bazë të dhënash me emrin që keni specifikuar në `.env` (p.sh., `drug_search_db`).
    *   Krijoni një përdorues të bazës së të dhënave (p.sh., `drug_user`) me fjalëkalimin përkatës dhe jepini të gjitha privilegjet për bazën e të dhënave të krijuar.
        Shembull komandash SQL (përshtateni sipas nevojës):
        ```sql
        CREATE DATABASE drug_search_db;
        CREATE USER 'drug_user'@'localhost' IDENTIFIED BY 'your_actual_db_password';
        GRANT ALL PRIVILEGES ON drug_search_db.* TO 'drug_user'@'localhost';
        FLUSH PRIVILEGES;
        ```

5.  **Ekzekutoni Migrimet e Bazës së të Dhënave:**
    Kjo krijon strukturën e tabelave (`users`, `drugs`, etj.).
    ```bash
    php artisan migrate
    ```

6.  **Instaloni Varësitë NPM dhe Ndërtoni Asetet Frontend:**
    ```bash
    npm install
    npm run build # Për ndërtim produksioni
    ```
    (Për zhvillim, mund të përdorni `npm run dev` dhe ta mbani të hapur në një terminal të veçantë).

7.  **Ekzekutoni Aplikacionin:**
    *   **Mënyra e Rekomanduar dhe më e Lehtë (Për Zhvillim Lokal):**
      Përdorni serverin e integruar të zhvillimit të Laravel:
      ```bash
      php artisan serve
      ```
      Aplikacioni do të jetë i aksesueshëm në `http://localhost:8000` (ose portin e treguar). Nëse përdorni këtë metodë, sigurohuni që `npm run dev` po ekzekutohet në një terminal tjetër nëse doni Hot Module Replacement për frontend.
    *   **Mënyra Alternative (Server Uebi i Dedikuar si Apache/Nginx):**
      Konfiguroni serverin tuaj ueb (Apache, Nginx, etj.) që DocumentRoot-i i tij të drejtohet në direktorinë `public` të projektit Laravel. Kjo kërkon konfigurim specifik të hostit virtual. Për Linux/macOS, sigurohuni që lejet e direktorive `storage` dhe `bootstrap/cache` lejojnë shkrim nga serveri ueb (p.sh., `sudo chown -R www-data:www-data storage bootstrap/cache && sudo chmod -R 775 storage bootstrap/cache`).

8.  **Aksesoni Aplikacionin:**
    Hapni URL-në përkatëse në shfletuesin tuaj (p.sh., `http://localhost:8000` ose URL-ja e hostit tuaj virtual). Regjistrohuni dhe logohuni për të përdorur funksionalitetin e kërkimit.

## Përshkrim i Shkurtër i Logjikës së Implementuar

*   **Rrugët (Routes):** Definohen në `routes/web.php`. Rrugët e autentifikimit sigurohen nga Laravel Breeze (`routes/auth.php`). Rrugët për kërkimin e ilaçeve (`/drug-search`) dhe menaxhimin e ilaçeve të ruajtura (`/stored-drugs`) janë të mbrojtura nga middleware `auth`.
*   **Kontrolluesi (`DrugSearchController.php`):**
    *   `showSearchForm()`: Shfaq formularin e kërkimit.
    *   `search()`: Menaxhon logjikën e kërkimit: validezon inputin, kontrollon DB lokale, thërret API-në OpenFDA, ruan rezultatet e reja në DB dhe përgatit të dhënat për shfaqje.
    *   `exportCsv()`: Gjeneron dhe shkarkon rezultatet aktuale të kërkimit si një fajll CSV.
*   **Kontrolluesi (`StoredDrugController.php`):**
    *   `index()`: Shfaq listën e paginuar të ilaçeve të ruajtura në DB lokale.
    *   `create()`: Shfaq formularin për të shtuar manualisht një ilaç të ri.
    *   `store()`: Validezon dhe ruan ilaçin e ri të futur manualisht në DB.
    *   `destroy()`: Fshin një ilaç të specifikuar nga DB lokale.
*   **Modeli (`Drug.php`):** Modeli Eloquent për tabelën `drugs`, me fushat e nevojshme (`ndc_code`, `brand_name`, etj.) të definuara si `$fillable`.
*   **Migrimi:** Krijohet tabela `drugs` me kolonat e specifikuara.
*   **Pamjet (Views):**
    *   `drug-search.blade.php`: Paraqet formularin e kërkimit, tabelën e rezultateve, dhe butonin e eksportit. Përfshin një tregues ngarkimi (spinner).
    *   `stored-drugs/index.blade.php`: Shfaq listën e paginuar të ilaçeve të ruajtura me opsione fshirjeje dhe një lidhje për të shtuar ilaçe të reja.
    *   `stored-drugs/create.blade.php`: Paraqet formularin për shtimin manual të ilaçeve.
*   **Ndërveprimi me OpenFDA API:** Përdor fasadën `Http` të Laravel për të bërë kërkesa GET.

## Ide për Përmirësime ose Funksionalitete Shtesë (Pikë Bonus të Mundshme)

*   Përdorimi i Laravel Livewire për një UI më reaktive pa rimbushje të plotë të faqes (veçanërisht për kërkimin).
*   Trajtim më i detajuar i gabimeve nga API dhe feedback më i mirë për përdoruesin (p.sh., mesazhe specifike kur API-ja nuk është e disponueshme).
*   Shtimi i testeve (Unit dhe Feature) për të mbuluar funksionalitetin e kërkimit dhe menaxhimit të ilaçeve.
*   Përmirësim i mëtejshëm i ndërfaqes së përdoruesit (UI/UX).
  
**KODIMI I APLIKACIONIT ËSHTË BËRË NË SISTEMIN OPERATIV UBUNTU ANDAJ MUND TË KENI PROBLEME ME PATHS**
---
