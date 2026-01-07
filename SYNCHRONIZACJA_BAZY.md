# 🔄 Synchronizacja bazy danych z serwera

## 📤 KOMENDY NA SERWERZE (SSH)

### Opcja 1: Eksport całej bazy MySQL

```bash
# Zaloguj się na serwer przez SSH
ssh uzytkownik@serwer.pl

# Przejdź do katalogu projektu
cd /sciezka/do/projektu/backend

# Eksport całej bazy danych
php artisan db:backup > backup_$(date +%Y%m%d_%H%M%S).sql

# LUB użyj mysqldump bezpośrednio (jeśli masz dostęp):
mysqldump -u DB_USER -p DB_NAME > backup_$(date +%Y%m%d_%H%M%S).sql

# LUB eksport tylko tabeli pages (jeśli chcesz tylko menu):
php artisan tinker
>>> \DB::table('pages')->get()->toJson();
# Skopiuj wynik i zapisz do pliku pages.json
```

### Opcja 2: Eksport tylko tabeli `pages` (dla menu)

```bash
# Na serwerze w katalogu projektu:
php artisan tinker --execute="file_put_contents('pages_export.json', json_encode(\DB::table('pages')->get(), JSON_PRETTY_PRINT));"
```

### Opcja 3: Eksport przez Laravel (najlepsze)

```bash
# Na serwerze:
php artisan db:export --table=pages --file=pages_export.json

# LUB eksport wszystkich danych:
php artisan db:export --file=full_backup.json
```

---

## 📥 KOMENDY LOKALNIE (Windows)

### Opcja 1: Import z pliku SQL (jeśli używasz MySQL lokalnie)

```bash
# Jeśli masz MySQL lokalnie:
mysql -u root -p nazwa_bazy < backup_20250101_120000.sql
```

### Opcja 2: Import z JSON (dla tabeli pages)

```bash
# Skopiuj plik pages_export.json z serwera do lokalnego projektu
# Następnie:
php artisan tinker --execute="
\$pages = json_decode(file_get_contents('pages_export.json'), true);
foreach(\$pages as \$page) {
    \App\Models\Page::updateOrCreate(['slug' => \$page['slug']], \$page);
}
echo 'Zaimportowano ' . count(\$pages) . ' stron';
"
```

### Opcja 3: Import przez artisan (jeśli masz pakiet)

```bash
php artisan db:import --file=full_backup.json
```

---

## 🚀 SZYBKA SYNCHRONIZACJA - KROK PO KROKU

### Krok 1: Na serwerze (SSH)

```bash
# Zaloguj się na serwer
ssh uzytkownik@serwer.pl
cd /sciezka/do/projektu/backend

# Eksport tabeli pages do JSON
php artisan tinker --execute="
\$pages = \App\Models\Page::all()->toArray();
file_put_contents('pages_export.json', json_encode(\$pages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo 'Eksportowano ' . count(\$pages) . ' stron';
"

# Pobierz plik (z innego terminala lokalnie):
# scp uzytkownik@serwer.pl:/sciezka/do/projektu/backend/pages_export.json .
```

### Krok 2: Lokalnie (Windows PowerShell)

```bash
# Skopiuj plik pages_export.json do katalogu backend

# Import do lokalnej bazy
php artisan tinker --execute="
\$pages = json_decode(file_get_contents('pages_export.json'), true);
foreach(\$pages as \$page) {
    unset(\$page['id'], \$page['created_at'], \$page['updated_at']);
    \App\Models\Page::updateOrCreate(['slug' => \$page['slug']], \$page);
}
echo 'Zaimportowano ' . count(\$pages) . ' stron';
"
```

---

## 📋 EKSPORT TYLKO STRON Z MENU HEADER

### Na serwerze:

```bash
php artisan tinker --execute="
\$headerPages = \App\Models\Page::inMenu('header')->get()->toArray();
file_put_contents('header_pages.json', json_encode(\$headerPages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo 'Eksportowano ' . count(\$headerPages) . ' stron z menu header';
"
```

### Lokalnie:

```bash
php artisan tinker --execute="
\$pages = json_decode(file_get_contents('header_pages.json'), true);
foreach(\$pages as \$page) {
    unset(\$page['id'], \$page['created_at'], \$page['updated_at']);
    \App\Models\Page::updateOrCreate(['slug' => \$page['slug']], \$page);
}
echo 'Zaimportowano ' . count(\$pages) . ' stron';
"
```

---

## 🔍 SPRAWDZENIE CO JEST NA SERWERZE

### Na serwerze (SSH):

```bash
php artisan tinker --execute="
echo 'Strony w menu HEADER:' . PHP_EOL;
\App\Models\Page::inMenu('header')->get(['title', 'slug', 'menu_order', 'route_name'])->each(function(\$p) {
    echo '- ' . \$p->title . ' (order: ' . \$p->menu_order . ', slug: ' . \$p->slug . ')' . PHP_EOL;
});
"
```

