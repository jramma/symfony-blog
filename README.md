# Start the server

```sh
symfony server:start --port=8080
```

```sh
symfony server:start --port=8080 --daemon
```

# Requisitos

- Definir .env , .env.dev , .env.test
- Recomiendo phpmyadmin
- mysql 
- PHP 8.2 o superior

# Instalación de dependencias

Para instalar las dependencias del proyecto, ejecuta el siguiente comando:

```sh
composer install
```

NO SIRVE PARA PRODUCCIÓN, las peticiones y respuestas son http, es un proyecto para localhost.