# Avisos

## Archivos agregados

- `avisos.php`
- `includes/avisos_support.php`
- `storage/avisos.php`
- `adminavisos/common.php`
- `adminavisos/index.php`
- `adminavisos/login.php`
- `adminavisos/dashboard.php`
- `adminavisos/save.php`
- `adminavisos/delete.php`
- `adminavisos/logout.php`

## Setup

1. Abre `http://tu-dominio/adminavisos/login.php`.
2. Ingresa con las credenciales configuradas en `adminavisos/common.php`.
3. Crea o edita avisos desde el dashboard.
4. Revisa la pagina publica en `http://tu-dominio/avisos.php`.

En el primer acceso se genera `storage/avisos.php` con los avisos predeterminados actuales. A partir de ese momento, el panel y la pagina publica leen y escriben sobre ese mismo archivo.

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
- El panel usa sesiones, CSRF y almacenamiento local en archivo para alta, edicion y borrado.
- El almacenamiento de avisos vive en `storage/avisos.php` y no depende de extensiones MySQL en Apache/PHP.
