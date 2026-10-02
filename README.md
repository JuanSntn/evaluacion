# Arquitectura del sistema

## Descripción general

La aplicación está construida con Laravel y organiza sus funciones mediante rutas, controladores, solicitudes de validación (`FormRequest`), modelos Eloquent, servicios, vistas Blade y JavaScript.

Las funciones internas están protegidas por el middleware `auth`. Las pantallas de registro, inicio de sesión y recuperación de contraseña utilizan el middleware `guest`.

## 1. Autenticación y sesiones

### Rutas

```text
GET  /register          Formulario de registro
POST /register          Registrar una cuenta
GET  /login             Formulario de inicio de sesión
POST /login             Iniciar sesión
GET  /forgot-password   Formulario de recuperación
POST /forgot-password   Actualizar una contraseña olvidada
POST /logout            Cerrar sesión
```

### Flujo de autenticación

- `RegisterRequest` valida nombre, correo, RFC y contraseña. También impide registrar un correo o RFC existente.
- `LoginRequest` busca la cuenta mediante `email_hash` y verifica la contraseña con `Hash::check`.
- `RecoverPasswordRequest` comprueba la combinación de correo y RFC antes de permitir el cambio de contraseña.
- `StartUserSession` se utiliza en el registro, el inicio de sesión y el cambio de contraseña desde Configuración de Cuenta.
- Durante la recuperación de contraseña no se inicia una sesión nueva: se actualiza la contraseña, se invalidan las sesiones existentes y se regresa al login.
- Al cerrar sesión se ejecuta `Auth::logout()`, se invalida la sesión y se regenera el token CSRF.

### Sesión única por usuario

Laravel utiliza `SESSION_DRIVER=database` y una tabla `sessions` basada en el esquema de sesiones de Laravel, con una restricción `UNIQUE` adicional sobre `user_id`.

Antes de iniciar una sesión, `StartUserSession` elimina cualquier sesión existente del usuario. Después ejecuta `Auth::login()` y regenera el identificador de sesión para prevenir ataques de fijación de sesión.

No existe una tabla `user_sessions` ni un token de sesión personalizado. Laravel administra el identificador de sesión mediante la cookie del navegador y el registro correspondiente en `sessions`.

## 2. Colaboradores

El módulo permite registrar, consultar, editar y eliminar colaboradores pertenecientes al dueño autenticado.

### Rutas

```text
GET     /collaborators              Listar colaboradores
GET     /collaborators/create       Mostrar formulario
POST    /collaborators              Guardar colaborador
GET     /collaborators/{id}         Mostrar colaborador
GET     /collaborators/{id}/edit    Mostrar edición
PUT     /collaborators/{id}         Actualizar colaborador
DELETE  /collaborators/{id}         Eliminar colaborador
```

### Arquitectura

- `CollaboratorController` controla el CRUD y obtiene únicamente registros del usuario autenticado.
- `StoreCollaboratorRequest` y `UpdateCollaboratorRequest` validan correo, RFC, CURP, NSS, domicilio fiscal, información laboral, salarios y estado.
- El `user_id` se obtiene en el backend desde la sesión autenticada; no se recibe desde el formulario.
- `ensureOwnership()` evita IDOR: si alguien intenta consultar, editar o eliminar un colaborador ajeno, la aplicación responde con `404`.
- `state_id` relaciona cada colaborador con el catálogo de estados de México.

Los datos sensibles son recuperables y se almacenan cifrados. Los hashes de correo, RFC, CURP y NSS se utilizan para detectar duplicados sin consultar directamente los valores cifrados.

## 3. Usuarios y Servicios

La pantalla `GET /users-and-services` contiene dos pestañas: **Usuarios** y **Servicios**.

### Usuarios

Representan los usuarios registrados y administrados por el dueño de la cuenta. No son cuentas autenticables del sistema.

```text
GET     /users-and-services/users/create       Mostrar formulario
POST    /users-and-services/users              Guardar usuario
GET     /users-and-services/users/{id}         Mostrar usuario
GET     /users-and-services/users/{id}/edit    Mostrar edición
PUT     /users-and-services/users/{id}         Actualizar usuario
DELETE  /users-and-services/users/{id}         Eliminar usuario
```

