# دليل التحقق من النشر (Deployment Verification)

## 1. تشغيل أمر التحقق التلقائي

على السيرفر (بعد `composer install`):

```bash
cd /home/dhclub/repositories/dba_elhesn_2026
/opt/cpanel/ea-php85/root/usr/bin/php artisan deploy:check
```

يعرض جدول بحالة: PHP، الامتدادات، .env، MySQL، SQL Server، Storage، Passport.

---

## 2. التحقق اليدوي

### أ) إصدار PHP والامتدادات

```bash
/opt/cpanel/ea-php85/root/usr/bin/php -v
/opt/cpanel/ea-php85/root/usr/bin/php -m | grep -E "sodium|sqlsrv|pdo_mysql"
```

### ب) اختبار الـ Artisan

```bash
cd /home/dhclub/repositories/dba_elhesn_2026
/opt/cpanel/ea-php85/root/usr/bin/php artisan --version
```

### ج) اختبار الاتصال بقاعدة MySQL

```bash
/opt/cpanel/ea-php85/root/usr/bin/php artisan tinker --execute="DB::connection('mysql')->getPdo(); echo 'MySQL OK';"
```

### د) اختبار أحد أوامر المزامنة (يتطلب ext-sqlsrv)

```bash
/opt/cpanel/ea-php85/root/usr/bin/php artisan sport_teams:daily
```

---

## 3. روابط للتحقق من المتصفح

| الرابط | الوصف |
|--------|--------|
| `https://dhclubapp.xyz/` | الصفحة الرئيسية (يجب أن ترجع "OK") |
| `https://dhclubapp.xyz/up` | Health check (Laravel) |
| `https://dhclubapp.xyz/admin/auth/login` | لوحة التحكم - تسجيل الدخول |
| `https://dhclubapp.xyz/api/v1/intros` | API عام (بدون مصادقة) |

---

## 4. اختبار الـ API (من الطرفية)

```bash
curl -s https://dhclubapp.xyz/
curl -s https://dhclubapp.xyz/up
curl -s https://dhclubapp.xyz/api/v1/intros
```

---

## 5. التحقق من الـ Cron

بعد تشغيل أحد المهام، راجع السجلات:

```bash
# إذا كان الـ cron يكتب لملف log
tail -f /home/dhclub/repositories/dba_elhesn_2026/storage/logs/laravel.log
```

أو شغّل الأمر يدوياً للتأكد:

```bash
/opt/cpanel/ea-php85/root/usr/bin/php /home/dhclub/repositories/dba_elhesn_2026/artisan news:daily
```

---

## 6. قائمة تحقق سريعة

- [ ] `composer install --no-dev --optimize-autoloader` نجح
- [ ] ملف `.env` موجود ومُعدّ (DB_*, APP_KEY, etc.)
- [ ] `php artisan key:generate` (إن لم يكن APP_KEY موجوداً)
- [ ] `php artisan storage:link`
- [ ] `php artisan config:cache`
- [ ] `php artisan passport:install` (أو المفاتيح موجودة)
- [ ] `php artisan deploy:check` يعرض OK لكل الفحوصات الحرجة
- [ ] الموقع يفتح من المتصفح
- [ ] لوحة الإدارة `/admin/auth/login` تعمل
- [ ] الـ API `/api/v1/intros` يرجع بيانات
