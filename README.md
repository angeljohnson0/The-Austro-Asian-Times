# 🗞️ The Austro-Asian Times

> A database-driven news content management system built with **PHP 8** and **MySQL 8**  
> *HIT326 – Internet Programming · Charles Darwin University*

---

## 📋 Requirements

| Requirement | Version |
|---|---|
| WampServer | 3.3 or later |
| PHP | 8.0+ |
| MySQL | 8.0+ |
| Browser | Any modern browser |

**Required PHP extensions:** `pdo` · `pdo_mysql` · `fileinfo`

---

## ⚙️ Installation

**1. Clone or extract the project into your WAMP document root:**

```
C:\wamp64\www\AustroAsian\
```

**2. Set up the database** — open phpMyAdmin and run:

```
database/create.sql   ← creates the database and tables
database/load.sql     ← loads test accounts, articles, tags, and sample data
```

**3. (Optional) Update database credentials** in `config/database.php`:

```php
$host     = 'localhost';
$dbname   = 'austro_asian_times';
$username = 'root';
$password = '';
```

**4. Visit the app in your browser:**

```
http://localhost/AustroAsian/
```

---

## 🔐 Test Accounts

| Username | Password | Role |
|---|---|---|
| `editor1` | `password` | Editor |
| `journalist1` | `password` | Journalist |
| `journalist2` | `password` | Journalist |

---

## ✨ Features

- **Journalist workflow** — login, create, edit, and submit articles
- **Editorial moderation** — approve or reject with feedback
- **Folksonomy tagging** — tag-click filtering across articles
- **Comment system** — submission and moderation
- **Monthly archive** — browse articles by month
- **Journalist profiles** — dedicated profile pages
- **Search** — keyword, tag, and date-range filtering
- **RSS 2.0 feed** — live feed at `/feed.php`
- **Image uploads** — server-side MIME validation
- **Responsive layout** — fully responsive down to 320px

---

## 🗄️ Database Testing

Run `database/test.sql` in phpMyAdmin to verify all CRUD operations across every table.

---

## 🗑️ Uninstalling

1. Delete the project folder from `C:\wamp64\www\`
2. Drop the database in phpMyAdmin:

```sql
DROP DATABASE IF EXISTS austro_asian_times;
```

---

## 👥 Team

| Name | Role |
|---|---|
| **Elka Mary Shibu** | Project Lead / Back-End Development |
| **Angel Johnson** | Front-End Development / CSS |
| **Navaneeth Sreekumar** | Database Design and Testing |
| **Alex Shaji** | Back-End Development / Security |
| **Alen Saji Saji** | Feature Development / Application Testing |

---

## 📄 License

Source code is licensed under **[GPL-3.0](LICENSE)**.  
Article content is licensed under **[CC BY 4.0](https://creativecommons.org/licenses/by/4.0/)**.