- `ManagedUserController` controla el CRUD.
- Los datos son nombre, RFC, dirección, teléfono y website.
- `created_by` se asigna en el backend con el ID de la cuenta autenticada.
- La cuenta autenticada funciona como dueño de los usuarios que registra.
- `ensureOwnership()` impide consultar, editar o eliminar registros creados por otra cuenta.
- Los datos se guardan cifrados y `rfc_hash` permite controlar RFC duplicados por dueño.

### Servicios

El módulo administra publicaciones obtenidas de JSONPlaceholder.

```text
GET     /users-and-services/services/posts
POST    /users-and-services/services/posts
PUT     /users-and-services/services/posts/{id}
DELETE  /users-and-services/services/posts/{id}
```

- `services.js` envía solicitudes asíncronas con `fetch` y actualiza la interfaz sin recargar la página.
- `ServicePostController` valida la solicitud y genera las respuestas HTTP de Laravel.
- `JsonPlaceholderPostService` encapsula la comunicación con el servicio externo.
- La creación exige `title` y `body` y responde con HTTP `201` cuando JSONPlaceholder acepta la solicitud.
- Los problemas de conexión responden con `504`; las respuestas incorrectas del servicio externo responden con `502`.

### Persistencia simulada

JSONPlaceholder simula las operaciones `POST`, `PUT` y `DELETE`, pero no conserva sus cambios.

- Las publicaciones creadas durante la sesión se mantienen temporalmente en memoria en `services.js`.
- Las publicaciones temporales pueden editarse y eliminarse localmente.
- Las publicaciones originales utilizan los endpoints Laravel para `PUT` y `DELETE`.
- Al recargar la página se consulta nuevamente JSONPlaceholder y se recupera su lista original.
- No se utiliza una tabla local para almacenar publicaciones.

## 4. Algoritmos

El módulo determina cuáles palabras son palíndromas.

```text
GET  /algorithm    Mostrar formulario
POST /algorithm    Ejecutar algoritmo
```

- `PalindromeRequest` exige entre 3 y 20 palabras.
- Cada palabra es obligatoria, sólo puede contener letras y admite un máximo de 100 caracteres.
- `AlgorithmController` elimina espacios exteriores y convierte cada palabra a minúsculas.
- La palabra se divide en caracteres y se compara con el arreglo invertido.
- El resultado de cada palabra se muestra en la misma vista.

## 5. Configuración de Cuenta

Este módulo permite modificar la cuenta autenticada y su contraseña.

```text
GET /account             Mostrar configuración
PUT /account             Actualizar información
PUT /account/password    Actualizar contraseña
```

`UpdateAccountRequest` valida nombre, correo, RFC, dirección, teléfono y website. El correo y el RFC no pueden estar registrados por otra cuenta. Si el website no incluye protocolo, se agrega `https://` antes de validarlo.

`UpdatePasswordRequest` exige la contraseña actual, una contraseña nueva de al menos ocho caracteres con letras y números, y su confirmación. Después del cambio, `StartUserSession` elimina otras sesiones activas y crea una nueva sesión.

## 6. Protección de información

Los campos sensibles recuperables utilizan el cifrado nativo de Laravel mediante casts `encrypted`.

Las contraseñas utilizan hashing mediante el cast `hashed`, por lo que no son reversibles.

Los índices HMAC-SHA-256 no sustituyen al cifrado. Se utilizan únicamente para permitir búsquedas y controles de unicidad sobre información cifrada.

```text
Correo -> email_hash o correo_hash
RFC    -> rfc_hash
CURP   -> curp_hash
NSS    -> nss_hash
```

El proceso de los índices ciegos es:

```text
Dato normalizado -> HMAC-SHA-256 -> columna hash
```

## 7. Base de datos

La aplicación utiliza una base de datos relacional MySQL.

### Tablas principales

```text
roles
users
states
collaborators
managed_users
password_reset_tokens
sessions
```

### Relaciones

```text
roles
  └── users
       ├── collaborators
       │    └── states
       ├── managed_users
       ├── password_reset_tokens
       └── sessions
```

```text
roles.id       -> users.role_id
users.id       -> collaborators.user_id
states.id      -> collaborators.state_id
users.id       -> managed_users.created_by
users.id       -> password_reset_tokens.user_id
users.id       -> sessions.user_id
```

El script SQL, el diagrama UML y el esquema DBML están disponibles en:

```text
database/scripts/database_schema.sql
database/scripts/database_uml.puml
database/scripts/db.bdml
```
