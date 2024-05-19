<p align="center"> 
    <img src="extra/logo/logo.svg" width="200" alt="Outline Logo"> 
</p>

<h2 align="center">Outline Admin</h2>

Outline Admin is a web interface for the Outline Manager API, providing a simple and user-friendly UI for managing VPN servers.


## request system 

Ubuntu 22, debian +10

## Features

Outline Manager features +

-   Ability to set expiration date for Access Keys
-   QR Code for access keys

Feel free to contribute and make this project better!

## Installation - Docker

Before proceeding with the installation of Outline Admin, ensure that `docker` and `docker-compose` are installed on your machine. Follow the instructions below:

```
apt update && apt upgrade -y
```

```
apt install docker-compose -y
```

```
git clone https://github.com/iPmartNetwork/OutlineManager.git
```
```
cd OutlineManager
```
```
cp .env.example .env
```
```
docker-compose up -d
```

Once the container is up and running, you can access the admin panel by opening the following URL in your browser:

```
http://{your_server_ip_or_hostname}:2249
```

**Note** The default port is `2249`, but you can modify it in the `.env` file.

## Admin User

To create an admin user, connect to your container using the following command:

```
docker exec -it {container_id_or_name} bash
```

**Note** To find the container ID or name you can use `docker ps` command.

Then, run this command:

```
php artisan admin:make
```

You will be prompted to enter a password. After entering the password, you can exit the container shell using the `exit` command.

## Reset Admin Password

If you need to reset the admin user password, use the `admin:password` command as follows:

```
docker exec -it {container_id_or_name} bash
php artisan admin:reset
exit
```

## Screenshots