---

## 💡 ALTERNATYWA: Eksport przez panel admina

Jeśli masz dostęp do panelu admina na serwerze:
1. Wejdź na `/admin/pages`
2. Zobacz wszystkie strony
3. Ręcznie dodaj je do `PageSeeder.php` lokalnie

---

## ⚠️ UWAGI

- **Backup przed importem**: Zawsze zrób backup lokalnej bazy przed importem
- **ID**: Usuń `id`, `created_at`, `updated_at` przy imporcie (będą wygenerowane automatycznie)
- **Relacje**: Jeśli strony mają `parent_id`, upewnij się że rodzic jest zaimportowany pierwszy
- **SQLite vs MySQL**: Jeśli serwer używa MySQL a lokalnie SQLite, użyj JSON zamiast SQL

---

## 🎯 NAJSZYBSZA METODA - EKSPORT WSZYSTKIEGO (rekomendowana)

### Eksport stron (pages) i wpisów blogowych (blog_posts)

1. **Na serwerze - eksport wszystkiego:**
```bash
# Eksport stron
php artisan tinker --execute="file_put_contents('pages_export.json', json_encode(\App\Models\Page::all()->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); echo 'Eksportowano stron: ' . \App\Models\Page::count();"

# Eksport wpisów blogowych
php artisan tinker --execute="file_put_contents('blog_posts_export.json', json_encode(\App\Models\BlogPost::all()->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); echo 'Eksportowano wpisów: ' . \App\Models\BlogPost::count();"

# LUB wszystko razem:
php artisan tinker --execute="
file_put_contents('pages_export.json', json_encode(\App\Models\Page::all()->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
file_put_contents('blog_posts_export.json', json_encode(\App\Models\BlogPost::all()->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo 'Eksportowano: ' . \App\Models\Page::count() . ' stron i ' . \App\Models\BlogPost::count() . ' wpisów';
"
```

2. **Pobierz pliki przez SCP lub FTP:**
```powershell
# PowerShell lokalnie:
scp uzytkownik@serwer.pl:/sciezka/do/projektu/backend/pages_export.json .
scp uzytkownik@serwer.pl:/sciezka/do/projektu/backend/blog_posts_export.json .
```

3. **Import lokalnie:**
```bash
# Import stron
php artisan tinker --execute="
\$pages = json_decode(file_get_contents('pages_export.json'), true);
foreach(\$pages as \$p) {
    unset(\$p['id'], \$p['created_at'], \$p['updated_at']);
    \App\Models\Page::updateOrCreate(['slug' => \$p['slug']], \$p);
}
echo 'Zaimportowano ' . count(\$pages) . ' stron';
"

# Import wpisów blogowych
php artisan tinker --execute="
\$posts = json_decode(file_get_contents('blog_posts_export.json'), true);
foreach(\$posts as \$post) {
    \$oldId = \$post['id'];
    unset(\$post['id'], \$post['created_at'], \$post['updated_at']);
    \$newPost = \App\Models\BlogPost::updateOrCreate(['slug' => \$post['slug']], \$post);

    // Import tagów jeśli są
    if(isset(\$post['tags']) && is_array(\$post['tags'])) {
        \$tagIds = [];
        foreach(\$post['tags'] as \$tag) {
            \$tagModel = \App\Models\Tag::firstOrCreate(['name' => \$tag['name'], 'type' => 'blog']);
            \$tagIds[] = \$tagModel->id;
        }
        \$newPost->tags()->sync(\$tagIds);
    }
}
echo 'Zaimportowano ' . count(\$posts) . ' wpisów blogowych';
"
```

---

## 📊 SPRAWDZENIE ILOŚCI WPISÓW

### Na serwerze:
```bash
php artisan tinker --execute="
echo 'Strony w menu HEADER: ' . \App\Models\Page::inMenu('header')->count() . PHP_EOL;
echo 'Wszystkie strony: ' . \App\Models\Page::count() . PHP_EOL;
echo 'Wpisy blogowe: ' . \App\Models\BlogPost::count() . PHP_EOL;
echo 'Opublikowane wpisy: ' . \App\Models\BlogPost::where('status', 'published')->count() . PHP_EOL;
"
```

### Lokalnie:
```bash
php artisan tinker --execute="
echo 'Strony w menu HEADER: ' . \App\Models\Page::inMenu('header')->count() . PHP_EOL;
echo 'Wszystkie strony: ' . \App\Models\Page::count() . PHP_EOL;
echo 'Wpisy blogowe: ' . \App\Models\BlogPost::count() . PHP_EOL;
echo 'Opublikowane wpisy: ' . \App\Models\BlogPost::where('status', 'published')->count() . PHP_EOL;
"
```

