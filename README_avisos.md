# Avisos

## Archivos agregados

- `migration_avisos.sql`
- `seed_avisos.sql`
- `avisos.php`
- `includes/avisos_support.php`
- `adminavisos/common.php`
- `adminavisos/index.php`
- `adminavisos/login.php`
- `adminavisos/dashboard.php`
- `adminavisos/save.php`
- `adminavisos/delete.php`
- `adminavisos/logout.php`

## Setup

1. Ejecuta `migration_avisos.sql` en la misma base de datos configurada para el sitio.
2. Ejecuta `seed_avisos.sql` para cargar los avisos iniciales.
3. Abre `http://tu-dominio/adminavisos/login.php`.
4. Ingresa con las credenciales configuradas en `adminavisos/common.php`.
5. Crea o edita avisos desde el dashboard.
6. Revisa la pagina publica en `http://tu-dominio/avisos.php`.

## Hash de contrasena

El hash bcrypt esta hardcodeado en `adminavisos/common.php`, en la constante `AVISOS_ADMIN_PASSWORD_HASH`.

Para generar un hash nuevo desde PHP CLI:

```bash
php -r "echo password_hash('TuPasswordSeguro', PASSWORD_BCRYPT), PHP_EOL;"
```

Despues reemplaza el valor de `AVISOS_ADMIN_PASSWORD_HASH` por el nuevo hash.

## Notas

- La pagina publica usa el mismo `header.php` y `footer.php` del sitio existente.
- El acceso del panel queda oculto porque no se agregan enlaces al sitio publico.
- El panel usa sesiones, CSRF y consultas preparadas con PDO para login, alta, edicion y borrado.
- `adminavisos/common.php` intenta primero cargar `../../includes/db.php` y, si ese path no existe en este repo, usa el path real `../includes/db.php`.
